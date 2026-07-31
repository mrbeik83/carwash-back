<?php

namespace App\Services;

use App\Contracts\SmsSender;
use App\Enums\OtpPurpose;
use App\Models\OtpCode;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;

class OtpService
{
    public function __construct(
        private readonly SmsSender $smsSender,
    ) {
    }

    public function request(string $mobile, OtpPurpose $purpose, ?string $ip = null): void
    {
        $key = "otp:{$purpose->value}:{$mobile}";

        if (RateLimiter::tooManyAttempts($key, 3)) {
            throw ValidationException::withMessages([
                'mobile' => 'تعداد درخواست بیش از حد مجاز است. کمی بعد دوباره تلاش کنید.',
            ]);
        }

        RateLimiter::hit($key, 10 * 60);

        OtpCode::query()
            ->where('mobile', $mobile)
            ->where('purpose', $purpose->value)
            ->whereNull('consumed_at')
            ->update(['consumed_at' => now()]);

        if(env('APP_ENV') == 'local'){
            $code = '123456'; 
        }else{
            $code = (string) random_int(100000, 999999);
        }

        OtpCode::query()->create([
            'mobile' => $mobile,
            'purpose' => $purpose,
            'code_hash' => hash('sha256', $code),
            'expires_at' => now()->addMinutes(config('carwash.otp_expires_minutes', 2)),
            'request_ip' => $ip,
        ]);

        $this->smsSender->send($mobile, "کد ورود کارواش: {$code}");
    }

    public function verify(string $mobile, string $code, OtpPurpose $purpose): bool
    {
        $otp = OtpCode::query()
            ->where('mobile', $mobile)
            ->where('purpose', $purpose->value)
            ->whereNull('consumed_at')
            ->latest('id')
            ->first();

        if (! $otp || ! $otp->isUsable()) {
            return false;
        }

        $otp->increment('attempts');

        if (! hash_equals($otp->code_hash, hash('sha256', $code))) {
            return false;
        }

        $otp->update(['consumed_at' => now()]);

        RateLimiter::clear("otp:{$purpose->value}:{$mobile}");

        return true;
    }
}
