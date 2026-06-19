<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Holiday extends Model
{
    use HasFactory;

    protected $fillable = [
        'date',
        'name',
        'recurring',
    ];

    protected function casts(): array
    {
        return [
            'date'      => 'date',
            'recurring' => 'boolean',
        ];
    }

    /**
     * Verifica se uma data específica é feriado, considerando
     * tanto feriados fixos (mesmo ano) quanto recorrentes (dia/mês, qualquer ano).
     */
    public static function isHoliday(\Carbon\Carbon $date): bool
    {
        return static::query()
            ->where(function ($query) use ($date) {
                $query->where('date', $date->format('Y-m-d'))
                    ->where('recurring', false);
            })
            ->orWhere(function ($query) use ($date) {
                $query->where('recurring', true)
                    ->whereMonth('date', $date->month)
                    ->whereDay('date', $date->day);
            })
            ->exists();
    }
}
