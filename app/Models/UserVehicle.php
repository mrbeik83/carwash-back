<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserVehicle extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'vehicle_type_id',
        'plate_number',
        'plate_number_normalized',
        'brand',
        'model',
        'color',
        'production_year',
        'nickname',
        'is_default',
    ];

    protected function casts(): array
    {
        return ['is_default' => 'boolean'];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function vehicleType(): BelongsTo
    {
        return $this->belongsTo(VehicleType::class);
    }
}
