<?php

namespace App\Services;

use App\Enums\AppointmentStatus;
use App\Repositories\Contracts\AppointmentRepositoryInterface;
use App\Repositories\Contracts\CustomerRepositoryInterface;
use Illuminate\Support\Facades\Cache;

class DashboardService
{
    private const CACHE_TTL_SECONDS = 120;

    public function __construct(
        private readonly AppointmentRepositoryInterface $appointments,
        private readonly CustomerRepositoryInterface $customers,
    ) {}

    public function today(): array
    {
        return Cache::remember('dashboard:today', self::CACHE_TTL_SECONDS, function () {
            $today = now()->toDateString();

            return $this->buildDayReport($today, $today);
        });
    }

    public function tomorrow(): array
    {
        return Cache::remember('dashboard:tomorrow', self::CACHE_TTL_SECONDS, function () {
            $tomorrow = now()->addDay()->toDateString();

            return $this->buildDayReport($tomorrow, $tomorrow);
        });
    }

    public function week(): array
    {
        return Cache::remember('dashboard:week', self::CACHE_TTL_SECONDS, function () {
            return $this->buildRangeReport(
                now()->startOfWeek()->toDateString(),
                now()->endOfWeek()->toDateString(),
            );
        });
    }

    public function month(): array
    {
        return Cache::remember('dashboard:month', self::CACHE_TTL_SECONDS, function () {
            return $this->buildRangeReport(
                now()->startOfMonth()->toDateString(),
                now()->endOfMonth()->toDateString(),
            );
        });
    }

    public function overview(): array
    {
        return [
            'today_count'         => $this->today()['total'],
            'tomorrow_count'      => $this->tomorrow()['total'],
            'week_count'          => $this->week()['total'],
            'month_count'         => $this->month()['total'],
            'registered_customers' => $this->customers->countActive(),
            'cancellations_month' => $this->appointments->countByDateRange(
                now()->startOfMonth()->toDateString(),
                now()->endOfMonth()->toDateString(),
                AppointmentStatus::CANCELADO->value,
            ),
            'reschedules_month' => $this->appointments->countByDateRange(
                now()->startOfMonth()->toDateString(),
                now()->endOfMonth()->toDateString(),
                AppointmentStatus::REMARCADO->value,
            ),
        ];
    }

    private function buildDayReport(string $start, string $end): array
    {
        $appointments = $this->appointments->getForDashboard($start, $end);

        return [
            'total'        => $appointments->count(),
            'appointments' => $appointments,
        ];
    }

    private function buildRangeReport(string $start, string $end): array
    {
        $appointments = $this->appointments->getForDashboard($start, $end);

        return [
            'total'        => $appointments->count(),
            'appointments' => $appointments,
        ];
    }
}
