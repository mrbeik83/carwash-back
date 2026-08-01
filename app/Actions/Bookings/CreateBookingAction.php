<?php

namespace App\Actions\Bookings;

use App\Enums\BookingPaymentStatus;
use App\Enums\BookingSource;
use App\Enums\BookingStatus;
use App\Enums\CarWashStatus;
use App\Models\Booking;
use App\Models\BookingSlot;
use App\Models\CarWashService;
use App\Models\QrLink;
use App\Models\UserVehicle;
use App\Models\VehicleType;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Throwable;

class CreateBookingAction
{
    /**
     * @throws Throwable
     */
    public function execute(array $data): Booking
    {
        return DB::transaction(function () use ($data): Booking {
            /** @var BookingSlot $slot */
            $slot = BookingSlot::query()
                ->with(['carWash.setting'])
                ->whereKey($data['booking_slot_id'])
                ->lockForUpdate()
                ->firstOrFail();

            if (
                isset($data['expected_car_wash_id'])
                && (int) $data['expected_car_wash_id'] !== (int) $slot->car_wash_id
            ) {
                throw ValidationException::withMessages([
                    'booking_slot_id' => 'این بازه متعلق به کارواش انتخاب‌شده نیست.',
                ]);
            }

            if ($slot->carWash->status !== CarWashStatus::ACTIVE) {
                throw ValidationException::withMessages([
                    'booking_slot_id' => 'کارواش انتخاب‌شده در حال حاضر فعال نیست.',
                ]);
            }

            $setting = $slot->carWash->setting;
            $minimumNotice = (int) ($setting?->minimum_booking_notice_minutes ?? 0);
            $maximumDaysAhead = (int) ($setting?->maximum_booking_days_ahead ?? 30);

            if ($slot->starts_at->lessThan(now()->addMinutes($minimumNotice))) {
                throw ValidationException::withMessages([
                    'booking_slot_id' => 'این بازه داخل حداقل زمان مجاز قبل از مراجعه قرار دارد.',
                ]);
            }

            if ($slot->starts_at->greaterThan(now()->addDays($maximumDaysAhead)->endOfDay())) {
                throw ValidationException::withMessages([
                    'booking_slot_id' => 'این بازه خارج از محدوده زمانی قابل رزرو است.',
                ]);
            }

            if (! $slot->hasCapacity()) {
                throw ValidationException::withMessages([
                    'booking_slot_id' => 'ظرفیت این بازه تکمیل شده است.',
                ]);
            }

            $customerUserId = isset($data['customer_user_id'])
                ? (int) $data['customer_user_id']
                : null;

            $vehicle = null;
            $vehicleType = null;

            if (! empty($data['vehicle_id'])) {
                if (! $customerUserId) {
                    throw ValidationException::withMessages([
                        'vehicle_id' => 'برای انتخاب خودروی ذخیره‌شده، ورود کاربر الزامی است.',
                    ]);
                }

                $vehicle = UserVehicle::query()
                    ->with('vehicleType')
                    ->whereKey($data['vehicle_id'])
                    ->where('user_id', $customerUserId)
                    ->first();

                if (! $vehicle) {
                    throw ValidationException::withMessages([
                        'vehicle_id' => 'خودروی انتخاب‌شده متعلق به این کاربر نیست.',
                    ]);
                }

                $vehicleType = $vehicle->vehicleType;
            } else {
                $vehicleType = VehicleType::query()
                    ->whereKey($data['vehicle_type_id'] ?? null)
                    ->where('is_active', true)
                    ->first();
            }

            if (! $vehicleType) {
                throw ValidationException::withMessages([
                    'vehicle_type_id' => 'نوع خودروی انتخاب‌شده معتبر نیست.',
                ]);
            }

            $serviceIds = array_values(array_unique(array_map(
                'intval',
                $data['service_ids'] ?? [],
            )));

            if ($serviceIds === []) {
                throw ValidationException::withMessages([
                    'service_ids' => 'حداقل یک خدمت باید انتخاب شود.',
                ]);
            }

            $services = CarWashService::query()
                ->where('car_wash_id', $slot->car_wash_id)
                ->where('is_active', true)
                ->whereIn('id', $serviceIds)
                ->with([
                    'vehiclePrices' => fn ($query) => $query
                        ->where('vehicle_type_id', $vehicleType->getKey())
                        ->where('is_active', true),
                ])
                ->get();

            if ($services->count() !== count($serviceIds)) {
                throw ValidationException::withMessages([
                    'service_ids' => 'یک یا چند خدمت انتخاب‌شده متعلق به این کارواش نیست یا غیرفعال شده است.',
                ]);
            }

            $items = [];
            $subtotal = 0;

            foreach ($services as $service) {
                $vehiclePrice = $service->vehiclePrices->first();
                $unitPrice = (int) ($vehiclePrice?->price ?? $service->base_price);
                $duration = (int) ($vehiclePrice?->duration_minutes ?? $service->default_duration_minutes);

                $items[] = [
                    'service_id' => $service->getKey(),
                    'service_name' => $service->name,
                    'quantity' => 1,
                    'unit_price' => $unitPrice,
                    'duration_minutes' => $duration,
                    'discount_amount' => 0,
                    'total_amount' => $unitPrice,
                ];

                $subtotal += $unitPrice;
            }

            $qrLink = null;

            if (! empty($data['qr_token'])) {
                $qrLink = QrLink::query()
                    ->where('token', $data['qr_token'])
                    ->where('car_wash_id', $slot->car_wash_id)
                    ->where('is_active', true)
                    ->where(fn ($query) => $query
                        ->whereNull('expires_at')
                        ->orWhere('expires_at', '>', now()))
                    ->first();

                if (! $qrLink) {
                    throw ValidationException::withMessages([
                        'qr_token' => 'لینک QR معتبر نیست یا منقضی شده است.',
                    ]);
                }
            }

            $source = $qrLink
                ? BookingSource::QR
                : ($data['source'] instanceof BookingSource
                    ? $data['source']
                    : (BookingSource::tryFrom((string) ($data['source'] ?? 'web')) ?? BookingSource::WEB));

            $autoConfirm = (bool) ($setting?->auto_confirm_booking ?? true);
            $status = $autoConfirm ? BookingStatus::CONFIRMED : BookingStatus::PENDING;

            $booking = Booking::query()->create([
                'tracking_code' => 'BK-'.Str::ulid(),
                'car_wash_id' => $slot->car_wash_id,
                'customer_user_id' => $customerUserId,
                'vehicle_id' => $vehicle?->getKey(),
                'booking_slot_id' => $slot->getKey(),
                'status' => $status,
                'payment_status' => BookingPaymentStatus::UNPAID,
                'source' => $source,
                'customer_name' => $data['customer_name'],
                'customer_mobile' => $data['customer_mobile'],
                'vehicle_plate_snapshot' => $vehicle?->plate_number ?? ($data['vehicle_plate'] ?? null),
                'vehicle_type_snapshot' => $vehicleType->name,
                'subtotal_amount' => $subtotal,
                'discount_amount' => 0,
                'payable_amount' => $subtotal,
                'currency_code' => $slot->carWash->currency_code,
                'customer_note' => $data['customer_note'] ?? null,
                'internal_note' => $data['internal_note'] ?? null,
                'confirmed_at' => $status === BookingStatus::CONFIRMED ? now() : null,
                'created_by' => $data['created_by'] ?? null,
            ]);

            $booking->items()->createMany($items);

            $newReservedCount = $slot->reserved_count + 1;

            $slot->update([
                'reserved_count' => $newReservedCount,
                'status' => $newReservedCount >= $slot->capacity ? 'full' : 'open',
            ]);

            $booking->statusHistory()->create([
                'from_status' => null,
                'to_status' => $status->value,
                'changed_by' => $data['created_by'] ?? null,
                'note' => 'رزرو ایجاد شد.',
                'created_at' => now(),
            ]);

            if ($qrLink) {
                $qrLink->scans()->create([
                    'user_id' => $customerUserId,
                    'ip_address' => $data['request_ip'] ?? null,
                    'user_agent' => $data['user_agent'] ?? null,
                    'referrer' => isset($data['referrer'])
                        ? Str::limit((string) $data['referrer'], 255, '')
                        : null,
                    'scanned_at' => now(),
                ]);
            }

            return $booking->load([
                'carWash',
                'slot',
                'items',
                'payments',
                'statusHistory',
            ]);
        });
    }
}
