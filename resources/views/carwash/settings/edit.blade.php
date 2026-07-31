@extends('layouts.carwash')
@section('title', 'تنظیمات رزرو')
@section('page-title', 'تنظیمات رزرو')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-extrabold text-gray-900 dark:text-white">تنظیمات رزرو</h1>
    <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">قواعد عمومی تولید اسلات، محدودیت رزرو و رفتار تأیید را تنظیم کنید.</p>
</div>

<form method="POST" action="{{ route('carwash.settings.update', $carWash) }}" class="space-y-6">
    @csrf
    @method('PUT')

    <section class="panel-card">
        <div class="panel-card-header">
            <div>
                <h2 class="font-bold text-gray-900 dark:text-white">قواعد زمانی و ظرفیت</h2>
                <p class="mt-1 text-xs text-gray-500">این تنظیمات روی رزروهای جدید اعمال می‌شوند.</p>
            </div>
        </div>
        <div class="panel-card-body grid gap-5 md:grid-cols-2">
            <div>
                <label class="form-label">فاصله اسلات‌ها</label>
                <select name="booking_interval_minutes" class="form-select">
                    @foreach([30, 60] as $value)
                        <option value="{{ $value }}" @selected((int) old('booking_interval_minutes', $settings->booking_interval_minutes) === $value)>
                            {{ $value }} دقیقه
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="form-label">ظرفیت پیش‌فرض هر اسلات</label>
                <input type="number" min="1" max="100" name="default_capacity" value="{{ old('default_capacity', $settings->default_capacity) }}" class="form-control">
            </div>
            <div>
                <label class="form-label">حداقل فاصله رزرو تا مراجعه (دقیقه)</label>
                <input type="number" min="0" name="minimum_booking_notice_minutes" value="{{ old('minimum_booking_notice_minutes', $settings->minimum_booking_notice_minutes) }}" class="form-control">
            </div>
            <div>
                <label class="form-label">حداکثر روز قابل رزرو در آینده</label>
                <input type="number" min="1" max="365" name="maximum_booking_days_ahead" value="{{ old('maximum_booking_days_ahead', $settings->maximum_booking_days_ahead) }}" class="form-control">
            </div>
            <div>
                <label class="form-label">مهلت لغو قبل از مراجعه (دقیقه)</label>
                <input type="number" min="0" name="cancellation_deadline_minutes" value="{{ old('cancellation_deadline_minutes', $settings->cancellation_deadline_minutes) }}" class="form-control">
            </div>
        </div>
    </section>

    <section class="panel-card">
        <div class="panel-card-header">
            <div>
                <h2 class="font-bold text-gray-900 dark:text-white">رفتار سیستم رزرو</h2>
                <p class="mt-1 text-xs text-gray-500">گزینه‌های عملیاتی و اطلاع‌رسانی</p>
            </div>
        </div>
        <div class="panel-card-body grid gap-4 sm:grid-cols-2">
            @foreach([
                'auto_confirm_booking' => ['تأیید خودکار رزرو', 'رزرو پس از ثبت مستقیماً تأیید شود.'],
                'allow_guest_booking' => ['رزرو مهمان', 'مشتری بدون تکمیل کامل حساب بتواند رزرو کند.'],
                'require_online_payment' => ['پرداخت آنلاین اجباری', 'تأیید رزرو به پرداخت آنلاین وابسته باشد.'],
                'send_sms_notifications' => ['ارسال پیامک', 'رویدادهای رزرو برای مشتری پیامک شوند.'],
            ] as $field => [$label, $help])
                <label class="flex cursor-pointer items-start gap-3 rounded-2xl border border-gray-200 p-4 hover:border-primary/50 dark:border-gray-700">
                    <input type="hidden" name="{{ $field }}" value="0">
                    <input type="checkbox" name="{{ $field }}" value="1" class="mt-1 h-4 w-4 rounded border-gray-300 text-primary focus:ring-primary" @checked((bool) old($field, $settings->$field))>
                    <span>
                        <span class="block font-semibold text-gray-900 dark:text-white">{{ $label }}</span>
                        <span class="mt-1 block text-xs leading-6 text-gray-500">{{ $help }}</span>
                    </span>
                </label>
            @endforeach
        </div>
    </section>

    <div class="flex justify-end">
        <button class="btn-primary"><x-icon name="check"/> ذخیره تنظیمات</button>
    </div>
</form>
@endsection
