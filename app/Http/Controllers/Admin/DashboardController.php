<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\CarWash;
use App\Models\Payment;
use App\Models\User;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        return view('admin.dashboard', [
            'summary' => [
                'car_washes' => CarWash::query()->count(),
                'pending_car_washes' => CarWash::query()->where('status', 'pending')->count(),
                'users' => User::query()->count(),
                'bookings_today' => Booking::query()->whereDate('created_at', today())->count(),
                'revenue_today' => Payment::query()->where('status', 'paid')->whereDate('paid_at', today())->sum('amount'),
            ],
        ]);
    }
}
