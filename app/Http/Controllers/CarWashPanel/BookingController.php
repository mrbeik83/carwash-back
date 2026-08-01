<?php

namespace App\Http\Controllers\CarWashPanel;

use App\Actions\Bookings\CreateBookingAction;
use App\Enums\BookingSource;
use App\Enums\BookingStatus;
use App\Enums\BookingPaymentStatus;
use App\Enums\PaymentStatus;
use App\Enums\PermissionName;
use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\CarWash;
use App\Models\VehicleType;
use App\Services\BookingLifecycleService;
use App\Support\PersianDate;
use Carbon\CarbonImmutable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Throwable;

class BookingController extends Controller
{
    public function index(Request $request, CarWash $carWash): View
    {
        $timezone = $carWash->timezone ?: 'Asia/Tehran';
        $today = CarbonImmutable::now($timezone)->startOfDay();
        $selectedDate = $this->parseDate(
            $request->string('date')->toString(),
            $today,
            $timezone,
        );
        $weekReference = $this->parseDate(
            $request->string('week')->toString(),
            $selectedDate,
            $timezone,
        );
        $weekStart = $this->weekStart($weekReference);
        $weekEnd = $weekStart->addDays(6)->endOfDay();

        $dayStartUtc = $selectedDate->startOfDay()->utc();
        $dayEndUtc = $selectedDate->endOfDay()->utc();
        $weekStartUtc = $weekStart->startOfDay()->utc();
        $weekEndUtc = $weekEnd->endOfDay()->utc();

        $selectedStatus = BookingStatus::tryFrom($request->string('status')->toString());
        $selectedSlotId = $request->filled('slot_id') ? $request->integer('slot_id') : null;
        $searchTerm = trim($this->toEnglishDigits($request->string('q')->toString()));

        $dailySlots = $carWash->bookingSlots()
            ->whereBetween('starts_at', [$dayStartUtc, $dayEndUtc])
            ->orderBy('starts_at')
            ->get();

        $dayBookings = $carWash->bookings()
            ->with(['slot', 'items', 'customer', 'payments'])
            ->whereHas('slot', fn ($query) => $query->whereBetween('starts_at', [$dayStartUtc, $dayEndUtc]))
            ->get();

        $filteredBookingsQuery = $carWash->bookings()
            ->with(['slot', 'items', 'customer'])
            ->whereHas('slot', fn ($slotQuery) => $slotQuery->whereBetween('starts_at', [$dayStartUtc, $dayEndUtc]))
            ->when(
                $selectedStatus,
                fn ($query) => $query->where('status', $selectedStatus->value),
            )
            ->when(
                $selectedSlotId,
                fn ($query) => $query->where('booking_slot_id', $selectedSlotId),
            )
            ->when($searchTerm !== '', function ($query) use ($searchTerm): void {
                $term = '%'.$searchTerm.'%';
                $query->where(fn ($inner) => $inner
                    ->where('tracking_code', 'like', $term)
                    ->orWhere('customer_name', 'like', $term)
                    ->orWhere('customer_mobile', 'like', $term)
                    ->orWhere('vehicle_plate_snapshot', 'like', $term));
            });

        $filteredBookings = (clone $filteredBookingsQuery)
            ->get()
            ->sortBy(fn (Booking $booking) => $booking->slot?->starts_at?->getTimestamp() ?? PHP_INT_MAX)
            ->values();

        $bookings = (clone $filteredBookingsQuery)
            ->join('booking_slots', 'bookings.booking_slot_id', '=', 'booking_slots.id')
            ->select('bookings.*')
            ->orderBy('booking_slots.starts_at')
            ->orderBy('bookings.created_at')
            ->paginate(30)
            ->withQueryString();

        $weekBookings = $carWash->bookings()
            ->with('slot:id,starts_at')
            ->whereHas('slot', fn ($query) => $query->whereBetween('starts_at', [$weekStartUtc, $weekEndUtc]))
            ->get();

        $countsByDate = $weekBookings
            ->filter->slot
            ->countBy(fn (Booking $booking): string => $booking->slot->starts_at->timezone($timezone)->toDateString());

        $weekDays = collect(range(0, 6))->map(function (int $offset) use ($weekStart, $countsByDate, $selectedDate, $timezone): array {
            $date = $weekStart->addDays($offset);

            return [
                'date' => $date,
                'date_key' => $date->toDateString(),
                'weekday' => PersianDate::weekday($date, $timezone),
                'persian_date' => PersianDate::short($date, $timezone),
                'count' => (int) $countsByDate->get($date->toDateString(), 0),
                'is_selected' => $date->isSameDay($selectedDate),
                'is_today' => $date->isSameDay($today),
            ];
        });

        $filterSlots = $dailySlots;

        $availableSlots = $carWash->bookingSlots()
            ->where('status', 'open')
            ->where('starts_at', '>=', now('UTC')->addMinutes((int) ($carWash->setting?->minimum_booking_notice_minutes ?? 0)))
            ->whereColumn('reserved_count', '<', 'capacity')
            ->orderBy('starts_at')
            ->limit(250)
            ->get();

        $statusCounts = $dayBookings->countBy(
            fn (Booking $booking): string => $booking->status->value,
        );
        $filteredStatusCounts = $filteredBookings->countBy(
            fn (Booking $booking): string => $booking->status->value,
        );

        $activeStatuses = [
            BookingStatus::PENDING,
            BookingStatus::CONFIRMED,
            BookingStatus::CHECKED_IN,
            BookingStatus::IN_PROGRESS,
        ];
        $terminalStatuses = [
            BookingStatus::CANCELLED,
            BookingStatus::NO_SHOW,
            BookingStatus::REJECTED,
        ];

        $openCapacity = (int) $dailySlots
            ->reject(fn ($slot) => $slot->status === 'closed')
            ->sum('capacity');
        $reservedCapacity = (int) $dailySlots->sum('reserved_count');
        $occupancyRate = $openCapacity > 0
            ? min(100, (int) round(($reservedCapacity / $openCapacity) * 100))
            : 0;

        $summary = [
            'total' => $dayBookings->count(),
            'active' => $dayBookings->filter(fn (Booking $booking) => in_array($booking->status, $activeStatuses, true))->count(),
            'waiting' => (int) $statusCounts->get(BookingStatus::PENDING->value, 0)
                + (int) $statusCounts->get(BookingStatus::CONFIRMED->value, 0),
            'in_service' => (int) $statusCounts->get(BookingStatus::CHECKED_IN->value, 0)
                + (int) $statusCounts->get(BookingStatus::IN_PROGRESS->value, 0),
            'completed' => (int) $statusCounts->get(BookingStatus::COMPLETED->value, 0),
            'cancelled' => $dayBookings->filter(fn (Booking $booking) => in_array($booking->status, $terminalStatuses, true))->count(),
            'paid_amount' => (int) $dayBookings
                ->flatMap->payments
                ->filter(fn ($payment) => $payment->status === PaymentStatus::PAID)
                ->sum('amount'),
            'open_capacity' => $openCapacity,
            'reserved_capacity' => $reservedCapacity,
            'remaining_capacity' => max(0, $openCapacity - $reservedCapacity),
            'occupancy_rate' => $occupancyRate,
            'slot_count' => $dailySlots->count(),
        ];

        $nowUtc = CarbonImmutable::now('UTC');
        $currentSlotId = $selectedDate->isSameDay($today)
            ? $dailySlots->first(fn ($slot) => $slot->starts_at->lte($nowUtc) && $slot->ends_at->gt($nowUtc))?->getKey()
            : null;

        return view('carwash.bookings.index', [
            'carWash' => $carWash,
            'bookings' => $bookings,
            'filteredBookings' => $filteredBookings,
            'timelineBookingsBySlot' => $filteredBookings->groupBy('booking_slot_id'),
            'dailySlots' => $dailySlots,
            'slots' => $availableSlots,
            'filterSlots' => $filterSlots,
            'vehicleTypes' => VehicleType::query()->where('is_active', true)->orderBy('sort_order')->get(),
            'services' => $carWash->services()
                ->where('is_active', true)
                ->with('vehiclePrices')
                ->orderBy('sort_order')
                ->get(),
            'selectedDate' => $selectedDate,
            'weekStart' => $weekStart,
            'weekEnd' => $weekEnd,
            'weekDays' => $weekDays,
            'summary' => $summary,
            'filteredStatusCounts' => $filteredStatusCounts,
            'currentSlotId' => $currentSlotId,
            'hasActiveFilters' => $selectedStatus !== null || $selectedSlotId !== null || $searchTerm !== '',
        ]);
    }

    public function show(CarWash $carWash, Booking $booking): View
    {
        abort_unless($booking->car_wash_id === $carWash->getKey(), 404);
        $this->authorize('view', $booking);

        return view('carwash.bookings.show', [
            'carWash' => $carWash,
            'booking' => $booking->load(['items', 'slot', 'payments', 'statusHistory']),
        ]);
    }

    public function store(Request $request, CarWash $carWash, CreateBookingAction $action): RedirectResponse
    {
        $request->merge([
            'customer_mobile' => $this->normalizeIranianMobile($request->string('customer_mobile')->toString()),
            'vehicle_plate' => trim($request->string('vehicle_plate')->toString()),
        ]);

        $data = $request->validate([
            'booking_slot_id' => ['required', 'integer', 'exists:booking_slots,id'],
            'vehicle_type_id' => ['required', 'integer', 'exists:vehicle_types,id'],
            'vehicle_plate' => ['nullable', 'string', 'max:30'],
            'customer_name' => ['required', 'string', 'max:150'],
            'customer_mobile' => ['required', 'regex:/^09\d{9}$/'],
            'customer_note' => ['nullable', 'string', 'max:1000'],
            'service_ids' => ['required', 'array', 'min:1'],
            'service_ids.*' => ['required', 'integer', 'distinct'],
        ], [
            'customer_mobile.regex' => 'شماره موبایل باید با ۰۹ شروع شود و ۱۱ رقم باشد.',
            'service_ids.required' => 'حداقل یک خدمت را انتخاب کنید.',
            'service_ids.min' => 'حداقل یک خدمت را انتخاب کنید.',
        ]);

        $booking = $action->execute([
            ...$data,
            'expected_car_wash_id' => $carWash->getKey(),
            'source' => BookingSource::PANEL,
            'created_by' => $request->user()->getKey(),
        ]);

        return redirect()
            ->route('carwash.bookings.show', [$carWash, $booking])
            ->with('success', 'رزرو با موفقیت ثبت شد.');
    }

    public function transition(
        Request $request,
        CarWash $carWash,
        Booking $booking,
        BookingLifecycleService $service,
    ): RedirectResponse {
        abort_unless($booking->car_wash_id === $carWash->getKey(), 404);

        $data = $request->validate([
            'status' => ['required', Rule::enum(BookingStatus::class)],
            'note' => ['nullable', 'string', 'max:1000'],
        ]);

        $status = BookingStatus::from($data['status']);
        $permission = $this->permissionForTransition($status);
        abort_unless($request->user()->can($permission->value), 403);

        $service->transition($booking, $status, $request->user(), $data['note'] ?? null);

        return back()->with('success', 'وضعیت رزرو تغییر کرد.');
    }

    private function permissionForTransition(BookingStatus $status): PermissionName
    {
        return match ($status) {
            BookingStatus::CONFIRMED => PermissionName::CAR_WASH_BOOKINGS_CONFIRM,
            BookingStatus::CHECKED_IN => PermissionName::CAR_WASH_BOOKINGS_CHECK_IN,
            BookingStatus::IN_PROGRESS => PermissionName::CAR_WASH_BOOKINGS_START,
            BookingStatus::COMPLETED => PermissionName::CAR_WASH_BOOKINGS_COMPLETE,
            BookingStatus::CANCELLED => PermissionName::CAR_WASH_BOOKINGS_CANCEL,
            BookingStatus::NO_SHOW => PermissionName::CAR_WASH_BOOKINGS_NO_SHOW,
            BookingStatus::REJECTED,
            BookingStatus::PENDING => PermissionName::CAR_WASH_BOOKINGS_UPDATE,
        };
    }

    private function parseDate(string $value, CarbonImmutable $fallback, string $timezone): CarbonImmutable
    {
        if ($value === '' || ! preg_match('/^\d{4}-\d{2}-\d{2}$/', $value)) {
            return $fallback->timezone($timezone)->startOfDay();
        }

        try {
            $date = CarbonImmutable::createFromFormat('!Y-m-d', $value, $timezone);

            if (! $date || $date->format('Y-m-d') !== $value) {
                return $fallback->timezone($timezone)->startOfDay();
            }

            return $date->startOfDay();
        } catch (Throwable) {
            return $fallback->timezone($timezone)->startOfDay();
        }
    }

    private function weekStart(CarbonImmutable $date): CarbonImmutable
    {
        $cursor = $date->startOfDay();

        while ($cursor->dayOfWeek !== CarbonImmutable::SATURDAY) {
            $cursor = $cursor->subDay();
        }

        return $cursor;
    }

    private function normalizeIranianMobile(string $mobile): string
    {
        $mobile = preg_replace('/[^0-9+]/', '', $this->toEnglishDigits($mobile)) ?? '';

        if (str_starts_with($mobile, '+98')) {
            return '0'.substr($mobile, 3);
        }

        if (str_starts_with($mobile, '0098')) {
            return '0'.substr($mobile, 4);
        }

        if (str_starts_with($mobile, '98') && strlen($mobile) === 12) {
            return '0'.substr($mobile, 2);
        }

        return $mobile;
    }

    private function toEnglishDigits(string $value): string
    {
        return strtr($value, [
            '۰' => '0', '۱' => '1', '۲' => '2', '۳' => '3', '۴' => '4',
            '۵' => '5', '۶' => '6', '۷' => '7', '۸' => '8', '۹' => '9',
            '٠' => '0', '١' => '1', '٢' => '2', '٣' => '3', '٤' => '4',
            '٥' => '5', '٦' => '6', '٧' => '7', '٨' => '8', '٩' => '9',
        ]);
    }
}
