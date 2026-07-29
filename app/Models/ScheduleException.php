<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ScheduleException extends Model
{
    protected $fillable = [
        'car_wash_id',
        'exception_date',
        'start_time',
        'end_time',
        'is_closed',
        'capacity_override',
        'reason',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'exception_date' => 'date',
            'is_closed' => 'boolean',
        ];
    }

    public function carWash(): BelongsTo
    {
        return $this->belongsTo(CarWash::class);
    }
}
