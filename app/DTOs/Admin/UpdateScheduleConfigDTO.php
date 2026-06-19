<?php

namespace App\DTOs\Admin;

final class UpdateScheduleConfigDTO
{
    public function __construct(
        public readonly ?string $workStart,
        public readonly ?string $workEnd,
        public readonly ?string $breakStart,
        public readonly ?string $breakEnd,
        public readonly ?int $slotDuration,
        public readonly ?int $minAdvanceHours,
        public readonly ?int $maxAdvanceDays,
        public readonly ?int $maxFutureAppointments,
        public readonly ?int $cancellationAdvanceHours,
        public readonly ?array $allowedDays,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            workStart: $data['work_start'] ?? null,
            workEnd: $data['work_end'] ?? null,
            breakStart: $data['break_start'] ?? null,
            breakEnd: $data['break_end'] ?? null,
            slotDuration: $data['slot_duration'] ?? null,
            minAdvanceHours: $data['min_advance_hours'] ?? null,
            maxAdvanceDays: $data['max_advance_days'] ?? null,
            maxFutureAppointments: $data['max_future_appointments'] ?? null,
            cancellationAdvanceHours: $data['cancellation_advance_hours'] ?? null,
            allowedDays: $data['allowed_days'] ?? null,
        );
    }

    public function toArrayFiltered(): array
    {
        return array_filter(
            [
                'work_start'                  => $this->workStart,
                'work_end'                    => $this->workEnd,
                'break_start'                  => $this->breakStart,
                'break_end'                    => $this->breakEnd,
                'slot_duration'                => $this->slotDuration,
                'min_advance_hours'            => $this->minAdvanceHours,
                'max_advance_days'             => $this->maxAdvanceDays,
                'max_future_appointments'      => $this->maxFutureAppointments,
                'cancellation_advance_hours'   => $this->cancellationAdvanceHours,
                'allowed_days'                 => $this->allowedDays,
            ],
            fn ($value) => $value !== null
        );
    }
}
