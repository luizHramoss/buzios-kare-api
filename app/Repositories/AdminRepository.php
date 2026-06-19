<?php

namespace App\Repositories;

use App\Enums\AdminRole;
use App\Models\Admin;
use App\Repositories\Contracts\AdminRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;

class AdminRepository implements AdminRepositoryInterface
{
    public function findById(int $id): ?Admin
    {
        return Admin::find($id);
    }

    public function findByUuid(string $uuid): ?Admin
    {
        return Admin::whereUuid($uuid)->first();
    }

    public function findByUuidOrFail(string $uuid): Admin
    {
        return Admin::whereUuid($uuid)->firstOrFail();
    }

    public function findByEmail(string $email): ?Admin
    {
        return Admin::where('email', $email)->first();
    }

    public function create(array $data): Admin
    {
        return Admin::create($data);
    }

    public function update(Admin $admin, array $data): Admin
    {
        $admin->update($data);

        return $admin->refresh();
    }

    public function delete(Admin $admin): bool
    {
        return $admin->delete();
    }

    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return Admin::query()->latest()->paginate($perPage);
    }

    public function countSuperAdmins(): int
    {
        return Admin::where('role', AdminRole::SUPER_ADMIN->value)->count();
    }
}
