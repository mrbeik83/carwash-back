<?php

namespace App\Http\Requests\CarWashPanel;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreServiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        $permission = $this->route('service')
            ? 'carwash.services.update'
            : 'carwash.services.create';

        return $this->user()?->can($permission) ?? false;
    }

    public function rules(): array
    {
        $carWash = $this->route('carWash');
        $service = $this->route('service');

        return [
            'name' => ['required', 'string', 'max:150'],
            'slug' => [
                'required',
                'alpha_dash',
                'max:150',
                Rule::unique('car_wash_services', 'slug')
                    ->where('car_wash_id', $carWash->getKey())
                    ->ignore($service?->getKey()),
            ],
            'description' => ['nullable', 'string', 'max:3000'],
            'base_price' => ['required', 'integer', 'min:0'],
            'default_duration_minutes' => [
                'required',
                'integer',
                'min:5',
                'max:1440',
            ],
            'is_active' => ['required', 'boolean'],
            'is_featured' => ['required', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:9999'],
            'prices' => ['required', 'array', 'min:1'],
            'prices.*.vehicle_type_id' => [
                'required',
                'integer',
                'distinct',
                'exists:vehicle_types,id',
            ],
            'prices.*.price' => ['required', 'integer', 'min:0'],
            'prices.*.duration_minutes' => [
                'required',
                'integer',
                'min:5',
                'max:1440',
            ],
            'prices.*.is_active' => ['required', 'boolean'],
        ];
    }
}
