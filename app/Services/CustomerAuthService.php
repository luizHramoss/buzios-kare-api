<?php

namespace App\Services;

use App\DTOs\Customer\LoginDTO;
use App\DTOs\Customer\RegisterCustomerDTO;
use App\Enums\CustomerStatus;
use App\Events\CustomerRegistered;
use App\Exceptions\CustomerBlockedException;
use App\Models\Customer;
use App\Repositories\Contracts\CustomerRepositoryInterface;
use App\Support\AuditLogger;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class CustomerAuthService
{
    public function __construct(
        private readonly CustomerRepositoryInterface $customers,
        private readonly AuditLogger $auditLogger,
    ) {}

    public function register(RegisterCustomerDTO $dto): array
    {
        $customer = $this->customers->create([
            ...$dto->toArray(),
            'password' => Hash::make($dto->password),
            'status'   => CustomerStatus::ACTIVE->value,
        ]);

        $this->auditLogger->log(
            event: 'customer.registered',
            auditable: $customer,
            newValues: ['email' => $customer->email],
            userId: $customer->id,
            userType: 'customer',
        );

        event(new CustomerRegistered($customer));

        $token = $customer->createToken('customer-auth', $customer->getTokenAbilities())->plainTextToken;

        return ['customer' => $customer, 'token' => $token];
    }

    public function login(LoginDTO $dto): array
    {
        $customer = $this->customers->findByEmail($dto->email);

        if (! $customer || ! Hash::check($dto->password, $customer->password)) {
            $this->auditLogger->log(
                event: 'customer.login_failed',
                newValues: ['email' => $dto->email],
            );

            throw ValidationException::withMessages([
                'email' => ['Credenciais inválidas.'],
            ]);
        }

        if ($customer->status === CustomerStatus::BLOCKED) {
            throw new CustomerBlockedException();
        }

        if (! $customer->isActive()) {
            throw ValidationException::withMessages([
                'email' => ['Esta conta está inativa. Entre em contato com o suporte.'],
            ]);
        }

        $this->auditLogger->log(
            event: 'customer.login_success',
            auditable: $customer,
            userId: $customer->id,
            userType: 'customer',
        );

        $token = $customer->createToken('customer-auth', $customer->getTokenAbilities())->plainTextToken;

        return ['customer' => $customer, 'token' => $token];
    }

    public function logout(Customer $customer): void
    {
        $this->auditLogger->log(
            event: 'customer.logout',
            auditable: $customer,
            userId: $customer->id,
            userType: 'customer',
        );

        $customer->currentAccessToken()->delete();
    }
}
