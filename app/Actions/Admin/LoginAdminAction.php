<?php

namespace App\Actions\Admin;

use App\DTOs\Customer\LoginDTO;
use App\Services\AdminAuthService;

class LoginAdminAction
{
    public function __construct(
        private readonly AdminAuthService $authService,
    ) {}

    public function execute(LoginDTO $dto): array
    {
        return $this->authService->login($dto);
    }
}
