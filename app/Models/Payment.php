<?php

namespace App\Models;

use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Models\Concerns\HasPublicUlid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    use HasPublicUlid;

    protected $fillable = [
        'booking_id',
        'user_id',
        'amount',
        'currency_code',
        'method',
        'gateway',
        'status',
        'authority',
        'reference_id',
        'transaction_id',
        'paid_at',
        'failed_at',
        'refunded_amount',
        'refunded_at',
        'failure_reason',
        'gateway_response',
    ];

    protected function casts(): array
    {
        return [
            'method' => PaymentMethod::class,
            'status' => PaymentStatus::class,
            'paid_at' => 'datetime',
            'failed_at' => 'datetime',
            'refunded_at' => 'datetime',
            'gateway_response' => 'array',
        ];
    }

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
