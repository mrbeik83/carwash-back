<?php

namespace App\Services;

use App\Models\CarWash;
use Illuminate\Support\Facades\DB;

class CarWashDashboardService
{
    public function summary(CarWash $carWash): array
    {
        $timezone = $carWash->timezone ?: 'Asia/Tehran';
        $todayStart = now($timezone)->startOfDay()->setTimezone('UTC');
        $todayEnd = now($timezone)->endOfDay()->setTimezone('UTC');
        $base = $carWash->bookings();

        $todaySlots = $carWash->bookingSlots()
            ->whereBetween('starts_at', [$todayStart, $todayEnd])
            ->orderBy('starts_at')
            ->get();

        $totalCapacity = (int) $todaySlots->sum('capacity');
        $reservedCapacity = (int) $todaySlots->sum('reserved_count');

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
            'today_slots' => $todaySlots,
            'open_slots' => $todaySlots->where('status', 'open')->count(),
            'full_slots' => $todaySlots->where('status', 'full')->count(),
            'total_capacity' => $totalCapacity,
            'reserved_capacity' => $reservedCapacity,
            'fill_rate' => $totalCapacity > 0 ? round(($reservedCapacity / $totalCapacity) * 100) : 0,
            'next_available_slot' => $carWash->bookingSlots()
                ->where('starts_at', '>=', now())
                ->where('status', 'open')
                ->whereColumn('reserved_count', '<', 'capacity')
                ->orderBy('starts_at')
                ->first(),
            'next_bookings' => (clone $base)
                ->with(['slot', 'items'])
                ->whereIn('status', ['pending', 'confirmed', 'checked_in'])
                ->whereHas('slot', fn ($q) => $q->where('starts_at', '>=', now()->subHour()))
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
