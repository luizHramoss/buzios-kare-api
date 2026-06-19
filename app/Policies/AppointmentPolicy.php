<?php

namespace App\Policies;

use App\Models\Appointment;
use App\Models\Customer;

class AppointmentPolicy
{
    /**
     * Cliente só pode visualizar/cancelar/remarcar seus PRÓPRIOS agendamentos.
     * Admin passa por fora desta policy (rota separada com guard admin).
     */
    public function view(Customer $customer, Appointment $appointment): bool
    {
        return $customer->id === $appointment->customer_id;
    }

    public function cancel(Customer $customer, Appointment $appointment): bool
    {
        return $customer->id === $appointment->customer_id;
    }

    public function reschedule(Customer $customer, Appointment $appointment): bool
    {
        return $customer->id === $appointment->customer_id;
    }
}
