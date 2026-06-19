<?php

namespace App\Http\Requests\Appointment;

use Illuminate\Foundation\Http\FormRequest;

class AdminUpdateAppointmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'service'        => ['sometimes', 'string', 'max:100'],
            'notes'          => ['sometimes', 'nullable', 'string', 'max:2000'],
            'value'          => ['sometimes', 'numeric', 'min:0'],
            'payment_method' => ['sometimes', 'nullable', 'string'],
        ];
    }
}
