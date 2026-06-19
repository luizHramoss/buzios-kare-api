<?php

namespace App\DTOs\Appointment;

final class CreateAppointmentDTO
{
    public function __construct(
        public readonly int $customerId,
        public readonly string $service,
        public readonly string $date,
        public readonly string $startTime,
        public readonly string $endTime,
        public readonly ?string $notes,
        public readonly float $value,
        public readonly ?string $paymentMethod,
        public readonly string $createdByType,
        public readonly int $createdById,
    ) {}

    public static function fromArray(array $data, int $customerId, string $createdByType, int $createdById): self
    {
        return new self(
            customerId: $customerId,
            service: $data['service'],
            date: $data['date'],
            startTime: $data['start_time'],
            endTime: $data['end_time'],
            notes: $data['notes'] ?? null,
            value: (float) ($data['value'] ?? 0),
            paymentMethod: $data['payment_method'] ?? null,
            createdByType: $createdByType,
            createdById: $createdById,
        );
    }

    public function toArray(): array
    {
        return [
            'customer_id'        => $this->customerId,
            'service'            => $this->service,
            'date'               => $this->date,
            'start_time'         => $this->startTime,
            'end_time'           => $this->endTime,
            'notes'              => $this->notes,
            'value'              => $this->value,
            'payment_method'     => $this->paymentMethod,
            'created_by_type'    => $this->createdByType,
            'created_by_id'      => $this->createdById,
        ];
    }
}
