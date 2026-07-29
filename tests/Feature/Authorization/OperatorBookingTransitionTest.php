<?php

namespace Tests\Feature\Authorization;

use App\Enums\BookingStatus;
use App\Enums\RoleName;
use App\Models\Booking;
use App\Models\CarWash;
use App\Models\User;
use App\Services\CarWashMemberService;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OperatorBookingTransitionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesAndPermissionsSeeder::class);
    }

    public function test_operator_can_check_in_a_booking_without_generic_update_permission(): void
    {
        $operator = User::factory()->create();
        $carWash = CarWash::factory()->create(['status' => 'active']);

        app(CarWashMemberService::class)->addOrUpdate(
            $carWash,
            $operator,
            RoleName::CAR_WASH_OPERATOR,
        );

        $booking = Booking::factory()->create([
            'car_wash_id' => $carWash->getKey(),
            'status' => BookingStatus::CONFIRMED,
        ]);

        $this->actingAs($operator)
            ->post(route('carwash.bookings.transition', [$carWash, $booking]), [
                'status' => BookingStatus::CHECKED_IN->value,
            ])
            ->assertRedirect();

        $this->assertSame(
            BookingStatus::CHECKED_IN,
            $booking->fresh()->status,
        );
    }
}
