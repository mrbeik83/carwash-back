<?php

namespace Database\Seeders;

use App\Enums\UserStatus;
use App\Models\User;
use Illuminate\Database\Seeder;
use RuntimeException;

class SuperAdminSeeder extends Seeder
{
    public function run(): void
    {
        $mobile = config('carwash.super_admin.mobile');
        $password = config('carwash.super_admin.password');
        $email = config('carwash.super_admin.email');

        if (blank($mobile) || blank($password)) {
            throw new RuntimeException(
                'SUPER_ADMIN_MOBILE and SUPER_ADMIN_PASSWORD must be configured in .env.'
            );
        }

        $user = User::query()->firstOrNew([
            'mobile' => $mobile,
        ]);

        $user->forceFill([
            'full_name' => config('carwash.super_admin.name', 'مدیر کل سیستم'),
            'email' => $email,
            // User model has the "hashed" cast, so plaintext is hashed exactly once.
            'password' => $password,
            'status' => UserStatus::ACTIVE,
            'is_super_admin' => true,
            'mobile_verified_at' => now(),
            'email_verified_at' => $email ? now() : null,
        ]);

        $user->save();

        $this->command?->info("Super Admin created successfully: {$user->mobile}");
    }
}
