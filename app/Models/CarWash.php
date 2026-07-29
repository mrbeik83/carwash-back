<?php

namespace App\Models;

use App\Enums\CarWashStatus;
use App\Models\Concerns\HasPublicUlid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class CarWash extends Model
{
    use HasFactory;
    use HasPublicUlid;
    use SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'code',
        'status',
        'phone',
        'mobile',
        'email',
        'province',
        'city',
        'address',
        'postal_code',
        'latitude',
        'longitude',
        'timezone',
        'currency_code',
        'description',
        'logo',
        'cover_image',
        'approved_by',
        'approved_at',
        'suspended_at',
        'suspension_reason',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'status' => CarWashStatus::class,
            'latitude' => 'decimal:7',
            'longitude' => 'decimal:7',
            'approved_at' => 'datetime',
            'suspended_at' => 'datetime',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function members(): BelongsToMany
    {
        return $this->belongsToMany(User::class)
            ->using(CarWashMembership::class)
            ->withPivot([
                'id',
                'status',
                'job_title',
                'invited_by',
                'invited_at',
                'joined_at',
                'suspended_at',
            ])
            ->withTimestamps();
    }

    public function activeMembers(): BelongsToMany
    {
        return $this->members()->wherePivot('status', 'active');
    }

    public function invitations(): HasMany
    {
        return $this->hasMany(CarWashInvitation::class);
    }

    public function setting(): HasOne
    {
        return $this->hasOne(CarWashSetting::class);
    }

    public function services(): HasMany
    {
        return $this->hasMany(CarWashService::class);
    }

    public function capacityRules(): HasMany
    {
        return $this->hasMany(CapacityRule::class);
    }

    public function scheduleExceptions(): HasMany
    {
        return $this->hasMany(ScheduleException::class);
    }

    public function bookingSlots(): HasMany
    {
        return $this->hasMany(BookingSlot::class);
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    public function qrLinks(): HasMany
    {
        return $this->hasMany(QrLink::class);
    }
}
