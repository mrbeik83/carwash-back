<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CapacityRule extends Model
{
    protected $fillable = [
        'car_wash_id',
        'weekday',
        'start_time',
        'end_time',
        'slot_duration_minutes',
        'capacity',
        'valid_from',
        'valid_until',
        'is_active',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'valid_from' => 'date',
            'valid_until' => 'date',
            'is_active' => 'boolean',
        ];
    }

    public function carWash(): BelongsTo
    {
        return $this->belongsTo(CarWash::class);
    }
}
