<?php

namespace App\Providers;

use App\Repositories\AdminRepository;
use App\Repositories\AppointmentRepository;
use App\Repositories\BlockedScheduleRepository;
use App\Repositories\Contracts\AdminRepositoryInterface;
use App\Repositories\Contracts\AppointmentRepositoryInterface;
use App\Repositories\Contracts\BlockedScheduleRepositoryInterface;
use App\Repositories\Contracts\CustomerRepositoryInterface;
use App\Repositories\Contracts\HolidayRepositoryInterface;
use App\Repositories\Contracts\ScheduleConfigRepositoryInterface;
use App\Repositories\CustomerRepository;
use App\Repositories\HolidayRepository;
use App\Repositories\ScheduleConfigRepository;
use Illuminate\Support\ServiceProvider;

/**
 * Liga cada contrato de Repository à sua implementação concreta.
 *
 * Isso permite que Services dependam apenas das interfaces (Dependency
 * Inversion), facilitando testes (mock das interfaces) e troca futura
 * de implementação sem alterar quem consome.
 */
class RepositoryServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(CustomerRepositoryInterface::class, CustomerRepository::class);
        $this->app->bind(AdminRepositoryInterface::class, AdminRepository::class);
        $this->app->bind(AppointmentRepositoryInterface::class, AppointmentRepository::class);
        $this->app->bind(ScheduleConfigRepositoryInterface::class, ScheduleConfigRepository::class);
        $this->app->bind(BlockedScheduleRepositoryInterface::class, BlockedScheduleRepository::class);
        $this->app->bind(HolidayRepositoryInterface::class, HolidayRepository::class);
    }
}
