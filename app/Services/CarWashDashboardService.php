<?php

namespace App\Services;

use App\Models\CarWash;
use Illuminate\Support\Facades\DB;

class CarWashDashboardService
{
    public function summary(CarWash $carWash): array
    {
        $todayStart = now($carWash->timezone)->startOfDay()->setTimezone('UTC');
        $todayEnd = now($carWash->timezone)->endOfDay()->setTimezone('UTC');

        $base = $carWash->bookings();

        return [
            'today_bookings' => (clone $base)->whereBetween('created_at', [$todayStart, $todayEnd])->count(),
            'today_visits' => (clone $base)->whereHas('slot', fn ($q) => $q->whereBetween('starts_at', [$todayStart, $todayEnd]))->count(),
            'waiting' => (clone $base)->whereIn('status', ['confirmed', 'checked_in'])->count(),
            'in_progress' => (clone $base)->where('status', 'in_progress')->count(),
            'completed_today' => (clone $base)->whereBetween('completed_at', [$todayStart, $todayEnd])->count(),
            'today_revenue' => (clone $base)
                ->where('status', 'completed')
                ->whereBetween('completed_at', [$todayStart, $todayEnd])
                ->sum('payable_amount'),
            'next_bookings' => (clone $base)
                ->with(['slot', 'items'])
                ->whereIn('status', ['pending', 'confirmed'])
                ->whereHas('slot', fn ($q) => $q->where('starts_at', '>=', now()))
                ->orderBy(
                    DB::table('booking_slots')
                        ->select('starts_at')
                        ->whereColumn('booking_slots.id', 'bookings.booking_slot_id')
                        ->limit(1)
                )
                ->limit(10)
                ->get(),
        ];
    }
}
