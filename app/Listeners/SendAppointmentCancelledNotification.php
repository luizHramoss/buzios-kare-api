<?php

namespace App\Listeners;

use App\Events\AppointmentCancelled;
use App\Jobs\SendEmailNotificationJob;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendAppointmentCancelledNotification implements ShouldQueue
{
    public function handle(AppointmentCancelled $event): void
    {
        SendEmailNotificationJob::dispatch(
            customerId: $event->appointment->customer_id,
            template: 'appointment_cancelled',
            data: [
                'appointment_uuid' => $event->appointment->uuid,
                'reason'           => $event->appointment->cancellation_reason,
            ],
        );
    }
}
