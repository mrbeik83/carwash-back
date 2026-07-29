<?php

return [
    'frontend_url' => env('FRONTEND_URL', 'http://localhost:3000'),
    'slot_generation_days' => (int) env('CARWASH_SLOT_GENERATION_DAYS', 45),
    'otp_expires_minutes' => (int) env('CARWASH_OTP_EXPIRES_MINUTES', 2),

    'super_admin' => [
        'name' => env('SUPER_ADMIN_NAME', 'مدیر کل سیستم'),
        'mobile' => env('SUPER_ADMIN_MOBILE'),
        'email' => env('SUPER_ADMIN_EMAIL'),
        'password' => env('SUPER_ADMIN_PASSWORD'),
    ],
];
