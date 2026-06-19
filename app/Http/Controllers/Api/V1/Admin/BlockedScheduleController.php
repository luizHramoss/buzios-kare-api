<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Actions\Admin\BlockScheduleAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\BlockedScheduleRequest;
use App\Http\Resources\BlockedScheduleResource;
use App\Models\BlockedSchedule;
use App\Services\BlockedScheduleService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BlockedScheduleController extends Controller
{
    public function __construct(
        private readonly BlockedScheduleService $service,
    ) {}

    public function index(): JsonResponse
    {
        return response()->json([
            'blocked_schedules' => BlockedScheduleResource::collection($this->service->paginate()),
        ]);
    }

    public function store(BlockedScheduleRequest $request, BlockScheduleAction $action): JsonResponse
    {
        $block = $action->execute($request->validated(), $request->user('admin'));

        return response()->json([
            'message'         => 'Horário bloqueado com sucesso.',
            'blocked_schedule' => new BlockedScheduleResource($block),
        ], 201);
    }

    public function destroy(Request $request, string $uuid): JsonResponse
    {
        $block = BlockedSchedule::whereUuid($uuid)->firstOrFail();

        $this->service->delete($block, $request->user('admin'));

        return response()->json([
            'message' => 'Bloqueio removido com sucesso.',
        ]);
    }
}
