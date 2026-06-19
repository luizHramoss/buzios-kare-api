<?php

namespace App\Repositories\Contracts;

use App\Models\Holiday;
use Illuminate\Support\Collection;

interface HolidayRepositoryInterface
{
    public function all(): Collection;

    public function create(array $data): Holiday;

    public function update(Holiday $holiday, array $data): Holiday;

    public function delete(Holiday $holiday): bool;

    public function isHoliday(\Carbon\Carbon $date): bool;
}
