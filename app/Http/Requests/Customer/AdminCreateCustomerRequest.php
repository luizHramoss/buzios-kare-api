<?php

namespace App\Http\Requests\Customer;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class AdminCreateCustomerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'       => ['required', 'string', 'max:150'],
            'cpf'        => ['required', 'string', 'max:14', 'unique:customers,cpf'],
            'birth_date' => ['required', 'date', 'before:today'],
            'email'      => ['required', 'string', 'email', 'max:150', 'unique:customers,email'],
            'phone'      => ['required', 'string', 'max:20'],
            'whatsapp'   => ['nullable', 'string', 'max:20'],
            'notes'      => ['nullable', 'string', 'max:2000'],
            'password'   => ['required', Password::min(8)->mixedCase()->numbers()],
            'status'     => ['sometimes', 'in:active,inactive,blocked'],
        ];
    }
}
