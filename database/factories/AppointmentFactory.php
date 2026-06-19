<?php

namespace Database\Factories;

use App\Enums\AppointmentStatus;
use App\Models\Customer;
use Illuminate\Database\Eloquent\Factories\Factory;

class AppointmentFactory extends Factory
{
    protected $model = \App\Models\Appointment::class;

    public function definition(): array
    {
        $customer = Customer::factory()->create();

        return [
            'customer_id'      => $customer->id,
            'service'          => 'Jogo de Búzios',
            'date'             => now()->addDays($this->faker->numberBetween(1, 30))->format('Y-m-d'),
            'start_time'       => '09:00',
            'end_time'         => '10:00',
            'notes'            => null,
            'status'           => AppointmentStatus::AGENDADO->value,
            'value'            => 100.00,
            'payment_method'   => null,
            'created_by_type'  => 'customer',
            'created_by_id'    => $customer->id,
        ];
    }

    public function cancelled(): static
    {
        return $this->state(['status' => AppointmentStatus::CANCELADO->value]);
    }

    public function confirmed(): static
    {
        return $this->state(['status' => AppointmentStatus::CONFIRMADO->value]);
    }
}
