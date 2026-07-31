<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\CarWashStatus;
use App\Http\Controllers\Controller;
use App\Models\CarWash;
use App\Models\CarWashService;
use App\Models\ServiceVehiclePrice;
use App\Models\VehicleType;
use App\Support\PersianDate;
use Carbon\CarbonImmutable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PublicCarWashController extends Controller
{
    public function show(CarWash $carWash): JsonResponse
    {
        abort_unless($carWash->status === CarWashStatus::ACTIVE, 404);

        $services = $carWash->services()
            ->where('is_active', true)
            ->with([
                'vehiclePrices' => fn ($query) => $query
                    ->where('is_active', true)
                    ->with('vehicleType'),
            ])
            ->orderBy('sort_order')
            ->get()
            ->map(fn (CarWashService $service): array => [
                'id' => $service->id,
                'name' => $service->name,
                'slug' => $service->slug,
                'description' => $service->description,
                'base_price' => (int) $service->base_price,
                'default_duration_minutes' => (int) $service->default_duration_minutes,
                'vehicle_prices' => $service->vehiclePrices->map(
                    fn (ServiceVehiclePrice $price): array => [
                        'vehicle_type_id' => $price->vehicle_type_id,
                        'vehicle_type' => [
                            'id' => $price->vehicleType?->id,
                            'name' => $price->vehicleType?->name,
                            'slug' => $price->vehicleType?->slug,
                            'size_class' => $price->vehicleType?->size_class,
                        ],
                        'price' => (int) $price->price,
                        'duration_minutes' => (int) $price->duration_minutes,
                    ],
                )->values(),
            ]);

        return response()->json([
            'data' => [
                'id' => $carWash->public_id,
                'name' => $carWash->name,
                'slug' => $carWash->slug,
                'phone' => $carWash->phone,
                'mobile' => $carWash->mobile,
                'city' => $carWash->city,
                'province' => $carWash->province,
                'address' => $carWash->address,
                'description' => $carWash->description,
                'logo_url' => $carWash->logo ? asset('storage/'.$carWash->logo) : null,
                'cover_image_url' => $carWash->cover_image ? asset('storage/'.$carWash->cover_image) : null,
                'location' => [
                    'latitude' => $carWash->latitude !== null ? (float) $carWash->latitude : null,
                    'longitude' => $carWash->longitude !== null ? (float) $carWash->longitude : null,
                ],
                'timezone' => $carWash->timezone,
                'currency_code' => $carWash->currency_code,
                'booking_policy' => [
                    'minimum_notice_minutes' => (int) ($carWash->setting?->minimum_booking_notice_minutes ?? 0),
                    'maximum_days_ahead' => (int) ($carWash->setting?->maximum_booking_days_ahead ?? 30),
                    'cancellation_deadline_minutes' => (int) ($carWash->setting?->cancellation_deadline_minutes ?? 120),
                    'auto_confirm' => (bool) ($carWash->setting?->auto_confirm_booking ?? true),
                    'online_payment_required' => (bool) ($carWash->setting?->require_online_payment ?? false),
                ],
                'vehicle_types' => VehicleType::query()
                    ->where('is_active', true)
                    ->orderBy('sort_order')
                    ->get(['id', 'name', 'slug', 'size_class']),
                'services' => $services,
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

        $timezone = $carWash->timezone ?: 'Asia/Tehran';
        $fromLocal = CarbonImmutable::parse($validated['from'], $timezone)->startOfDay();
        $toLocal = CarbonImmutable::parse($validated['to'], $timezone)->endOfDay();
        $maximumDaysAhead = (int) ($carWash->setting?->maximum_booking_days_ahead ?? 30);

        if ($toLocal->greaterThan(now($timezone)->addDays($maximumDaysAhead)->endOfDay())) {
            abort(422, 'بازه درخواستی از حداکثر روزهای قابل رزرو بیشتر است.');
        }

        if ($fromLocal->diffInDays($toLocal) > 62) {
            abort(422, 'بازه جست‌وجوی ظرفیت نمی‌تواند بیشتر از ۶۲ روز باشد.');
        }

        $minimumNotice = (int) ($carWash->setting?->minimum_booking_notice_minutes ?? 0);
        $slots = $carWash->bookingSlots()
            ->whereBetween('starts_at', [
                $fromLocal->setTimezone('UTC'),
                $toLocal->setTimezone('UTC'),
            ])
            ->where('starts_at', '>=', now()->addMinutes($minimumNotice))
            ->where('status', 'open')
            ->whereColumn('reserved_count', '<', 'capacity')
            ->orderBy('starts_at')
            ->get()
            ->map(function ($slot) use ($timezone): array {
                $startsAt = $slot->starts_at->timezone($timezone);
                $endsAt = $slot->ends_at->timezone($timezone);

                return [
                    'id' => $slot->id,
                    'date' => $startsAt->toDateString(),
                    'persian_date' => PersianDate::date($startsAt, $timezone),
                    'persian_date_label' => PersianDate::human($startsAt, $timezone),
                    'weekday' => PersianDate::weekday($startsAt, $timezone),
                    'starts_at' => $slot->starts_at->toIso8601String(),
                    'ends_at' => $slot->ends_at->toIso8601String(),
                    'local_start_time' => $startsAt->format('H:i'),
                    'local_end_time' => $endsAt->format('H:i'),
                    'capacity' => (int) $slot->capacity,
                    'reserved_count' => (int) $slot->reserved_count,
                    'remaining_capacity' => max(0, (int) $slot->capacity - (int) $slot->reserved_count),
                    'status' => $slot->status,
                ];
            });

        return response()->json([
            'data' => $slots,
            'meta' => [
                'timezone' => $timezone,
                'from' => $fromLocal->toDateString(),
                'to' => $toLocal->toDateString(),
                'persian_from' => PersianDate::date($fromLocal, $timezone),
                'persian_to' => PersianDate::date($toLocal, $timezone),
            ],
        ]);
    }
}
