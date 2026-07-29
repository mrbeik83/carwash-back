<?php

namespace App\Http\Controllers\Auth;

use App\Enums\OtpPurpose;
use App\Enums\UserStatus;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\OtpService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    public function create(): View
    {
        return view('auth.login');
    }

    public function store(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'mobile' => ['required', 'string', 'max:20'],
            'password' => ['required', 'string'],
        ]);

        $user = User::query()
            ->where('mobile', $credentials['mobile'])
            ->first();

        if (
            ! $user
            || $user->status !== UserStatus::ACTIVE
            || ! Auth::guard('web')->attempt([
                'mobile' => $credentials['mobile'],
                'password' => $credentials['password'],
            ], $request->boolean('remember'))
        ) {
            throw ValidationException::withMessages([
                'mobile' => 'شماره موبایل یا رمز عبور صحیح نیست.',
            ]);
        }

        $request->session()->regenerate();

        $user->forceFill([
            'last_login_at' => now(),
            'last_login_ip' => $request->ip(),
        ])->save();

        return redirect()->intended(route('account.home'));
    }

    public function requestOtp(Request $request, OtpService $service): RedirectResponse
    {
        $data = $request->validate([
            'mobile' => ['required', 'string', 'max:20'],
        ]);

        $service->request(
            $data['mobile'],
            OtpPurpose::LOGIN,
            $request->ip(),
        );

        return back()
            ->with('otp_mobile', $data['mobile'])
            ->with('success', 'کد ورود ارسال شد.');
    }

    public function verifyOtp(Request $request, OtpService $service): RedirectResponse
    {
        $data = $request->validate([
            'mobile' => ['required', 'string', 'max:20'],
            'code' => ['required', 'digits:6'],
        ]);

        if (! $service->verify($data['mobile'], $data['code'], OtpPurpose::LOGIN)) {
            throw ValidationException::withMessages([
                'code' => 'کد تایید صحیح نیست یا منقضی شده است.',
            ]);
        }

        $user = User::query()->firstOrCreate(
            ['mobile' => $data['mobile']],
            [
                'status' => UserStatus::ACTIVE,
                'mobile_verified_at' => now(),
            ],
        );

        if ($user->status !== UserStatus::ACTIVE) {
            throw ValidationException::withMessages([
                'mobile' => 'حساب کاربری شما غیرفعال است.',
            ]);
        }

        $user->forceFill([
            'mobile_verified_at' => $user->mobile_verified_at ?? now(),
            'last_login_at' => now(),
            'last_login_ip' => $request->ip(),
        ])->save();

        Auth::guard('web')->login($user, true);
        $request->session()->regenerate();

        return redirect()->intended(route('account.home'));
    }

    public function home(Request $request): RedirectResponse|View
    {
        $user = $request->user();

        if ($user->is_super_admin) {
            return redirect()->route('admin.dashboard');
        }

        $carWash = $user->activeCarWashes()
            ->where('car_washes.status', 'active')
            ->orderBy('car_washes.id')
            ->first();

        if ($carWash) {
            return redirect()->route('carwash.dashboard', $carWash);
        }

        return view('auth.no-access');
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
