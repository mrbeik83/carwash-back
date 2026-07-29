<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class StoreVehicleRequest extends FormRequest
{
    public function authorize(): bool { return $this->user() !== null; }

    public function rules(): array
    {
        return [
            'vehicle_type_id' => ['required', 'exists:vehicle_types,id'],
            'plate_number' => ['nullable', 'string', 'max:30'],
            'brand' => ['nullable', 'string', 'max:100'],
            'model' => ['nullable', 'string', 'max:100'],
            'color' => ['nullable', 'string', 'max:50'],
            'production_year' => ['nullable', 'integer', 'between:1300,2200'],
            'nickname' => ['nullable', 'string', 'max:100'],
            'is_default' => ['required', 'boolean'],
        ];
    }
}
