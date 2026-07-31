@extends('layouts.carwash')
@section('title', 'پیشخوان کارواش')
@section('page-title', 'پیشخوان امروز')

@section('content')
<div class="mb-6 flex flex-wrap items-start justify-between gap-4">
    <div>
        <h1 class="panel-page-heading">سلام {{ auth()->user()->full_name ?: 'همکار عزیز' }} 👋</h1>
        <p class="panel-page-description">وضعیت امروز {{ $carWash->name }} در یک نگاه؛ برای شروع کار فقط صف رزروها را بررسی کنید.</p>
    </div>
    <div class="flex flex-wrap gap-2">
        @can('carwash.bookings.create')
            <a href="{{ route('carwash.bookings.index', $carWash) }}#new-booking" class="btn-primary"><x-icon name="plus"/> ثبت رزرو حضوری</a>
        @endcan
        <a href="{{ route('carwash.bookings.index', $carWash) }}" class="btn-secondary"><x-icon name="bookings"/> مشاهده صف امروز</a>
    </div>
</div>

<section class="mb-6 overflow-hidden rounded-3xl bg-secondary text-white shadow-xl shadow-secondary/15">
    <div class="grid gap-6 p-6 lg:grid-cols-[1.2fr_.8fr] lg:items-center">
        <div>
            <div class="flex flex-wrap items-center gap-2 text-xs font-semibold text-gray-300">
                <span>{{ \App\Support\PersianDate::human(now($carWash->timezone), $carWash->timezone) }}</span>
                <span class="h-1 w-1 rounded-full bg-gray-500"></span>
                <span>{{ $carWash->city ?: 'ایران' }}</span>
            </div>
            <h2 class="mt-3 text-2xl font-extrabold">{{ $summary['fill_rate'] }}٪ ظرفیت امروز رزرو شده</h2>
            <p class="mt-2 text-sm leading-7 text-gray-300">
                {{ \App\Support\PersianDate::digits($summary['reserved_capacity']) }} خودرو از مجموع {{ \App\Support\PersianDate::digits($summary['total_capacity']) }} ظرفیت روزانه ثبت شده است.
            </p>
            <div class="mt-5 h-2 overflow-hidden rounded-full bg-white/10">
                <div class="h-full rounded-full bg-primary" style="width: {{ min(100, $summary['fill_rate']) }}%"></div>
            </div>
        </div>
        <div class="grid grid-cols-2 gap-3">
            <div class="rounded-2xl bg-white/10 p-4"><div class="text-xs text-gray-300">اسلات باز</div><div class="mt-2 text-2xl font-extrabold">{{ \App\Support\PersianDate::digits($summary['open_slots']) }}</div></div>
            <div class="rounded-2xl bg-white/10 p-4"><div class="text-xs text-gray-300">تکمیل ظرفیت</div><div class="mt-2 text-2xl font-extrabold">{{ \App\Support\PersianDate::digits($summary['full_slots']) }}</div></div>
            <div class="col-span-2 rounded-2xl bg-white/10 p-4">
                <div class="text-xs text-gray-300">نزدیک‌ترین زمان آزاد</div>
                <div class="mt-2 font-extrabold">
                    @if($summary['next_available_slot'])
                        {{ \App\Support\PersianDate::human($summary['next_available_slot']->starts_at, $carWash->timezone, true) }}
                    @else
                        زمان آزادی وجود ندارد
                    @endif
                </div>
            </div>
        </div>
    </div>
</section>

<div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
    <x-panel.stat-card label="رزروهای ثبت‌شده امروز" :value="\App\Support\PersianDate::digits(number_format($summary['today_bookings']))" icon="bookings" hint="رزروهایی که امروز در سیستم ایجاد شده‌اند"/>
    <x-panel.stat-card label="مراجعه‌های برنامه‌ریزی‌شده" :value="\App\Support\PersianDate::digits(number_format($summary['today_visits']))" icon="clock" tone="blue" hint="بر اساس زمان اسلات امروز"/>
    <x-panel.stat-card label="در انتظار خدمت" :value="\App\Support\PersianDate::digits(number_format($summary['waiting']))" icon="customers" tone="violet" hint="تأییدشده یا مراجعه‌کرده"/>
    <x-panel.stat-card label="در حال شست‌وشو" :value="\App\Support\PersianDate::digits(number_format($summary['in_progress']))" icon="carwash" tone="red" hint="عملیات در حال اجرا"/>
    <x-panel.stat-card label="تکمیل‌شده امروز" :value="\App\Support\PersianDate::digits(number_format($summary['completed_today']))" icon="check" tone="green" hint="خدمات پایان‌یافته"/>
    <x-panel.stat-card label="فروش امروز" :value="\App\Support\PersianDate::digits(number_format($summary['today_revenue'])).' ریال'" icon="finance" tone="green" hint="مجموع رزروهای تکمیل‌شده"/>
</div>

<div class="mt-6 grid gap-6 xl:grid-cols-[minmax(0,1.45fr)_minmax(320px,.7fr)]">
    <section class="panel-card overflow-hidden">
        <div class="panel-card-header">
            <div>
                <h2 class="font-extrabold text-gray-900 dark:text-white">صف بعدی کارواش</h2>
                <p class="mt-1 text-xs text-gray-500">مرتب‌شده بر اساس زمان مراجعه</p>
            </div>
            <a href="{{ route('carwash.bookings.index', $carWash) }}" class="text-sm font-bold text-primary">همه رزروها</a>
        </div>
        <div class="overflow-x-auto">
            <table class="data-table">
                <thead><tr><th>ساعت</th><th>مشتری</th><th>خدمات</th><th>مبلغ</th><th>وضعیت</th><th></th></tr></thead>
                <tbody>
                @forelse($summary['next_bookings'] as $booking)
                    <tr>
                        <td><div class="font-extrabold" dir="ltr">{{ \App\Support\PersianDate::digits($booking->slot?->starts_at?->timezone($carWash->timezone)->format('H:i')) }}</div><div class="mt-1 text-xs text-gray-500">{{ \App\Support\PersianDate::short($booking->slot?->starts_at, $carWash->timezone) }}</div></td>
                        <td><div class="font-semibold text-gray-900 dark:text-white">{{ $booking->customer_name }}</div><div class="text-xs text-gray-500" dir="ltr">{{ $booking->customer_mobile }}</div></td>
                        <td class="max-w-xs truncate">{{ $booking->items->pluck('service_name')->join('، ') }}</td>
                        <td>{{ \App\Support\PersianDate::digits(number_format($booking->payable_amount)) }} ریال</td>
                        <td><x-status-badge :value="$booking->status"/></td>
                        <td><a href="{{ route('carwash.bookings.show', [$carWash, $booking]) }}" class="rounded-xl p-2 text-primary hover:bg-primary-50"><x-icon name="eye"/></a></td>
                    </tr>
                @empty
                    <tr><td colspan="6"><x-empty-state title="رزروی در صف نیست" description="رزروهای آینده در این بخش نمایش داده می‌شوند." icon="bookings"/></td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </section>

    <section class="space-y-3">
        <div class="mb-2"><h2 class="text-lg font-extrabold text-gray-900 dark:text-white">دسترسی سریع</h2><p class="mt-1 text-xs text-gray-500">کارهای پرتکرار مدیر کارواش</p></div>
        <a href="{{ route('carwash.schedule.index', $carWash) }}" class="quick-action-card group">
            <span class="flex h-12 w-12 items-center justify-center rounded-2xl bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300"><x-icon name="schedule"/></span>
            <div class="flex-1"><div class="font-extrabold text-gray-900 dark:text-white">تقویم کاری و ظرفیت</div><div class="mt-1 text-xs text-gray-500">تنظیم اسلات‌های ۳۰ یا ۶۰ دقیقه‌ای</div></div>
            <x-icon name="arrow-left" class="text-gray-400 group-hover:text-primary"/>
        </a>
        <a href="{{ route('carwash.services.index', $carWash) }}" class="quick-action-card group">
            <span class="flex h-12 w-12 items-center justify-center rounded-2xl bg-primary-100 text-primary-700 dark:bg-primary-900/30 dark:text-primary-300"><x-icon name="services"/></span>
            <div class="flex-1"><div class="font-extrabold text-gray-900 dark:text-white">خدمات و قیمت‌ها</div><div class="mt-1 text-xs text-gray-500">ویرایش خدمات برای انواع خودرو</div></div>
            <x-icon name="arrow-left" class="text-gray-400 group-hover:text-primary"/>
        </a>
        <a href="{{ route('carwash.reports.index', $carWash) }}" class="quick-action-card group">
            <span class="flex h-12 w-12 items-center justify-center rounded-2xl bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300"><x-icon name="reports"/></span>
            <div class="flex-1"><div class="font-extrabold text-gray-900 dark:text-white">گزارش عملکرد</div><div class="mt-1 text-xs text-gray-500">رزرو، لغو، فروش و روند روزانه</div></div>
            <x-icon name="arrow-left" class="text-gray-400 group-hover:text-primary"/>
        </a>
        <a href="{{ route('carwash.qr.index', $carWash) }}" class="quick-action-card group">
            <span class="flex h-12 w-12 items-center justify-center rounded-2xl bg-violet-100 text-violet-700 dark:bg-violet-900/30 dark:text-violet-300"><x-icon name="qr"/></span>
            <div class="flex-1"><div class="font-extrabold text-gray-900 dark:text-white">لینک رزرو و QR</div><div class="mt-1 text-xs text-gray-500">ساخت لینک اختصاصی برای مشتریان</div></div>
            <x-icon name="arrow-left" class="text-gray-400 group-hover:text-primary"/>
        </a>
    </section>
</div>
@endsection
