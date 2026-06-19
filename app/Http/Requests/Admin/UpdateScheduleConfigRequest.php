<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateScheduleConfigRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // já filtrado por auth.role:admin
    }

    public function rules(): array
    {
        return [
            'work_start'                  => ['sometimes', 'date_format:H:i'],
            'work_end'                    => ['sometimes', 'date_format:H:i', 'after:work_start'],
            'break_start'                 => ['sometimes', 'nullable', 'date_format:H:i'],
            'break_end'                   => ['sometimes', 'nullable', 'date_format:H:i', 'after:break_start'],
            'slot_duration'                => ['sometimes', 'integer', 'min:5', 'max:480'],
            'min_advance_hours'            => ['sometimes', 'integer', 'min:0', 'max:720'],
            'max_advance_days'             => ['sometimes', 'integer', 'min:1', 'max:365'],
            'max_future_appointments'      => ['sometimes', 'integer', 'min:1', 'max:50'],
            'cancellation_advance_hours'   => ['sometimes', 'integer', 'min:0', 'max:720'],
            'allowed_days'                 => ['sometimes', 'array', 'min:1'],
            'allowed_days.*'               => ['integer', 'min:1', 'max:7'],
        ];
    }
}
