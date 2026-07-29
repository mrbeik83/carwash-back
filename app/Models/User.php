<?php

namespace App\Models;

use App\Enums\UserStatus;
use App\Models\Concerns\HasPublicUlid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens;
    use HasFactory;
    use HasPublicUlid;
    use HasRoles;
    use Notifiable;
    use SoftDeletes;

    protected $guard_name = 'web';

    protected $fillable = [
        'full_name',
        'mobile',
        'email',
        'password',
        'status',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'status' => UserStatus::class,
            'is_super_admin' => 'boolean',
            'mobile_verified_at' => 'datetime',
            'email_verified_at' => 'datetime',
            'last_login_at' => 'datetime',
        ];
    }

    public function carWashes(): BelongsToMany
    {
        return $this->belongsToMany(CarWash::class)
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

    public function activeCarWashes(): BelongsToMany
    {
        return $this->carWashes()->wherePivot('status', 'active');
    }

    public function vehicles(): HasMany
    {
        return $this->hasMany(UserVehicle::class);
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class, 'customer_user_id');
    }
}
