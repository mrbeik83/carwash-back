<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\CarWash;
use App\Models\Payment;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $start = CarbonImmutable::now()->subDays(6)->startOfDay();

        $dailyBookings = Booking::query()
            ->where('created_at', '>=', $start)
            ->selectRaw('DATE(created_at) as day, COUNT(*) as total')
            ->groupBy('day')
            ->orderBy('day')
            ->get();

        return view('admin.dashboard', [
            'summary' => [
                'car_washes' => CarWash::query()->count(),
                'pending_car_washes' => CarWash::query()->where('status', 'pending')->count(),
                'users' => User::query()->count(),
                'bookings_today' => Booking::query()->whereDate('created_at', today())->count(),
                'revenue_today' => Payment::query()
                    ->where('status', 'paid')
                    ->whereDate('paid_at', today())
                    ->sum('amount'),
            ],
            'recentCarWashes' => CarWash::query()
                ->withCount('members')
                ->latest()
                ->limit(6)
                ->get(),
            'recentBookings' => Booking::query()
                ->with(['carWash', 'slot'])
                ->latest()
                ->limit(8)
                ->get(),
            'chart' => [
                'labels' => $dailyBookings->pluck('day')->values(),
                'values' => $dailyBookings->pluck('total')->values(),
            ],
        ]);
    }
}
