<?php

namespace App\Http\Requests\CarWashPanel;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateSettingsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('carwash.settings.update') ?? false;
    }

    public function rules(): array
    {
        return [
            'booking_interval_minutes' => ['required', Rule::in([15, 30, 45, 60, 90, 120])],
            'minimum_booking_notice_minutes' => ['required', 'integer', 'min:0', 'max:10080'],
            'maximum_booking_days_ahead' => ['required', 'integer', 'min:1', 'max:365'],
            'cancellation_deadline_minutes' => ['required', 'integer', 'min:0', 'max:10080'],
            'default_capacity' => ['required', 'integer', 'min:1', 'max:100'],
            'auto_confirm_booking' => ['required', 'boolean'],
            'allow_guest_booking' => ['required', 'boolean'],
            'require_online_payment' => ['required', 'boolean'],
            'send_sms_notifications' => ['required', 'boolean'],
        ];
    }
}
