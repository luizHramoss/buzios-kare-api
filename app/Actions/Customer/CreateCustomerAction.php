<?php

namespace App\Actions\Customer;

use App\Models\Customer;
use App\Services\CustomerAdminService;

class CreateCustomerAction
{
    public function __construct(
        private readonly CustomerAdminService $service,
    ) {}

    public function execute(array $data, int $adminId): Customer
    {
        return $this->service->create($data, $adminId);
    }
}
