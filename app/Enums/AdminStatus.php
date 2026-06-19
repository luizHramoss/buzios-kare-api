<?php

namespace App\Enums;

enum AdminStatus: string
{
    case ACTIVE   = 'active';
    case INACTIVE = 'inactive';

    public function label(): string
    {
        return match ($this) {
            self::ACTIVE   => 'Ativo',
            self::INACTIVE => 'Inativo',
        };
    }

    public function canAuthenticate(): bool
    {
        return $this === self::ACTIVE;
    }
}
