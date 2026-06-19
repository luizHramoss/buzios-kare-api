<?php

namespace App\Listeners;

use App\Events\AppointmentCancelled;
use App\Events\AppointmentCreated;
use App\Events\AppointmentRescheduled;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

/**
 * Tarefa secundária: estatísticas leves em cache para o dashboard
 * (evita recalcular contadores pesados a cada request).
 */
class LogAppointmentActivity implements ShouldQueue
{
    public function handleCreated(AppointmentCreated $event): void
    {
        Log::info('Appointment created', ['uuid' => $event->appointment->uuid]);
        $this->invalidateDashboardCache();
    }

    public function handleCancelled(AppointmentCancelled $event): void
    {
        Log::info('Appointment cancelled', ['uuid' => $event->appointment->uuid]);
        $this->invalidateDashboardCache();
    }

    public function handleRescheduled(AppointmentRescheduled $event): void
    {
        Log::info('Appointment rescheduled', [
            'from' => $event->originalAppointment->uuid,
            'to'   => $event->newAppointment->uuid,
        ]);
        $this->invalidateDashboardCache();
    }

    private function invalidateDashboardCache(): void
    {
        Cache::forget('dashboard:today');
        Cache::forget('dashboard:tomorrow');
        Cache::forget('dashboard:week');
        Cache::forget('dashboard:month');
    }
}
