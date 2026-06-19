<?php

namespace App\Actions\Customer;

use App\DTOs\Customer\RegisterCustomerDTO;
use App\Services\CustomerAuthService;

class RegisterCustomerAction
{
    public function __construct(
        private readonly CustomerAuthService $authService,
    ) {}

    public function execute(RegisterCustomerDTO $dto): array
    {
        return $this->authService->register($dto);
    }
}
