<?php

namespace App\Services;

use App\DTOs\Admin\UpdateScheduleConfigDTO;
use App\Models\Admin;
use App\Models\ScheduleConfig;
use App\Repositories\Contracts\ScheduleConfigRepositoryInterface;
use App\Support\AuditLogger;

class ScheduleConfigService
{
    public function __construct(
        private readonly ScheduleConfigRepositoryInterface $configs,
        private readonly AuditLogger $auditLogger,
    ) {}

    public function getActive(): ScheduleConfig
    {
        return $this->configs->getActive();
    }

    public function update(UpdateScheduleConfigDTO $dto, Admin $admin): ScheduleConfig
    {
        $oldConfig = $this->configs->getActive();
        $oldValues = $oldConfig->only([
            'work_start', 'work_end', 'break_start', 'break_end',
            'slot_duration', 'min_advance_hours', 'max_advance_days',
            'max_future_appointments', 'cancellation_advance_hours', 'allowed_days',
        ]);

        $newValues = $dto->toArrayFiltered();

        $updated = $this->configs->update($newValues);

        $this->auditLogger->log(
            event: 'schedule_config.updated',
            auditable: $updated,
            oldValues: $oldValues,
            newValues: $newValues,
            userId: $admin->id,
            userType: 'admin',
        );

        return $updated;
    }
}
