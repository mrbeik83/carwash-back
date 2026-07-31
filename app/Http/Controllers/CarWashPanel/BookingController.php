<?php

namespace App\Http\Controllers\CarWashPanel;

use App\Actions\Bookings\CreateBookingAction;
use App\Enums\BookingSource;
use App\Enums\BookingStatus;
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

class BookingController extends Controller
{
    public function index(Request $request, CarWash $carWash): View
    {
        $timezone = $carWash->timezone ?: 'Asia/Tehran';
        $selectedDate = CarbonImmutable::parse(
            $request->string('date')->toString() ?: now($timezone)->toDateString(),
            $timezone,
        )->startOfDay();
        $weekStart = $this->weekStart(
            $request->string('week')->toString() ?: $selectedDate->toDateString(),
            $timezone,
        );
        $weekEnd = $weekStart->addDays(6)->endOfDay();

        $bookingsQuery = $carWash->bookings()
            ->with(['slot', 'items', 'customer'])
            ->whereHas('slot', fn ($slotQuery) => $slotQuery->whereBetween('starts_at', [
                $selectedDate->startOfDay()->setTimezone('UTC'),
                $selectedDate->endOfDay()->setTimezone('UTC'),
            ]))
            ->when(
                $request->filled('status'),
                fn ($query) => $query->where('status', $request->string('status')->toString()),
            )
            ->when(
                $request->filled('slot_id'),
                fn ($query) => $query->where('booking_slot_id', $request->integer('slot_id')),
            )
            ->when($request->filled('q'), function ($query) use ($request): void {
                $term = '%'.$request->string('q')->toString().'%';
                $query->where(fn ($inner) => $inner
                    ->where('tracking_code', 'like', $term)
                    ->orWhere('customer_name', 'like', $term)
                    ->orWhere('customer_mobile', 'like', $term)
                    ->orWhere('vehicle_plate_snapshot', 'like', $term));
            });

        $bookings = $bookingsQuery
            ->join('booking_slots', 'bookings.booking_slot_id', '=', 'booking_slots.id')
            ->select('bookings.*')
            ->orderBy('booking_slots.starts_at')
            ->paginate(30)
            ->withQueryString();

        $weekBookings = $carWash->bookings()
            ->with('slot:id,starts_at')
            ->whereHas('slot', fn ($query) => $query->whereBetween('starts_at', [
                $weekStart->setTimezone('UTC'),
                $weekEnd->setTimezone('UTC'),
            ]))
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
                'is_today' => $date->isToday(),
            ];
        });

        $filterSlots = $carWash->bookingSlots()
            ->whereBetween('starts_at', [
                $selectedDate->startOfDay()->setTimezone('UTC'),
                $selectedDate->endOfDay()->setTimezone('UTC'),
            ])
            ->orderBy('starts_at')
            ->get();

        $availableSlots = $carWash->bookingSlots()
            ->where('status', 'open')
            ->where('starts_at', '>=', now()->addMinutes((int) ($carWash->setting?->minimum_booking_notice_minutes ?? 0)))
            ->whereColumn('reserved_count', '<', 'capacity')
            ->orderBy('starts_at')
            ->limit(250)
            ->get();

        return view('carwash.bookings.index', [
            'carWash' => $carWash,
            'bookings' => $bookings,
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
        $data = $request->validate([
            'booking_slot_id' => ['required', 'exists:booking_slots,id'],
            'vehicle_type_id' => ['required', 'exists:vehicle_types,id'],
            'vehicle_plate' => ['nullable', 'string', 'max:30'],
            'customer_name' => ['required', 'string', 'max:150'],
            'customer_mobile' => ['required', 'string', 'max:20'],
            'customer_note' => ['nullable', 'string', 'max:1000'],
            'service_ids' => ['required', 'array', 'min:1'],
            'service_ids.*' => ['required', 'integer', 'distinct'],
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

    private function weekStart(string $date, string $timezone): CarbonImmutable
    {
        $cursor = CarbonImmutable::parse($date, $timezone)->startOfDay();
        while ($cursor->dayOfWeek !== CarbonImmutable::SATURDAY) {
            $cursor = $cursor->subDay();
        }

        return $cursor;
    }
}
