<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BookingSlot extends Model
{
    protected $fillable = [
        'car_wash_id',
        'starts_at',
        'ends_at',
        'capacity',
        'reserved_count',
        'status',
        'source',
    ];

    protected function casts(): array
    {
        return [
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
        ];
    }

    public function carWash(): BelongsTo
    {
        return $this->belongsTo(CarWash::class);
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    public function hasCapacity(): bool
    {
        return $this->status === 'open' && $this->reserved_count < $this->capacity;
    }
}
