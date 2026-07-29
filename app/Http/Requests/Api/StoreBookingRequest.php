<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class StoreBookingRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'booking_slot_id' => ['required', 'exists:booking_slots,id'],
            'vehicle_id' => ['nullable', 'exists:user_vehicles,id'],
            'vehicle_type_id' => ['required_without:vehicle_id', 'exists:vehicle_types,id'],
            'vehicle_plate' => ['nullable', 'string', 'max:30'],
            'customer_name' => ['required', 'string', 'max:150'],
            'customer_mobile' => ['required', 'string', 'max:20'],
            'customer_note' => ['nullable', 'string', 'max:1000'],
            'qr_token' => ['nullable', 'string', 'max:64'],
            'service_ids' => ['required', 'array', 'min:1'],
            'service_ids.*' => ['integer', 'distinct', 'exists:car_wash_services,id'],
        ];
    }
}
