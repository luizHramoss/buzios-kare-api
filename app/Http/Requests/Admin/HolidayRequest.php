<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class HolidayRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'date'      => ['required', 'date'],
            'name'      => ['required', 'string', 'max:100'],
            'recurring' => ['sometimes', 'boolean'],
        ];
    }
}
