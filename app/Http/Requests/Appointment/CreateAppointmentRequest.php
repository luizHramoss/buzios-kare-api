<?php

namespace App\Http\Requests\Appointment;

use Illuminate\Foundation\Http\FormRequest;

class CreateAppointmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // já filtrado por auth.role:customer
    }

    public function rules(): array
    {
        return [
            'service'        => ['required', 'string', 'max:100'],
            'date'           => ['required', 'date', 'after_or_equal:today'],
            'start_time'     => ['required', 'date_format:H:i'],
            'end_time'       => ['required', 'date_format:H:i', 'after:start_time'],
            'notes'          => ['nullable', 'string', 'max:2000'],
            'value'          => ['nullable', 'numeric', 'min:0'],
            'payment_method' => ['nullable', 'string'],
        ];
    }
}
