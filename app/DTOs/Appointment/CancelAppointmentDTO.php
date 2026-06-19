<?php

namespace App\DTOs\Appointment;

final class CancelAppointmentDTO
{
    public function __construct(
        public readonly string $appointmentUuid,
        public readonly ?string $reason,
        public readonly string $cancelledByType,
        public readonly int $cancelledById,
    ) {}

    public static function fromArray(array $data, string $uuid, string $cancelledByType, int $cancelledById): self
    {
        return new self(
            appointmentUuid: $uuid,
            reason: $data['reason'] ?? null,
            cancelledByType: $cancelledByType,
            cancelledById: $cancelledById,
        );
    }
}
