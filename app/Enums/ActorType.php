<?php

namespace App\Enums;

use App\Models\Admin;
use App\Models\Customer;

/**
 * Identifica o tipo de ator responsável por uma ação (created_by, cancelled_by).
 * Evita strings mágicas como 'App\Models\Customer' espalhadas pelo código.
 */
enum ActorType: string
{
    case CUSTOMER = 'customer';
    case ADMIN    = 'admin';

    public function modelClass(): string
    {
        return match ($this) {
            self::CUSTOMER => Customer::class,
            self::ADMIN    => Admin::class,
        };
    }

    public static function fromModel(object $model): self
    {
        return match (true) {
            $model instanceof Customer => self::CUSTOMER,
            $model instanceof Admin    => self::ADMIN,
            default => throw new \InvalidArgumentException('Tipo de ator desconhecido: ' . get_class($model)),
        };
    }
}
