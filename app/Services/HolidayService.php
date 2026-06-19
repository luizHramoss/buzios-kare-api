<?php

namespace App\Services;

use App\Models\Holiday;
use App\Repositories\Contracts\HolidayRepositoryInterface;
use App\Support\AuditLogger;
use Illuminate\Support\Collection;

class HolidayService
{
    public function __construct(
        private readonly HolidayRepositoryInterface $holidays,
        private readonly AuditLogger $auditLogger,
    ) {}

    public function list(): Collection
    {
        return $this->holidays->all();
    }

    public function create(array $data, int $adminId): Holiday
    {
        $holiday = $this->holidays->create($data);

        $this->auditLogger->log(
            event: 'holiday.created',
            auditable: $holiday,
            newValues: $data,
            userId: $adminId,
            userType: 'admin',
        );

        return $holiday;
    }

    public function update(Holiday $holiday, array $data, int $adminId): Holiday
    {
        $oldValues = $holiday->only(['date', 'name', 'recurring']);

        $updated = $this->holidays->update($holiday, $data);

        $this->auditLogger->log(
            event: 'holiday.updated',
            auditable: $updated,
            oldValues: $oldValues,
            newValues: $data,
            userId: $adminId,
            userType: 'admin',
        );

        return $updated;
    }

    public function delete(Holiday $holiday, int $adminId): void
    {
        $this->auditLogger->log(
            event: 'holiday.deleted',
            auditable: $holiday,
            oldValues: $holiday->only(['date', 'name']),
            userId: $adminId,
            userType: 'admin',
        );

        $this->holidays->delete($holiday);
    }
}
