<?php

namespace App\Services;

use App\DTOs\Customer\UpdateCustomerProfileDTO;
use App\Models\Customer;
use App\Repositories\Contracts\CustomerRepositoryInterface;
use App\Support\AuditLogger;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class CustomerProfileService
{
    public function __construct(
        private readonly CustomerRepositoryInterface $customers,
        private readonly AuditLogger $auditLogger,
    ) {}

    public function updateProfile(Customer $customer, UpdateCustomerProfileDTO $dto): Customer
    {
        $oldValues = $customer->only(['name', 'phone', 'whatsapp', 'notes']);
        $newValues = $dto->toArrayFiltered();

        if (empty($newValues)) {
            return $customer;
        }

        $updated = $this->customers->update($customer, $newValues);

        $this->auditLogger->log(
            event: 'customer.profile_updated',
            auditable: $updated,
            oldValues: $oldValues,
            newValues: $newValues,
            userId: $customer->id,
            userType: 'customer',
        );

        return $updated;
    }

    public function updatePassword(Customer $customer, string $currentPassword, string $newPassword): void
    {
        if (! Hash::check($currentPassword, $customer->password)) {
            throw ValidationException::withMessages([
                'current_password' => ['Senha atual incorreta.'],
            ]);
        }

        $this->customers->update($customer, [
            'password' => Hash::make($newPassword),
        ]);

        $this->auditLogger->log(
            event: 'customer.password_changed',
            auditable: $customer,
            userId: $customer->id,
            userType: 'customer',
        );

        // Revoga todos os outros tokens por segurança após troca de senha
        $customer->tokens()->where('id', '!=', $customer->currentAccessToken()?->id)->delete();
    }
}
