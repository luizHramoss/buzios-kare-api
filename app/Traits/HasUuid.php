<?php

namespace App\Traits;

use Illuminate\Support\Str;

/**
 * Gera automaticamente um UUID v4 ao criar o Model.
 *
 * Uso: use HasUuid; em qualquer Model.
 *
 * O campo `uuid` deve existir na migration como CHAR(36) UNIQUE.
 * Queries por UUID: Model::whereUuid($uuid)->firstOrFail()
 */
trait HasUuid
{
    protected static function bootHasUuid(): void
    {
        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = (string) Str::uuid();
            }
        });
    }

    /**
     * Scope para buscar por UUID.
     * Uso: Customer::whereUuid($uuid)->firstOrFail()
     */
    public function scopeWhereUuid($query, string $uuid)
    {
        return $query->where('uuid', $uuid);
    }
}
