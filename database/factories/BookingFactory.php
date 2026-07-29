<?php

namespace Database\Factories;

use App\Enums\BookingStatus;
use App\Models\BookingSlot;
use App\Models\CarWash;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class BookingFactory extends Factory
{
    public function definition(): array
    {
        return [
            'public_id' => (string) Str::ulid(),
            'tracking_code' => 'BK-'.Str::upper(Str::random(10)),
            'car_wash_id' => CarWash::factory(),
            'booking_slot_id' => static function (array $attributes): int {
                return BookingSlot::factory()->create([
                    'car_wash_id' => $attributes['car_wash_id'],
                ])->getKey();
            },
            'status' => BookingStatus::PENDING,
            'payment_status' => 'unpaid',
            'source' => 'web',
            'customer_name' => fake()->name(),
            'customer_mobile' => '989'.fake()->numerify('#########'),
            'subtotal_amount' => 500000,
            'discount_amount' => 0,
            'payable_amount' => 500000,
            'currency_code' => 'IRR',
        ];
    }
}
