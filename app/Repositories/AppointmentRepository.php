<?php

namespace App\Repositories;

use App\Enums\AppointmentStatus;
use App\Models\Appointment;
use App\Repositories\Contracts\AppointmentRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class AppointmentRepository implements AppointmentRepositoryInterface
{
    public function findById(int $id): ?Appointment
    {
        return Appointment::find($id);
    }

    public function findByUuid(string $uuid): ?Appointment
    {
        return Appointment::whereUuid($uuid)->first();
    }

    public function findByUuidOrFail(string $uuid): Appointment
    {
        return Appointment::whereUuid($uuid)->firstOrFail();
    }

    public function create(array $data): Appointment
    {
        return Appointment::create($data);
    }

    public function update(Appointment $appointment, array $data): Appointment
    {
        $appointment->update($data);

        return $appointment->refresh();
    }

    public function paginateForCustomer(int $customerId, int $perPage = 15, array $filters = []): LengthAwarePaginator
    {
        $query = Appointment::query()->where('customer_id', $customerId);

        $this->applyFilters($query, $filters);

        return $query->orderByDesc('date')->orderByDesc('start_time')->paginate($perPage);
    }

    public function paginateAll(int $perPage = 15, array $filters = []): LengthAwarePaginator
    {
        $query = Appointment::query()->with('customer');

        $this->applyFilters($query, $filters);

        return $query->orderByDesc('date')->orderByDesc('start_time')->paginate($perPage);
    }

    /**
     * CRÍTICO: deve ser chamado dentro de um DB::transaction() já aberto.
     * O lockForUpdate() bloqueia as linhas retornadas até o fim da transação,
     * impedindo que duas requisições concorrentes criem agendamentos
     * conflitantes no mesmo horário.
     */
    public function findConflictingWithLock(string $date, string $startTime, string $endTime): Collection
    {
        return Appointment::query()
            ->conflictingWith($date, $startTime, $endTime)
            ->lockForUpdate()
            ->get();
    }

    public function hasConflict(string $date, string $startTime, string $endTime, ?int $excludeId = null): bool
    {
        $query = Appointment::query()->conflictingWith($date, $startTime, $endTime);

        if ($excludeId !== null) {
            $query->where('id', '!=', $excludeId);
        }

        return $query->exists();
    }

    public function getOccupiedSlotsForDate(string $date): Collection
    {
        return Appointment::query()
            ->where('date', $date)
            ->active()
            ->get(['start_time', 'end_time']);
    }

    public function countFutureActiveForCustomer(int $customerId): int
    {
        return Appointment::query()
            ->where('customer_id', $customerId)
            ->where('date', '>=', now()->toDateString())
            ->active()
            ->count();
    }

    public function countByDateRange(string $startDate, string $endDate, ?string $status = null): int
    {
        $query = Appointment::query()->whereBetween('date', [$startDate, $endDate]);

        if ($status !== null) {
            $query->where('status', $status);
        }

        return $query->count();
    }

    public function getForDashboard(string $startDate, string $endDate): Collection
    {
        return Appointment::query()
            ->with('customer')
            ->whereBetween('date', [$startDate, $endDate])
            ->orderBy('date')
            ->orderBy('start_time')
            ->get();
    }

    private function applyFilters($query, array $filters): void
    {
        if (! empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (! empty($filters['date_from'])) {
            $query->where('date', '>=', $filters['date_from']);
        }

        if (! empty($filters['date_to'])) {
            $query->where('date', '<=', $filters['date_to']);
        }
    }
}
