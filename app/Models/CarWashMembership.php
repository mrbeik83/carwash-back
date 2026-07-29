<?php

namespace App\Models;

use App\Enums\MembershipStatus;
use Illuminate\Database\Eloquent\Relations\Pivot;

class CarWashMembership extends Pivot
{
    protected $table = 'car_wash_user';

    public $incrementing = true;

    protected $fillable = [
        'car_wash_id',
        'user_id',
        'status',
        'job_title',
        'invited_by',
        'invited_at',
        'joined_at',
        'suspended_at',
    ];

    protected function casts(): array
    {
        return [
            'status' => MembershipStatus::class,
            'invited_at' => 'datetime',
            'joined_at' => 'datetime',
            'suspended_at' => 'datetime',
        ];
    }
}
