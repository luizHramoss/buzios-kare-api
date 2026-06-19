<?php

namespace App\Enums;

enum BlockType: string
{
    case FULL_DAY = 'full_day';
    case PERIOD   = 'period';
    case SLOT     = 'slot';

    public function label(): string
    {
        return match ($this) {
            self::FULL_DAY => 'Dia inteiro',
            self::PERIOD   => 'Período',
            self::SLOT     => 'Horário específico',
        };
    }

    /**
     * Indica se este tipo de bloqueio exige start_time/end_time.
     */
    public function requiresTimeRange(): bool
    {
        return $this !== self::FULL_DAY;
    }
}
