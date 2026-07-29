<?php

namespace App\Models;

use App\Enums\QrLinkType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class QrLink extends Model
{
    protected $fillable = [
        'car_wash_id',
        'token',
        'type',
        'title',
        'campaign',
        'is_active',
        'expires_at',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'type' => QrLinkType::class,
            'is_active' => 'boolean',
            'expires_at' => 'datetime',
        ];
    }

    public function carWash(): BelongsTo
    {
        return $this->belongsTo(CarWash::class);
    }

    public function scans(): HasMany
    {
        return $this->hasMany(QrScan::class);
    }
}
