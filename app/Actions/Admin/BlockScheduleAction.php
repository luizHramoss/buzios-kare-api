<?php

namespace App\Actions\Admin;

use App\Models\Admin;
use App\Models\BlockedSchedule;
use App\Services\BlockedScheduleService;

class BlockScheduleAction
{
    public function __construct(
        private readonly BlockedScheduleService $service,
    ) {}

    public function execute(array $data, Admin $admin): BlockedSchedule
    {
        return $this->service->create($data, $admin);
    }
}
