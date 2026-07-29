<?php

namespace Database\Factories;

use App\Enums\CarWashStatus;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class CarWashFactory extends Factory
{
    public function definition(): array
    {
        $name = 'کارواش '.fake()->unique()->company();
        return [
            'public_id' => (string) Str::ulid(),
            'name' => $name,
            'slug' => Str::slug(fake()->unique()->company()).'-'.Str::lower(Str::random(4)),
            'code' => 'CW-'.Str::upper(Str::random(8)),
            'status' => CarWashStatus::ACTIVE,
            'city' => 'تهران',
            'timezone' => 'Asia/Tehran',
            'currency_code' => 'IRR',
        ];
    }
}
