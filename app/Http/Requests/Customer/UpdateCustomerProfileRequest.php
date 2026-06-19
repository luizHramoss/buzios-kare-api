<?php

namespace App\Http\Requests\Customer;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCustomerProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // já filtrado pelo middleware auth.role:customer
    }

    public function rules(): array
    {
        return [
            'name'     => ['sometimes', 'string', 'max:150'],
            'phone'    => ['sometimes', 'string', 'max:20'],
            'whatsapp' => ['sometimes', 'nullable', 'string', 'max:20'],
            'notes'    => ['sometimes', 'nullable', 'string', 'max:2000'],
        ];
    }
}
