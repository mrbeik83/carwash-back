<?php

namespace Database\Factories;

use App\Models\CarWash;
use Illuminate\Database\Eloquent\Factories\Factory;

class BookingSlotFactory extends Factory
{
    public function definition(): array
    {
        $start = now()->addDay()->startOfHour();
        return [
            'car_wash_id' => CarWash::factory(),
            'starts_at' => $start,
            'ends_at' => $start->copy()->addHour(),
            'capacity' => 2,
            'reserved_count' => 0,
            'status' => 'open',
            'source' => 'rule',
        ];
    }
}
