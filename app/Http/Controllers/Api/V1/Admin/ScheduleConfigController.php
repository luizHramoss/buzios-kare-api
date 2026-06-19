<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Actions\Admin\ConfigureScheduleAction;
use App\DTOs\Admin\UpdateScheduleConfigDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateScheduleConfigRequest;
use App\Http\Resources\ScheduleConfigResource;
use App\Services\ScheduleConfigService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ScheduleConfigController extends Controller
{
    public function __construct(
        private readonly ScheduleConfigService $service,
    ) {}

    public function show(): JsonResponse
    {
        return response()->json([
            'schedule_config' => new ScheduleConfigResource($this->service->getActive()),
        ]);
    }

    public function update(UpdateScheduleConfigRequest $request, ConfigureScheduleAction $action): JsonResponse
    {
        $dto = UpdateScheduleConfigDTO::fromArray($request->validated());

        $config = $action->execute($dto, $request->user('admin'));

        return response()->json([
            'message'         => 'Configuração da agenda atualizada com sucesso.',
            'schedule_config' => new ScheduleConfigResource($config),
        ]);
    }
}
