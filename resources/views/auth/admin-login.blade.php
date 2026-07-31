@extends('layouts.auth')
@section('title', 'ورود مدیر اصلی')
@section('hero-title', 'کنترل کامل پلتفرم در یک پنل حرفه‌ای')
@section('hero-description', 'این مسیر فقط برای مدیر اصلی سامانه است و دسترسی سایر کاربران به آن مسدود می‌شود.')
@section('content')
<div class="mb-8">
    <span class="inline-flex rounded-full bg-secondary-50 px-3 py-1.5 text-xs font-semibold text-secondary dark:bg-secondary-800 dark:text-secondary-100">پنل مدیریت کل</span>
    <h2 class="mt-4 text-2xl font-extrabold text-gray-900 dark:text-white">ورود مدیر اصلی</h2>
    <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">شماره موبایل و رمز عبور مدیر اصلی سامانه را وارد کنید.</p>
</div>
<form method="POST" action="{{ route('admin.login.store') }}" class="space-y-5">
    @csrf
    <div>
        <label class="form-label" for="mobile">شماره موبایل</label>
        <input id="mobile" type="tel" name="mobile" value="{{ old('mobile') }}" class="form-control" dir="ltr" inputmode="tel" autocomplete="username" placeholder="0912xxxxxxx" required autofocus>
    </div>
    <div>
        <label class="form-label" for="admin-password">رمز عبور</label>
        <div class="relative">
            <input id="admin-password" type="password" name="password" class="form-control pl-12" dir="ltr" autocomplete="current-password" required>
            <button type="button" data-password-toggle="admin-password" class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-primary">
                <x-icon name="eye"/>
            </button>
        </div>
    </div>
    <label class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-300">
        <input type="checkbox" name="remember" value="1" class="h-4 w-4 rounded border-gray-300 text-primary focus:ring-primary">
        مرا به خاطر بسپار
    </label>
    <button class="btn-primary w-full py-3">ورود به مدیریت کل</button>
</form>
<div class="mt-6 text-center">
    <a href="{{ route('carwash.login') }}" class="text-sm text-gray-500 hover:text-primary">عضو یک کارواش هستید؟ ورود پنل کارواش</a>
</div>
@endsection
