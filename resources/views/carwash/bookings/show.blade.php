@extends('layouts.carwash')
@section('title', 'جزئیات رزرو')
@section('page-title', 'جزئیات رزرو')

@section('content')
<div class="mb-6 flex flex-wrap items-start justify-between gap-4">
    <div>
        <div class="flex flex-wrap items-center gap-3">
            <h1 class="panel-page-heading">رزرو {{ $booking->tracking_code }}</h1>
            <x-status-badge :value="$booking->status"/>
        </div>
        <p class="panel-page-description">{{ $booking->customer_name }} · <span dir="ltr">{{ $booking->customer_mobile }}</span></p>
    </div>
    <a href="{{ route('carwash.bookings.index', ['carWash' => $carWash, 'date' => $booking->slot?->starts_at?->timezone($carWash->timezone)->toDateString()]) }}" class="btn-secondary"><x-icon name="arrow-left" class="rotate-180"/> بازگشت به صف رزروها</a>
</div>

<div class="grid gap-6 xl:grid-cols-3">
    <div class="space-y-6 xl:col-span-2">
        <section class="panel-card overflow-hidden">
            <div class="bg-secondary p-6 text-white">
                <div class="grid gap-5 sm:grid-cols-3">
                    <div><div class="text-xs text-gray-300">زمان مراجعه</div><div class="mt-2 text-lg font-extrabold">{{ \App\Support\PersianDate::human($booking->slot?->starts_at, $carWash->timezone, true) }}</div></div>
                    <div><div class="text-xs text-gray-300">خودرو</div><div class="mt-2 text-lg font-extrabold">{{ $booking->vehicle_type_snapshot ?: 'نامشخص' }}</div><div class="mt-1 text-xs text-gray-300">{{ $booking->vehicle_plate_snapshot ?: 'بدون پلاک' }}</div></div>
                    <div><div class="text-xs text-gray-300">مبلغ قابل پرداخت</div><div class="mt-2 text-lg font-extrabold">{{ \App\Support\PersianDate::digits(number_format($booking->payable_amount)) }} ریال</div><div class="mt-1"><x-status-badge :value="$booking->payment_status"/></div></div>
                </div>
            </div>
            <div class="panel-card-body grid gap-5 sm:grid-cols-2">
                <div><div class="text-xs text-gray-500">نام مشتری</div><div class="mt-1 font-bold text-gray-900 dark:text-white">{{ $booking->customer_name }}</div></div>
                <div><div class="text-xs text-gray-500">شماره موبایل</div><div class="mt-1 font-bold text-gray-900 dark:text-white" dir="ltr">{{ $booking->customer_mobile }}</div></div>
                <div><div class="text-xs text-gray-500">منبع ثبت رزرو</div><div class="mt-1 font-bold text-gray-900 dark:text-white">{{ $booking->source?->label() ?: 'نامشخص' }}</div></div>
                <div><div class="text-xs text-gray-500">تاریخ ثبت</div><div class="mt-1 font-bold text-gray-900 dark:text-white">{{ \App\Support\PersianDate::dateTime($booking->created_at, $carWash->timezone) }}</div></div>
                @if($booking->customer_note)
                    <div class="sm:col-span-2 rounded-2xl bg-gray-50 p-4 text-sm leading-7 dark:bg-gray-700/60"><div class="mb-1 font-extrabold">یادداشت مشتری</div>{{ $booking->customer_note }}</div>
                @endif
            </div>
        </section>

        <section class="panel-card overflow-hidden">
            <div class="panel-card-header"><h2 class="font-extrabold">خدمات رزرو</h2><span class="text-sm text-gray-500">{{ \App\Support\PersianDate::digits($booking->items->count()) }} مورد</span></div>
            <div class="divide-y divide-gray-100 dark:divide-gray-700">
                @foreach($booking->items as $item)
                    <div class="flex items-center justify-between gap-3 px-5 py-4">
                        <div><div class="font-bold text-gray-900 dark:text-white">{{ $item->service_name }}</div><div class="mt-1 text-xs text-gray-500">{{ \App\Support\PersianDate::digits($item->duration_minutes) }} دقیقه · تعداد {{ \App\Support\PersianDate::digits($item->quantity) }}</div></div>
                        <div class="font-extrabold">{{ \App\Support\PersianDate::digits(number_format($item->total_amount)) }} ریال</div>
                    </div>
                @endforeach
            </div>
        </section>

        <section class="panel-card">
            <div class="panel-card-header"><h2 class="font-extrabold">تاریخچه وضعیت</h2></div>
            <div class="panel-card-body">
                <ol class="relative border-r border-gray-200 pr-6 dark:border-gray-700">
                    @forelse($booking->statusHistory->sortByDesc('created_at') as $history)
                        <li class="mb-6 last:mb-0">
                            <span class="absolute -right-2.5 mt-1 flex h-5 w-5 items-center justify-center rounded-full bg-primary ring-4 ring-white dark:ring-gray-800"></span>
                            <div class="flex flex-wrap items-center gap-2"><x-status-badge :value="$history->to_status"/><span class="text-xs text-gray-500">{{ \App\Support\PersianDate::dateTime($history->created_at, $carWash->timezone) }}</span></div>
                            @if($history->note)<p class="mt-2 text-sm text-gray-600 dark:text-gray-300">{{ $history->note }}</p>@endif
                        </li>
                    @empty
                        <x-empty-state title="تاریخچه‌ای ثبت نشده است" icon="clock"/>
                    @endforelse
                </ol>
            </div>
        </section>
    </div>

    <div class="space-y-6">
        <section class="panel-card">
            <div class="panel-card-header"><div><h2 class="font-extrabold">تغییر وضعیت عملیاتی</h2><p class="mt-1 text-xs text-gray-500">مرحله بعدی رزرو را ثبت کنید.</p></div></div>
            <form method="POST" action="{{ route('carwash.bookings.transition', [$carWash, $booking]) }}" class="panel-card-body space-y-4">
                @csrf
                <div><label class="form-label">وضعیت جدید</label><select name="status" class="form-select">
                    @foreach([
                        'confirmed' => ['تأیید رزرو', 'carwash.bookings.confirm'],
                        'checked_in' => ['ثبت مراجعه مشتری', 'carwash.bookings.check-in'],
                        'in_progress' => ['شروع شست‌وشو', 'carwash.bookings.start'],
                        'completed' => ['اتمام و تحویل خودرو', 'carwash.bookings.complete'],
                        'cancelled' => ['لغو رزرو', 'carwash.bookings.cancel'],
                        'no_show' => ['ثبت عدم مراجعه', 'carwash.bookings.no-show'],
                        'rejected' => ['رد رزرو', 'carwash.bookings.cancel'],
                    ] as $value => [$label, $permission])
                        @can($permission)<option value="{{ $value }}">{{ $label }}</option>@endcan
                    @endforeach
                </select></div>
                <div><label class="form-label">توضیحات تغییر</label><textarea name="note" rows="3" class="form-control" placeholder="اختیاری"></textarea></div>
                <button class="btn-primary w-full"><x-icon name="check"/> ثبت وضعیت</button>
            </form>
        </section>

        @can('carwash.payments.create')
            <section class="panel-card">
                <div class="panel-card-header"><div><h2 class="font-extrabold">ثبت پرداخت حضوری</h2><p class="mt-1 text-xs text-gray-500">پرداخت نقدی یا کارت‌خوان</p></div></div>
                <form method="POST" action="{{ route('carwash.payments.store', [$carWash, $booking]) }}" class="panel-card-body space-y-4">
                    @csrf
                    <div><label class="form-label">روش پرداخت</label><select name="method" class="form-select"><option value="cash">نقدی</option><option value="pos">کارت‌خوان</option></select></div>
                    <div><label class="form-label">مبلغ (ریال)</label><input type="number" name="amount" value="{{ $booking->payable_amount }}" class="form-control" min="1"></div>
                    <div><label class="form-label">شماره مرجع</label><input name="reference_id" class="form-control" dir="ltr" placeholder="اختیاری"></div>
                    <button class="btn-success w-full"><x-icon name="check"/> ثبت پرداخت</button>
                </form>
            </section>
        @endcan

        <section class="panel-card">
            <div class="panel-card-header"><h2 class="font-extrabold">پرداخت‌های ثبت‌شده</h2></div>
            <div class="divide-y divide-gray-100 dark:divide-gray-700">
                @forelse($booking->payments as $payment)
                    <div class="p-4">
                        <div class="flex items-center justify-between gap-2"><span class="font-bold">{{ \App\Support\PersianDate::digits(number_format($payment->amount)) }} ریال</span><x-status-badge :value="$payment->status"/></div>
                        <div class="mt-2 text-xs text-gray-500">{{ \App\Support\PersianDate::dateTime($payment->paid_at ?: $payment->created_at, $carWash->timezone) }} · {{ $payment->method instanceof \App\Enums\PaymentMethod ? $payment->method->label() : $payment->method }}</div>
                    </div>
                @empty
                    <div class="p-5 text-center text-sm text-gray-400">هنوز پرداختی ثبت نشده است.</div>
                @endforelse
            </div>
        </section>
    </div>
</div>
@endsection
