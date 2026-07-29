<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CarWashSetting extends Model
{
    protected $fillable = [
        'car_wash_id',
        'booking_interval_minutes',
        'minimum_booking_notice_minutes',
        'maximum_booking_days_ahead',
        'cancellation_deadline_minutes',
        'default_capacity',
        'auto_confirm_booking',
        'allow_guest_booking',
        'require_online_payment',
        'send_sms_notifications',
        'extra',
    ];

    protected function casts(): array
    {
        return [
            'auto_confirm_booking' => 'boolean',
            'allow_guest_booking' => 'boolean',
            'require_online_payment' => 'boolean',
            'send_sms_notifications' => 'boolean',
            'extra' => 'array',
        ];
    }

    public function carWash(): BelongsTo
    {
        return $this->belongsTo(CarWash::class);
    }
}
