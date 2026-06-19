<?php

namespace App\Services;

use App\Models\ScheduleConfig;
use App\Repositories\Contracts\AppointmentRepositoryInterface;
use App\Repositories\Contracts\BlockedScheduleRepositoryInterface;
use App\Repositories\Contracts\HolidayRepositoryInterface;
use App\Repositories\Contracts\ScheduleConfigRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Support\Collection;

/**
 * Calcula disponibilidade de datas e horários.
 *
 * Regra de segurança: NUNCA retorna nomes de clientes, quantidade de
 * consultas, horários ocupados específicos ou qualquer dado de terceiros.
 * Apenas true/false sobre disponibilidade.
 */
class AvailabilityService
{
    public function __construct(
        private readonly ScheduleConfigRepositoryInterface $configs,
        private readonly HolidayRepositoryInterface $holidays,
        private readonly BlockedScheduleRepositoryInterface $blockedSchedules,
        private readonly AppointmentRepositoryInterface $appointments,
    ) {}

    /**
     * Retorna as datas disponíveis dentro da janela configurada
     * (hoje + min_advance_hours até hoje + max_advance_days).
     */
    public function getAvailableDates(): array
    {
        $config = $this->configs->getActive();

        $start = now()->addHours($config->min_advance_hours)->startOfDay();
        $end   = now()->addDays($config->max_advance_days)->endOfDay();

        $dates = [];
        $cursor = $start->copy();

        while ($cursor->lte($end)) {
            if ($this->isDateAvailable($cursor, $config)) {
                $dates[] = $cursor->format('Y-m-d');
            }
            $cursor->addDay();
        }

        return $dates;
    }

    /**
     * Retorna os horários (start_time) disponíveis para uma data específica.
     * Formato: ['09:00', '10:00', ...]
     */
    public function getAvailableSlots(string $date): array
    {
        $config = $this->configs->getActive();
        $carbonDate = Carbon::parse($date);

        if (! $this->passesDateLevelChecks($carbonDate, $config)) {
            return [];
        }

        return $this->computeFreeSlots($date, $carbonDate, $config);
    }

    private function isDateAvailable(Carbon $date, ScheduleConfig $config): bool
    {
        if (! $this->passesDateLevelChecks($date, $config)) {
            return false;
        }

        return ! empty($this->computeFreeSlots($date->format('Y-m-d'), $date, $config));
    }

    /**
     * Checagens que não dependem de slot individual: dia da semana,
     * feriado e bloqueio de dia inteiro.
     */
    private function passesDateLevelChecks(Carbon $date, ScheduleConfig $config): bool
    {
        if (! $config->isDayAllowed($date->isoWeekday())) {
            return false;
        }

        if ($this->holidays->isHoliday($date)) {
            return false;
        }

        if ($this->blockedSchedules->isDateFullyBlocked($date->format('Y-m-d'))) {
            return false;
        }

        return true;
    }

    /**
     * Calcula os slots livres de fato: gera todos os horários do expediente,
     * remove ocupados, bloqueados e os que violam antecedência mínima.
     */
    private function computeFreeSlots(string $date, Carbon $carbonDate, ScheduleConfig $config): array
    {
        $allSlots = $this->generateAllSlotsForDay($config);

        $occupied = $this->appointments->getOccupiedSlotsForDate($date)
            ->map(fn ($appt) => $appt->start_time)
            ->toArray();

        $blocks = $this->blockedSchedules->getForDate($date);

        $minDateTime = now()->addHours($config->min_advance_hours);

        return array_values(array_filter($allSlots, function (string $slot) use (
            $occupied, $blocks, $carbonDate, $minDateTime
        ) {
            if (in_array($slot, $occupied, true)) {
                return false;
            }

            if ($this->isSlotBlocked($slot, $blocks)) {
                return false;
            }

            $slotDateTime = Carbon::parse($carbonDate->format('Y-m-d') . ' ' . $slot);
            if ($slotDateTime->lt($minDateTime)) {
                return false;
            }

            return true;
        }));
    }

    /**
     * Gera todos os horários possíveis do dia, considerando expediente,
     * intervalo e duração da consulta. Formato: ['08:00', '09:00', ...]
     */
    private function generateAllSlotsForDay(ScheduleConfig $config): array
    {
        $slots = [];

        $cursor = Carbon::parse($config->work_start);
        $end    = Carbon::parse($config->work_end);
        $duration = $config->slot_duration;

        $breakStart = $config->break_start ? Carbon::parse($config->break_start) : null;
        $breakEnd   = $config->break_end ? Carbon::parse($config->break_end) : null;

        while ($cursor->copy()->addMinutes($duration)->lte($end)) {
            $slotEnd = $cursor->copy()->addMinutes($duration);

            $isDuringBreak = $breakStart && $breakEnd
                && $cursor->lt($breakEnd) && $slotEnd->gt($breakStart);

            if (! $isDuringBreak) {
                $slots[] = $cursor->format('H:i');
            }

            $cursor->addMinutes($duration);
        }

        return $slots;
    }

    private function isSlotBlocked(string $slot, Collection $blocks): bool
    {
        foreach ($blocks as $block) {
            if ($block->isFullDay()) {
                return true;
            }

            $slotTime = Carbon::parse($slot);
            $blockStart = Carbon::parse($block->start_time);
            $blockEnd = Carbon::parse($block->end_time);

            if ($slotTime->gte($blockStart) && $slotTime->lt($blockEnd)) {
                return true;
            }
        }

        return false;
    }
}
