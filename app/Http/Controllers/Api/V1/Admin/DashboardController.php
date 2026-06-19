<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\DashboardResource;
use App\Services\DashboardService;
use Illuminate\Http\JsonResponse;

class DashboardController extends Controller
{
    public function __construct(
        private readonly DashboardService $dashboardService,
    ) {}

    public function index(): JsonResponse
    {
        return response()->json([
            'overview' => $this->dashboardService->overview(),
        ]);
    }

    public function today(): JsonResponse
    {
        return response()->json([
            'today' => new DashboardResource($this->dashboardService->today()),
        ]);
    }

    public function tomorrow(): JsonResponse
    {
        return response()->json([
            'tomorrow' => new DashboardResource($this->dashboardService->tomorrow()),
        ]);
    }

    public function week(): JsonResponse
    {
        return response()->json([
            'week' => new DashboardResource($this->dashboardService->week()),
        ]);
    }

    public function month(): JsonResponse
    {
        return response()->json([
            'month' => new DashboardResource($this->dashboardService->month()),
        ]);
    }
}
