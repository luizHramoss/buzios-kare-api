<?php

namespace App\Actions\Admin;

use App\Models\Admin;
use App\Services\AdminManagementService;

class CreateAdminAction
{
    public function __construct(
        private readonly AdminManagementService $service,
    ) {}

    public function execute(array $data, Admin $actingAdmin): Admin
    {
        return $this->service->create($data, $actingAdmin);
    }
}
