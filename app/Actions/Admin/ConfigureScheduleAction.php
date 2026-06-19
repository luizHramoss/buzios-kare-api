<?php

namespace App\Actions\Admin;

use App\DTOs\Admin\UpdateScheduleConfigDTO;
use App\Models\Admin;
use App\Models\ScheduleConfig;
use App\Services\ScheduleConfigService;

class ConfigureScheduleAction
{
    public function __construct(
        private readonly ScheduleConfigService $service,
    ) {}

    public function execute(UpdateScheduleConfigDTO $dto, Admin $admin): ScheduleConfig
    {
        return $this->service->update($dto, $admin);
    }
}
