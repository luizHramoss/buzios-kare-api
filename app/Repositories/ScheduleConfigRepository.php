<?php

namespace App\Repositories;

use App\Models\ScheduleConfig;
use App\Repositories\Contracts\ScheduleConfigRepositoryInterface;

class ScheduleConfigRepository implements ScheduleConfigRepositoryInterface
{
    public function getActive(): ScheduleConfig
    {
        return ScheduleConfig::active();
    }

    public function update(array $data): ScheduleConfig
    {
        $config = $this->getActive();
        $config->update($data);

        return $config->refresh();
    }
}
