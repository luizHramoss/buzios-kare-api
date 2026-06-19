<?php

namespace App\Actions\Appointment;

use App\DTOs\Appointment\RescheduleAppointmentDTO;
use App\Models\Appointment;
use App\Services\AppointmentService;

class RescheduleAppointmentAction
{
    public function __construct(
        private readonly AppointmentService $service,
    ) {}

    public function execute(RescheduleAppointmentDTO $dto, Appointment $appointment, bool $isAdmin = false): Appointment
    {
        return $this->service->reschedule($dto, $appointment, $isAdmin);
    }
}
