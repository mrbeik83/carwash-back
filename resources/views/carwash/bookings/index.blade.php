@extends('layouts.carwash')
@section('title', 'رزروها')
@section('page-title', 'رزروها و صف کاری')

@section('content')
<div class="mb-6 flex flex-wrap items-start justify-between gap-4">
    <div>
        <h1 class="panel-page-heading">رزروها و صف کاری</h1>
        <p class="panel-page-description">هر روز را از تقویم هفتگی انتخاب کنید، صف همان روز را ببینید و رزرو حضوری ثبت کنید.</p>
    </div>
    @can('carwash.bookings.create')
        <a href="#new-booking" class="btn-primary"><x-icon name="plus"/> ثبت رزرو حضوری</a>
    @endcan
</div>

<section class="panel-card mb-5 overflow-hidden">
    <div class="flex flex-wrap items-center justify-between gap-3 border-b border-gray-100 px-4 py-3 dark:border-gray-700">
        <div>
            <h2 class="font-extrabold text-gray-900 dark:text-white">تقویم هفتگی رزروها</h2>
            <p class="mt-1 text-xs text-gray-500">{{ \App\Support\PersianDate::short($weekStart, $carWash->timezone) }} تا {{ \App\Support\PersianDate::short($weekEnd, $carWash->timezone) }}</p>
        </div>
        <div class="flex items-center gap-2">
            <a class="btn-secondary px-3 py-2" href="{{ route('carwash.bookings.index', array_merge(['carWash' => $carWash, 'week' => $weekStart->subWeek()->toDateString(), 'date' => $weekStart->subWeek()->toDateString()], request()->only('status', 'q'))) }}"><x-icon name="arrow-left" class="rotate-180"/> هفته قبل</a>
            <a class="btn-secondary px-3 py-2" href="{{ route('carwash.bookings.index', array_merge(['carWash' => $carWash, 'week' => $weekStart->addWeek()->toDateString(), 'date' => $weekStart->addWeek()->toDateString()], request()->only('status', 'q'))) }}">هفته بعد <x-icon name="arrow-left"/></a>
        </div>
    </div>
    <div class="flex gap-3 overflow-x-auto p-4">
        @foreach($weekDays as $day)
            <a
                href="{{ route('carwash.bookings.index', array_merge(['carWash' => $carWash, 'date' => $day['date_key'], 'week' => $weekStart->toDateString()], request()->only('status', 'q'))) }}"
                class="week-day-filter {{ $day['is_selected'] ? 'is-selected' : '' }} {{ $day['is_today'] ? 'is-today' : '' }}"
            >
                <span class="text-xs font-semibold {{ $day['is_selected'] ? 'text-white/80' : 'text-gray-500' }}">{{ $day['weekday'] }}</span>
                <span class="mt-1 whitespace-nowrap font-extrabold">{{ $day['persian_date'] }}</span>
                <span class="mt-2 rounded-full px-2 py-0.5 text-xs {{ $day['is_selected'] ? 'bg-white/20 text-white' : 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-200' }}">
                    {{ \App\Support\PersianDate::digits($day['count']) }} رزرو
                </span>
            </a>
        @endforeach
    </div>
</section>

<form class="panel-card mb-5 grid gap-3 p-4 md:grid-cols-2 xl:grid-cols-[1.2fr_1fr_1fr_1.2fr_auto]">
    <input type="hidden" name="week" value="{{ $weekStart->toDateString() }}">
    <x-persian-date-input name="date" label="تاریخ" :value="$selectedDate->toDateString()" required/>
    <div>
        <label class="form-label">اسلات زمانی</label>
        <select name="slot_id" class="form-select">
            <option value="">همه ساعت‌ها</option>
            @foreach($filterSlots as $slot)
                <option value="{{ $slot->id }}" @selected((int) request('slot_id') === $slot->id)>
                    {{ \App\Support\PersianDate::digits($slot->starts_at->timezone($carWash->timezone)->format('H:i')) }} تا {{ \App\Support\PersianDate::digits($slot->ends_at->timezone($carWash->timezone)->format('H:i')) }}
                </option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="form-label">وضعیت رزرو</label>
        <select name="status" class="form-select">
            <option value="">همه وضعیت‌ها</option>
            @foreach(\App\Enums\BookingStatus::cases() as $status)
                <option value="{{ $status->value }}" @selected(request('status') === $status->value)>{{ $status->label() }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="form-label">جست‌وجوی مشتری یا پلاک</label>
        <input name="q" value="{{ request('q') }}" class="form-control" placeholder="نام، موبایل، کد رزرو یا پلاک">
    </div>
    <div class="flex items-end gap-2">
        <button class="btn-primary flex-1"><x-icon name="search"/> فیلتر</button>
        <a href="{{ route('carwash.bookings.index', ['carWash' => $carWash]) }}" class="btn-secondary px-3" title="پاک‌کردن فیلتر"><x-icon name="close"/></a>
    </div>
</form>

<div class="mb-4 flex flex-wrap items-center justify-between gap-3">
    <div>
        <h2 class="text-lg font-extrabold text-gray-900 dark:text-white">صف {{ \App\Support\PersianDate::human($selectedDate, $carWash->timezone) }}</h2>
        <p class="mt-1 text-xs text-gray-500">{{ \App\Support\PersianDate::digits($bookings->total()) }} رزرو مطابق فیلتر</p>
    </div>
    <div class="flex flex-wrap gap-2">
        @foreach(\App\Enums\BookingStatus::cases() as $status)
            @php($count = $bookings->getCollection()->filter(fn ($booking) => $booking->status === $status)->count())
            @if($count)
                <span class="operational-chip"><x-status-badge :value="$status"/> {{ \App\Support\PersianDate::digits($count) }}</span>
            @endif
        @endforeach
    </div>
</div>

<div class="table-shell">
    <div class="overflow-x-auto">
        <table class="data-table">
            <thead><tr><th>ساعت</th><th>کد رزرو</th><th>مشتری</th><th>خودرو</th><th>خدمات</th><th>مبلغ</th><th>پرداخت</th><th>وضعیت</th><th></th></tr></thead>
            <tbody>
            @forelse($bookings as $booking)
                <tr>
                    <td>
                        <div class="font-extrabold text-gray-900 dark:text-white" dir="ltr">{{ \App\Support\PersianDate::digits($booking->slot?->starts_at?->timezone($carWash->timezone)->format('H:i')) }}</div>
                        <div class="mt-1 text-xs text-gray-500">تا {{ \App\Support\PersianDate::digits($booking->slot?->ends_at?->timezone($carWash->timezone)->format('H:i')) }}</div>
                    </td>
                    <td class="font-latin text-xs">{{ $booking->tracking_code }}</td>
                    <td><div class="font-semibold text-gray-900 dark:text-white">{{ $booking->customer_name }}</div><div class="text-xs text-gray-500" dir="ltr">{{ $booking->customer_mobile }}</div></td>
                    <td><div>{{ $booking->vehicle_type_snapshot ?: '—' }}</div><div class="mt-1 text-xs text-gray-500">{{ $booking->vehicle_plate_snapshot ?: 'بدون پلاک' }}</div></td>
                    <td class="max-w-xs"><div class="truncate">{{ $booking->items->pluck('service_name')->join('، ') }}</div><div class="mt-1 text-xs text-gray-500">{{ \App\Support\PersianDate::digits($booking->items->count()) }} خدمت</div></td>
                    <td>{{ \App\Support\PersianDate::digits(number_format($booking->payable_amount)) }} ریال</td>
                    <td><x-status-badge :value="$booking->payment_status"/></td>
                    <td><x-status-badge :value="$booking->status"/></td>
                    <td><a href="{{ route('carwash.bookings.show', [$carWash, $booking]) }}" class="rounded-xl p-2 text-primary hover:bg-primary-50"><x-icon name="eye"/></a></td>
                </tr>
            @empty
                <tr><td colspan="9"><x-empty-state title="رزروی برای این روز پیدا نشد" description="تاریخ یا فیلتر را تغییر دهید، یا یک رزرو حضوری ثبت کنید." icon="bookings"/></td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
<div class="mt-5">{{ $bookings->links() }}</div>

@can('carwash.bookings.create')
<section id="new-booking" class="panel-card mt-8 scroll-mt-28 overflow-hidden">
    <div class="border-b border-gray-100 bg-gradient-to-l from-primary-50 to-white px-5 py-5 dark:border-gray-700 dark:from-primary-950/30 dark:to-gray-800">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <h2 class="text-xl font-extrabold text-gray-900 dark:text-white">ثبت رزرو حضوری</h2>
                <p class="mt-1 text-sm text-gray-500">اطلاعات مشتری، خودرو، زمان و خدمات را در یک فرم سریع ثبت کنید.</p>
            </div>
            <span class="operational-chip"><x-icon name="check" class="h-4 w-4"/> قیمت در سرور محاسبه می‌شود</span>
        </div>
    </div>
    <form method="POST" action="{{ route('carwash.bookings.store', $carWash) }}" class="panel-card-body">
        @csrf
        <div class="grid gap-5 md:grid-cols-2 xl:grid-cols-4">
            <div><label class="form-label">نام و نام خانوادگی مشتری</label><input name="customer_name" value="{{ old('customer_name') }}" class="form-control" placeholder="مثلاً علی رضایی" required></div>
            <div><label class="form-label">شماره موبایل</label><input type="tel" name="customer_mobile" value="{{ old('customer_mobile') }}" class="form-control" dir="ltr" inputmode="tel" autocomplete="tel" placeholder="09xxxxxxxxx" required></div>
            <div><label class="form-label">نوع خودرو</label><select name="vehicle_type_id" class="form-select" required><option value="">انتخاب کنید</option>@foreach($vehicleTypes as $type)<option value="{{ $type->id }}" @selected(old('vehicle_type_id') == $type->id)>{{ $type->name }}</option>@endforeach</select></div>
            <div><label class="form-label">پلاک خودرو</label><input name="vehicle_plate" value="{{ old('vehicle_plate') }}" class="form-control" placeholder="اختیاری"></div>
        </div>

        <div class="mt-6">
            <div class="mb-3 flex items-center justify-between gap-3"><div><label class="form-label mb-0">زمان رزرو</label><p class="mt-1 text-xs text-gray-500">اسلات‌های آزاد آینده به ترتیب زمان نمایش داده شده‌اند.</p></div></div>
            <select name="booking_slot_id" class="form-select" required data-booking-slot-select>
                <option value="">یک زمان خالی انتخاب کنید</option>
                @foreach($slots->groupBy(fn($slot) => $slot->starts_at->timezone($carWash->timezone)->toDateString()) as $date => $dateSlots)
                    <optgroup label="{{ \App\Support\PersianDate::human($date, $carWash->timezone) }}">
                        @foreach($dateSlots as $slot)
                            <option value="{{ $slot->id }}" @selected((int) old('booking_slot_id') === $slot->id)>
                                {{ \App\Support\PersianDate::digits($slot->starts_at->timezone($carWash->timezone)->format('H:i')) }} تا {{ \App\Support\PersianDate::digits($slot->ends_at->timezone($carWash->timezone)->format('H:i')) }} · {{ \App\Support\PersianDate::digits($slot->capacity - $slot->reserved_count) }} ظرفیت خالی
                            </option>
                        @endforeach
                    </optgroup>
                @endforeach
            </select>
            @if($slots->isNotEmpty())
                <div class="mt-3 grid gap-3 sm:grid-cols-2 md:grid-cols-3 xl:grid-cols-6">
                    @foreach($slots->take(12) as $slot)
                        <button type="button" class="booking-slot-card {{ (int) old('booking_slot_id') === $slot->id ? 'is-selected' : '' }}" data-booking-slot-card data-slot-id="{{ $slot->id }}">
                            <span class="block text-xs text-gray-500">{{ \App\Support\PersianDate::short($slot->starts_at, $carWash->timezone) }}</span>
                            <span class="mt-1 block font-extrabold text-gray-900 dark:text-white" dir="ltr">{{ \App\Support\PersianDate::digits($slot->starts_at->timezone($carWash->timezone)->format('H:i')) }}–{{ \App\Support\PersianDate::digits($slot->ends_at->timezone($carWash->timezone)->format('H:i')) }}</span>
                            <span class="mt-2 block text-xs font-semibold text-emerald-600">{{ \App\Support\PersianDate::digits($slot->capacity - $slot->reserved_count) }} جای خالی</span>
                        </button>
                    @endforeach
                </div>
            @else
                <div class="mt-3 rounded-2xl bg-amber-50 p-4 text-sm text-amber-700 dark:bg-amber-900/20 dark:text-amber-300">اسلات آزادی وجود ندارد. ابتدا از بخش تقویم کاری، برنامه هفتگی را ذخیره و اسلات‌ها را بسازید.</div>
            @endif
        </div>

        <div class="mt-6">
            <label class="form-label">خدمات موردنیاز</label>
            <div class="grid gap-3 sm:grid-cols-2 xl:grid-cols-3">
                @foreach($services as $service)
                    <label class="flex cursor-pointer items-start gap-3 rounded-2xl border border-gray-200 p-4 hover:border-primary dark:border-gray-700">
                        <input type="checkbox" name="service_ids[]" value="{{ $service->id }}" @checked(in_array($service->id, old('service_ids', []))) class="mt-1 h-4 w-4 rounded border-gray-300 text-primary">
                        <span class="min-w-0">
                            <span class="block font-semibold text-gray-900 dark:text-white">{{ $service->name }}</span>
                            <span class="mt-1 block text-xs text-gray-500">از {{ \App\Support\PersianDate::digits(number_format($service->base_price)) }} ریال · حدود {{ \App\Support\PersianDate::digits($service->default_duration_minutes) }} دقیقه</span>
                        </span>
                    </label>
                @endforeach
            </div>
        </div>

        <div class="mt-5"><label class="form-label">توضیحات مشتری</label><textarea name="customer_note" rows="3" class="form-control" placeholder="توضیحات خاص، نوع آلودگی یا درخواست مشتری">{{ old('customer_note') }}</textarea></div>
        <div class="mt-6 flex justify-end"><button class="btn-primary px-7" @disabled($slots->isEmpty())><x-icon name="check"/> ثبت نهایی رزرو</button></div>
    </form>
</section>
@endcan
@endsection
