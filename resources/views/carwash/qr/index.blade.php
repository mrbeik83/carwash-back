@extends('layouts.carwash')
@section('title', 'QR و کمپین‌ها')
@section('page-title', 'QR و لینک‌های رهگیری')

@section('content')
<div class="mb-6 flex flex-wrap items-start justify-between gap-4">
    <div>
        <h1 class="text-2xl font-extrabold text-gray-900 dark:text-white">لینک‌های QR</h1>
        <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">برای کانتر، تراکت یا کمپین‌های تبلیغاتی لینک اختصاصی بسازید و تعداد اسکن را ببینید.</p>
    </div>
</div>

@can('carwash.qr.manage')
<section class="panel-card mb-6">
    <div class="panel-card-header">
        <div>
            <h2 class="font-bold text-gray-900 dark:text-white">ساخت لینک جدید</h2>
            <p class="mt-1 text-xs text-gray-500">توکن امن به‌صورت خودکار ساخته می‌شود.</p>
        </div>
    </div>
    <form method="POST" action="{{ route('carwash.qr.store', $carWash) }}" class="panel-card-body grid gap-4 md:grid-cols-2 xl:grid-cols-5">
        @csrf
        <div>
            <label class="form-label">عنوان</label>
            <input name="title" value="{{ old('title') }}" class="form-control" placeholder="مثلاً QR کانتر" required>
        </div>
        <div>
            <label class="form-label">نوع لینک</label>
            <select name="type" class="form-select" required>
                <option value="booking">رزرو مستقیم</option>
                <option value="campaign">کمپین تبلیغاتی</option>
                <option value="counter">کانتر پذیرش</option>
            </select>
        </div>
        <div>
            <label class="form-label">نام کمپین</label>
            <input name="campaign" value="{{ old('campaign') }}" class="form-control" placeholder="اختیاری">
        </div>
        <x-persian-date-input name="expires_at_date" label="تاریخ انقضا" :value="old('expires_at_date')" placeholder="بدون انقضا"/>
        <div>
            <label class="form-label">ساعت انقضا</label>
            <input type="time" name="expires_at_time" value="{{ old('expires_at_time', '23:59') }}" class="form-control">
        </div>
        <div class="md:col-span-2 xl:col-span-5">
            <button class="btn-primary"><x-icon name="plus"/> ساخت لینک QR</button>
        </div>
    </form>
</section>
@endcan

<div class="grid gap-5 lg:grid-cols-2">
    @forelse($links as $link)
        @php
            $type = $link->type instanceof \BackedEnum ? $link->type->value : $link->type;
            $url = rtrim(config('carwash.frontend_url'), '/').'/book/'.$carWash->slug.'?ref='.$link->token;
        @endphp
        <article class="panel-card">
            <div class="panel-card-header">
                <div class="flex items-center gap-3">
                    <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-primary-50 text-primary-700 dark:bg-primary-950/40 dark:text-primary-300">
                        <x-icon name="qr"/>
                    </span>
                    <div>
                        <h2 class="font-bold text-gray-900 dark:text-white">{{ $link->title }}</h2>
                        <div class="mt-1 text-xs text-gray-500">{{ ['booking'=>'رزرو مستقیم','campaign'=>'کمپین','counter'=>'کانتر'][$type] ?? $type }}</div>
                    </div>
                </div>
                <x-status-badge :value="$link->is_active ? 'active' : 'inactive'"/>
            </div>
            <div class="panel-card-body space-y-4">
                <div class="grid grid-cols-2 gap-3">
                    <div class="rounded-xl bg-gray-50 p-4 dark:bg-gray-700/50">
                        <div class="text-xs text-gray-500">تعداد اسکن</div>
                        <div class="mt-1 text-2xl font-extrabold text-gray-900 dark:text-white">{{ number_format($link->scans_count) }}</div>
                    </div>
                    <div class="rounded-xl bg-gray-50 p-4 dark:bg-gray-700/50">
                        <div class="text-xs text-gray-500">کمپین</div>
                        <div class="mt-1 truncate font-semibold text-gray-900 dark:text-white">{{ $link->campaign ?: 'بدون کمپین' }}</div>
                    </div>
                </div>
                <div>
                    <label class="form-label">لینک رزرو</label>
                    <input readonly value="{{ $url }}" class="form-control" dir="ltr">
                </div>
                <div class="flex flex-wrap items-center justify-between gap-3 text-xs text-gray-500">
                    <span>انقضا: {{ $link->expires_at ? \App\Support\PersianDate::human($link->expires_at, $carWash->timezone, true) : 'بدون انقضا' }}</span>
                    @can('carwash.qr.manage')
                        @if($link->is_active)
                            <form method="POST" action="{{ route('carwash.qr.destroy', [$carWash, $link]) }}">
                                @csrf
                                @method('DELETE')
                                <button class="text-red-600 hover:underline" data-confirm="این لینک غیرفعال شود؟">غیرفعال‌کردن</button>
                            </form>
                        @endif
                    @endcan
                </div>
            </div>
        </article>
    @empty
        <div class="panel-card lg:col-span-2">
            <x-empty-state title="هنوز لینک QR ساخته نشده است" description="برای شروع، یک لینک رزرو مستقیم یا کمپین بسازید." icon="qr"/>
        </div>
    @endforelse
</div>

@if($links->hasPages())
    <div class="mt-6">{{ $links->links() }}</div>
@endif
@endsection
