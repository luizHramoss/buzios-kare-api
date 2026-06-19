<?php

namespace App\Actions\Customer;

use App\Models\Customer;
use App\Services\CustomerAdminService;

class DeleteCustomerAction
{
    public function __construct(
        private readonly CustomerAdminService $service,
    ) {}

    public function execute(Customer $customer, int $adminId): void
    {
        $this->service->delete($customer, $adminId);
    }
}
