<?php

namespace App\Actions\Customer;

use App\Models\Customer;
use App\Services\CustomerAdminService;

class UpdateCustomerAction
{
    public function __construct(
        private readonly CustomerAdminService $service,
    ) {}

    public function execute(Customer $customer, array $data, int $adminId): Customer
    {
        return $this->service->update($customer, $data, $adminId);
    }
}
