<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\CarWash;
use App\Models\Payment;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function index(Request $request): View
    {
        $data = $request->validate([
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date', 'after_or_equal:from'],
        ]);

        $from = isset($data['from'])
            ? CarbonImmutable::parse($data['from'])->startOfDay()
            : CarbonImmutable::now()->startOfMonth();

        $to = isset($data['to'])
            ? CarbonImmutable::parse($data['to'])->endOfDay()
            : CarbonImmutable::now()->endOfMonth();

        $bookingQuery = Booking::query()->whereBetween('created_at', [$from, $to]);
        $paymentQuery = Payment::query()
            ->where('status', 'paid')
            ->whereBetween('paid_at', [$from, $to]);

        $summary = [
            'bookings' => (clone $bookingQuery)->count(),
            'completed' => (clone $bookingQuery)->where('status', 'completed')->count(),
            'cancelled' => (clone $bookingQuery)->whereIn('status', ['cancelled', 'rejected'])->count(),
            'revenue' => (clone $paymentQuery)->sum('amount'),
            'new_users' => User::query()->whereBetween('created_at', [$from, $to])->count(),
            'new_car_washes' => CarWash::query()->whereBetween('created_at', [$from, $to])->count(),
        ];

        $daily = Booking::query()
            ->whereBetween('created_at', [$from, $to])
            ->selectRaw('DATE(created_at) as day, COUNT(*) as bookings')
            ->groupBy('day')
            ->orderBy('day')
            ->get();

        $topCarWashes = Booking::query()
            ->join('car_washes', 'car_washes.id', '=', 'bookings.car_wash_id')
            ->whereBetween('bookings.created_at', [$from, $to])
            ->select([
                'car_washes.id',
                'car_washes.name',
                DB::raw('COUNT(bookings.id) as bookings_count'),
                DB::raw("SUM(CASE WHEN bookings.status = 'completed' THEN bookings.payable_amount ELSE 0 END) as revenue"),
            ])
            ->groupBy('car_washes.id', 'car_washes.name')
            ->orderByDesc('bookings_count')
            ->limit(10)
            ->get();

        return view('admin.reports.index', compact(
            'from',
            'to',
            'summary',
            'daily',
            'topCarWashes',
        ));
    }
}
