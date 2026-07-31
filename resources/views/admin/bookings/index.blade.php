@extends('layouts.admin')
@section('title', 'همه رزروها')
@section('page-title', 'رزروهای پلتفرم')
@section('content')
<div class="mb-6"><h1 class="text-2xl font-extrabold text-gray-900 dark:text-white">همه رزروها</h1><p class="mt-1 text-sm text-gray-500">کنترل و جست‌وجوی رزروهای تمام کارواش‌ها</p></div>
<form class="panel-card mb-5 grid gap-3 p-4 md:grid-cols-[minmax(0,1fr)_220px_220px_auto]">
<div class="relative"><x-icon name="search" class="absolute right-3 top-1/2 h-5 w-5 -translate-y-1/2 text-gray-400"/><input name="q" value="{{ request('q') }}" class="form-control pr-10" placeholder="کد پیگیری، مشتری یا موبایل"></div>
<select name="car_wash_id" class="form-select"><option value="">همه کارواش‌ها</option>@foreach($carWashes as $wash)<option value="{{ $wash->id }}" @selected((string)request('car_wash_id')===(string)$wash->id)>{{ $wash->name }}</option>@endforeach</select>
<select name="status" class="form-select"><option value="">همه وضعیت‌ها</option>@foreach(\App\Enums\BookingStatus::cases() as $status)<option value="{{ $status->value }}" @selected(request('status')===$status->value)>{{ $status->label() }}</option>@endforeach</select>
<button class="btn-primary">فیلتر</button>
</form>
<div class="table-shell"><div class="overflow-x-auto"><table class="data-table">
<thead><tr><th>کد پیگیری</th><th>کارواش</th><th>مشتری</th><th>زمان مراجعه</th><th>مبلغ</th><th>پرداخت</th><th>وضعیت</th></tr></thead>
<tbody>@forelse($bookings as $booking)<tr>
<td class="font-latin text-xs">{{ $booking->tracking_code }}</td><td><a class="font-medium text-primary" href="{{ route('admin.car-washes.show',$booking->carWash) }}">{{ $booking->carWash?->name }}</a></td>
<td><div class="font-medium">{{ $booking->customer_name }}</div><div class="text-xs text-gray-500" dir="ltr">{{ $booking->customer_mobile }}</div></td>
<td>{{ \App\Support\PersianDate::dateTime($booking->slot?->starts_at, $booking->carWash?->timezone ?? 'Asia/Tehran') }}</td>
<td>{{ number_format($booking->payable_amount) }} ریال</td><td><x-status-badge :value="$booking->payment_status"/></td><td><x-status-badge :value="$booking->status"/></td>
</tr>@empty<tr><td colspan="7"><x-empty-state title="رزروی پیدا نشد"/></td></tr>@endforelse</tbody>
</table></div></div><div class="mt-5">{{ $bookings->links() }}</div>
@endsection
