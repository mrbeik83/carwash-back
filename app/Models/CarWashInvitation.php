<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CarWashInvitation extends Model
{
    protected $fillable = [
        'car_wash_id',
        'mobile',
        'email',
        'role_name',
        'token_hash',
        'invited_by',
        'expires_at',
        'accepted_by',
        'accepted_at',
        'cancelled_at',
    ];

    protected $hidden = ['token_hash'];

    protected function casts(): array
    {
        return [
            'expires_at' => 'datetime',
            'accepted_at' => 'datetime',
            'cancelled_at' => 'datetime',
        ];
    }

    public function carWash(): BelongsTo
    {
        return $this->belongsTo(CarWash::class);
    }

    public function inviter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'invited_by');
    }

    public function acceptedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'accepted_by');
    }

    public function isUsable(): bool
    {
        return $this->accepted_at === null
            && $this->cancelled_at === null
            && $this->expires_at->isFuture();
    }
}
