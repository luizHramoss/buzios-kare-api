<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class RegisterCustomerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // rota pública de cadastro
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
            'password'   => ['required', 'confirmed', Password::min(8)->mixedCase()->numbers()],
        ];
    }

    public function messages(): array
    {
        return [
            'cpf.unique'   => 'Este CPF já está cadastrado.',
            'email.unique' => 'Este e-mail já está cadastrado.',
        ];
    }
}
