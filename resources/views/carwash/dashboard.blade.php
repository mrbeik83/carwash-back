@extends('layouts.panel')
@section('title', 'داشبورد کارواش')
@section('navigation') @include('carwash.partials.navigation') @endsection
@section('content')
<h1 class="mb-6 text-2xl font-bold">داشبورد {{ $carWash->name }}</h1>
<div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
    @foreach([
        'رزروهای امروز' => $summary['today_bookings'],
        'مراجعه امروز' => $summary['today_visits'],
        'در انتظار' => $summary['waiting'],
        'در حال انجام' => $summary['in_progress'],
        'تکمیل امروز' => $summary['completed_today'],
        'فروش امروز (ریال)' => number_format($summary['today_revenue']),
    ] as $label => $value)
        <div class="rounded-2xl bg-white p-5 shadow-sm"><div class="text-sm text-slate-500">{{ $label }}</div><div class="mt-2 text-2xl font-bold">{{ $value }}</div></div>
    @endforeach
</div>
<div class="mt-8 rounded-2xl bg-white p-5 shadow-sm">
    <h2 class="mb-4 font-bold">رزروهای بعدی</h2>
    <div class="space-y-3">
        @forelse($summary['next_bookings'] as $booking)
            <a class="flex justify-between rounded-xl border p-3" href="{{ route('carwash.bookings.show', [$carWash, $booking]) }}">
                <span>{{ $booking->customer_name }} - {{ $booking->customer_mobile }}</span>
                <span>{{ optional($booking->slot->starts_at)->timezone($carWash->timezone)->format('Y-m-d H:i') }}</span>
            </a>
        @empty <p class="text-slate-500">رزروی در صف نیست.</p> @endforelse
    </div>
</div>
@endsection
