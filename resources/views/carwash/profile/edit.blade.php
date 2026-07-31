@extends('layouts.carwash')
@section('title', 'پروفایل کارواش')
@section('page-title', 'پروفایل عمومی کارواش')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-extrabold text-gray-900 dark:text-white">اطلاعات عمومی کارواش</h1>
    <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">اطلاعاتی که در لندینگ رزرو و نتایج جست‌وجوی مشتری نمایش داده می‌شود.</p>
</div>

<form method="POST" action="{{ route('carwash.profile.update', $carWash) }}" class="space-y-6">
    @csrf
    @method('PUT')

    <section class="panel-card">
        <div class="panel-card-header">
            <div>
                <h2 class="font-bold text-gray-900 dark:text-white">مشخصات اصلی</h2>
                <p class="mt-1 text-xs text-gray-500">نام، راه‌های تماس و آدرس عمومی</p>
            </div>
            <x-status-badge :value="$carWash->status"/>
        </div>
        <div class="panel-card-body grid gap-5 md:grid-cols-2">
            <div>
                <label class="form-label">نام کارواش</label>
                <input name="name" value="{{ old('name', $carWash->name) }}" class="form-control" required>
            </div>
            <div>
                <label class="form-label">شناسه URL (Slug)</label>
                <input name="slug" value="{{ old('slug', $carWash->slug) }}" class="form-control" dir="ltr" required>
                <p class="mt-1 text-xs text-gray-500">برای مثال: arya-carwash</p>
            </div>
            <div>
                <label class="form-label">تلفن ثابت</label>
                <input type="tel" inputmode="tel" autocomplete="tel" name="phone" value="{{ old('phone', $carWash->phone) }}" class="form-control" dir="ltr">
            </div>
            <div>
                <label class="form-label">شماره موبایل</label>
                <input type="tel" inputmode="tel" autocomplete="tel" name="mobile" value="{{ old('mobile', $carWash->mobile) }}" class="form-control" dir="ltr">
            </div>
            <div>
                <label class="form-label">ایمیل</label>
                <input type="email" name="email" value="{{ old('email', $carWash->email) }}" class="form-control" dir="ltr">
            </div>
            <div>
                <label class="form-label">کد پستی</label>
                <input inputmode="numeric" autocomplete="postal-code" name="postal_code" value="{{ old('postal_code', $carWash->postal_code) }}" class="form-control" dir="ltr">
            </div>
        </div>
    </section>

    <section class="panel-card">
        <div class="panel-card-header">
            <div>
                <h2 class="font-bold text-gray-900 dark:text-white">موقعیت جغرافیایی</h2>
                <p class="mt-1 text-xs text-gray-500">برای نمایش درست در لندینگ و نقشه</p>
            </div>
        </div>
        <div class="panel-card-body grid gap-5 md:grid-cols-2">
            <div>
                <label class="form-label">استان</label>
                <input name="province" value="{{ old('province', $carWash->province) }}" class="form-control">
            </div>
            <div>
                <label class="form-label">شهر</label>
                <input name="city" value="{{ old('city', $carWash->city) }}" class="form-control">
            </div>
            <div class="md:col-span-2">
                <label class="form-label">آدرس کامل</label>
                <textarea name="address" rows="3" class="form-control">{{ old('address', $carWash->address) }}</textarea>
            </div>
            <div>
                <label class="form-label">عرض جغرافیایی</label>
                <input type="number" step="0.0000001" name="latitude" value="{{ old('latitude', $carWash->latitude) }}" class="form-control" dir="ltr">
            </div>
            <div>
                <label class="form-label">طول جغرافیایی</label>
                <input type="number" step="0.0000001" name="longitude" value="{{ old('longitude', $carWash->longitude) }}" class="form-control" dir="ltr">
            </div>
        </div>
    </section>

    <section class="panel-card">
        <div class="panel-card-header"><h2 class="font-bold text-gray-900 dark:text-white">معرفی کارواش</h2></div>
        <div class="panel-card-body">
            <label class="form-label">توضیحات عمومی</label>
            <textarea name="description" rows="6" class="form-control" placeholder="خدمات، تجهیزات، مزایا و توضیحاتی که مشتری باید بداند...">{{ old('description', $carWash->description) }}</textarea>
        </div>
    </section>

    <div class="flex justify-end">
        <button class="btn-primary"><x-icon name="check"/> ذخیره پروفایل</button>
    </div>
</form>
@endsection
