<?php

namespace App\Models;

use App\Enums\AppointmentStatus;
use App\Enums\PaymentMethod;
use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Appointment extends Model
{
    use HasFactory, HasUuid, SoftDeletes;

    protected $fillable = [
        'customer_id',
        'service',
        'date',
        'start_time',
        'end_time',
        'notes',
        'status',
        'value',
        'payment_method',
        'created_by_type',
        'created_by_id',
        'cancelled_by_type',
        'cancelled_by_id',
        'cancellation_reason',
        'rescheduled_from_id',
    ];

    protected function casts(): array
    {
        return [
            'date'            => 'date',
            'status'          => AppointmentStatus::class,
            'payment_method'  => PaymentMethod::class,
            'value'           => 'decimal:2',
        ];
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    /**
     * Quem criou o agendamento (Customer ou Admin).
     * Usa o morph map registrado em MorphMapServiceProvider.
     */
    public function createdBy(): MorphTo
    {
        return $this->morphTo(__FUNCTION__, 'created_by_type', 'created_by_id');
    }

    /**
     * Quem cancelou o agendamento, se aplicável.
     */
    public function cancelledBy(): MorphTo
    {
        return $this->morphTo(__FUNCTION__, 'cancelled_by_type', 'cancelled_by_id');
    }

    /**
     * Agendamento original, em caso de remarcação.
     */
    public function rescheduledFrom(): BelongsTo
    {
        return $this->belongsTo(self::class, 'rescheduled_from_id');
    }

    public function isActive(): bool
    {
        return in_array($this->status, AppointmentStatus::activeStatuses(), true);
    }

    public function isFinal(): bool
    {
        return $this->status->isFinal();
    }

    /**
     * Combina date + start_time/end_time em instâncias Carbon completas.
     */
    public function getStartDateTime(): \Carbon\Carbon
    {
        return \Carbon\Carbon::parse($this->date->format('Y-m-d') . ' ' . $this->start_time);
    }

    public function getEndDateTime(): \Carbon\Carbon
    {
        return \Carbon\Carbon::parse($this->date->format('Y-m-d') . ' ' . $this->end_time);
    }

    /**
     * Scope: agendamentos que ocupam um slot (não cancelados/remarcados).
     */
    public function scopeActive($query)
    {
        return $query->whereIn('status', array_map(
            fn ($status) => $status->value,
            AppointmentStatus::activeStatuses()
        ));
    }

    /**
     * Scope: conflito de horário em uma data/intervalo específico.
     */
    public function scopeConflictingWith($query, string $date, string $startTime, string $endTime)
    {
        return $query
            ->where('date', $date)
            ->where('start_time', '<', $endTime)
            ->where('end_time', '>', $startTime)
            ->active();
    }
}
