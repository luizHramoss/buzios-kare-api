<?php

namespace App\Services;

use App\Enums\CustomerStatus;
use App\Models\Customer;
use App\Repositories\Contracts\CustomerRepositoryInterface;
use App\Support\AuditLogger;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Hash;

class CustomerAdminService
{
    public function __construct(
        private readonly CustomerRepositoryInterface $customers,
        private readonly AuditLogger $auditLogger,
    ) {}

    public function paginate(int $perPage, array $filters): LengthAwarePaginator
    {
        return $this->customers->paginate($perPage, $filters);
    }

    public function create(array $data, int $adminId): Customer
    {
        $customer = $this->customers->create([
            ...$data,
            'password' => Hash::make($data['password']),
            'status'   => $data['status'] ?? CustomerStatus::ACTIVE->value,
        ]);

        $this->auditLogger->log(
            event: 'customer.created_by_admin',
            auditable: $customer,
            newValues: ['email' => $customer->email, 'name' => $customer->name],
            userId: $adminId,
            userType: 'admin',
        );

        return $customer;
    }

    public function update(Customer $customer, array $data, int $adminId): Customer
    {
        $oldValues = $customer->only(['name', 'email', 'phone', 'whatsapp', 'status']);

        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        $updated = $this->customers->update($customer, $data);

        $this->auditLogger->log(
            event: 'customer.updated_by_admin',
            auditable: $updated,
            oldValues: $oldValues,
            newValues: array_diff_key($data, ['password' => null]),
            userId: $adminId,
            userType: 'admin',
        );

        return $updated;
    }

    public function delete(Customer $customer, int $adminId): void
    {
        $this->auditLogger->log(
            event: 'customer.deleted_by_admin',
            auditable: $customer,
            oldValues: ['email' => $customer->email, 'name' => $customer->name],
            userId: $adminId,
            userType: 'admin',
        );

        $this->customers->delete($customer);
    }
}
