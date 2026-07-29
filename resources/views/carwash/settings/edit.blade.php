@extends('layouts.panel')
@section('title', 'تنظیمات رزرو')
@section('navigation') @include('carwash.partials.navigation') @endsection
@section('content')
<h1 class="mb-6 text-2xl font-bold">تنظیمات رزرو</h1>
<form method="POST" action="{{ route('carwash.settings.update', $carWash) }}" class="grid gap-4 rounded-2xl bg-white p-6 shadow-sm md:grid-cols-2">
@csrf @method('PUT')
<label>فاصله اسلات‌ها<select name="booking_interval_minutes" class="mt-1 w-full rounded-xl border p-3">@foreach([15,30,45,60,90,120] as $v)<option value="{{ $v }}" @selected(old('booking_interval_minutes',$settings->booking_interval_minutes)==$v)>{{ $v }} دقیقه</option>@endforeach</select></label>
@foreach(['minimum_booking_notice_minutes'=>'حداقل فاصله رزرو تا مراجعه (دقیقه)','maximum_booking_days_ahead'=>'حداکثر روز قابل رزرو','cancellation_deadline_minutes'=>'مهلت لغو (دقیقه)','default_capacity'=>'ظرفیت پیش‌فرض'] as $field=>$label)
<label>{{ $label }}<input type="number" class="mt-1 w-full rounded-xl border p-3" name="{{ $field }}" value="{{ old($field,$settings->$field) }}"></label>
@endforeach
<div class="md:col-span-2 grid gap-3 sm:grid-cols-2">
@foreach(['auto_confirm_booking'=>'تایید خودکار رزرو','allow_guest_booking'=>'اجازه رزرو مهمان','require_online_payment'=>'پرداخت آنلاین اجباری','send_sms_notifications'=>'ارسال پیامک'] as $field=>$label)
<label class="flex items-center gap-3 rounded-xl border p-3"><input type="hidden" name="{{ $field }}" value="0"><input type="checkbox" name="{{ $field }}" value="1" @checked(old($field,$settings->$field))><span>{{ $label }}</span></label>
@endforeach
</div><div class="md:col-span-2"><button class="rounded-xl bg-slate-900 px-5 py-3 text-white">ذخیره تنظیمات</button></div>
</form>
@endsection
