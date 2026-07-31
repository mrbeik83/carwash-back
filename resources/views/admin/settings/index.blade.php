@extends('layouts.admin')
@section('title', 'تنظیمات سامانه')
@section('page-title', 'تنظیمات سامانه')
@section('content')
@php($value = fn($key,$default='') => data_get($settings->get($key)?->value, 'value', $default))
<div class="mb-6"><h1 class="text-2xl font-extrabold text-gray-900 dark:text-white">تنظیمات عمومی</h1><p class="mt-1 text-sm text-gray-500">اطلاعات پایه و راه‌های ارتباطی پلتفرم</p></div>
<form method="POST" action="{{ route('admin.settings.update') }}" class="panel-card">
@csrf @method('PUT')
<div class="panel-card-header"><h2 class="font-bold">اطلاعات سامانه</h2></div>
<div class="panel-card-body grid gap-5 md:grid-cols-2">
<div><label class="form-label">نام پلتفرم</label><input name="platform_name" value="{{ old('platform_name',$value('platform_name',config('app.name'))) }}" class="form-control" required></div>
<div><label class="form-label">واحد پول پیش‌فرض</label><input name="default_currency" value="{{ old('default_currency',$value('default_currency','IRR')) }}" class="form-control" dir="ltr" maxlength="3" required></div>
<div><label class="form-label">موبایل پشتیبانی</label><input type="tel" inputmode="tel" autocomplete="tel" name="support_mobile" value="{{ old('support_mobile',$value('support_mobile')) }}" class="form-control" dir="ltr"></div>
<div><label class="form-label">ایمیل پشتیبانی</label><input type="email" name="support_email" value="{{ old('support_email',$value('support_email')) }}" class="form-control" dir="ltr"></div>
<div class="md:col-span-2"><label class="form-label">پیام نگهداری یا اطلاع‌رسانی</label><textarea name="maintenance_message" rows="4" class="form-control">{{ old('maintenance_message',$value('maintenance_message')) }}</textarea></div>
<div class="md:col-span-2 flex justify-end"><button class="btn-primary">ذخیره تنظیمات</button></div>
</div></form>
@endsection
