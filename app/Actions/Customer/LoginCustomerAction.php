<?php

namespace App\Actions\Customer;

use App\DTOs\Customer\LoginDTO;
use App\Services\CustomerAuthService;

class LoginCustomerAction
{
    public function __construct(
        private readonly CustomerAuthService $authService,
    ) {}

    public function execute(LoginDTO $dto): array
    {
        return $this->authService->login($dto);
    }
}
