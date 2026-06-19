<?php

namespace App\Http\Requests\Admin;

use App\Enums\BlockType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BlockedScheduleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'date'       => ['required', 'date', 'after_or_equal:today'],
            'type'       => ['required', Rule::in(array_map(fn ($t) => $t->value, BlockType::cases()))],
            'start_time' => ['required_unless:type,' . BlockType::FULL_DAY->value, 'date_format:H:i'],
            'end_time'   => ['required_unless:type,' . BlockType::FULL_DAY->value, 'date_format:H:i', 'after:start_time'],
            'reason'     => ['nullable', 'string', 'max:255'],
        ];
    }
}
