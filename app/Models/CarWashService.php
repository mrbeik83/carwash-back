<?php

namespace App\Models;

use App\Models\Concerns\HasPublicUlid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class CarWashService extends Model
{
    use HasPublicUlid;
    use SoftDeletes;

    protected $fillable = [
        'car_wash_id',
        'name',
        'slug',
        'description',
        'base_price',
        'default_duration_minutes',
        'is_active',
        'is_featured',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
        ];
    }

    public function carWash(): BelongsTo
    {
        return $this->belongsTo(CarWash::class);
    }

    public function vehiclePrices(): HasMany
    {
        return $this->hasMany(ServiceVehiclePrice::class, 'service_id');
    }
}
