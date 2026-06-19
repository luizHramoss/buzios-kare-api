<?php

namespace App\Repositories\Contracts;

use App\Models\Admin;
use Illuminate\Pagination\LengthAwarePaginator;

interface AdminRepositoryInterface
{
    public function findById(int $id): ?Admin;

    public function findByUuid(string $uuid): ?Admin;

    public function findByUuidOrFail(string $uuid): Admin;

    public function findByEmail(string $email): ?Admin;

    public function create(array $data): Admin;

    public function update(Admin $admin, array $data): Admin;

    public function delete(Admin $admin): bool;

    public function paginate(int $perPage = 15): LengthAwarePaginator;

    public function countSuperAdmins(): int;
}
