<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\HolidayRequest;
use App\Http\Resources\HolidayResource;
use App\Models\Holiday;
use App\Services\HolidayService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class HolidayController extends Controller
{
    public function __construct(
        private readonly HolidayService $service,
    ) {}

    public function index(): JsonResponse
    {
        return response()->json([
            'holidays' => HolidayResource::collection($this->service->list()),
        ]);
    }

    public function store(HolidayRequest $request): JsonResponse
    {
        $holiday = $this->service->create($request->validated(), $request->user('admin')->id);

        return response()->json([
            'message' => 'Feriado cadastrado com sucesso.',
            'holiday' => new HolidayResource($holiday),
        ], 201);
    }

    public function update(HolidayRequest $request, Holiday $holiday): JsonResponse
    {
        $holiday = $this->service->update($holiday, $request->validated(), $request->user('admin')->id);

        return response()->json([
            'message' => 'Feriado atualizado com sucesso.',
            'holiday' => new HolidayResource($holiday),
        ]);
    }

    public function destroy(Request $request, Holiday $holiday): JsonResponse
    {
        $this->service->delete($holiday, $request->user('admin')->id);

        return response()->json([
            'message' => 'Feriado removido com sucesso.',
        ]);
    }
}
