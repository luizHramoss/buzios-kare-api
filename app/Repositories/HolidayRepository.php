<?php

namespace App\Repositories;

use App\Models\Holiday;
use App\Repositories\Contracts\HolidayRepositoryInterface;
use Illuminate\Support\Collection;

class HolidayRepository implements HolidayRepositoryInterface
{
    public function all(): Collection
    {
        return Holiday::query()->orderBy('date')->get();
    }

    public function create(array $data): Holiday
    {
        return Holiday::create($data);
    }

    public function update(Holiday $holiday, array $data): Holiday
    {
        $holiday->update($data);

        return $holiday->refresh();
    }

    public function delete(Holiday $holiday): bool
    {
        return $holiday->delete();
    }

    public function isHoliday(\Carbon\Carbon $date): bool
    {
        return Holiday::isHoliday($date);
    }
}
