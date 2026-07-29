<?php

namespace App\Http\Requests\CarWashPanel;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCapacityRuleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('carwash.schedule.manage') ?? false;
    }

    public function rules(): array
    {
        return [
            'weekday' => ['required', 'integer', 'between:0,6'],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['required', 'date_format:H:i', 'after:start_time'],
            'slot_duration_minutes' => ['required', Rule::in([15, 30, 45, 60, 90, 120])],
            'capacity' => ['required', 'integer', 'min:1', 'max:100'],
            'valid_from' => ['nullable', 'date'],
            'valid_until' => ['nullable', 'date', 'after_or_equal:valid_from'],
            'is_active' => ['required', 'boolean'],
        ];
    }
}
