<?php

namespace App\Repositories\Contracts;

use App\Models\Customer;
use Illuminate\Pagination\LengthAwarePaginator;

interface CustomerRepositoryInterface
{
    public function findById(int $id): ?Customer;

    public function findByUuid(string $uuid): ?Customer;

    public function findByUuidOrFail(string $uuid): Customer;

    public function findByEmail(string $email): ?Customer;

    public function findByCpf(string $cpf): ?Customer;

    public function create(array $data): Customer;

    public function update(Customer $customer, array $data): Customer;

    public function delete(Customer $customer): bool;

    public function paginate(int $perPage = 15, array $filters = []): LengthAwarePaginator;

    public function countActive(): int;

    /**
     * Conta quantos agendamentos futuros ativos o cliente possui.
     * Usado para validar max_future_appointments.
     */
    public function countFutureActiveAppointments(int $customerId): int;
}
