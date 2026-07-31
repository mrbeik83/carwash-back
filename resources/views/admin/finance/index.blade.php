@extends('layouts.admin')
@section('title', 'مالی و تراکنش‌ها')
@section('page-title', 'امور مالی')
@section('content')
<div class="mb-6"><h1 class="text-2xl font-extrabold text-gray-900 dark:text-white">مالی و تراکنش‌ها</h1><p class="mt-1 text-sm text-gray-500">نمای کلی دریافتی‌ها و وضعیت پرداخت‌ها</p></div>
<div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
<x-panel.stat-card label="کل پرداخت موفق" :value="number_format($summary['paid_total']).' ریال'" icon="finance" tone="green"/>
<x-panel.stat-card label="دریافتی امروز" :value="number_format($summary['paid_today']).' ریال'" icon="finance" tone="blue"/>
<x-panel.stat-card label="در انتظار پردازش" :value="number_format($summary['pending'])" icon="clock"/>
<x-panel.stat-card label="مبلغ بازگشتی" :value="number_format($summary['refunded']).' ریال'" icon="finance" tone="red"/>
</div>
<form class="panel-card my-5 grid gap-3 p-4 md:grid-cols-[minmax(0,1fr)_220px_auto]">
<input name="q" value="{{ request('q') }}" class="form-control" placeholder="شماره مرجع، تراکنش یا رزرو">
<select name="status" class="form-select"><option value="">همه وضعیت‌ها</option>@foreach(\App\Enums\PaymentStatus::cases() as $status)<option value="{{ $status->value }}" @selected(request('status')===$status->value)>{{ $status->label() }}</option>@endforeach</select>
<button class="btn-primary">فیلتر</button>
</form>
<div class="table-shell"><div class="overflow-x-auto"><table class="data-table">
<thead><tr><th>تراکنش</th><th>رزرو</th><th>کارواش</th><th>روش</th><th>مبلغ</th><th>وضعیت</th><th>تاریخ</th></tr></thead>
<tbody>@forelse($payments as $payment)<tr>
<td class="font-latin text-xs">{{ $payment->transaction_id ?: $payment->public_id }}</td><td>{{ $payment->booking?->tracking_code }}</td><td>{{ $payment->booking?->carWash?->name }}</td>
<td>{{ $payment->method instanceof \App\Enums\PaymentMethod ? $payment->method->label() : $payment->method }}</td><td>{{ number_format($payment->amount) }} ریال</td><td><x-status-badge :value="$payment->status"/></td><td>{{ \App\Support\PersianDate::dateTime($payment->paid_at ?: $payment->created_at, $payment->booking?->carWash?->timezone ?? 'Asia/Tehran') }}</td>
</tr>@empty<tr><td colspan="7"><x-empty-state title="تراکنشی وجود ندارد" icon="finance"/></td></tr>@endforelse</tbody>
</table></div></div><div class="mt-5">{{ $payments->links() }}</div>
@endsection
