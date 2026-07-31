@extends('layouts.carwash')
@section('title', 'مشتریان')
@section('page-title', 'مشتریان کارواش')

@section('content')
<div class="mb-6 flex flex-wrap items-start justify-between gap-4">
    <div>
        <h1 class="text-2xl font-extrabold text-gray-900 dark:text-white">مشتریان</h1>
        <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">فهرست مشتریانی که در {{ $carWash->name }} رزرو ثبت کرده‌اند.</p>
    </div>
    <span class="viz-badge rounded-full bg-primary-50 px-3 py-1.5 text-sm font-semibold text-primary-700 dark:bg-primary-950/40 dark:text-primary-300">
        {{ number_format($customers->total()) }} مشتری
    </span>
</div>

<section class="panel-card mb-6">
    <form method="GET" action="{{ route('carwash.customers.index', $carWash) }}" class="panel-card-body flex flex-col gap-3 sm:flex-row">
        <div class="relative flex-1">
            <span class="pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 text-gray-400"><x-icon name="search"/></span>
            <input name="q" value="{{ request('q') }}" class="form-control pr-11" placeholder="جست‌وجو بر اساس نام یا شماره موبایل">
        </div>
        <button class="btn-primary"><x-icon name="search"/> جست‌وجو</button>
        @if(request()->filled('q'))
            <a href="{{ route('carwash.customers.index', $carWash) }}" class="btn-secondary">پاک‌کردن</a>
        @endif
    </form>
</section>

<div class="table-shell">
    <div class="overflow-x-auto">
        <table class="data-table">
            <thead>
            <tr>
                <th>مشتری</th>
                <th>شماره موبایل</th>
                <th>تعداد خودرو</th>
                <th>تعداد رزرو در این کارواش</th>
                <th>آخرین فعالیت</th>
            </tr>
            </thead>
            <tbody>
            @forelse($customers as $customer)
                <tr>
                    <td>
                        <div class="flex items-center gap-3">
                            <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-primary-50 text-primary-700 dark:bg-primary-950/40 dark:text-primary-300">
                                <x-icon name="profile"/>
                            </span>
                            <div>
                                <div class="font-semibold text-gray-900 dark:text-white">{{ $customer->full_name ?: 'مشتری بدون نام' }}</div>
                                <div class="text-xs text-gray-500">شناسه {{ $customer->public_id }}</div>
                            </div>
                        </div>
                    </td>
                    <td dir="ltr" class="text-right">{{ $customer->mobile ?: '—' }}</td>
                    <td>{{ number_format($customer->vehicles_count) }}</td>
                    <td>{{ number_format($customer->car_wash_bookings_count) }}</td>
                    <td>{{ $customer->last_login_at ? \App\Support\PersianDate::dateTime($customer->last_login_at, $carWash->timezone) : '—' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5">
                        <x-empty-state title="مشتری‌ای پیدا نشد" description="پس از ثبت اولین رزرو، اطلاعات مشتری در این بخش نمایش داده می‌شود." icon="customers"/>
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>

@if($customers->hasPages())
    <div class="mt-6">{{ $customers->links() }}</div>
@endif
@endsection
