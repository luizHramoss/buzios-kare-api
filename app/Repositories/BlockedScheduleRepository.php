<?php

namespace App\Repositories;

use App\Enums\BlockType;
use App\Models\BlockedSchedule;
use App\Repositories\Contracts\BlockedScheduleRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class BlockedScheduleRepository implements BlockedScheduleRepositoryInterface
{
    public function findByUuidOrFail(string $uuid): BlockedSchedule
    {
        return BlockedSchedule::whereUuid($uuid)->firstOrFail();
    }

    public function create(array $data): BlockedSchedule
    {
        return BlockedSchedule::create($data);
    }

    public function delete(BlockedSchedule $blockedSchedule): bool
    {
        return $blockedSchedule->delete();
    }

    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return BlockedSchedule::query()->orderByDesc('date')->paginate($perPage);
    }

    public function getForDate(string $date): Collection
    {
        return BlockedSchedule::query()->where('date', $date)->get();
    }

    public function isDateFullyBlocked(string $date): bool
    {
        return BlockedSchedule::query()
            ->where('date', $date)
            ->where('type', BlockType::FULL_DAY->value)
            ->exists();
    }
}
