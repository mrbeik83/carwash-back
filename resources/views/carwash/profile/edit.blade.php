@extends('layouts.panel')
@section('title', 'پروفایل کارواش')
@section('navigation') @include('carwash.partials.navigation') @endsection
@section('content')
<h1 class="mb-6 text-2xl font-bold">پروفایل عمومی کارواش</h1>
<form method="POST" action="{{ route('carwash.profile.update', $carWash) }}" class="grid gap-4 rounded-2xl bg-white p-6 shadow-sm md:grid-cols-2">
@csrf @method('PUT')
@foreach(['name'=>'نام کارواش','slug'=>'آدرس انگلیسی','phone'=>'تلفن','mobile'=>'موبایل','email'=>'ایمیل','province'=>'استان','city'=>'شهر','postal_code'=>'کد پستی','latitude'=>'عرض جغرافیایی','longitude'=>'طول جغرافیایی'] as $field=>$label)
<label class="block"><span class="mb-1 block text-sm text-slate-600">{{ $label }}</span><input class="w-full rounded-xl border p-3" name="{{ $field }}" value="{{ old($field, $carWash->$field) }}"></label>
@endforeach
<label class="block md:col-span-2"><span class="mb-1 block text-sm text-slate-600">آدرس</span><textarea class="w-full rounded-xl border p-3" name="address" rows="3">{{ old('address', $carWash->address) }}</textarea></label>
<label class="block md:col-span-2"><span class="mb-1 block text-sm text-slate-600">توضیحات عمومی</span><textarea class="w-full rounded-xl border p-3" name="description" rows="5">{{ old('description', $carWash->description) }}</textarea></label>
<div class="md:col-span-2"><button class="rounded-xl bg-slate-900 px-5 py-3 text-white">ذخیره پروفایل</button></div>
</form>
@endsection
