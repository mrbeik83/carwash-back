<?php

namespace Database\Factories;

use App\Enums\UserStatus;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    public function definition(): array
    {
        return [
            'public_id' => (string) Str::ulid(),
            'full_name' => fake()->name(),
            'mobile' => '989'.fake()->unique()->numerify('#########'),
            'mobile_verified_at' => now(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
            'status' => UserStatus::ACTIVE,
            'is_super_admin' => false,
        ];
    }
}
