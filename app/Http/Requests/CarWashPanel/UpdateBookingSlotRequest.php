<?php

namespace App\Http\Requests\CarWashPanel;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateBookingSlotRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('carwash.schedule.manage') ?? false;
    }

    public function rules(): array
    {
        return [
            'capacity' => ['required', 'integer', 'min:1', 'max:100'],
            'status' => ['required', Rule::in(['open', 'closed'])],
        ];
    }
}
