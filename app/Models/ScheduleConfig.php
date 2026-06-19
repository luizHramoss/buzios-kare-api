<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ScheduleConfig extends Model
{
    protected $fillable = [
        'work_start',
        'work_end',
        'break_start',
        'break_end',
        'slot_duration',
        'min_advance_hours',
        'max_advance_days',
        'max_future_appointments',
        'cancellation_advance_hours',
        'allowed_days',
    ];

    protected function casts(): array
    {
        return [
            'allowed_days' => 'array',
        ];
    }

    /**
     * Configuração ativa (singleton lógico — espera-se uma única linha,
     * mas estruturado como tabela para permitir histórico futuro).
     */
    public static function active(): self
    {
        return static::query()->latest('id')->firstOrFail();
    }

    public function isDayAllowed(int $isoWeekday): bool
    {
        return in_array($isoWeekday, $this->allowed_days, true);
    }
}
