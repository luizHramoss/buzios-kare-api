<?php

namespace App\Http\Controllers\Api\V1\Profile;

use App\Actions\Customer\UpdateCustomerProfileAction;
use App\DTOs\Customer\UpdateCustomerProfileDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\Customer\UpdateCustomerProfileRequest;
use App\Http\Requests\Customer\UpdatePasswordRequest;
use App\Http\Resources\CustomerResource;
use App\Services\CustomerProfileService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function __construct(
        private readonly CustomerProfileService $profileService,
    ) {}

    public function show(Request $request): JsonResponse
    {
        return response()->json([
            'customer' => new CustomerResource($request->user('customer')),
        ]);
    }

    public function update(
        UpdateCustomerProfileRequest $request,
        UpdateCustomerProfileAction $action
    ): JsonResponse {
        $dto = UpdateCustomerProfileDTO::fromArray($request->validated());

        $customer = $action->execute($request->user('customer'), $dto);

        return response()->json([
            'message'  => 'Perfil atualizado com sucesso.',
            'customer' => new CustomerResource($customer),
        ]);
    }

    public function updatePassword(UpdatePasswordRequest $request): JsonResponse
    {
        $this->profileService->updatePassword(
            $request->user('customer'),
            $request->validated('current_password'),
            $request->validated('password'),
        );

        return response()->json([
            'message' => 'Senha alterada com sucesso.',
        ]);
    }
}
