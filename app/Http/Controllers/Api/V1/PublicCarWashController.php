<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\CarWashStatus;
use App\Http\Controllers\Controller;
use App\Models\CarWash;
use Carbon\CarbonImmutable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PublicCarWashController extends Controller
{
    public function show(CarWash $carWash): JsonResponse
    {
        abort_unless($carWash->status === CarWashStatus::ACTIVE, 404);

        return response()->json([
            'data' => [
                'id' => $carWash->public_id,
                'name' => $carWash->name,
                'slug' => $carWash->slug,
                'city' => $carWash->city,
                'address' => $carWash->address,
                'location' => [
                    'latitude' => $carWash->latitude,
                    'longitude' => $carWash->longitude,
                ],
                'services' => $carWash->services()
                    ->where('is_active', true)
                    ->with([
                        'vehiclePrices' => fn ($query) => $query
                            ->where('is_active', true)
                            ->with('vehicleType'),
                    ])
                    ->orderBy('sort_order')
                    ->get(),
            ],
        ]);
    }

    public function availability(Request $request, CarWash $carWash): JsonResponse
    {
        abort_unless($carWash->status === CarWashStatus::ACTIVE, 404);

        $validated = $request->validate([
            'from' => ['required', 'date'],
            'to' => ['required', 'date', 'after_or_equal:from'],
        ]);

        $timezone = $carWash->timezone;
        $fromLocal = CarbonImmutable::parse($validated['from'], $timezone)->startOfDay();
        $toLocal = CarbonImmutable::parse($validated['to'], $timezone)->endOfDay();
        $maximumDaysAhead = (int) ($carWash->setting?->maximum_booking_days_ahead ?? 30);

        if ($toLocal->greaterThan(now($timezone)->addDays($maximumDaysAhead)->endOfDay())) {
            abort(422, 'بازه درخواستی از حداکثر روزهای قابل رزرو بیشتر است.');
        }

        if ($fromLocal->diffInDays($toLocal) > 62) {
            abort(422, 'بازه جست‌وجوی ظرفیت نمی‌تواند بیشتر از ۶۲ روز باشد.');
        }

        $from = $fromLocal->setTimezone('UTC');
        $to = $toLocal->setTimezone('UTC');
        $minimumNotice = (int) ($carWash->setting?->minimum_booking_notice_minutes ?? 0);

        $slots = $carWash->bookingSlots()
            ->whereBetween('starts_at', [$from, $to])
            ->where('starts_at', '>=', now()->addMinutes($minimumNotice))
            ->where('status', 'open')
            ->whereColumn('reserved_count', '<', 'capacity')
            ->orderBy('starts_at')
            ->get();

        return response()->json(['data' => $slots]);
    }
}
