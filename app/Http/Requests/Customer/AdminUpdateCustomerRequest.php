<?php

namespace App\Http\Requests\Customer;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class AdminUpdateCustomerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $customerUuid = $this->route('uuid');
        $customer = $customerUuid ? \App\Models\Customer::whereUuid($customerUuid)->first() : null;

        return [
            'name'       => ['sometimes', 'string', 'max:150'],
            'cpf'        => ['sometimes', 'string', 'max:14', Rule::unique('customers', 'cpf')->ignore($customer)],
            'birth_date' => ['sometimes', 'date', 'before:today'],
            'email'      => ['sometimes', 'string', 'email', 'max:150', Rule::unique('customers', 'email')->ignore($customer)],
            'phone'      => ['sometimes', 'string', 'max:20'],
            'whatsapp'   => ['sometimes', 'nullable', 'string', 'max:20'],
            'notes'      => ['sometimes', 'nullable', 'string', 'max:2000'],
            'password'   => ['sometimes', Password::min(8)->mixedCase()->numbers()],
            'status'     => ['sometimes', 'in:active,inactive,blocked'],
        ];
    }
}
