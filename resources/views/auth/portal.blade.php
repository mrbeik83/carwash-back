@extends('layouts.auth')
@section('title', 'انتخاب پنل')
@section('hero-title', 'هر بخش، ورود و فضای مدیریتی مستقل')
@section('hero-description', 'برای امنیت و نظم بیشتر، پنل مدیریت کل و پنل کارواش مسیرهای ورود جداگانه دارند.')
@section('content')
<div class="mb-8">
    <h2 class="text-2xl font-extrabold text-gray-900 dark:text-white">انتخاب نوع ورود</h2>
    <p class="mt-2 text-sm leading-7 text-gray-500 dark:text-gray-400">پنل موردنظر خود را انتخاب کنید. اطلاعات و دسترسی هر بخش کاملاً مستقل بررسی می‌شود.</p>
</div>
<div class="space-y-4">
    <a href="{{ route('admin.login') }}" class="panel-card group flex items-center gap-4 p-5 hover:border-primary">
        <span class="flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl bg-secondary text-white">
            <x-icon name="settings" class="h-7 w-7"/>
        </span>
        <div class="min-w-0 flex-1">
            <div class="font-bold text-gray-900 dark:text-white">ورود مدیر اصلی</div>
            <div class="mt-1 text-sm text-gray-500 dark:text-gray-400">مدیریت کل کارواش‌ها، کاربران، گزارش‌ها و تنظیمات</div>
        </div>
        <x-icon name="arrow-left" class="h-5 w-5 text-gray-400 group-hover:text-primary"/>
    </a>
    <a href="{{ route('carwash.login') }}" class="panel-card group flex items-center gap-4 p-5 hover:border-primary">
        <span class="bg-primary-grad flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl text-white">
            <x-icon name="carwash" class="h-7 w-7"/>
        </span>
        <div class="min-w-0 flex-1">
            <div class="font-bold text-gray-900 dark:text-white">ورود پنل کارواش</div>
            <div class="mt-1 text-sm text-gray-500 dark:text-gray-400">مدیریت رزرو، خدمات، ظرفیت، کارکنان و پرداخت‌ها</div>
        </div>
        <x-icon name="arrow-left" class="h-5 w-5 text-gray-400 group-hover:text-primary"/>
    </a>
</div>
@endsection
