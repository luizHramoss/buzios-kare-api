<?php

namespace App\Enums;

enum AdminRole: string
{
    case SUPER_ADMIN = 'super_admin';
    case ADMIN        = 'admin';

    public function label(): string
    {
        return match ($this) {
            self::SUPER_ADMIN => 'Super Administrador',
            self::ADMIN       => 'Administrador',
        };
    }

    /**
     * Apenas super_admin pode gerenciar outros administradores.
     */
    public function canManageAdmins(): bool
    {
        return $this === self::SUPER_ADMIN;
    }
}
