<?php

namespace Database\Factories;

use App\Models\CarWash;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class CarWashServiceFactory extends Factory
{
    public function definition(): array
    {
        $name = fake()->randomElement(['شست‌وشوی بدنه','صفرشویی','واکس','موتورشویی']);
        return [
            'public_id' => (string) Str::ulid(),
            'car_wash_id' => CarWash::factory(),
            'name' => $name,
            'slug' => Str::slug($name).'-'.Str::lower(Str::random(4)),
            'base_price' => 500000,
            'default_duration_minutes' => 30,
            'is_active' => true,
            'is_featured' => false,
            'sort_order' => 0,
        ];
    }
}
