@extends('layouts.auth')
@section('title', 'عدم دسترسی')
@section('hero-title', 'حساب شما به پنل فعالی متصل نیست')
@section('hero-description', 'برای فعال‌سازی عضویت با مدیر کارواش یا پشتیبانی سامانه در ارتباط باشید.')

@section('content')
<div class="rounded-2xl border border-gray-200 bg-white p-8 text-center shadow-sm dark:border-gray-700 dark:bg-gray-800">
    <span class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl bg-amber-50 text-amber-600 dark:bg-amber-950/40 dark:text-amber-300">
        <x-icon name="roles" class="h-8 w-8"/>
    </span>
    <h1 class="mt-5 text-xl font-extrabold text-gray-900 dark:text-white">دسترسی فعال پیدا نشد</h1>
    <p class="mt-3 text-sm leading-7 text-gray-500 dark:text-gray-400">عضویت شما ممکن است در انتظار تأیید، تعلیق‌شده یا متعلق به کارواش غیرفعال باشد.</p>
    <form method="POST" action="{{ route('carwash.logout') }}" class="mt-6">
        @csrf
        <button class="btn-secondary w-full">خروج و بازگشت به صفحه ورود</button>
    </form>
</div>
@endsection
