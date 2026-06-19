<?php

namespace App\Repositories\Contracts;

use App\Models\Appointment;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface AppointmentRepositoryInterface
{
    public function findById(int $id): ?Appointment;

    public function findByUuid(string $uuid): ?Appointment;

    public function findByUuidOrFail(string $uuid): Appointment;

    public function create(array $data): Appointment;

    public function update(Appointment $appointment, array $data): Appointment;

    public function paginateForCustomer(int $customerId, int $perPage = 15, array $filters = []): LengthAwarePaginator;

    public function paginateAll(int $perPage = 15, array $filters = []): LengthAwarePaginator;

    /**
     * Busca agendamentos ativos (não cancelados/remarcados) que conflitam
     * com o intervalo informado, COM lockForUpdate() — deve ser chamado
     * dentro de uma DB::transaction() já aberta pelo Service.
     */
    public function findConflictingWithLock(string $date, string $startTime, string $endTime): Collection;

    /**
     * Mesma verificação de conflito, sem lock — usado para checagens
     * rápidas de disponibilidade (leitura fora de transação).
     */
    public function hasConflict(string $date, string $startTime, string $endTime, ?int $excludeId = null): bool;

    /**
     * Retorna os horários (start_time) já ocupados em uma data específica.
     * Usado pelo AvailabilityService para montar a lista de slots livres.
     */
    public function getOccupiedSlotsForDate(string $date): Collection;

    public function countFutureActiveForCustomer(int $customerId): int;

    public function countByDateRange(string $startDate, string $endDate, ?string $status = null): int;

    public function getForDashboard(string $startDate, string $endDate): Collection;
}
