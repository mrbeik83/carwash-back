<?php

namespace App\Http\Requests\Admin;

use App\Enums\CarWashStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCarWashRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can(
            $this->route('carWash')
                ? 'platform.car-washes.update'
                : 'platform.car-washes.create'
        ) ?? false;
    }

    public function rules(): array
    {
        $carWash = $this->route('carWash');
        $isCreate = $carWash === null;

        return [
            'name' => ['required', 'string', 'max:150'],
            'slug' => [
                'nullable',
                'alpha_dash',
                'max:150',
                Rule::unique('car_washes', 'slug')->ignore($carWash?->getKey()),
            ],
            'code' => [
                'nullable',
                'string',
                'max:30',
                Rule::unique('car_washes', 'code')->ignore($carWash?->getKey()),
            ],
            'status' => [Rule::requiredIf($isCreate), 'nullable', Rule::enum(CarWashStatus::class)],
            'phone' => ['nullable', 'string', 'max:30'],
            'mobile' => ['nullable', 'string', 'max:20'],
            'email' => ['nullable', 'email', 'max:255'],
            'province' => ['nullable', 'string', 'max:100'],
            'city' => ['nullable', 'string', 'max:100'],
            'address' => ['nullable', 'string', 'max:1000'],
            'postal_code' => ['nullable', 'string', 'max:20'],
            'timezone' => ['required', 'timezone'],
            'owner_name' => [
                Rule::requiredIf($isCreate),
                'nullable',
                'string',
                'max:150',
            ],
            'owner_mobile' => [
                Rule::requiredIf($isCreate),
                'nullable',
                'string',
                'max:20',
            ],
        ];
    }
}
