<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Actions\Customer\LoginCustomerAction;
use App\Actions\Customer\RegisterCustomerAction;
use App\DTOs\Customer\LoginDTO;
use App\DTOs\Customer\RegisterCustomerDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterCustomerRequest;
use App\Http\Resources\CustomerResource;
use App\Services\CustomerAuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CustomerAuthController extends Controller
{
    public function __construct(
        private readonly CustomerAuthService $authService,
    ) {}

    public function register(RegisterCustomerRequest $request, RegisterCustomerAction $action): JsonResponse
    {
        $dto = RegisterCustomerDTO::fromArray($request->validated());

        $result = $action->execute($dto);

        return response()->json([
            'message'  => 'Cadastro realizado com sucesso.',
            'customer' => new CustomerResource($result['customer']),
            'token'    => $result['token'],
        ], 201);
    }

    public function login(LoginRequest $request, LoginCustomerAction $action): JsonResponse
    {
        $dto = LoginDTO::fromArray($request->validated());

        $result = $action->execute($dto);

        return response()->json([
            'message'  => 'Login realizado com sucesso.',
            'customer' => new CustomerResource($result['customer']),
            'token'    => $result['token'],
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        $this->authService->logout($request->user('customer'));

        return response()->json([
            'message' => 'Logout realizado com sucesso.',
        ]);
    }
}
