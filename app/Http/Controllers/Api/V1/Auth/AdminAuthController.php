<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Actions\Admin\LoginAdminAction;
use App\DTOs\Customer\LoginDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Resources\AdminResource;
use App\Services\AdminAuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdminAuthController extends Controller
{
    public function __construct(
        private readonly AdminAuthService $authService,
    ) {}

    public function login(LoginRequest $request, LoginAdminAction $action): JsonResponse
    {
        $dto = LoginDTO::fromArray($request->validated());

        $result = $action->execute($dto);

        return response()->json([
            'message' => 'Login realizado com sucesso.',
            'admin'   => new AdminResource($result['admin']),
            'token'   => $result['token'],
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        $this->authService->logout($request->user('admin'));

        return response()->json([
            'message' => 'Logout realizado com sucesso.',
        ]);
    }
}
