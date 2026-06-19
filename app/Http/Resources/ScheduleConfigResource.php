<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ScheduleConfigResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'work_start'                  => $this->work_start,
            'work_end'                    => $this->work_end,
            'break_start'                 => $this->break_start,
            'break_end'                   => $this->break_end,
            'slot_duration'               => $this->slot_duration,
            'min_advance_hours'           => $this->min_advance_hours,
            'max_advance_days'            => $this->max_advance_days,
            'max_future_appointments'     => $this->max_future_appointments,
            'cancellation_advance_hours'  => $this->cancellation_advance_hours,
            'allowed_days'                => $this->allowed_days,
        ];
    }
}
