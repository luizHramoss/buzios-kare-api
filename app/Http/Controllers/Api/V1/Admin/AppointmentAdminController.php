<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Actions\Appointment\CancelAppointmentAction;
use App\Actions\Appointment\CreateAppointmentAction;
use App\Actions\Appointment\RescheduleAppointmentAction;
use App\DTOs\Appointment\CancelAppointmentDTO;
use App\DTOs\Appointment\CreateAppointmentDTO;
use App\DTOs\Appointment\RescheduleAppointmentDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\Appointment\AdminCreateAppointmentRequest;
use App\Http\Requests\Appointment\AdminUpdateAppointmentRequest;
use App\Http\Requests\Appointment\CancelAppointmentRequest;
use App\Http\Requests\Appointment\RescheduleAppointmentRequest;
use App\Http\Resources\AppointmentResource;
use App\Models\Customer;
use App\Repositories\Contracts\AppointmentRepositoryInterface;
use App\Services\AppointmentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AppointmentAdminController extends Controller
{
    public function __construct(
        private readonly AppointmentRepositoryInterface $appointments,
        private readonly AppointmentService $appointmentService,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $appointments = $this->appointments->paginateAll(
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

    public function store(AdminCreateAppointmentRequest $request, CreateAppointmentAction $action): JsonResponse
    {
        $admin = $request->user('admin');
        $customer = Customer::whereUuid($request->validated('customer_uuid'))->firstOrFail();

        $dto = CreateAppointmentDTO::fromArray(
            $request->validated(),
            customerId: $customer->id,
            createdByType: 'admin',
            createdById: $admin->id,
        );

        // Admin pode ultrapassar os limites configurados para o cliente
        $appointment = $action->execute($dto, bypassCustomerLimits: true);

        return response()->json([
            'message'     => 'Agendamento criado com sucesso.',
            'appointment' => new AppointmentResource($appointment->load('customer')),
        ], 201);
    }

    public function show(string $uuid): JsonResponse
    {
        $appointment = $this->appointments->findByUuidOrFail($uuid);
        $appointment->load('customer');

        return response()->json([
            'appointment' => new AppointmentResource($appointment),
        ]);
    }

    public function update(AdminUpdateAppointmentRequest $request, string $uuid): JsonResponse
    {
        $appointment = $this->appointments->findByUuidOrFail($uuid);

        $updated = $this->appointments->update($appointment, $request->validated());

        return response()->json([
            'message'     => 'Agendamento atualizado com sucesso.',
            'appointment' => new AppointmentResource($updated->load('customer')),
        ]);
    }

    public function cancel(CancelAppointmentRequest $request, string $uuid, CancelAppointmentAction $action): JsonResponse
    {
        $appointment = $this->appointments->findByUuidOrFail($uuid);
        $admin = $request->user('admin');

        $dto = CancelAppointmentDTO::fromArray(
            $request->validated(),
            uuid: $uuid,
            cancelledByType: 'admin',
            cancelledById: $admin->id,
        );

        $cancelled = $action->execute($dto, $appointment, isAdmin: true);

        return response()->json([
            'message'     => 'Agendamento cancelado com sucesso.',
            'appointment' => new AppointmentResource($cancelled),
        ]);
    }

    public function reschedule(RescheduleAppointmentRequest $request, string $uuid, RescheduleAppointmentAction $action): JsonResponse
    {
        $appointment = $this->appointments->findByUuidOrFail($uuid);
        $admin = $request->user('admin');

        $dto = RescheduleAppointmentDTO::fromArray(
            $request->validated(),
            uuid: $uuid,
            byType: 'admin',
            byId: $admin->id,
        );

        $newAppointment = $action->execute($dto, $appointment, isAdmin: true);

        return response()->json([
            'message'     => 'Agendamento remarcado com sucesso.',
            'appointment' => new AppointmentResource($newAppointment->load('customer')),
        ]);
    }

    public function confirm(Request $request, string $uuid): JsonResponse
    {
        $appointment = $this->appointments->findByUuidOrFail($uuid);

        $updated = $this->appointmentService->confirm($appointment, $request->user('admin')->id);

        return response()->json([
            'message'     => 'Agendamento confirmado.',
            'appointment' => new AppointmentResource($updated),
        ]);
    }

    public function start(Request $request, string $uuid): JsonResponse
    {
        $appointment = $this->appointments->findByUuidOrFail($uuid);

        $updated = $this->appointmentService->start($appointment, $request->user('admin')->id);

        return response()->json([
            'message'     => 'Atendimento iniciado.',
            'appointment' => new AppointmentResource($updated),
        ]);
    }

    public function finish(Request $request, string $uuid): JsonResponse
    {
        $appointment = $this->appointments->findByUuidOrFail($uuid);

        $updated = $this->appointmentService->finish($appointment, $request->user('admin')->id);

        return response()->json([
            'message'     => 'Atendimento finalizado.',
            'appointment' => new AppointmentResource($updated),
        ]);
    }

    public function noShow(Request $request, string $uuid): JsonResponse
    {
        $appointment = $this->appointments->findByUuidOrFail($uuid);

        $updated = $this->appointmentService->markNoShow($appointment, $request->user('admin')->id);

        return response()->json([
            'message'     => 'Agendamento marcado como "não compareceu".',
            'appointment' => new AppointmentResource($updated),
        ]);
    }
}
