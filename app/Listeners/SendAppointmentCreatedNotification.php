<?php

namespace App\Listeners;

use App\Events\AppointmentCreated;
use App\Jobs\SendEmailNotificationJob;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendAppointmentCreatedNotification implements ShouldQueue
{
    public function handle(AppointmentCreated $event): void
    {
        SendEmailNotificationJob::dispatch(
            customerId: $event->appointment->customer_id,
            template: 'appointment_created',
            data: [
                'appointment_uuid' => $event->appointment->uuid,
                'date'             => $event->appointment->date->format('Y-m-d'),
                'start_time'       => $event->appointment->start_time,
            ],
        );
    }
}
