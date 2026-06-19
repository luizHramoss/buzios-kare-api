<?php

namespace App\Http\Requests\Appointment;

use Illuminate\Foundation\Http\FormRequest;

class AdminCreateAppointmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'customer_uuid'  => ['required', 'uuid', 'exists:customers,uuid'],
            'service'        => ['required', 'string', 'max:100'],
            'date'           => ['required', 'date'],
            'start_time'     => ['required', 'date_format:H:i'],
            'end_time'       => ['required', 'date_format:H:i', 'after:start_time'],
            'notes'          => ['nullable', 'string', 'max:2000'],
            'value'          => ['nullable', 'numeric', 'min:0'],
            'payment_method' => ['nullable', 'string'],
        ];
    }
}
