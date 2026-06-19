<?php

namespace App\Repositories\Contracts;

use App\Models\BlockedSchedule;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface BlockedScheduleRepositoryInterface
{
    public function findByUuidOrFail(string $uuid): BlockedSchedule;

    public function create(array $data): BlockedSchedule;

    public function delete(BlockedSchedule $blockedSchedule): bool;

    public function paginate(int $perPage = 15): LengthAwarePaginator;

    /**
     * Retorna todos os bloqueios que afetam uma data específica.
     */
    public function getForDate(string $date): Collection;

    public function isDateFullyBlocked(string $date): bool;
}
