<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class AdminUpdateAdminRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $uuid = $this->route('uuid');
        $admin = $uuid ? \App\Models\Admin::whereUuid($uuid)->first() : null;

        return [
            'name'     => ['sometimes', 'string', 'max:150'],
            'email'    => ['sometimes', 'string', 'email', 'max:150', Rule::unique('admins', 'email')->ignore($admin)],
            'phone'    => ['sometimes', 'nullable', 'string', 'max:20'],
            'password' => ['sometimes', Password::min(8)->mixedCase()->numbers()],
            'role'     => ['sometimes', 'in:super_admin,admin'],
            'status'   => ['sometimes', 'in:active,inactive'],
        ];
    }
}
