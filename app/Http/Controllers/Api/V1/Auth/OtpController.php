<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Enums\OtpPurpose;
use App\Enums\UserStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\RequestOtpRequest;
use App\Http\Requests\Api\VerifyOtpRequest;
use App\Models\User;
use App\Services\OtpService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class OtpController extends Controller
{
    public function request(RequestOtpRequest $request, OtpService $service): JsonResponse
    {
        $purpose = OtpPurpose::tryFrom(
            $request->string('purpose', 'login')->toString()
        ) ?? OtpPurpose::LOGIN;

        $service->request(
            $request->string('mobile')->toString(),
            $purpose,
            $request->ip(),
        );

        return response()->json(['message' => 'کد تایید ارسال شد.']);
    }

    public function verify(VerifyOtpRequest $request, OtpService $service): JsonResponse
    {
        $purpose = OtpPurpose::tryFrom(
            $request->string('purpose', 'login')->toString()
        ) ?? OtpPurpose::LOGIN;

        $mobile = $request->string('mobile')->toString();

        if (! $service->verify(
            $mobile,
            $request->string('code')->toString(),
            $purpose,
        )) {
            throw ValidationException::withMessages([
                'code' => 'کد تایید صحیح نیست یا منقضی شده است.',
            ]);
        }

        $user = User::query()->firstOrCreate(
            ['mobile' => $mobile],
            [
                'full_name' => $request->string('full_name')->toString() ?: null,
                'status' => UserStatus::ACTIVE,
                'mobile_verified_at' => now(),
            ],
        );

        if ($user->status !== UserStatus::ACTIVE) {
            throw ValidationException::withMessages([
                'mobile' => 'حساب کاربری شما غیرفعال است.',
            ]);
        }

        $updates = [
            'mobile_verified_at' => $user->mobile_verified_at ?? now(),
            'last_login_at' => now(),
            'last_login_ip' => $request->ip(),
        ];

        if (blank($user->full_name) && $request->filled('full_name')) {
            $updates['full_name'] = $request->string('full_name')->toString();
        }

        $user->forceFill($updates)->save();

        Auth::guard('web')->login($user, true);
        $request->session()->regenerate();

        return response()->json([
            'data' => [
                'id' => $user->public_id,
                'full_name' => $user->full_name,
                'mobile' => $user->mobile,
            ],
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()->json(['message' => 'خروج انجام شد.']);
    }
}
