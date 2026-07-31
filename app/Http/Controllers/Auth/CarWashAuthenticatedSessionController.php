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
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class CarWashAuthenticatedSessionController extends Controller
{
    public function create(): View|RedirectResponse
    {
        if (auth()->check() && ! auth()->user()->is_super_admin) {
            return $this->redirectToPanel(auth()->user());
        }

        return view('auth.carwash-login');
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
            || ! Hash::check($credentials['password'], $user->password ?? '')
            || ! $this->hasActiveCarWash($user)
        ) {
            throw ValidationException::withMessages([
                'mobile' => 'شماره موبایل، رمز عبور یا دسترسی کارواش صحیح نیست.',
            ]);
        }

        Auth::guard('web')->login($user, $request->boolean('remember'));
        $request->session()->regenerate();

        $user->forceFill([
            'last_login_at' => now(),
            'last_login_ip' => $request->ip(),
        ])->save();

        return $this->redirectToPanel($user);
    }

    public function requestOtp(Request $request, OtpService $service): RedirectResponse
    {
        $data = $request->validate([
            'mobile' => ['required', 'string', 'max:20'],
        ]);

        $user = User::query()->where('mobile', $data['mobile'])->first();

        if (
            ! $user
            || $user->status !== UserStatus::ACTIVE
            || ! $this->hasActiveCarWash($user)
        ) {
            throw ValidationException::withMessages([
                'mobile' => 'برای این شماره عضویت فعال در کارواش پیدا نشد.',
            ]);
        }

        $service->request($data['mobile'], OtpPurpose::LOGIN, $request->ip());

        return back()
            ->with('auth_tab', 'otp')
            ->with('otp_mobile', $data['mobile'])
            ->with('success', 'کد ورود برای شما ارسال شد.');
    }

    public function verifyOtp(Request $request, OtpService $service): RedirectResponse
    {
        $data = $request->validate([
            'mobile' => ['required', 'string', 'max:20'],
            'code' => ['required', 'digits:6'],
        ]);

        $user = User::query()->where('mobile', $data['mobile'])->first();

        if (
            ! $user
            || $user->status !== UserStatus::ACTIVE
            || ! $this->hasActiveCarWash($user)
            || ! $service->verify($data['mobile'], $data['code'], OtpPurpose::LOGIN)
        ) {
            throw ValidationException::withMessages([
                'code' => 'کد ورود صحیح نیست، منقضی شده یا دسترسی شما غیرفعال است.',
            ]);
        }

        Auth::guard('web')->login($user, true);
        $request->session()->regenerate();

        $user->forceFill([
            'mobile_verified_at' => $user->mobile_verified_at ?? now(),
            'last_login_at' => now(),
            'last_login_ip' => $request->ip(),
        ])->save();

        return $this->redirectToPanel($user);
    }

    public function select(Request $request): View|RedirectResponse
    {
        $carWashes = $request->user()
            ->activeCarWashes()
            ->where('car_washes.status', 'active')
            ->orderBy('car_washes.name')
            ->get();

        if ($carWashes->count() === 1) {
            return redirect()->route('carwash.dashboard', $carWashes->first());
        }

        return view('auth.carwash-select', compact('carWashes'));
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('carwash.login')
            ->with('success', 'با موفقیت از پنل کارواش خارج شدید.');
    }

    private function hasActiveCarWash(User $user): bool
    {
        return $user->activeCarWashes()
            ->where('car_washes.status', 'active')
            ->exists();
    }

    private function redirectToPanel(User $user): RedirectResponse
    {
        $carWashes = $user->activeCarWashes()
            ->where('car_washes.status', 'active')
            ->orderBy('car_washes.name')
            ->get();

        if ($carWashes->count() === 1) {
            return redirect()->route('carwash.dashboard', $carWashes->first());
        }

        return redirect()->route('carwash.select');
    }
}
