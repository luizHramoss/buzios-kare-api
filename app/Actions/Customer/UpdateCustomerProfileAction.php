<?php

namespace App\Actions\Customer;

use App\DTOs\Customer\UpdateCustomerProfileDTO;
use App\Models\Customer;
use App\Services\CustomerProfileService;

class UpdateCustomerProfileAction
{
    public function __construct(
        private readonly CustomerProfileService $profileService,
    ) {}

    public function execute(Customer $customer, UpdateCustomerProfileDTO $dto): Customer
    {
        return $this->profileService->updateProfile($customer, $dto);
    }
}
