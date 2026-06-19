<?php

namespace App\Providers;

use App\Events\AppointmentCancelled;
use App\Events\AppointmentConfirmed;
use App\Events\AppointmentCreated;
use App\Events\AppointmentRescheduled;
use App\Events\CustomerRegistered;
use App\Listeners\LogAppointmentActivity;
use App\Listeners\SendAppointmentCancelledNotification;
use App\Listeners\SendAppointmentConfirmedNotification;
use App\Listeners\SendAppointmentCreatedNotification;
use App\Listeners\SendAppointmentRescheduledNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        AppointmentCreated::class => [
            SendAppointmentCreatedNotification::class,
            [LogAppointmentActivity::class, 'handleCreated'],
        ],
        AppointmentCancelled::class => [
            SendAppointmentCancelledNotification::class,
            [LogAppointmentActivity::class, 'handleCancelled'],
        ],
        AppointmentRescheduled::class => [
            SendAppointmentRescheduledNotification::class,
            [LogAppointmentActivity::class, 'handleRescheduled'],
        ],
        AppointmentConfirmed::class => [
            SendAppointmentConfirmedNotification::class,
        ],
        CustomerRegistered::class => [
            // Espaço reservado para e-mail de boas-vindas (opcional no MVP)
        ],
    ];

    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
