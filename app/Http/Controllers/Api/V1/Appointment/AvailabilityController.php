<?php

namespace App\Http\Controllers\Api\V1\Appointment;

use App\Http\Controllers\Controller;
use App\Http\Requests\Appointment\AvailabilitySlotsRequest;
use App\Services\AvailabilityService;
use Illuminate\Http\JsonResponse;

class AvailabilityController extends Controller
{
    public function __construct(
        private readonly AvailabilityService $availabilityService,
    ) {}

    public function dates(): JsonResponse
    {
        return response()->json([
            'available_dates' => $this->availabilityService->getAvailableDates(),
        ]);
    }

    public function slots(AvailabilitySlotsRequest $request): JsonResponse
    {
        $slots = $this->availabilityService->getAvailableSlots($request->validated('date'));

        return response()->json([
            'date'             => $request->validated('date'),
            'available_slots'  => $slots,
        ]);
    }
}
