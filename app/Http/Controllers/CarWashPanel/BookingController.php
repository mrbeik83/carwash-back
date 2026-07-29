<?php

namespace App\Http\Controllers\CarWashPanel;

use App\Actions\Bookings\CreateBookingAction;
use App\Enums\BookingSource;
use App\Enums\BookingStatus;
use App\Enums\PermissionName;
use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\CarWash;
use App\Services\BookingLifecycleService;
use Carbon\CarbonImmutable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class BookingController extends Controller
{
    public function index(Request $request, CarWash $carWash): View
    {
        $bookings = $carWash->bookings()
            ->with(['slot', 'items', 'customer'])
            ->when(
                $request->filled('status'),
                fn ($query) => $query->where('status', $request->string('status')->toString()),
            )
            ->when($request->filled('date'), function ($query) use ($request, $carWash): void {
                $day = CarbonImmutable::parse(
                    $request->string('date')->toString(),
                    $carWash->timezone,
                );

                $query->whereHas(
                    'slot',
                    fn ($slotQuery) => $slotQuery->whereBetween('starts_at', [
                        $day->startOfDay()->setTimezone('UTC'),
                        $day->endOfDay()->setTimezone('UTC'),
                    ]),
                );
            })
            ->latest()
            ->paginate(30)
            ->withQueryString();

        return view('carwash.bookings.index', compact('carWash', 'bookings'));
    }

    public function show(CarWash $carWash, Booking $booking): View
    {
        abort_unless($booking->car_wash_id === $carWash->getKey(), 404);

        $this->authorize('view', $booking);

        return view('carwash.bookings.show', [
            'carWash' => $carWash,
            'booking' => $booking->load([
                'items',
                'slot',
                'payments',
                'statusHistory',
            ]),
        ]);
    }

    public function store(
        Request $request,
        CarWash $carWash,
        CreateBookingAction $action,
    ): RedirectResponse {
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
            ->with('success', 'رزرو ثبت شد.');
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

        $service->transition(
            $booking,
            $status,
            $request->user(),
            $data['note'] ?? null,
        );

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
}
