<?php

namespace App\Listeners;

use App\Events\AppointmentRescheduled;
use App\Jobs\SendEmailNotificationJob;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendAppointmentRescheduledNotification implements ShouldQueue
{
    public function handle(AppointmentRescheduled $event): void
    {
        SendEmailNotificationJob::dispatch(
            customerId: $event->newAppointment->customer_id,
            template: 'appointment_rescheduled',
            data: [
                'new_date'       => $event->newAppointment->date->format('Y-m-d'),
                'new_start_time' => $event->newAppointment->start_time,
                'original_uuid'  => $event->originalAppointment->uuid,
            ],
        );
    }
}
