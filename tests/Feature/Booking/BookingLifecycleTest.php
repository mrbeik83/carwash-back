<?php

namespace Tests\Feature\Booking;

use App\Enums\BookingStatus;
use App\Models\Booking;
use App\Services\BookingLifecycleService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class BookingLifecycleTest extends TestCase
{
    use RefreshDatabase;

    public function test_completed_booking_cannot_return_to_pending(): void
    {
        $booking = Booking::factory()->create(['status' => BookingStatus::COMPLETED]);
        $this->expectException(ValidationException::class);
        app(BookingLifecycleService::class)->transition($booking, BookingStatus::PENDING);
    }
}
