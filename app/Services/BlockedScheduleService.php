<?php

namespace App\Services;

use App\Models\Admin;
use App\Models\BlockedSchedule;
use App\Repositories\Contracts\BlockedScheduleRepositoryInterface;
use App\Support\AuditLogger;
use Illuminate\Pagination\LengthAwarePaginator;

class BlockedScheduleService
{
    public function __construct(
        private readonly BlockedScheduleRepositoryInterface $blockedSchedules,
        private readonly AuditLogger $auditLogger,
    ) {}

    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return $this->blockedSchedules->paginate($perPage);
    }

    public function create(array $data, Admin $admin): BlockedSchedule
    {
        $block = $this->blockedSchedules->create([
            ...$data,
            'created_by_id' => $admin->id,
        ]);

        $this->auditLogger->log(
            event: 'blocked_schedule.created',
            auditable: $block,
            newValues: $data,
            userId: $admin->id,
            userType: 'admin',
        );

        return $block;
    }

    public function delete(BlockedSchedule $block, Admin $admin): void
    {
        $this->auditLogger->log(
            event: 'blocked_schedule.deleted',
            auditable: $block,
            oldValues: $block->only(['date', 'start_time', 'end_time', 'type']),
            userId: $admin->id,
            userType: 'admin',
        );

        $this->blockedSchedules->delete($block);
    }
}
