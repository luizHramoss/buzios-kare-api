<?php

namespace App\Models;

use App\Enums\AdminRole;
use App\Enums\AdminStatus;
use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Admin extends Authenticatable
{
    use HasApiTokens, HasFactory, HasUuid, Notifiable, SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'role',
        'status',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
            'role'              => AdminRole::class,
            'status'            => AdminStatus::class,
        ];
    }

    public function getTokenAbilities(): array
    {
        return ['admin'];
    }

    public function blockedSchedules(): HasMany
    {
        return $this->hasMany(BlockedSchedule::class, 'created_by_id');
    }

    public function isSuperAdmin(): bool
    {
        return $this->role === AdminRole::SUPER_ADMIN;
    }

    public function isActive(): bool
    {
        return $this->status === AdminStatus::ACTIVE;
    }
}
