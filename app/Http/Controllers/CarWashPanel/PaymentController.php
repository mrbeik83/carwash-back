<?php

namespace App\Http\Controllers\CarWashPanel;

use App\Enums\BookingStatus;
use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\CarWash;
use App\Models\Payment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Throwable;

class PaymentController extends Controller
{
    public function index(CarWash $carWash): View
    {
        $payments = Payment::query()
            ->whereHas(
                'booking',
                fn ($query) => $query->where('car_wash_id', $carWash->getKey()),
            )
            ->with('booking')
            ->latest()
            ->paginate(30);

        return view('carwash.payments.index', compact('carWash', 'payments'));
    }

    /**
     * @throws Throwable
     */
    public function store(
        Request $request,
        CarWash $carWash,
        Booking $booking,
    ): RedirectResponse {
        abort_unless($booking->car_wash_id === $carWash->getKey(), 404);

        $data = $request->validate([
            'method' => ['required', Rule::enum(PaymentMethod::class)],
            'amount' => ['required', 'integer', 'min:1'],
            'reference_id' => ['nullable', 'string', 'max:150'],
        ]);

        DB::transaction(function () use ($request, $booking, $data): void {
            /** @var Booking $lockedBooking */
            $lockedBooking = Booking::query()
                ->whereKey($booking->getKey())
                ->lockForUpdate()
                ->firstOrFail();

            if (in_array($lockedBooking->status, [
                BookingStatus::CANCELLED,
                BookingStatus::REJECTED,
            ], true)) {
                throw ValidationException::withMessages([
                    'amount' => 'برای رزرو لغوشده یا ردشده امکان ثبت پرداخت وجود ندارد.',
                ]);
            }

            $paid = (int) $lockedBooking->payments()
                ->where('status', PaymentStatus::PAID->value)
                ->sum('amount');

            $remaining = max(0, (int) $lockedBooking->payable_amount - $paid);

            if ((int) $data['amount'] > $remaining) {
                throw ValidationException::withMessages([
                    'amount' => "مبلغ واردشده از مانده قابل پرداخت ({$remaining} ریال) بیشتر است.",
                ]);
            }

            $lockedBooking->payments()->create([
                'user_id' => $request->user()->getKey(),
                'amount' => (int) $data['amount'],
                'currency_code' => $lockedBooking->currency_code,
                'method' => PaymentMethod::from($data['method']),
                'status' => PaymentStatus::PAID,
                'reference_id' => $data['reference_id'] ?? null,
                'transaction_id' => (string) Str::ulid(),
                'paid_at' => now(),
            ]);

            $newPaid = $paid + (int) $data['amount'];

            $lockedBooking->update([
                'payment_status' => $newPaid >= $lockedBooking->payable_amount
                    ? 'paid'
                    : 'partial',
            ]);
        });

        return back()->with('success', 'پرداخت ثبت شد.');
    }
}
