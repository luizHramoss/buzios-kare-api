<?php

namespace App\Listeners;

use App\Events\AppointmentConfirmed;
use App\Jobs\SendEmailNotificationJob;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendAppointmentConfirmedNotification implements ShouldQueue
{
    public function handle(AppointmentConfirmed $event): void
    {
        SendEmailNotificationJob::dispatch(
            customerId: $event->appointment->customer_id,
            template: 'appointment_confirmed',
            data: [
                'appointment_uuid' => $event->appointment->uuid,
            ],
        );
    }
}
