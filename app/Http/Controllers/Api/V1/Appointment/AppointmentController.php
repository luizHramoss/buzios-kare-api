<?php

namespace App\Http\Controllers\Api\V1\Appointment;

use App\Actions\Appointment\CancelAppointmentAction;
use App\Actions\Appointment\CreateAppointmentAction;
use App\Actions\Appointment\RescheduleAppointmentAction;
use App\DTOs\Appointment\CancelAppointmentDTO;
use App\DTOs\Appointment\CreateAppointmentDTO;
use App\DTOs\Appointment\RescheduleAppointmentDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\Appointment\CancelAppointmentRequest;
use App\Http\Requests\Appointment\CreateAppointmentRequest;
use App\Http\Requests\Appointment\RescheduleAppointmentRequest;
use App\Http\Resources\AppointmentResource;
use App\Models\Appointment;
use App\Repositories\Contracts\AppointmentRepositoryInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AppointmentController extends Controller
{
    public function __construct(
        private readonly AppointmentRepositoryInterface $appointments,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $customer = $request->user('customer');

        $appointments = $this->appointments->paginateForCustomer(
            $customer->id,
            filters: $request->only(['status', 'date_from', 'date_to']),
        );

        return response()->json([
            'appointments' => AppointmentResource::collection($appointments),
            'meta' => [
                'current_page' => $appointments->currentPage(),
                'last_page'    => $appointments->lastPage(),
                'total'        => $appointments->total(),
            ],
        ]);
    }

    public function store(CreateAppointmentRequest $request, CreateAppointmentAction $action): JsonResponse
    {
        $customer = $request->user('customer');

        $dto = CreateAppointmentDTO::fromArray(
            $request->validated(),
            customerId: $customer->id,
            createdByType: 'customer',
            createdById: $customer->id,
        );

        $appointment = $action->execute($dto);

        return response()->json([
            'message'     => 'Agendamento criado com sucesso.',
            'appointment' => new AppointmentResource($appointment),
        ], 201);
    }

    public function show(Request $request, string $uuid): JsonResponse
    {
        $appointment = $this->appointments->findByUuidOrFail($uuid);

        $this->authorize('view', $appointment);

        return response()->json([
            'appointment' => new AppointmentResource($appointment),
        ]);
    }

    public function cancel(
        CancelAppointmentRequest $request,
        string $uuid,
        CancelAppointmentAction $action
    ): JsonResponse {
        $appointment = $this->appointments->findByUuidOrFail($uuid);

        $this->authorize('cancel', $appointment);

        $customer = $request->user('customer');

        $dto = CancelAppointmentDTO::fromArray(
            $request->validated(),
            uuid: $uuid,
            cancelledByType: 'customer',
            cancelledById: $customer->id,
        );

        $cancelled = $action->execute($dto, $appointment, isAdmin: false);

        return response()->json([
            'message'     => 'Agendamento cancelado com sucesso.',
            'appointment' => new AppointmentResource($cancelled),
        ]);
    }

    public function reschedule(
        RescheduleAppointmentRequest $request,
        string $uuid,
        RescheduleAppointmentAction $action
    ): JsonResponse {
        $appointment = $this->appointments->findByUuidOrFail($uuid);

        $this->authorize('reschedule', $appointment);

        $customer = $request->user('customer');

        $dto = RescheduleAppointmentDTO::fromArray(
            $request->validated(),
            uuid: $uuid,
            byType: 'customer',
            byId: $customer->id,
        );

        $newAppointment = $action->execute($dto, $appointment, isAdmin: false);

        return response()->json([
            'message'     => 'Agendamento remarcado com sucesso.',
            'appointment' => new AppointmentResource($newAppointment),
        ]);
    }
}
