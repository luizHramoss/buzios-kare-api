<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class AdminCreateAdminRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // policy aplicada no Service (apenas super_admin)
    }

    public function rules(): array
    {
        return [
            'name'     => ['required', 'string', 'max:150'],
            'email'    => ['required', 'string', 'email', 'max:150', 'unique:admins,email'],
            'phone'    => ['nullable', 'string', 'max:20'],
            'password' => ['required', Password::min(8)->mixedCase()->numbers()],
            'role'     => ['required', 'in:super_admin,admin'],
            'status'   => ['sometimes', 'in:active,inactive'],
        ];
    }
}
