<?php

namespace App\DTOs\Appointment;

final class RescheduleAppointmentDTO
{
    public function __construct(
        public readonly string $appointmentUuid,
        public readonly string $newDate,
        public readonly string $newStartTime,
        public readonly string $newEndTime,
        public readonly string $rescheduledByType,
        public readonly int $rescheduledById,
    ) {}

    public static function fromArray(array $data, string $uuid, string $byType, int $byId): self
    {
        return new self(
            appointmentUuid: $uuid,
            newDate: $data['date'],
            newStartTime: $data['start_time'],
            newEndTime: $data['end_time'],
            rescheduledByType: $byType,
            rescheduledById: $byId,
        );
    }
}
