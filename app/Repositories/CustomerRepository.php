<?php

namespace App\Repositories;

use App\Enums\AppointmentStatus;
use App\Enums\CustomerStatus;
use App\Models\Appointment;
use App\Models\Customer;
use App\Repositories\Contracts\CustomerRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;

class CustomerRepository implements CustomerRepositoryInterface
{
    public function findById(int $id): ?Customer
    {
        return Customer::find($id);
    }

    public function findByUuid(string $uuid): ?Customer
    {
        return Customer::whereUuid($uuid)->first();
    }

    public function findByUuidOrFail(string $uuid): Customer
    {
        return Customer::whereUuid($uuid)->firstOrFail();
    }

    public function findByEmail(string $email): ?Customer
    {
        return Customer::where('email', $email)->first();
    }

    public function findByCpf(string $cpf): ?Customer
    {
        return Customer::where('cpf', $cpf)->first();
    }

    public function create(array $data): Customer
    {
        return Customer::create($data);
    }

    public function update(Customer $customer, array $data): Customer
    {
        $customer->update($data);

        return $customer->refresh();
    }

    public function delete(Customer $customer): bool
    {
        return $customer->delete();
    }

    public function paginate(int $perPage = 15, array $filters = []): LengthAwarePaginator
    {
        $query = Customer::query();

        if (! empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (! empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('cpf', 'like', "%{$search}%");
            });
        }

        return $query->latest()->paginate($perPage);
    }

    public function countActive(): int
    {
        return Customer::where('status', CustomerStatus::ACTIVE->value)->count();
    }

    public function countFutureActiveAppointments(int $customerId): int
    {
        return Appointment::query()
            ->where('customer_id', $customerId)
            ->where('date', '>=', now()->toDateString())
            ->whereIn('status', array_map(fn ($s) => $s->value, AppointmentStatus::activeStatuses()))
            ->count();
    }
}
