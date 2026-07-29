<?php

namespace App\Http\Controllers\Api\V1;

use App\Actions\Bookings\CreateBookingAction;
use App\Enums\BookingSource;
use App\Enums\BookingStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreBookingRequest;
use App\Models\Booking;
use App\Services\BookingLifecycleService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $bookings = $request->user()->bookings()
            ->with(['carWash', 'slot', 'items'])
            ->latest()
            ->paginate(20);

        return response()->json($bookings);
    }

    public function store(
        StoreBookingRequest $request,
        CreateBookingAction $action,
    ): JsonResponse {
        $user = $request->user();

        $booking = $action->execute([
            ...$request->validated(),
            'customer_name' => $user->full_name ?: $request->validated('customer_name'),
            'customer_mobile' => $user->mobile ?: $request->validated('customer_mobile'),
            'customer_user_id' => $user->getKey(),
            'created_by' => $request->user()->getKey(),
            'source' => BookingSource::WEB,
            'request_ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'referrer' => $request->headers->get('referer'),
        ]);

        return response()->json(['data' => $booking], 201);
    }

    public function show(Request $request, Booking $booking): JsonResponse
    {
        abort_unless($booking->customer_user_id === $request->user()->getKey(), 404);

        return response()->json([
            'data' => $booking->load([
                'carWash',
                'slot',
                'items',
                'payments',
                'statusHistory',
            ]),
        ]);
    }

    public function cancel(
        Request $request,
        Booking $booking,
        BookingLifecycleService $service,
    ): JsonResponse {
        abort_unless($booking->customer_user_id === $request->user()->getKey(), 404);

        $data = $request->validate([
            'reason' => ['nullable', 'string', 'max:1000'],
        ]);

        $deadline = $booking->carWash->setting?->cancellation_deadline_minutes ?? 120;

        if ($booking->slot->starts_at->copy()->subMinutes($deadline)->isPast()) {
            abort(422, 'مهلت لغو رزرو گذشته است.');
        }

        $updated = $service->transition(
            $booking,
            BookingStatus::CANCELLED,
            $request->user(),
            $data['reason'] ?? null,
        );

        return response()->json(['data' => $updated]);
    }
}
