<?php

namespace App\Repositories\Contracts;

use App\Models\ScheduleConfig;

interface ScheduleConfigRepositoryInterface
{
    public function getActive(): ScheduleConfig;

    public function update(array $data): ScheduleConfig;
}
