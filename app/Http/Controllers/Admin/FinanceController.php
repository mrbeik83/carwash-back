<?php

namespace App\Http\Controllers\Admin;

use App\Enums\PaymentStatus;
use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class FinanceController extends Controller
{
    public function index(Request $request): View
    {
        $request->validate([
            'status' => ['nullable', Rule::enum(PaymentStatus::class)],
            'q' => ['nullable', 'string', 'max:100'],
        ]);

        $base = Payment::query();

        $summary = [
            'paid_total' => (clone $base)->where('status', PaymentStatus::PAID->value)->sum('amount'),
            'paid_today' => (clone $base)->where('status', PaymentStatus::PAID->value)->whereDate('paid_at', today())->sum('amount'),
            'pending' => (clone $base)->whereIn('status', [
                PaymentStatus::PENDING->value,
                PaymentStatus::PROCESSING->value,
            ])->count(),
            'refunded' => (clone $base)->sum('refunded_amount'),
        ];

        $payments = Payment::query()
            ->with(['booking.carWash', 'user'])
            ->when($request->filled('status'), fn ($query) =>
                $query->where('status', $request->string('status')->toString()))
            ->when($request->filled('q'), function ($query) use ($request): void {
                $term = '%'.$request->string('q')->toString().'%';
                $query->where(fn ($inner) => $inner
                    ->where('reference_id', 'like', $term)
                    ->orWhere('transaction_id', 'like', $term)
                    ->orWhereHas('booking', fn ($booking) =>
                        $booking->where('tracking_code', 'like', $term)));
            })
            ->latest()
            ->paginate(30)
            ->withQueryString();

        return view('admin.finance.index', compact('summary', 'payments'));
    }
}
