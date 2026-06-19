<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Actions\Admin\CreateAdminAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AdminCreateAdminRequest;
use App\Http\Requests\Admin\AdminUpdateAdminRequest;
use App\Http\Resources\AdminResource;
use App\Models\Admin;
use App\Services\AdminManagementService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function __construct(
        private readonly AdminManagementService $service,
    ) {}

    public function index(): JsonResponse
    {
        $admins = $this->service->paginate();

        return response()->json([
            'admins' => AdminResource::collection($admins),
            'meta' => [
                'current_page' => $admins->currentPage(),
                'last_page'    => $admins->lastPage(),
                'total'        => $admins->total(),
            ],
        ]);
    }

    public function store(AdminCreateAdminRequest $request, CreateAdminAction $action): JsonResponse
    {
        $admin = $action->execute($request->validated(), $request->user('admin'));

        return response()->json([
            'message' => 'Administrador cadastrado com sucesso.',
            'admin'   => new AdminResource($admin),
        ], 201);
    }

    public function show(string $uuid): JsonResponse
    {
        $admin = Admin::whereUuid($uuid)->firstOrFail();

        return response()->json([
            'admin' => new AdminResource($admin),
        ]);
    }

    public function update(AdminUpdateAdminRequest $request, string $uuid): JsonResponse
    {
        $target = Admin::whereUuid($uuid)->firstOrFail();

        $updated = $this->service->update($target, $request->validated(), $request->user('admin'));

        return response()->json([
            'message' => 'Administrador atualizado com sucesso.',
            'admin'   => new AdminResource($updated),
        ]);
    }

    public function destroy(Request $request, string $uuid): JsonResponse
    {
        $target = Admin::whereUuid($uuid)->firstOrFail();

        $this->service->delete($target, $request->user('admin'));

        return response()->json([
            'message' => 'Administrador removido com sucesso.',
        ]);
    }
}
