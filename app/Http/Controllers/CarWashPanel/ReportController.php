<?php

namespace App\Http\Controllers\CarWashPanel;

use App\Http\Controllers\Controller;
use App\Models\CarWash;
use Carbon\CarbonImmutable;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function index(Request $request, CarWash $carWash): View
    {
        $request->validate([
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date', 'after_or_equal:from'],
        ]);

        $timezone = $carWash->timezone;
        $nowLocal = CarbonImmutable::now($timezone);
        $from = $request->filled('from')
            ? CarbonImmutable::parse($request->string('from')->toString(), $timezone)
            : $nowLocal->startOfMonth();

        $to = $request->filled('to')
            ? CarbonImmutable::parse($request->string('to')->toString(), $timezone)
            : $nowLocal->endOfMonth();

        abort_if($from->isAfter($to), 422, 'تاریخ شروع نمی‌تواند بعد از تاریخ پایان باشد.');

        $query = $carWash->bookings()
            ->whereBetween('created_at', [
                $from->startOfDay()->setTimezone('UTC'),
                $to->endOfDay()->setTimezone('UTC'),
            ]);

        $summary = [
            'total_bookings' => (clone $query)->count(),
            'completed' => (clone $query)->where('status', 'completed')->count(),
            'cancelled' => (clone $query)->where('status', 'cancelled')->count(),
            'no_show' => (clone $query)->where('status', 'no_show')->count(),
            'revenue' => (clone $query)
                ->where('status', 'completed')
                ->sum('payable_amount'),
        ];

        $daily = (clone $query)
            ->selectRaw(
                'DATE(created_at) as day, COUNT(*) as bookings, SUM(CASE WHEN status = ? THEN payable_amount ELSE 0 END) as revenue',
                ['completed'],
            )
            ->groupBy('day')
            ->orderBy('day')
            ->get();

        return view(
            'carwash.reports.index',
            compact('carWash', 'summary', 'daily', 'from', 'to'),
        );
    }
}
