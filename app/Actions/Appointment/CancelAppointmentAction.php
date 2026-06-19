<?php

namespace App\Actions\Appointment;

use App\DTOs\Appointment\CancelAppointmentDTO;
use App\Models\Appointment;
use App\Services\AppointmentService;

class CancelAppointmentAction
{
    public function __construct(
        private readonly AppointmentService $service,
    ) {}

    public function execute(CancelAppointmentDTO $dto, Appointment $appointment, bool $isAdmin = false): Appointment
    {
        return $this->service->cancel($dto, $appointment, $isAdmin);
    }
}
