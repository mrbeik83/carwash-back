@extends('layouts.auth')
@section('title', 'ورود پنل کارواش')
@section('hero-title', 'همه عملیات روزانه کارواش، مرتب و در دسترس')
@section('hero-description', 'رزروهای امروز، صف شست‌وشو، ظرفیت، خدمات، پرسنل و گزارش‌ها را در یک محیط یکپارچه مدیریت کنید.')
@section('content')
<div class="mb-6">
    <span class="inline-flex rounded-full bg-primary-50 px-3 py-1.5 text-xs font-semibold text-primary-700 dark:bg-primary-900/30 dark:text-primary-300">پنل اختصاصی کارواش</span>
    <h2 class="mt-4 text-2xl font-extrabold text-gray-900 dark:text-white">ورود کارکنان کارواش</h2>
    <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">با رمز عبور یا کد یک‌بارمصرف وارد شوید.</p>
</div>

<div data-auth-tabs data-default-tab="{{ session('auth_tab', $errors->has('code') ? 'otp' : 'password') }}" class="mb-6 grid grid-cols-2 rounded-xl bg-gray-100 p-1 dark:bg-gray-800">
    <button type="button" data-auth-tab="password" class="rounded-lg px-3 py-2.5 text-sm font-semibold">رمز عبور</button>
    <button type="button" data-auth-tab="otp" class="rounded-lg px-3 py-2.5 text-sm font-semibold">کد یک‌بارمصرف</button>
</div>

<div data-auth-panel="password">
    <form method="POST" action="{{ route('carwash.login.store') }}" class="space-y-5">
        @csrf
        <div>
            <label class="form-label" for="cw-mobile">شماره موبایل</label>
            <input id="cw-mobile" type="tel" name="mobile" value="{{ old('mobile') }}" class="form-control" dir="ltr" inputmode="tel" autocomplete="username" required>
        </div>
        <div>
            <label class="form-label" for="cw-password">رمز عبور</label>
            <div class="relative">
                <input id="cw-password" type="password" name="password" class="form-control pl-12" dir="ltr" autocomplete="current-password" required>
                <button type="button" data-password-toggle="cw-password" class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-primary">
                    <x-icon name="eye"/>
                </button>
            </div>
        </div>
        <label class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-300">
            <input type="checkbox" name="remember" value="1" class="h-4 w-4 rounded border-gray-300 text-primary focus:ring-primary">
            مرا به خاطر بسپار
        </label>
        <button class="btn-primary w-full py-3">ورود به پنل کارواش</button>
    </form>
</div>

<div data-auth-panel="otp" class="hidden space-y-5">
    @php($otpMobile = session('otp_mobile') ?: old('mobile'))
    @if($otpMobile)
        <form method="POST" action="{{ route('carwash.login.otp.verify') }}" class="space-y-5">
            @csrf
            <div>
                <label class="form-label">شماره موبایل</label>
                <input type="tel" name="mobile" value="{{ $otpMobile }}" class="form-control bg-gray-50" dir="ltr" readonly>
            </div>
            <div>
                <label class="form-label" for="otp-code">کد شش‌رقمی</label>
                <input id="otp-code" name="code" autocomplete="one-time-code" class="form-control text-center text-xl tracking-[.4em]" dir="ltr" inputmode="numeric" maxlength="6" required autofocus>
            </div>
            <button class="btn-primary w-full py-3">تأیید و ورود</button>
        </form>
        <form method="POST" action="{{ route('carwash.login.otp.request') }}">
            @csrf
            <input type="hidden" name="mobile" value="{{ $otpMobile }}">
            <button class="btn-secondary w-full">ارسال مجدد کد</button>
        </form>
    @else
        <form method="POST" action="{{ route('carwash.login.otp.request') }}" class="space-y-5">
            @csrf
            <div>
                <label class="form-label" for="otp-mobile">شماره موبایل عضو کارواش</label>
                <input id="otp-mobile" type="tel" name="mobile" value="{{ old('mobile') }}" class="form-control" dir="ltr" inputmode="tel" autocomplete="tel" required>
            </div>
            <button class="btn-primary w-full py-3">دریافت کد ورود</button>
        </form>
    @endif
</div>

<div class="mt-6 text-center">
    <a href="{{ route('admin.login') }}" class="text-sm text-gray-500 hover:text-primary">مدیر اصلی سامانه هستید؟ ورود مدیریت کل</a>
</div>
@endsection
