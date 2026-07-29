<?php

namespace Tests\Feature\Booking;

use App\Actions\Bookings\CreateBookingAction;
use App\Models\BookingSlot;
use App\Models\CarWash;
use App\Models\CarWashService;
use App\Models\VehicleType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class BookingCapacityTest extends TestCase
{
    use RefreshDatabase;

    public function test_full_slot_rejects_new_booking(): void
    {
        $carWash = CarWash::factory()->create(['status' => 'active']);
        $vehicleType = VehicleType::query()->create(['name' => 'سواری', 'slug' => 'sedan', 'is_active' => true]);
        $service = CarWashService::factory()->create(['car_wash_id' => $carWash->id, 'is_active' => true, 'base_price' => 100000]);
        $slot = BookingSlot::query()->create([
            'car_wash_id' => $carWash->id,
            'starts_at' => now()->addDay(),
            'ends_at' => now()->addDay()->addHour(),
            'capacity' => 1,
            'reserved_count' => 1,
            'status' => 'full',
            'source' => 'rule',
        ]);

        $this->expectException(ValidationException::class);

        app(CreateBookingAction::class)->execute([
            'booking_slot_id' => $slot->id,
            'vehicle_type_id' => $vehicleType->id,
            'service_ids' => [$service->id],
            'customer_name' => 'کاربر تست',
            'customer_mobile' => '989121234567',
        ]);
    }
}
