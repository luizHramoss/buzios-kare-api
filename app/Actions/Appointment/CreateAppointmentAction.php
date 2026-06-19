<?php

namespace App\Actions\Appointment;

use App\DTOs\Appointment\CreateAppointmentDTO;
use App\Models\Appointment;
use App\Services\AppointmentService;

class CreateAppointmentAction
{
    public function __construct(
        private readonly AppointmentService $service,
    ) {}

    public function execute(CreateAppointmentDTO $dto, bool $bypassCustomerLimits = false): Appointment
    {
        return $this->service->create($dto, $bypassCustomerLimits);
    }
}
