<?php

namespace App\Http\Requests\CarWashPanel;

use Illuminate\Foundation\Http\FormRequest;

class StoreScheduleExceptionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('carwash.schedule.manage') ?? false;
    }

    public function rules(): array
    {
        return [
            'exception_date' => ['required', 'date'],
            'start_time' => [
                'nullable',
                'date_format:H:i',
                'required_if:is_closed,0',
            ],
            'end_time' => [
                'nullable',
                'date_format:H:i',
                'after:start_time',
                'required_if:is_closed,0',
            ],
            'is_closed' => ['required', 'boolean'],
            'capacity_override' => [
                'nullable',
                'integer',
                'min:1',
                'max:100',
                'required_if:is_closed,0',
            ],
            'reason' => ['nullable', 'string', 'max:500'],
        ];
    }
}
