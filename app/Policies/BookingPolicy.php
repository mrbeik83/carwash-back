<?php

namespace App\Policies;

use App\Enums\PermissionName;
use App\Models\Booking;
use App\Models\User;
use App\Support\CurrentCarWash;

class BookingPolicy
{
    public function __construct(
        private readonly CurrentCarWash $currentCarWash,
    ) {
    }

    public function view(User $user, Booking $booking): bool
    {
        return $this->belongsToCurrentCarWash($booking)
            && $user->can(PermissionName::CAR_WASH_BOOKINGS_VIEW->value);
    }

    public function update(User $user, Booking $booking): bool
    {
        return $this->belongsToCurrentCarWash($booking)
            && $user->can(PermissionName::CAR_WASH_BOOKINGS_UPDATE->value);
    }

    public function cancel(User $user, Booking $booking): bool
    {
        return $this->belongsToCurrentCarWash($booking)
            && $user->can(PermissionName::CAR_WASH_BOOKINGS_CANCEL->value);
    }

    private function belongsToCurrentCarWash(Booking $booking): bool
    {
        return $this->currentCarWash->has()
            && $booking->car_wash_id === $this->currentCarWash->id();
    }
}
