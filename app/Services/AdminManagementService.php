<?php

namespace App\Services;

use App\Enums\AdminRole;
use App\Exceptions\InsufficientPermissionException;
use App\Models\Admin;
use App\Repositories\Contracts\AdminRepositoryInterface;
use App\Support\AuditLogger;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AdminManagementService
{
    public function __construct(
        private readonly AdminRepositoryInterface $admins,
        private readonly AuditLogger $auditLogger,
    ) {}

    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return $this->admins->paginate($perPage);
    }

    public function create(array $data, Admin $actingAdmin): Admin
    {
        $this->guardSuperAdmin($actingAdmin);

        $admin = $this->admins->create([
            ...$data,
            'password' => Hash::make($data['password']),
        ]);

        $this->auditLogger->log(
            event: 'admin.created',
            auditable: $admin,
            newValues: ['email' => $admin->email, 'role' => $admin->role->value ?? $data['role']],
            userId: $actingAdmin->id,
            userType: 'admin',
        );

        return $admin;
    }

    public function update(Admin $target, array $data, Admin $actingAdmin): Admin
    {
        $this->guardSuperAdmin($actingAdmin);

        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        $oldValues = $target->only(['name', 'email', 'role', 'status']);

        $updated = $this->admins->update($target, $data);

        $this->auditLogger->log(
            event: 'admin.updated',
            auditable: $updated,
            oldValues: $oldValues,
            newValues: array_diff_key($data, ['password' => null]),
            userId: $actingAdmin->id,
            userType: 'admin',
        );

        return $updated;
    }

    public function delete(Admin $target, Admin $actingAdmin): void
    {
        $this->guardSuperAdmin($actingAdmin);

        if ($actingAdmin->id === $target->id) {
            throw ValidationException::withMessages([
                'admin' => ['Você não pode excluir sua própria conta.'],
            ]);
        }

        if ($target->isSuperAdmin() && $this->admins->countSuperAdmins() <= 1) {
            throw ValidationException::withMessages([
                'admin' => ['Não é possível remover o último super administrador.'],
            ]);
        }

        $this->auditLogger->log(
            event: 'admin.deleted',
            auditable: $target,
            oldValues: ['email' => $target->email],
            userId: $actingAdmin->id,
            userType: 'admin',
        );

        $this->admins->delete($target);
    }

    private function guardSuperAdmin(Admin $actingAdmin): void
    {
        if (! $actingAdmin->isSuperAdmin()) {
            throw new InsufficientPermissionException('Apenas super administradores podem gerenciar administradores.');
        }
    }
}
