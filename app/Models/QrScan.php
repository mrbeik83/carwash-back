<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QrScan extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'qr_link_id',
        'user_id',
        'ip_address',
        'user_agent',
        'referrer',
        'scanned_at',
    ];

    protected function casts(): array
    {
        return ['scanned_at' => 'datetime'];
    }

    public function qrLink(): BelongsTo
    {
        return $this->belongsTo(QrLink::class);
    }
}
