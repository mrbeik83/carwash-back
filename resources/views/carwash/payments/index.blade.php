@extends('layouts.carwash')
@section('title', 'پرداخت‌ها')
@section('page-title', 'پرداخت‌ها و تراکنش‌ها')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-extrabold text-gray-900 dark:text-white">پرداخت‌ها</h1>
    <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">پرداخت‌های نقدی، کارت‌خوان و آنلاین رزروهای این کارواش.</p>
</div>

@php
    $methodLabels = ['cash' => 'نقدی', 'pos' => 'کارت‌خوان', 'online' => 'آنلاین', 'wallet' => 'کیف پول'];
@endphp

<div class="table-shell">
    <div class="overflow-x-auto">
        <table class="data-table">
            <thead>
            <tr>
                <th>رزرو</th>
                <th>مشتری</th>
                <th>مبلغ</th>
                <th>روش</th>
                <th>وضعیت</th>
                <th>شماره مرجع</th>
                <th>زمان پرداخت</th>
            </tr>
            </thead>
            <tbody>
            @forelse($payments as $payment)
                @php
                    $method = $payment->method instanceof \BackedEnum ? $payment->method->value : $payment->method;
                    $status = $payment->status instanceof \BackedEnum ? $payment->status->value : $payment->status;
                @endphp
                <tr>
                    <td>
                        <a href="{{ route('carwash.bookings.show', [$carWash, $payment->booking]) }}" class="font-semibold text-primary hover:underline">
                            {{ $payment->booking?->tracking_code ?: '—' }}
                        </a>
                    </td>
                    <td>
                        <div class="font-medium text-gray-900 dark:text-white">{{ $payment->booking?->customer_name ?: '—' }}</div>
                        <div class="text-xs text-gray-500" dir="ltr">{{ $payment->booking?->customer_mobile }}</div>
                    </td>
                    <td class="font-bold">{{ number_format($payment->amount) }} ریال</td>
                    <td>{{ $methodLabels[$method] ?? $method }}</td>
                    <td><x-status-badge :value="$status"/></td>
                    <td dir="ltr" class="text-right">{{ $payment->reference_id ?: '—' }}</td>
                    <td>{{ $payment->paid_at ? \App\Support\PersianDate::dateTime($payment->paid_at, $carWash->timezone) : '—' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7">
                        <x-empty-state title="هنوز پرداختی ثبت نشده است" description="پرداخت حضوری را از صفحه جزئیات رزرو ثبت کنید." icon="finance"/>
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>

@if($payments->hasPages())
    <div class="mt-6">{{ $payments->links() }}</div>
@endif
@endsection
