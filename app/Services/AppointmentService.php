<?php

namespace App\Services;

use App\DTOs\Appointment\CancelAppointmentDTO;
use App\DTOs\Appointment\CreateAppointmentDTO;
use App\DTOs\Appointment\RescheduleAppointmentDTO;
use App\Enums\AppointmentStatus;
use App\Events\AppointmentCancelled;
use App\Events\AppointmentConfirmed;
use App\Events\AppointmentCreated;
use App\Events\AppointmentRescheduled;
use App\Exceptions\AppointmentAlreadyExistsException;
use App\Exceptions\InvalidAppointmentDateException;
use App\Exceptions\ScheduleUnavailableException;
use App\Exceptions\UnauthorizedAppointmentException;
use App\Models\Appointment;
use App\Models\ScheduleConfig;
use App\Repositories\Contracts\AppointmentRepositoryInterface;
use App\Repositories\Contracts\BlockedScheduleRepositoryInterface;
use App\Repositories\Contracts\CustomerRepositoryInterface;
use App\Repositories\Contracts\HolidayRepositoryInterface;
use App\Repositories\Contracts\ScheduleConfigRepositoryInterface;
use App\Support\AuditLogger;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AppointmentService
{
    public function __construct(
        private readonly AppointmentRepositoryInterface $appointments,
        private readonly CustomerRepositoryInterface $customers,
        private readonly ScheduleConfigRepositoryInterface $configs,
        private readonly HolidayRepositoryInterface $holidays,
        private readonly BlockedScheduleRepositoryInterface $blockedSchedules,
        private readonly AuditLogger $auditLogger,
    ) {}

    /**
     * Cria um agendamento de forma segura contra concorrência.
     *
     * Fluxo:
     *  1. Validações que não exigem lock (data, expediente, feriado, limites)
     *  2. Abre transação
     *  3. Busca conflitos COM lockForUpdate() (bloqueia linhas concorrentes)
     *  4. Revalida conflito dentro da transação
     *  5. Cria o registro
     *  6. Dispara evento (listeners cuidam de notificações fora da transação)
     */
    public function create(CreateAppointmentDTO $dto, bool $bypassCustomerLimits = false): Appointment
    {
        $config = $this->configs->getActive();

        $this->validateDateTime($dto->date, $dto->startTime, $dto->endTime, $config);

        if (! $bypassCustomerLimits) {
            $this->validateCustomerLimits($dto->customerId, $config);
        }

        return DB::transaction(function () use ($dto) {
            $conflicts = $this->appointments->findConflictingWithLock(
                $dto->date,
                $dto->startTime,
                $dto->endTime
            );

            if ($conflicts->isNotEmpty()) {
                throw new AppointmentAlreadyExistsException();
            }

            $appointment = $this->appointments->create($dto->toArray());

            $this->auditLogger->log(
                event: 'appointment.created',
                auditable: $appointment,
                newValues: $dto->toArray(),
                userId: $dto->createdById,
                userType: $dto->createdByType,
            );

            event(new AppointmentCreated($appointment));

            return $appointment;
        });
    }

    public function cancel(CancelAppointmentDTO $dto, Appointment $appointment, bool $isAdmin = false): Appointment
    {
        if (! $isAdmin) {
            $this->guardCustomerOwnership($appointment, $dto->cancelledById);
            $this->validateCancellationAdvance($appointment);
        }

        if ($appointment->isFinal()) {
            throw new InvalidAppointmentDateException('Este agendamento já está em um status final e não pode ser cancelado.');
        }

        return DB::transaction(function () use ($dto, $appointment) {
            $oldStatus = $appointment->status->value;

            $updated = $this->appointments->update($appointment, [
                'status'               => AppointmentStatus::CANCELADO->value,
                'cancelled_by_type'     => $dto->cancelledByType,
                'cancelled_by_id'       => $dto->cancelledById,
                'cancellation_reason'   => $dto->reason,
            ]);

            $this->auditLogger->log(
                event: 'appointment.cancelled',
                auditable: $updated,
                oldValues: ['status' => $oldStatus],
                newValues: ['status' => AppointmentStatus::CANCELADO->value, 'reason' => $dto->reason],
                userId: $dto->cancelledById,
                userType: $dto->cancelledByType,
            );

            event(new AppointmentCancelled($updated));

            return $updated;
        });
    }

    /**
     * Remarcação: o agendamento original é marcado como 'remarcado' e
     * um NOVO registro é criado apontando para ele via rescheduled_from_id.
     * Isso preserva histórico completo em vez de sobrescrever a linha.
     */
    public function reschedule(RescheduleAppointmentDTO $dto, Appointment $original, bool $isAdmin = false): Appointment
    {
        if (! $isAdmin) {
            $this->guardCustomerOwnership($original, $dto->rescheduledById);
            $this->validateCancellationAdvance($original);
        }

        if ($original->isFinal()) {
            throw new InvalidAppointmentDateException('Este agendamento já está em um status final e não pode ser remarcado.');
        }

        $config = $this->configs->getActive();
        $this->validateDateTime($dto->newDate, $dto->newStartTime, $dto->newEndTime, $config);

        return DB::transaction(function () use ($dto, $original) {
            $conflicts = $this->appointments->findConflictingWithLock(
                $dto->newDate,
                $dto->newStartTime,
                $dto->newEndTime
            );

            if ($conflicts->isNotEmpty()) {
                throw new AppointmentAlreadyExistsException();
            }

            $this->appointments->update($original, [
                'status' => AppointmentStatus::REMARCADO->value,
            ]);

            $newAppointment = $this->appointments->create([
                'customer_id'          => $original->customer_id,
                'service'              => $original->service,
                'date'                 => $dto->newDate,
                'start_time'           => $dto->newStartTime,
                'end_time'             => $dto->newEndTime,
                'notes'                => $original->notes,
                'value'                => $original->value,
                'payment_method'       => $original->payment_method?->value,
                'created_by_type'      => $dto->rescheduledByType,
                'created_by_id'        => $dto->rescheduledById,
                'rescheduled_from_id'  => $original->id,
            ]);

            $this->auditLogger->log(
                event: 'appointment.rescheduled',
                auditable: $newAppointment,
                oldValues: ['date' => $original->date->format('Y-m-d'), 'start_time' => $original->start_time],
                newValues: ['date' => $dto->newDate, 'start_time' => $dto->newStartTime],
                userId: $dto->rescheduledById,
                userType: $dto->rescheduledByType,
            );

            event(new AppointmentRescheduled($newAppointment, $original));

            return $newAppointment;
        });
    }

    public function confirm(Appointment $appointment, int $adminId): Appointment
    {
        $this->guardTransition($appointment, AppointmentStatus::CONFIRMADO);

        $updated = $this->appointments->update($appointment, [
            'status' => AppointmentStatus::CONFIRMADO->value,
        ]);

        $this->auditLogger->log(
            event: 'appointment.confirmed',
            auditable: $updated,
            userId: $adminId,
            userType: 'admin',
        );

        event(new AppointmentConfirmed($updated));

        return $updated;
    }

    public function start(Appointment $appointment, int $adminId): Appointment
    {
        $this->guardTransition($appointment, AppointmentStatus::EM_ATENDIMENTO);

        return $this->appointments->update($appointment, [
            'status' => AppointmentStatus::EM_ATENDIMENTO->value,
        ]);
    }

    public function finish(Appointment $appointment, int $adminId): Appointment
    {
        $this->guardTransition($appointment, AppointmentStatus::FINALIZADO);

        return $this->appointments->update($appointment, [
            'status' => AppointmentStatus::FINALIZADO->value,
        ]);
    }

    public function markNoShow(Appointment $appointment, int $adminId): Appointment
    {
        $this->guardTransition($appointment, AppointmentStatus::NAO_COMPARECEU);

        return $this->appointments->update($appointment, [
            'status' => AppointmentStatus::NAO_COMPARECEU->value,
        ]);
    }

    // ------------------------------------------------------------------
    // Validações privadas
    // ------------------------------------------------------------------

    private function validateDateTime(string $date, string $startTime, string $endTime, ScheduleConfig $config): void
    {
        $carbonDate = Carbon::parse($date);
        $startDateTime = Carbon::parse("{$date} {$startTime}");

        if ($carbonDate->lt(now()->startOfDay())) {
            throw new InvalidAppointmentDateException('Não é possível agendar em uma data passada.');
        }

        if ($startDateTime->lt(now())) {
            throw new InvalidAppointmentDateException('Não é possível agendar em um horário passado.');
        }

        if ($startDateTime->lt(now()->addHours($config->min_advance_hours))) {
            throw new InvalidAppointmentDateException(
                "É necessário agendar com pelo menos {$config->min_advance_hours}h de antecedência."
            );
        }

        if ($carbonDate->gt(now()->addDays($config->max_advance_days))) {
            throw new InvalidAppointmentDateException(
                "Não é possível agendar mais de {$config->max_advance_days} dias no futuro."
            );
        }

        if (! $config->isDayAllowed($carbonDate->isoWeekday())) {
            throw new ScheduleUnavailableException('Este dia da semana não está disponível para agendamento.');
        }

        if ($this->holidays->isHoliday($carbonDate)) {
            throw new ScheduleUnavailableException('Esta data é um feriado.');
        }

        if ($this->blockedSchedules->isDateFullyBlocked($date)) {
            throw new ScheduleUnavailableException('Esta data está bloqueada.');
        }

        $this->validateWithinWorkingHours($startTime, $endTime, $config);
        $this->validateNotDuringBreak($startTime, $endTime, $config);
        $this->validateSlotAlignment($startTime, $config);
    }

    private function validateWithinWorkingHours(string $startTime, string $endTime, ScheduleConfig $config): void
    {
        $start = Carbon::parse($startTime);
        $end = Carbon::parse($endTime);
        $workStart = Carbon::parse($config->work_start);
        $workEnd = Carbon::parse($config->work_end);

        if ($start->lt($workStart) || $end->gt($workEnd)) {
            throw new ScheduleUnavailableException('Horário fora do expediente.');
        }
    }

    private function validateNotDuringBreak(string $startTime, string $endTime, ScheduleConfig $config): void
    {
        if (! $config->break_start || ! $config->break_end) {
            return;
        }

        $start = Carbon::parse($startTime);
        $end = Carbon::parse($endTime);
        $breakStart = Carbon::parse($config->break_start);
        $breakEnd = Carbon::parse($config->break_end);

        if ($start->lt($breakEnd) && $end->gt($breakStart)) {
            throw new ScheduleUnavailableException('Horário dentro do intervalo de descanso.');
        }
    }

    private function validateSlotAlignment(string $startTime, ScheduleConfig $config): void
    {
        $workStart = Carbon::parse($config->work_start);
        $requested = Carbon::parse($startTime);

        $diffMinutes = $workStart->diffInMinutes($requested);

        if ($diffMinutes % $config->slot_duration !== 0) {
            throw new ScheduleUnavailableException('Horário inválido — não corresponde a um slot configurado.');
        }
    }

    private function validateCustomerLimits(int $customerId, ScheduleConfig $config): void
    {
        $count = $this->appointments->countFutureActiveForCustomer($customerId);

        if ($count >= $config->max_future_appointments) {
            throw new ScheduleUnavailableException(
                "Você atingiu o limite de {$config->max_future_appointments} agendamentos futuros."
            );
        }
    }

    private function validateCancellationAdvance(Appointment $appointment): void
    {
        $config = $this->configs->getActive();
        $appointmentDateTime = $appointment->getStartDateTime();

        $minCancelTime = now()->addHours($config->cancellation_advance_hours);

        if ($appointmentDateTime->lt($minCancelTime)) {
            throw new InvalidAppointmentDateException(
                "Cancelamentos devem ser feitos com pelo menos {$config->cancellation_advance_hours}h de antecedência."
            );
        }
    }

    private function guardCustomerOwnership(Appointment $appointment, int $customerId): void
    {
        if ($appointment->customer_id !== $customerId) {
            throw new UnauthorizedAppointmentException();
        }
    }

    private function guardTransition(Appointment $appointment, AppointmentStatus $target): void
    {
        if (! $appointment->status->canTransitionTo($target)) {
            throw new InvalidAppointmentDateException(
                "Não é possível alterar o status de '{$appointment->status->label()}' para '{$target->label()}'."
            );
        }
    }
}
