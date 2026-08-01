@extends('layouts.carwash')
@section('title', 'رزروها')
@section('page-title', 'رزروها و صف کاری')

@section('content')
@php
    $timezone = $carWash->timezone ?: 'Asia/Tehran';
    $todayDate = now($timezone)->toDateString();
    $selectedDateKey = $selectedDate->toDateString();
    $previousWeekDate = $weekStart->subWeek()->toDateString();
    $nextWeekDate = $weekStart->addWeek()->toDateString();
    $preservedFilters = request()->only('status', 'q');
    $oldServices = array_map('intval', old('service_ids', []));
@endphp

<div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
    <div>
        <div class="mb-2 flex flex-wrap items-center gap-2">
            <span class="operational-chip"><x-icon name="clock" class="h-4 w-4"/> {{ \App\Support\PersianDate::human($selectedDate, $timezone) }}</span>
            @if($selectedDateKey === $todayDate)
                <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-100 px-3 py-1.5 text-xs font-extrabold text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300">
                    <span class="h-2 w-2 animate-pulse rounded-full bg-emerald-500"></span>
                    عملیات امروز
                </span>
            @endif
        </div>
        <h1 class="panel-page-heading">رزروها و صف کاری</h1>
        <p class="panel-page-description">صف روزانه را بر اساس ساعت و ظرفیت مدیریت کنید، وضعیت هر خودرو را سریع ببینید و رزرو حضوری ثبت کنید.</p>
    </div>

    <div class="flex w-full flex-wrap gap-2 sm:w-auto sm:justify-end">
        @if($selectedDateKey !== $todayDate)
            <a href="{{ route('carwash.bookings.index', ['carWash' => $carWash, 'date' => $todayDate]) }}" class="btn-secondary flex-1 sm:flex-none">
                <x-icon name="clock" class="h-4 w-4"/> رفتن به امروز
            </a>
        @endif
        @can('carwash.bookings.create')
            <a href="#new-booking" class="btn-primary flex-1 sm:flex-none" data-open-booking-form>
                <x-icon name="plus"/> ثبت رزرو حضوری
            </a>
        @endcan
    </div>
</div>

@if($summary['slot_count'] === 0)
    <div class="mb-5 flex flex-col gap-4 rounded-2xl border border-amber-200 bg-amber-50 p-4 text-amber-900 dark:border-amber-800 dark:bg-amber-950/30 dark:text-amber-200 sm:flex-row sm:items-center sm:justify-between">
        <div class="flex items-start gap-3">
            <span class="mt-0.5 rounded-xl bg-amber-100 p-2 dark:bg-amber-900/50"><x-icon name="schedule"/></span>
            <div>
                <div class="font-extrabold">برای این روز هیچ اسلات کاری ساخته نشده است</div>
                <p class="mt-1 text-sm leading-7 opacity-80">تا زمانی که برنامه هفتگی ذخیره و اسلات‌ها تولید نشوند، رزرو جدیدی برای این تاریخ قابل ثبت نیست.</p>
            </div>
        </div>
        @can('carwash.schedule.manage')
            <a href="{{ route('carwash.schedule.index', $carWash) }}" class="btn-secondary shrink-0">مدیریت تقویم کاری</a>
        @endcan
    </div>
@endif

<section class="panel-card mb-5 overflow-hidden">
    <div class="flex flex-col gap-3 border-b border-gray-100 px-4 py-4 dark:border-gray-700 sm:flex-row sm:items-center sm:justify-between sm:px-5">
        <div>
            <h2 class="font-extrabold text-gray-900 dark:text-white">تقویم هفتگی رزروها</h2>
            <p class="mt-1 text-xs text-gray-500">{{ \App\Support\PersianDate::short($weekStart, $timezone) }} تا {{ \App\Support\PersianDate::short($weekEnd, $timezone) }}</p>
        </div>
        <div class="grid grid-cols-2 gap-2 sm:flex">
            <a class="btn-secondary px-3 py-2" href="{{ route('carwash.bookings.index', array_merge(['carWash' => $carWash, 'week' => $previousWeekDate, 'date' => $previousWeekDate], $preservedFilters)) }}">
                <x-icon name="arrow-left" class="h-4 w-4 rotate-180"/> هفته قبل
            </a>
            <a class="btn-secondary px-3 py-2" href="{{ route('carwash.bookings.index', array_merge(['carWash' => $carWash, 'week' => $nextWeekDate, 'date' => $nextWeekDate], $preservedFilters)) }}">
                هفته بعد <x-icon name="arrow-left" class="h-4 w-4"/>
            </a>
        </div>
    </div>

    <div class="booking-week-scroll flex gap-2 overflow-x-auto p-3 sm:gap-3 sm:p-4" role="navigation" aria-label="روزهای هفته">
        @foreach($weekDays as $day)
            <a
                href="{{ route('carwash.bookings.index', array_merge(['carWash' => $carWash, 'date' => $day['date_key'], 'week' => $weekStart->toDateString()], $preservedFilters)) }}"
                class="week-day-filter {{ $day['is_selected'] ? 'is-selected' : '' }} {{ $day['is_today'] ? 'is-today' : '' }}"
                @if($day['is_selected']) aria-current="date" @endif
            >
                <span class="text-xs font-semibold {{ $day['is_selected'] ? 'text-white/80' : 'text-gray-500 dark:text-gray-400' }}">{{ $day['weekday'] }}</span>
                <span class="mt-1 whitespace-nowrap font-extrabold">{{ $day['persian_date'] }}</span>
                <span class="mt-2 rounded-full px-2.5 py-0.5 text-xs {{ $day['is_selected'] ? 'bg-white/20 text-white' : 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-200' }}">
                    {{ \App\Support\PersianDate::digits($day['count']) }} رزرو
                </span>
            </a>
        @endforeach
    </div>
</section>

<div class="mb-5 grid gap-3 sm:grid-cols-2 xl:grid-cols-5">
    <div class="booking-stat-card">
        <span class="booking-stat-icon bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300"><x-icon name="bookings"/></span>
        <div class="min-w-0"><p class="booking-stat-label">کل رزروهای روز</p><p class="booking-stat-value">{{ \App\Support\PersianDate::digits($summary['total']) }}</p><p class="booking-stat-hint">{{ \App\Support\PersianDate::digits($summary['active']) }} رزرو فعال</p></div>
    </div>
    <div class="booking-stat-card">
        <span class="booking-stat-icon bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-300"><x-icon name="clock"/></span>
        <div class="min-w-0"><p class="booking-stat-label">در انتظار و صف</p><p class="booking-stat-value">{{ \App\Support\PersianDate::digits($summary['waiting']) }}</p><p class="booking-stat-hint">{{ \App\Support\PersianDate::digits($summary['in_service']) }} خودرو در مجموعه</p></div>
    </div>
    <div class="booking-stat-card">
        <span class="booking-stat-icon bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300"><x-icon name="check"/></span>
        <div class="min-w-0"><p class="booking-stat-label">تکمیل‌شده</p><p class="booking-stat-value">{{ \App\Support\PersianDate::digits($summary['completed']) }}</p><p class="booking-stat-hint">{{ \App\Support\PersianDate::digits($summary['cancelled']) }} لغو یا عدم مراجعه</p></div>
    </div>
    <div class="booking-stat-card">
        <span class="booking-stat-icon bg-violet-100 text-violet-700 dark:bg-violet-900/30 dark:text-violet-300"><x-icon name="schedule"/></span>
        <div class="min-w-0 flex-1">
            <div class="flex items-center justify-between gap-2"><p class="booking-stat-label">اشغال ظرفیت</p><span class="text-xs font-extrabold text-violet-600">{{ \App\Support\PersianDate::digits($summary['occupancy_rate']) }}٪</span></div>
            <p class="booking-stat-value">{{ \App\Support\PersianDate::digits($summary['reserved_capacity']) }}<span class="mr-1 text-sm font-semibold text-gray-400">از {{ \App\Support\PersianDate::digits($summary['open_capacity']) }}</span></p>
            <div class="mt-2 h-1.5 overflow-hidden rounded-full bg-gray-100 dark:bg-gray-700"><span class="block h-full rounded-full bg-violet-500" style="width: {{ $summary['occupancy_rate'] }}%"></span></div>
        </div>
    </div>
    <div class="booking-stat-card sm:col-span-2 xl:col-span-1">
        <span class="booking-stat-icon bg-primary-100 text-primary-700 dark:bg-primary-900/30 dark:text-primary-300"><x-icon name="finance"/></span>
        <div class="min-w-0"><p class="booking-stat-label">مبلغ پرداخت‌شده</p><p class="booking-stat-value text-xl">{{ \App\Support\PersianDate::digits(number_format($summary['paid_amount'])) }}</p><p class="booking-stat-hint">ریال · {{ \App\Support\PersianDate::digits($summary['remaining_capacity']) }} ظرفیت باقی‌مانده</p></div>
    </div>
</div>

<details class="panel-card mb-5 overflow-hidden" @if($hasActiveFilters) open @endif data-filter-details>
    <summary class="flex cursor-pointer list-none items-center justify-between gap-3 px-4 py-4 sm:px-5">
        <div class="flex items-center gap-3">
            <span class="rounded-xl bg-gray-100 p-2 text-gray-600 dark:bg-gray-700 dark:text-gray-300"><x-icon name="search" class="h-4 w-4"/></span>
            <div>
                <h2 class="font-extrabold text-gray-900 dark:text-white">جست‌وجو و فیلتر صف</h2>
                <p class="mt-0.5 text-xs text-gray-500">نام، شماره موبایل، پلاک، ساعت یا وضعیت را محدود کنید.</p>
            </div>
        </div>
        <span class="filter-chevron text-gray-400"><x-icon name="arrow-left" class="h-4 w-4 -rotate-90"/></span>
    </summary>

    <form class="grid gap-4 border-t border-gray-100 p-4 dark:border-gray-700 md:grid-cols-2 xl:grid-cols-[1.1fr_1fr_1fr_1.35fr_auto] sm:p-5">
        <input type="hidden" name="week" value="{{ $weekStart->toDateString() }}">
        <x-persian-date-input name="date" label="تاریخ" :value="$selectedDateKey" required/>
        <div>
            <label class="form-label">اسلات زمانی</label>
            <select name="slot_id" class="form-select">
                <option value="">همه ساعت‌ها</option>
                @foreach($filterSlots as $slot)
                    <option value="{{ $slot->id }}" @selected((int) request('slot_id') === $slot->id)>
                        {{ \App\Support\PersianDate::digits($slot->starts_at->timezone($timezone)->format('H:i')) }} تا {{ \App\Support\PersianDate::digits($slot->ends_at->timezone($timezone)->format('H:i')) }} · {{ \App\Support\PersianDate::digits($slot->reserved_count) }}/{{ \App\Support\PersianDate::digits($slot->capacity) }}
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
            <input name="q" value="{{ request('q') }}" class="form-control" placeholder="نام، موبایل، کد رزرو یا پلاک" autocomplete="off">
        </div>
        <div class="flex items-end gap-2">
            <button class="btn-primary flex-1"><x-icon name="search"/> اعمال</button>
            @if($hasActiveFilters)
                <a href="{{ route('carwash.bookings.index', ['carWash' => $carWash, 'date' => $selectedDateKey, 'week' => $weekStart->toDateString()]) }}" class="btn-secondary px-3" title="پاک‌کردن فیلتر" aria-label="پاک‌کردن فیلتر"><x-icon name="close"/></a>
            @endif
        </div>
    </form>
</details>

<section class="mb-5" data-booking-views data-default-view="timeline">
    <div class="mb-4 flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <h2 class="text-lg font-extrabold text-gray-900 dark:text-white">صف {{ \App\Support\PersianDate::human($selectedDate, $timezone) }}</h2>
            <div class="mt-1 flex flex-wrap items-center gap-2 text-xs text-gray-500">
                <span>{{ \App\Support\PersianDate::digits($filteredBookings->count()) }} رزرو مطابق فیلتر</span>
                @if($hasActiveFilters)<span class="h-1 w-1 rounded-full bg-gray-300"></span><span class="font-bold text-primary">فیلتر فعال است</span>@endif
            </div>
        </div>

        <div class="booking-view-switcher" role="tablist" aria-label="نوع نمایش رزروها">
            <button type="button" class="booking-view-button is-active" data-booking-view-button="timeline" role="tab" aria-selected="true"><x-icon name="clock" class="h-4 w-4"/> صف زمانی</button>
            <button type="button" class="booking-view-button" data-booking-view-button="list" role="tab" aria-selected="false"><x-icon name="bookings" class="h-4 w-4"/> لیست فشرده</button>
        </div>
    </div>

    @if($filteredStatusCounts->isNotEmpty())
        <div class="mb-4 flex gap-2 overflow-x-auto pb-1">
            @foreach(\App\Enums\BookingStatus::cases() as $status)
                @php
                    $count = (int) $filteredStatusCounts->get($status->value, 0);
                @endphp
                @if($count > 0)
                    <span class="operational-chip shrink-0"><x-status-badge :value="$status"/> {{ \App\Support\PersianDate::digits($count) }}</span>
                @endif
            @endforeach
        </div>
    @endif

    <div data-booking-view-panel="timeline" role="tabpanel">
        <div class="space-y-3">
            @forelse($dailySlots as $slot)
                @php
                    $slotBookings = $timelineBookingsBySlot->get($slot->id, collect());
                    $slotPercent = $slot->capacity > 0 ? min(100, (int) round(($slot->reserved_count / $slot->capacity) * 100)) : 0;
                    $isCurrentSlot = (int) $currentSlotId === (int) $slot->id;
                    $slotStart = $slot->starts_at->timezone($timezone);
                    $slotEnd = $slot->ends_at->timezone($timezone);
                @endphp
                <article class="booking-timeline-slot {{ $isCurrentSlot ? 'is-current' : '' }} {{ $slot->status === 'closed' ? 'is-closed' : '' }}" @if($isCurrentSlot) aria-current="time" @endif>
                    <div class="booking-timeline-time">
                        @if($isCurrentSlot)<span class="booking-current-label">اکنون</span>@endif
                        <span class="time-value text-lg font-black text-gray-950 dark:text-white" dir="ltr">{{ \App\Support\PersianDate::digits($slotStart->format('H:i')) }}</span>
                        <span class="time-value text-xs text-gray-400" dir="ltr">تا {{ \App\Support\PersianDate::digits($slotEnd->format('H:i')) }}</span>
                    </div>

                    <div class="min-w-0 flex-1">
                        <div class="mb-3 flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                            <div class="flex flex-wrap items-center gap-2">
                                <x-status-badge :value="$slot->status"/>
                                <span class="text-xs font-bold text-gray-500">{{ \App\Support\PersianDate::digits($slot->reserved_count) }} رزرو از {{ \App\Support\PersianDate::digits($slot->capacity) }} ظرفیت</span>
                                @if($hasActiveFilters && $slotBookings->count() !== (int) $slot->reserved_count)
                                    <span class="text-xs text-primary">{{ \App\Support\PersianDate::digits($slotBookings->count()) }} مورد مطابق فیلتر</span>
                                @endif
                            </div>
                            <div class="flex items-center gap-2 sm:w-44">
                                <div class="h-1.5 flex-1 overflow-hidden rounded-full bg-gray-100 dark:bg-gray-700"><span class="block h-full rounded-full {{ $slotPercent >= 100 ? 'bg-red-500' : ($slotPercent >= 70 ? 'bg-amber-500' : 'bg-emerald-500') }}" style="width: {{ $slotPercent }}%"></span></div>
                                <span class="w-9 text-left text-xs font-extrabold text-gray-500">{{ \App\Support\PersianDate::digits($slotPercent) }}٪</span>
                            </div>
                        </div>

                        @if($slotBookings->isNotEmpty())
                            <div class="grid gap-2 xl:grid-cols-2 2xl:grid-cols-3">
                                @foreach($slotBookings as $booking)
                                    <div class="booking-queue-card group">
                                        <a href="{{ route('carwash.bookings.show', [$carWash, $booking]) }}" class="flex min-w-0 flex-1 items-start gap-3 rounded-lg focus-visible:ring-4 focus-visible:ring-primary/10">
                                            <span class="booking-queue-avatar">{{ mb_substr($booking->customer_name ?: 'م', 0, 1) }}</span>
                                            <div class="min-w-0 flex-1">
                                                <div class="flex flex-wrap items-center gap-2">
                                                    <span class="truncate font-extrabold text-gray-950 dark:text-white">{{ $booking->customer_name }}</span>
                                                    <x-status-badge :value="$booking->status"/>
                                                </div>
                                                <div class="mt-1 flex flex-wrap items-center gap-x-3 gap-y-1 text-xs text-gray-500">
                                                    <span dir="ltr" class="font-latin">{{ $booking->customer_mobile }}</span>
                                                    <span>{{ $booking->vehicle_type_snapshot ?: 'خودرو نامشخص' }}</span>
                                                    <span>{{ $booking->vehicle_plate_snapshot ?: 'بدون پلاک' }}</span>
                                                </div>
                                                <p class="mt-2 line-clamp-1 text-xs text-gray-500">{{ $booking->items->pluck('service_name')->join('، ') ?: 'بدون خدمت ثبت‌شده' }}</p>
                                            </div>
                                        </a>
                                        <div class="booking-queue-actions">
                                            <div class="flex items-center gap-2"><x-status-badge :value="$booking->payment_status"/><a href="{{ route('carwash.bookings.show', [$carWash, $booking]) }}" class="text-xs font-extrabold text-primary">جزئیات</a></div>

                                            @if($booking->status === \App\Enums\BookingStatus::PENDING)
                                                @can('carwash.bookings.confirm')
                                                    <form method="POST" action="{{ route('carwash.bookings.transition', [$carWash, $booking]) }}">
                                                        @csrf
                                                        <input type="hidden" name="status" value="confirmed">
                                                        <button class="booking-quick-action">تأیید رزرو</button>
                                                    </form>
                                                @endcan
                                            @elseif($booking->status === \App\Enums\BookingStatus::CONFIRMED)
                                                @can('carwash.bookings.check-in')
                                                    <form method="POST" action="{{ route('carwash.bookings.transition', [$carWash, $booking]) }}">
                                                        @csrf
                                                        <input type="hidden" name="status" value="checked_in">
                                                        <button class="booking-quick-action">پذیرش خودرو</button>
                                                    </form>
                                                @endcan
                                            @elseif($booking->status === \App\Enums\BookingStatus::CHECKED_IN)
                                                @can('carwash.bookings.start')
                                                    <form method="POST" action="{{ route('carwash.bookings.transition', [$carWash, $booking]) }}">
                                                        @csrf
                                                        <input type="hidden" name="status" value="in_progress">
                                                        <button class="booking-quick-action">شروع شست‌وشو</button>
                                                    </form>
                                                @endcan
                                            @elseif($booking->status === \App\Enums\BookingStatus::IN_PROGRESS)
                                                @can('carwash.bookings.complete')
                                                    <form method="POST" action="{{ route('carwash.bookings.transition', [$carWash, $booking]) }}">
                                                        @csrf
                                                        <input type="hidden" name="status" value="completed">
                                                        <button class="booking-quick-action is-success" data-confirm="وضعیت این خودرو به تکمیل‌شده تغییر کند؟">تکمیل و تحویل</button>
                                                    </form>
                                                @endcan
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="booking-empty-slot">
                                @if($slot->status === 'closed')
                                    این بازه بسته است.
                                @elseif($hasActiveFilters && $slot->reserved_count > 0)
                                    رزروی در این اسلات وجود دارد، اما با فیلتر فعلی مطابقت ندارد.
                                @else
                                    این بازه هنوز رزروی ندارد و {{ \App\Support\PersianDate::digits(max(0, $slot->capacity - $slot->reserved_count)) }} ظرفیت خالی دارد.
                                @endif
                            </div>
                        @endif
                    </div>
                </article>
            @empty
                <div class="panel-card p-6"><x-empty-state title="اسلاتی برای این روز وجود ندارد" description="از بخش تقویم کاری، ساعات فعالیت و ظرفیت روز را تنظیم کنید." icon="schedule"/></div>
            @endforelse
        </div>
    </div>

    <div class="hidden" data-booking-view-panel="list" role="tabpanel" aria-hidden="true">
        <div class="hidden md:block">
            <div class="table-shell">
                <table class="data-table booking-list-table">
                    <thead><tr><th>ساعت</th><th>مشتری</th><th>خودرو</th><th>خدمات</th><th>مبلغ</th><th>پرداخت</th><th>وضعیت</th><th><span class="sr-only">عملیات</span></th></tr></thead>
                    <tbody>
                    @forelse($bookings as $booking)
                        <tr>
                            <td>
                                <div class="time-value font-extrabold text-gray-900 dark:text-white" dir="ltr">{{ \App\Support\PersianDate::digits($booking->slot?->starts_at?->timezone($timezone)->format('H:i')) }}</div>
                                <div class="time-value mt-1 text-xs text-gray-500" dir="ltr">{{ \App\Support\PersianDate::digits($booking->slot?->ends_at?->timezone($timezone)->format('H:i')) }}</div>
                            </td>
                            <td>
                                <div class="font-semibold text-gray-900 dark:text-white">{{ $booking->customer_name }}</div>
                                <div class="mt-1 text-xs text-gray-500 font-latin" dir="ltr">{{ $booking->customer_mobile }}</div>
                                <div class="mt-1 text-[11px] text-gray-400 font-latin" dir="ltr">{{ $booking->tracking_code }}</div>
                            </td>
                            <td><div>{{ $booking->vehicle_type_snapshot ?: '—' }}</div><div class="mt-1 text-xs text-gray-500">{{ $booking->vehicle_plate_snapshot ?: 'بدون پلاک' }}</div></td>
                            <td class="max-w-xs"><div class="max-w-64 truncate">{{ $booking->items->pluck('service_name')->join('، ') ?: '—' }}</div><div class="mt-1 text-xs text-gray-500">{{ \App\Support\PersianDate::digits($booking->items->count()) }} خدمت</div></td>
                            <td class="money-value font-bold">{{ \App\Support\PersianDate::digits(number_format($booking->payable_amount)) }} <span class="text-xs font-normal text-gray-400">ریال</span></td>
                            <td><x-status-badge :value="$booking->payment_status"/></td>
                            <td><x-status-badge :value="$booking->status"/></td>
                            <td><a href="{{ route('carwash.bookings.show', [$carWash, $booking]) }}" class="inline-flex rounded-xl p-2.5 text-primary hover:bg-primary-50 dark:hover:bg-primary-950/30" aria-label="مشاهده رزرو {{ $booking->tracking_code }}"><x-icon name="eye"/></a></td>
                        </tr>
                    @empty
                        <tr><td colspan="8"><x-empty-state title="رزروی برای این روز پیدا نشد" description="تاریخ یا فیلتر را تغییر دهید، یا یک رزرو حضوری ثبت کنید." icon="bookings"/></td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="grid gap-3 md:hidden">
            @forelse($bookings as $booking)
                <a href="{{ route('carwash.bookings.show', [$carWash, $booking]) }}" class="panel-card block p-4 active:scale-[.99]">
                    <div class="flex items-start justify-between gap-3">
                        <div class="flex min-w-0 items-start gap-3">
                            <span class="booking-queue-avatar">{{ mb_substr($booking->customer_name ?: 'م', 0, 1) }}</span>
                            <div class="min-w-0"><div class="truncate font-extrabold text-gray-950 dark:text-white">{{ $booking->customer_name }}</div><div class="mt-1 text-xs text-gray-500 font-latin" dir="ltr">{{ $booking->customer_mobile }}</div></div>
                        </div>
                        <div class="shrink-0 text-left"><div class="time-value text-lg font-black text-primary" dir="ltr">{{ \App\Support\PersianDate::digits($booking->slot?->starts_at?->timezone($timezone)->format('H:i')) }}</div><div class="mt-1"><x-status-badge :value="$booking->status"/></div></div>
                    </div>
                    <div class="mt-4 grid grid-cols-2 gap-3 rounded-xl bg-gray-50 p-3 text-xs dark:bg-gray-700/50">
                        <div><span class="block text-gray-400">خودرو</span><span class="mt-1 block font-bold">{{ $booking->vehicle_type_snapshot ?: 'نامشخص' }} · {{ $booking->vehicle_plate_snapshot ?: 'بدون پلاک' }}</span></div>
                        <div><span class="block text-gray-400">مبلغ</span><span class="money-value mt-1 block font-bold">{{ \App\Support\PersianDate::digits(number_format($booking->payable_amount)) }} ریال</span></div>
                    </div>
                    <div class="mt-3 flex items-center justify-between gap-3"><span class="truncate text-xs text-gray-500">{{ $booking->items->pluck('service_name')->join('، ') }}</span><x-status-badge :value="$booking->payment_status"/></div>
                </a>
            @empty
                <div class="panel-card p-6"><x-empty-state title="رزروی پیدا نشد" description="فیلترها را تغییر دهید یا رزرو جدیدی ثبت کنید." icon="bookings"/></div>
            @endforelse
        </div>

        @if($bookings->hasPages())
            <div class="mt-5">{{ $bookings->links() }}</div>
        @endif
    </div>
</section>

@can('carwash.bookings.create')
<details id="new-booking" class="panel-card mt-8 scroll-mt-28 overflow-hidden" @if($errors->any()) open @endif data-booking-form-details>
    <summary class="flex cursor-pointer list-none items-center justify-between gap-4 bg-gradient-to-l from-primary-50 to-white px-4 py-5 dark:from-primary-950/30 dark:to-gray-800 sm:px-6">
        <div class="flex min-w-0 items-center gap-3">
            <span class="bg-primary-grad flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl text-white shadow-lg shadow-primary/20"><x-icon name="plus"/></span>
            <div class="min-w-0">
                <h2 class="text-lg font-extrabold text-gray-900 dark:text-white sm:text-xl">ثبت رزرو حضوری یا تلفنی</h2>
                <p class="mt-1 text-xs leading-6 text-gray-500 sm:text-sm">اطلاعات مشتری، زمان و خدمات را وارد کنید؛ مبلغ نهایی در سرور محاسبه می‌شود.</p>
            </div>
        </div>
        <span class="booking-form-chevron shrink-0 rounded-xl border border-primary/20 bg-white p-2 text-primary dark:bg-gray-800"><x-icon name="arrow-left" class="h-4 w-4 -rotate-90"/></span>
    </summary>

    <form method="POST" action="{{ route('carwash.bookings.store', $carWash) }}" class="border-t border-gray-100 dark:border-gray-700" data-booking-create-form>
        @csrf
        <div class="p-4 sm:p-6">
            <div class="booking-form-section">
                <div class="booking-form-section-number">۱</div>
                <div class="min-w-0 flex-1">
                    <div class="mb-4"><h3 class="font-extrabold text-gray-950 dark:text-white">مشخصات مشتری و خودرو</h3><p class="mt-1 text-xs text-gray-500">فیلدهای ستاره‌دار برای ثبت رزرو الزامی‌اند.</p></div>
                    <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
                        <div><label class="form-label">نام و نام خانوادگی <span class="text-red-500">*</span></label><input name="customer_name" value="{{ old('customer_name') }}" class="form-control" placeholder="مثلاً علی رضایی" autocomplete="name" required aria-invalid="{{ $errors->has('customer_name') ? 'true' : 'false' }}">@error('customer_name')<span class="form-error">{{ $message }}</span>@enderror</div>
                        <div><label class="form-label">شماره موبایل <span class="text-red-500">*</span></label><input type="tel" name="customer_mobile" value="{{ old('customer_mobile') }}" class="form-control text-left" dir="ltr" inputmode="tel" autocomplete="tel" placeholder="09123456789" maxlength="13" required aria-invalid="{{ $errors->has('customer_mobile') ? 'true' : 'false' }}">@error('customer_mobile')<span class="form-error">{{ $message }}</span>@enderror</div>
                        <div><label class="form-label">نوع خودرو <span class="text-red-500">*</span></label><select name="vehicle_type_id" class="form-select" required data-booking-vehicle-type aria-invalid="{{ $errors->has('vehicle_type_id') ? 'true' : 'false' }}"><option value="">انتخاب کنید</option>@foreach($vehicleTypes as $type)<option value="{{ $type->id }}" @selected((int) old('vehicle_type_id') === $type->id)>{{ $type->name }}</option>@endforeach</select>@error('vehicle_type_id')<span class="form-error">{{ $message }}</span>@enderror</div>
                        <div><label class="form-label">پلاک خودرو</label><input name="vehicle_plate" value="{{ old('vehicle_plate') }}" class="form-control" placeholder="مثلاً ۱۲ ب ۳۴۵ ایران ۶۷" autocomplete="off">@error('vehicle_plate')<span class="form-error">{{ $message }}</span>@enderror</div>
                    </div>
                </div>
            </div>

            <div class="booking-form-section mt-7 border-t border-gray-100 pt-7 dark:border-gray-700">
                <div class="booking-form-section-number">۲</div>
                <div class="min-w-0 flex-1">
                    <div class="mb-4 flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between"><div><h3 class="font-extrabold text-gray-950 dark:text-white">انتخاب زمان مراجعه</h3><p class="mt-1 text-xs text-gray-500">فقط اسلات‌های آزاد و قابل رزرو نمایش داده می‌شوند.</p></div><span class="text-xs font-bold text-emerald-600">{{ \App\Support\PersianDate::digits($slots->count()) }} اسلات آزاد آینده</span></div>
                    <select name="booking_slot_id" class="form-select" required data-booking-slot-select aria-invalid="{{ $errors->has('booking_slot_id') ? 'true' : 'false' }}">
                        <option value="">یک زمان خالی انتخاب کنید</option>
                        @foreach($slots->groupBy(fn($slot) => $slot->starts_at->timezone($timezone)->toDateString()) as $date => $dateSlots)
                            <optgroup label="{{ \App\Support\PersianDate::human($date, $timezone) }}">
                                @foreach($dateSlots as $slot)
                                    <option value="{{ $slot->id }}" @selected((int) old('booking_slot_id') === $slot->id)>
                                        {{ \App\Support\PersianDate::digits($slot->starts_at->timezone($timezone)->format('H:i')) }} تا {{ \App\Support\PersianDate::digits($slot->ends_at->timezone($timezone)->format('H:i')) }} · {{ \App\Support\PersianDate::digits(max(0, $slot->capacity - $slot->reserved_count)) }} ظرفیت خالی
                                    </option>
                                @endforeach
                            </optgroup>
                        @endforeach
                    </select>
                    @error('booking_slot_id')<span class="form-error">{{ $message }}</span>@enderror

                    @if($slots->isNotEmpty())
                        <div class="mt-3 grid gap-2 sm:grid-cols-2 md:grid-cols-3 xl:grid-cols-6" role="listbox" aria-label="اسلات‌های پیشنهادی">
                            @foreach($slots->take(18) as $slot)
                                <button type="button" class="booking-slot-card {{ (int) old('booking_slot_id') === $slot->id ? 'is-selected' : '' }}" data-booking-slot-card data-slot-id="{{ $slot->id }}">
                                    <span class="block text-xs text-gray-500">{{ \App\Support\PersianDate::short($slot->starts_at, $timezone) }}</span>
                                    <span class="time-value mt-1 block font-extrabold text-gray-900 dark:text-white" dir="ltr">{{ \App\Support\PersianDate::digits($slot->starts_at->timezone($timezone)->format('H:i')) }}–{{ \App\Support\PersianDate::digits($slot->ends_at->timezone($timezone)->format('H:i')) }}</span>
                                    <span class="mt-2 block text-xs font-semibold text-emerald-600">{{ \App\Support\PersianDate::digits(max(0, $slot->capacity - $slot->reserved_count)) }} جای خالی</span>
                                </button>
                            @endforeach
                        </div>
                    @else
                        <div class="mt-3 rounded-2xl bg-amber-50 p-4 text-sm leading-7 text-amber-700 dark:bg-amber-900/20 dark:text-amber-300">اسلات آزادی وجود ندارد. ابتدا تقویم کاری و ظرفیت‌ها را بررسی کنید.</div>
                    @endif
                </div>
            </div>

            <div class="booking-form-section mt-7 border-t border-gray-100 pt-7 dark:border-gray-700">
                <div class="booking-form-section-number">۳</div>
                <div class="min-w-0 flex-1">
                    <div class="mb-4 flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between"><div><h3 class="font-extrabold text-gray-950 dark:text-white">خدمات موردنیاز</h3><p class="mt-1 text-xs text-gray-500">یک یا چند خدمت انتخاب کنید. قیمت با نوع خودرو تطبیق داده می‌شود.</p></div><div class="text-sm font-extrabold text-primary" data-booking-estimate>برآورد اولیه: ۰ ریال</div></div>
                    <div class="grid gap-3 sm:grid-cols-2 xl:grid-cols-3">
                        @forelse($services as $service)
                            @php
                                $priceMap = $service->vehiclePrices->mapWithKeys(fn ($price) => [(string) $price->vehicle_type_id => (int) $price->price]);
                                $isServiceChecked = in_array((int) $service->id, $oldServices, true);
                            @endphp
                            <label class="booking-service-option {{ $isServiceChecked ? 'is-selected' : '' }}" data-booking-service-option data-base-price="{{ (int) $service->base_price }}" data-price-map="{{ e($priceMap->toJson()) }}">
                                <input type="checkbox" name="service_ids[]" value="{{ $service->id }}" @checked($isServiceChecked) class="mt-1 h-5 w-5 shrink-0 rounded border-gray-300 text-primary" data-booking-service-checkbox>
                                <span class="min-w-0 flex-1">
                                    <span class="flex items-start justify-between gap-2"><span class="block font-extrabold text-gray-900 dark:text-white">{{ $service->name }}</span><span class="booking-service-check"><x-icon name="check" class="h-3.5 w-3.5"/></span></span>
                                    <span class="mt-1 block text-xs leading-6 text-gray-500">از {{ \App\Support\PersianDate::digits(number_format($service->base_price)) }} ریال · حدود {{ \App\Support\PersianDate::digits($service->default_duration_minutes) }} دقیقه</span>
                                </span>
                            </label>
                        @empty
                            <div class="sm:col-span-2 xl:col-span-3 rounded-2xl bg-amber-50 p-4 text-sm text-amber-700 dark:bg-amber-900/20 dark:text-amber-300">خدمت فعالی تعریف نشده است. ابتدا از بخش خدمات، حداقل یک خدمت ایجاد کنید.</div>
                        @endforelse
                    </div>
                    @error('service_ids')<span class="form-error">{{ $message }}</span>@enderror
                </div>
            </div>

            <div class="mt-7 border-t border-gray-100 pt-6 dark:border-gray-700">
                <label class="form-label">توضیحات مشتری یا نکته عملیاتی</label>
                <textarea name="customer_note" rows="3" class="form-control" placeholder="مثلاً خودرو گل‌آلود است، مشتری عجله دارد یا درخواست خاصی مطرح کرده است">{{ old('customer_note') }}</textarea>
                @error('customer_note')<span class="form-error">{{ $message }}</span>@enderror
            </div>
        </div>

        <div class="booking-form-submit-bar">
            <div class="hidden text-xs leading-6 text-gray-500 sm:block"><span class="font-extrabold text-gray-700 dark:text-gray-200">پیش از ثبت:</span> شماره موبایل، زمان و حداقل یک خدمت را بررسی کنید.</div>
            <button class="btn-primary w-full px-7 sm:w-auto" @disabled($slots->isEmpty() || $services->isEmpty())><x-icon name="check"/> ثبت نهایی رزرو</button>
        </div>
    </form>
</details>
@endcan
@endsection
