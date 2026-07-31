@extends('layouts.carwash')
@section('title', 'تقویم کاری و ظرفیت')
@section('page-title', 'تقویم کاری و ظرفیت')

@section('content')
<div class="mb-6 flex flex-wrap items-start justify-between gap-4">
    <div>
        <h1 class="panel-page-heading">تقویم کاری و ظرفیت رزرو</h1>
        <p class="panel-page-description">برنامه هفتگی را یک‌بار تنظیم کنید؛ سیستم اسلات‌ها را برای روزهای آینده می‌سازد و در اختیار سایت رزرو قرار می‌دهد.</p>
    </div>
    <div class="flex flex-wrap items-center gap-2">
        <span class="operational-chip"><x-icon name="clock" class="h-4 w-4"/> منطقه زمانی: تهران</span>
        @can('carwash.slots.regenerate')
            <form method="POST" action="{{ route('carwash.schedule.regenerate', $carWash) }}">
                @csrf
                <button class="btn-secondary"><x-icon name="schedule"/> همگام‌سازی اسلات‌ها</button>
            </form>
        @endcan
    </div>
</div>

<section class="mb-7 overflow-hidden rounded-3xl bg-secondary text-white shadow-xl shadow-secondary/10">
    <div class="grid gap-6 p-6 lg:grid-cols-[1fr_auto] lg:items-center">
        <div>
            <div class="text-sm font-semibold text-primary-200">راهنمای سریع</div>
            <h2 class="mt-2 text-xl font-extrabold">مثال: از ۹ تا ۱۰ با اسلات ۳۰ دقیقه‌ای</h2>
            <p class="mt-2 max-w-3xl text-sm leading-7 text-gray-300">سیستم دو اسلات ۹:۰۰ تا ۹:۳۰ و ۹:۳۰ تا ۱۰:۰۰ می‌سازد. برای هر کدام می‌توانید ظرفیت متفاوت، مثلاً ۲ و ۳ خودرو، تعیین کنید.</p>
        </div>
        <div class="grid grid-cols-2 gap-2 text-center text-sm">
            <div class="rounded-2xl bg-white/10 px-5 py-3"><div class="font-extrabold" dir="ltr">۹:۰۰–۹:۳۰</div><div class="mt-1 text-xs text-gray-300">ظرفیت ۲ خودرو</div></div>
            <div class="rounded-2xl bg-white/10 px-5 py-3"><div class="font-extrabold" dir="ltr">۹:۳۰–۱۰:۰۰</div><div class="mt-1 text-xs text-gray-300">ظرفیت ۳ خودرو</div></div>
        </div>
    </div>
</section>

@can('carwash.schedule.manage')
<form method="POST" action="{{ route('carwash.schedule.weekly.save', $carWash) }}" class="mb-8">
    @csrf
    <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
        <div>
            <h2 class="text-xl font-extrabold text-gray-900 dark:text-white">برنامه ثابت هفتگی</h2>
            <p class="mt-1 text-sm text-gray-500">روزهای کاری را فعال کنید و ظرفیت هر بازه را جداگانه مشخص کنید.</p>
        </div>
        <button class="btn-primary"><x-icon name="check"/> ذخیره و ساخت اسلات‌ها</button>
    </div>

    <div class="space-y-4">
        @foreach($weekdayOrder as $weekday)
            @php
                $rule = $rulesByDay->get($weekday)?->first();
                $enabled = (bool) old("days.$weekday.enabled", $rule ? 1 : 0);
                $startTime = old("days.$weekday.start_time", $rule ? substr($rule->start_time, 0, 5) : '09:00');
                $endTime = old("days.$weekday.end_time", $rule ? substr($rule->end_time, 0, 5) : '18:00');
                $duration = (int) old("days.$weekday.slot_duration_minutes", $rule?->slot_duration_minutes ?: 60);
                $capacity = (int) old("days.$weekday.capacity", $rule?->capacity ?: ($carWash->setting?->default_capacity ?? 1));
                $slotCapacities = old("days.$weekday.slot_capacities", $rule?->slot_capacities ?: []);
            @endphp
            <article
                class="panel-card overflow-hidden"
                data-weekly-day
                data-weekday="{{ $weekday }}"
                data-initial-capacities="{{ e(json_encode($slotCapacities, JSON_UNESCAPED_UNICODE)) }}"
            >
                <div class="flex flex-wrap items-center justify-between gap-4 border-b border-gray-100 px-5 py-4 dark:border-gray-700">
                    <div class="flex items-center gap-3">
                        <label class="relative inline-flex cursor-pointer items-center">
                            <input type="hidden" name="days[{{ $weekday }}][enabled]" value="0">
                            <input type="checkbox" name="days[{{ $weekday }}][enabled]" value="1" class="peer sr-only" data-day-enabled @checked($enabled)>
                            <span class="h-7 w-12 rounded-full bg-gray-200 after:absolute after:right-1 after:top-1 after:h-5 after:w-5 after:rounded-full after:bg-white after:shadow after:transition-all peer-checked:bg-primary peer-checked:after:-translate-x-5 dark:bg-gray-700"></span>
                        </label>
                        <div>
                            <h3 class="font-extrabold text-gray-900 dark:text-white">{{ $weekdayLabels[$weekday] }}</h3>
                            <p class="text-xs text-gray-500" data-day-state>{{ $enabled ? 'روز کاری' : 'تعطیل' }}</p>
                        </div>
                    </div>
                    <button type="button" class="text-sm font-semibold text-primary hover:underline" data-copy-day>کپی این تنظیمات برای همه روزها</button>
                </div>

                <div class="p-5" data-day-content>
                    <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-5">
                        <div>
                            <label class="form-label">ساعت شروع</label>
                            <input type="time" name="days[{{ $weekday }}][start_time]" value="{{ $startTime }}" class="form-control" data-day-control data-day-start>
                        </div>
                        <div>
                            <label class="form-label">ساعت پایان</label>
                            <input type="time" name="days[{{ $weekday }}][end_time]" value="{{ $endTime }}" class="form-control" data-day-control data-day-end>
                        </div>
                        <div>
                            <label class="form-label">مدت هر اسلات</label>
                            <select name="days[{{ $weekday }}][slot_duration_minutes]" class="form-select" data-day-control data-day-duration>
                                <option value="30" @selected($duration === 30)>۳۰ دقیقه</option>
                                <option value="60" @selected($duration === 60)>۶۰ دقیقه</option>
                            </select>
                        </div>
                        <div>
                            <label class="form-label">ظرفیت پیش‌فرض</label>
                            <input type="number" min="1" max="100" name="days[{{ $weekday }}][capacity]" value="{{ $capacity }}" class="form-control" data-day-control data-day-capacity>
                        </div>
                        <div class="flex items-end">
                            <button type="button" class="btn-secondary w-full" data-apply-default>اعمال ظرفیت به همه اسلات‌ها</button>
                        </div>
                    </div>

                    <div class="mt-5">
                        <div class="mb-3 flex items-center justify-between gap-3">
                            <div>
                                <h4 class="text-sm font-bold text-gray-800 dark:text-gray-100">ظرفیت تک‌تک اسلات‌ها</h4>
                                <p class="mt-1 text-xs text-gray-500">در ساعت‌های شلوغ ظرفیت را کمتر یا بیشتر کنید.</p>
                            </div>
                        </div>
                        <div class="grid gap-3 sm:grid-cols-2 md:grid-cols-3 xl:grid-cols-6" data-day-slots></div>
                    </div>
                </div>
            </article>
        @endforeach
    </div>

    <div class="sticky bottom-4 z-20 mt-5 flex justify-end">
        <button class="btn-primary px-6 shadow-xl shadow-primary/20"><x-icon name="check"/> ذخیره برنامه هفتگی</button>
    </div>
</form>
@endcan

<section class="mb-8">
    <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
        <div>
            <h2 class="text-xl font-extrabold text-gray-900 dark:text-white">نمای عملیاتی هفته</h2>
            <p class="mt-1 text-sm text-gray-500">رزرو و ظرفیت واقعی اسلات‌های ساخته‌شده را برای هر روز ببینید.</p>
        </div>
        <div class="flex items-center gap-2">
            <a class="btn-secondary px-3" href="{{ route('carwash.schedule.index', ['carWash' => $carWash, 'week' => $weekStart->subWeek()->toDateString()]) }}"><x-icon name="arrow-left" class="rotate-180"/> هفته قبل</a>
            <span class="rounded-xl bg-white px-4 py-2.5 text-sm font-bold shadow-sm dark:bg-gray-800">
                {{ \App\Support\PersianDate::short($weekStart, $carWash->timezone) }} تا {{ \App\Support\PersianDate::short($weekEnd, $carWash->timezone) }}
            </span>
            <a class="btn-secondary px-3" href="{{ route('carwash.schedule.index', ['carWash' => $carWash, 'week' => $weekStart->addWeek()->toDateString()]) }}">هفته بعد <x-icon name="arrow-left"/></a>
        </div>
    </div>

    <div class="grid gap-4 lg:grid-cols-2 xl:grid-cols-4">
        @foreach($weekDays as $day)
            <article class="panel-card overflow-hidden {{ $day['is_today'] ? 'ring-2 ring-primary/20' : '' }}">
                <div class="flex items-center justify-between border-b border-gray-100 px-4 py-3 dark:border-gray-700">
                    <div>
                        <div class="font-extrabold text-gray-900 dark:text-white">{{ $day['label'] }}</div>
                        <div class="mt-1 text-xs text-gray-500">{{ $day['persian_date'] }}</div>
                    </div>
                    <span class="rounded-full bg-gray-100 px-2.5 py-1 text-xs font-bold text-gray-600 dark:bg-gray-700 dark:text-gray-200">{{ \App\Support\PersianDate::digits($day['slots']->count()) }} اسلات</span>
                </div>
                <div class="max-h-[420px] space-y-2 overflow-y-auto p-3">
                    @forelse($day['slots'] as $slot)
                        <form method="POST" action="{{ route('carwash.schedule.slots.update', [$carWash, $slot]) }}" class="rounded-2xl border border-gray-100 bg-gray-50 p-3 dark:border-gray-700 dark:bg-gray-900/30">
                            @csrf
                            @method('PUT')
                            <div class="flex items-center justify-between gap-2">
                                <div>
                                    <div class="font-extrabold text-gray-900 dark:text-white" dir="ltr">
                                        {{ \App\Support\PersianDate::digits($slot->starts_at->timezone($carWash->timezone)->format('H:i')) }}–{{ \App\Support\PersianDate::digits($slot->ends_at->timezone($carWash->timezone)->format('H:i')) }}
                                    </div>
                                    <div class="mt-1 text-xs text-gray-500">{{ \App\Support\PersianDate::digits($slot->reserved_count) }} رزرو از {{ \App\Support\PersianDate::digits($slot->capacity) }} ظرفیت</div>
                                </div>
                                <x-status-badge :value="$slot->status"/>
                            </div>
                            @can('carwash.schedule.manage')
                                <div class="mt-3 grid grid-cols-[1fr_1fr_auto] gap-2">
                                    <input type="number" min="{{ max(1, $slot->reserved_count) }}" max="100" name="capacity" value="{{ $slot->capacity }}" class="form-control py-2" aria-label="ظرفیت">
                                    <select name="status" class="form-select py-2" aria-label="وضعیت">
                                        <option value="open" @selected($slot->status !== 'closed')>باز</option>
                                        <option value="closed" @selected($slot->status === 'closed') @disabled($slot->reserved_count > 0)>بسته</option>
                                    </select>
                                    <button class="rounded-xl bg-white px-3 text-primary shadow-sm hover:bg-primary-50 dark:bg-gray-700" title="ذخیره"><x-icon name="check"/></button>
                                </div>
                            @endcan
                        </form>
                    @empty
                        <div class="rounded-2xl border border-dashed border-gray-200 px-4 py-8 text-center text-sm text-gray-400 dark:border-gray-700">اسلاتی برای این روز ساخته نشده است.</div>
                    @endforelse
                </div>
            </article>
        @endforeach
    </div>
</section>

@can('carwash.schedule.manage')
<section class="panel-card mb-7">
    <div class="panel-card-header">
        <div>
            <h2 class="font-extrabold text-gray-900 dark:text-white">تعطیلی یا ظرفیت ویژه</h2>
            <p class="mt-1 text-xs text-gray-500">برای تعطیلات رسمی، تعمیرات یا افزایش ظرفیت یک روز خاص استفاده کنید.</p>
        </div>
    </div>
    <form method="POST" action="{{ route('carwash.schedule.exceptions.store', $carWash) }}" class="panel-card-body grid gap-4 md:grid-cols-2 xl:grid-cols-6">
        @csrf
        <x-persian-date-input name="exception_date" label="تاریخ شمسی" :value="old('exception_date')" required/>
        <div><label class="form-label">شروع بازه</label><input type="time" name="start_time" value="{{ old('start_time') }}" class="form-control"></div>
        <div><label class="form-label">پایان بازه</label><input type="time" name="end_time" value="{{ old('end_time') }}" class="form-control"></div>
        <div><label class="form-label">ظرفیت جایگزین</label><input type="number" name="capacity_override" value="{{ old('capacity_override') }}" class="form-control" min="1" max="100"></div>
        <label class="flex items-center gap-3 rounded-xl border border-gray-200 px-4 py-3 dark:border-gray-700">
            <input type="hidden" name="is_closed" value="0">
            <input type="checkbox" name="is_closed" value="1" class="h-4 w-4 rounded text-primary" @checked(old('is_closed'))>
            <span class="text-sm font-semibold">تعطیل / بسته</span>
        </label>
        <div class="flex items-end"><button class="btn-primary w-full">ثبت استثنا</button></div>
        <div class="md:col-span-2 xl:col-span-6"><label class="form-label">دلیل یا توضیح</label><input name="reason" value="{{ old('reason') }}" class="form-control" placeholder="مثلاً تعطیلات رسمی یا تعمیر تجهیزات"></div>
    </form>
</section>
@endcan

<section class="panel-card">
    <div class="panel-card-header"><h2 class="font-extrabold">استثناهای ثبت‌شده</h2><span class="text-sm text-gray-500">آخرین {{ \App\Support\PersianDate::digits($exceptions->count()) }} مورد</span></div>
    <div class="divide-y divide-gray-100 dark:divide-gray-700">
        @forelse($exceptions as $exception)
            <div class="flex flex-wrap items-center justify-between gap-3 p-4">
                <div>
                    <div class="flex items-center gap-2">
                        <span class="font-bold"><x-persian-date :value="$exception->exception_date" :timezone="$carWash->timezone" human/></span>
                        <x-status-badge :value="$exception->is_closed ? 'closed' : 'active'"/>
                    </div>
                    <div class="mt-1 text-sm text-gray-500">
                        {{ $exception->is_closed ? 'تعطیلی' : 'ظرفیت '.\App\Support\PersianDate::digits($exception->capacity_override).' خودرو' }}
                        @if($exception->start_time)
                            · {{ \App\Support\PersianDate::digits(substr($exception->start_time, 0, 5)) }} تا {{ \App\Support\PersianDate::digits(substr($exception->end_time, 0, 5)) }}
                        @else
                            · کل روز
                        @endif
                        @if($exception->reason) · {{ $exception->reason }} @endif
                    </div>
                </div>
                @can('carwash.schedule.manage')
                    <form method="POST" action="{{ route('carwash.schedule.exceptions.destroy', [$carWash, $exception]) }}">
                        @csrf
                        @method('DELETE')
                        <button data-confirm="این استثنا حذف شود؟" class="rounded-xl p-2 text-red-600 hover:bg-red-50"><x-icon name="trash"/></button>
                    </form>
                @endcan
            </div>
        @empty
            <x-empty-state title="استثنایی ثبت نشده است" description="تعطیلی‌ها و ظرفیت‌های خاص در این بخش نمایش داده می‌شوند." icon="schedule"/>
        @endforelse
    </div>
</section>
@endsection
