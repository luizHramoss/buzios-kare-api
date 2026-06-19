<?php

use App\Http\Controllers\Api\V1\Auth\CustomerAuthController;
use App\Http\Controllers\Api\V1\Auth\AdminAuthController;
use App\Http\Controllers\Api\V1\Auth\PasswordResetController;
use App\Http\Controllers\Api\V1\Profile\ProfileController;
use App\Http\Controllers\Api\V1\Appointment\AppointmentController;
use App\Http\Controllers\Api\V1\Appointment\AvailabilityController;
use App\Http\Controllers\Api\V1\Admin\CustomerAdminController;
use App\Http\Controllers\Api\V1\Admin\AdminController;
use App\Http\Controllers\Api\V1\Admin\AppointmentAdminController;
use App\Http\Controllers\Api\V1\Admin\ScheduleConfigController;
use App\Http\Controllers\Api\V1\Admin\BlockedScheduleController;
use App\Http\Controllers\Api\V1\Admin\HolidayController;
use App\Http\Controllers\Api\V1\Admin\DashboardController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes — v1
|--------------------------------------------------------------------------
|
| Todas as rotas são prefixadas com /api/v1 (configurado no bootstrap/app.php).
| Rate limiting:
|   - throttle:auth   → 10 req/min  (login, register)
|   - throttle:api    → 60 req/min  (rotas gerais)
|
*/

Route::prefix('v1')->group(function () {

    /*
    |----------------------------------------------------------------------
    | Autenticação — Cliente
    |----------------------------------------------------------------------
    */
    Route::prefix('auth/customer')->middleware('throttle:auth')->group(function () {
        Route::post('register',        [CustomerAuthController::class, 'register']);
        Route::post('login',           [CustomerAuthController::class, 'login']);
        Route::post('forgot-password', [PasswordResetController::class, 'sendResetLink']);
        Route::post('reset-password',  [PasswordResetController::class, 'resetPassword']);
    });

    Route::post('auth/customer/logout', [CustomerAuthController::class, 'logout'])
        ->middleware(['auth:sanctum', 'auth.role:customer', 'throttle:api']);

    /*
    |----------------------------------------------------------------------
    | Autenticação — Admin
    |----------------------------------------------------------------------
    */
    Route::prefix('auth/admin')->middleware('throttle:auth')->group(function () {
        Route::post('login', [AdminAuthController::class, 'login']);
    });

    Route::post('auth/admin/logout', [AdminAuthController::class, 'logout'])
        ->middleware(['auth:sanctum', 'auth.role:admin', 'throttle:api']);

    /*
    |----------------------------------------------------------------------
    | Rotas do Cliente autenticado
    |----------------------------------------------------------------------
    */
    Route::middleware(['auth:sanctum', 'auth.role:customer', 'throttle:api'])->group(function () {

        // Perfil
        Route::get('profile',          [ProfileController::class, 'show']);
        Route::put('profile',          [ProfileController::class, 'update']);
        Route::put('profile/password', [ProfileController::class, 'updatePassword']);

        // Disponibilidade
        Route::get('availability/dates', [AvailabilityController::class, 'dates']);
        Route::get('availability/slots', [AvailabilityController::class, 'slots']);

        // Agendamentos
        Route::get('appointments',                         [AppointmentController::class, 'index']);
        Route::post('appointments',                        [AppointmentController::class, 'store']);
        Route::get('appointments/{uuid}',                  [AppointmentController::class, 'show']);
        Route::post('appointments/{uuid}/cancel',          [AppointmentController::class, 'cancel']);
        Route::post('appointments/{uuid}/reschedule',      [AppointmentController::class, 'reschedule']);
    });

    /*
    |----------------------------------------------------------------------
    | Rotas do Administrador autenticado
    |----------------------------------------------------------------------
    */
    Route::prefix('admin')
        ->middleware(['auth:sanctum', 'auth.role:admin', 'throttle:api'])
        ->group(function () {

            // Dashboard
            Route::prefix('dashboard')->group(function () {
                Route::get('/',        [DashboardController::class, 'index']);
                Route::get('today',    [DashboardController::class, 'today']);
                Route::get('tomorrow', [DashboardController::class, 'tomorrow']);
                Route::get('week',     [DashboardController::class, 'week']);
                Route::get('month',    [DashboardController::class, 'month']);
            });

            // Gerenciamento de Clientes
            Route::apiResource('customers', CustomerAdminController::class)
                ->parameters(['customers' => 'uuid']);

            // Gerenciamento de Administradores
            Route::apiResource('admins', AdminController::class)
                ->parameters(['admins' => 'uuid']);

            // Gerenciamento de Agendamentos
            Route::prefix('appointments')->group(function () {
                Route::get('/',                       [AppointmentAdminController::class, 'index']);
                Route::post('/',                      [AppointmentAdminController::class, 'store']);
                Route::get('{uuid}',                  [AppointmentAdminController::class, 'show']);
                Route::put('{uuid}',                  [AppointmentAdminController::class, 'update']);
                Route::post('{uuid}/cancel',          [AppointmentAdminController::class, 'cancel']);
                Route::post('{uuid}/reschedule',      [AppointmentAdminController::class, 'reschedule']);
                Route::post('{uuid}/confirm',         [AppointmentAdminController::class, 'confirm']);
                Route::post('{uuid}/start',           [AppointmentAdminController::class, 'start']);
                Route::post('{uuid}/finish',          [AppointmentAdminController::class, 'finish']);
                Route::post('{uuid}/no-show',         [AppointmentAdminController::class, 'noShow']);
            });

            // Configuração da Agenda
            Route::get('schedule-config',  [ScheduleConfigController::class, 'show']);
            Route::put('schedule-config',  [ScheduleConfigController::class, 'update']);

            // Bloqueios de Horário
            Route::get('blocked-schedules',        [BlockedScheduleController::class, 'index']);
            Route::post('blocked-schedules',       [BlockedScheduleController::class, 'store']);
            Route::delete('blocked-schedules/{uuid}', [BlockedScheduleController::class, 'destroy']);

            // Feriados
            Route::apiResource('holidays', HolidayController::class)
                ->except(['show']);
        });

    /*
    |----------------------------------------------------------------------
    | Health check
    |----------------------------------------------------------------------
    */
    Route::get('health', fn () => response()->json([
        'status'    => 'ok',
        'version'   => 'v1',
        'timestamp' => now()->toIso8601String(),
    ]));
});
