<?php

use Illuminate\Support\Facades\Schedule;

/*
|--------------------------------------------------------------------------
| Console Routes / Scheduler
|--------------------------------------------------------------------------
|
| Os jobs agendados serão implementados na Etapa 13/14.
| Aqui ficam registrados para referência da estrutura.
|
*/

// Exemplo: limpar tokens expirados do Sanctum diariamente
Schedule::command('sanctum:prune-expired --hours=24')
    ->daily()
    ->withoutOverlapping();

// Placeholder: confirmar agendamentos automaticamente (Etapa 13)
// Schedule::job(new \App\Jobs\AutoConfirmAppointmentsJob)->hourly();

// Placeholder: cache do dashboard (Etapa 14)
// Schedule::job(new \App\Jobs\GenerateDashboardCacheJob)->everyFiveMinutes();
