<?php

namespace App\Models;

use App\Enums\CustomerStatus;
use App\Traits\HasUuid;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Customer extends Authenticatable implements CanResetPasswordContract
{
    use CanResetPassword, HasApiTokens, HasFactory, HasUuid, Notifiable, SoftDeletes;

    protected $fillable = [
        'name',
        'cpf',
        'birth_date',
        'email',
        'phone',
        'whatsapp',
        'notes',
        'password',
        'status',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'birth_date'        => 'date',
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
            'status'            => CustomerStatus::class,
        ];
    }

    /**
     * Define a ability do token Sanctum emitido para este guard.
     */
    public function getTokenAbilities(): array
    {
        return ['customer'];
    }

    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class, 'customer_id');
    }

    public function isActive(): bool
    {
        return $this->status === CustomerStatus::ACTIVE;
    }

    /**
     * Sobrescreve o envio padrão para apontar para o frontend (SPA),
     * já que esta API não possui views. O frontend deve ler o token
     * da query string e enviar para POST /api/v1/auth/customer/reset-password.
     */
    public function sendPasswordResetNotification($token): void
    {
        $url = config('app.frontend_url', config('app.url')) . '/reset-password?token=' . $token . '&email=' . urlencode($this->email);

        $this->notify(new \App\Notifications\CustomerResetPasswordNotification($url));
    }
}
