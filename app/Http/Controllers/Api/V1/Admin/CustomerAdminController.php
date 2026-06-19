<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Actions\Customer\CreateCustomerAction;
use App\Actions\Customer\DeleteCustomerAction;
use App\Actions\Customer\UpdateCustomerAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Customer\AdminCreateCustomerRequest;
use App\Http\Requests\Customer\AdminUpdateCustomerRequest;
use App\Http\Resources\CustomerResource;
use App\Models\Customer;
use App\Services\CustomerAdminService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CustomerAdminController extends Controller
{
    public function __construct(
        private readonly CustomerAdminService $service,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $customers = $this->service->paginate(
            perPage: (int) $request->get('per_page', 15),
            filters: $request->only(['status', 'search']),
        );

        return response()->json([
            'customers' => CustomerResource::collection($customers),
            'meta' => [
                'current_page' => $customers->currentPage(),
                'last_page'    => $customers->lastPage(),
                'total'        => $customers->total(),
            ],
        ]);
    }

    public function store(AdminCreateCustomerRequest $request, CreateCustomerAction $action): JsonResponse
    {
        $customer = $action->execute($request->validated(), $request->user('admin')->id);

        return response()->json([
            'message'  => 'Cliente cadastrado com sucesso.',
            'customer' => new CustomerResource($customer),
        ], 201);
    }

    public function show(string $uuid): JsonResponse
    {
        $customer = Customer::whereUuid($uuid)->firstOrFail();

        return response()->json([
            'customer' => new CustomerResource($customer),
        ]);
    }

    public function update(AdminUpdateCustomerRequest $request, string $uuid, UpdateCustomerAction $action): JsonResponse
    {
        $customer = Customer::whereUuid($uuid)->firstOrFail();

        $updated = $action->execute($customer, $request->validated(), $request->user('admin')->id);

        return response()->json([
            'message'  => 'Cliente atualizado com sucesso.',
            'customer' => new CustomerResource($updated),
        ]);
    }

    public function destroy(Request $request, string $uuid, DeleteCustomerAction $action): JsonResponse
    {
        $customer = Customer::whereUuid($uuid)->firstOrFail();

        $action->execute($customer, $request->user('admin')->id);

        return response()->json([
            'message' => 'Cliente removido com sucesso.',
        ]);
    }
}
