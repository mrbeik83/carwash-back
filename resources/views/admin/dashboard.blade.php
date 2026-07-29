@extends('layouts.panel')
@section('title', 'مدیریت کل')
@section('navigation')
<a class="block rounded-lg p-2 hover:bg-white/10" href="{{ route('admin.dashboard') }}">داشبورد</a>
<a class="block rounded-lg p-2 hover:bg-white/10" href="{{ route('admin.car-washes.index') }}">کارواش‌ها</a>
<a class="block rounded-lg p-2 hover:bg-white/10" href="{{ route('admin.users.index') }}">کاربران</a>
<a class="block rounded-lg p-2 hover:bg-white/10" href="{{ route('admin.bookings.index') }}">همه رزروها</a>
<a class="block rounded-lg p-2 hover:bg-white/10" href="{{ route('admin.finance.index') }}">مالی</a>
<a class="block rounded-lg p-2 hover:bg-white/10" href="{{ route('admin.audit-logs.index') }}">لاگ‌ها</a>
@endsection
@section('content')
<h1 class="mb-6 text-2xl font-bold">داشبورد مدیریت کل</h1>
<div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-5">
@foreach([
    'کارواش‌ها' => $summary['car_washes'],
    'در انتظار تایید' => $summary['pending_car_washes'],
    'کاربران' => $summary['users'],
    'رزرو امروز' => $summary['bookings_today'],
    'پرداخت امروز (ریال)' => number_format($summary['revenue_today']),
] as $label => $value)
<div class="rounded-2xl bg-white p-5 shadow-sm"><div class="text-sm text-slate-500">{{ $label }}</div><div class="mt-2 text-2xl font-bold">{{ $value }}</div></div>
@endforeach
</div>
@endsection
