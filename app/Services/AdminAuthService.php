<?php

namespace App\Services;

use App\DTOs\Customer\LoginDTO;
use App\Enums\AdminStatus;
use App\Models\Admin;
use App\Repositories\Contracts\AdminRepositoryInterface;
use App\Support\AuditLogger;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AdminAuthService
{
    public function __construct(
        private readonly AdminRepositoryInterface $admins,
        private readonly AuditLogger $auditLogger,
    ) {}

    public function login(LoginDTO $dto): array
    {
        $admin = $this->admins->findByEmail($dto->email);

        if (! $admin || ! Hash::check($dto->password, $admin->password)) {
            $this->auditLogger->log(
                event: 'admin.login_failed',
                newValues: ['email' => $dto->email],
            );

            throw ValidationException::withMessages([
                'email' => ['Credenciais inválidas.'],
            ]);
        }

        if (! $admin->isActive()) {
            throw ValidationException::withMessages([
                'email' => ['Esta conta de administrador está inativa.'],
            ]);
        }

        $this->auditLogger->log(
            event: 'admin.login_success',
            auditable: $admin,
            userId: $admin->id,
            userType: 'admin',
        );

        $token = $admin->createToken('admin-auth', $admin->getTokenAbilities())->plainTextToken;

        return ['admin' => $admin, 'token' => $token];
    }

    public function logout(Admin $admin): void
    {
        $this->auditLogger->log(
            event: 'admin.logout',
            auditable: $admin,
            userId: $admin->id,
            userType: 'admin',
        );

        $admin->currentAccessToken()->delete();
    }
}
