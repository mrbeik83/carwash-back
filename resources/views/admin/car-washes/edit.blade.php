@extends('layouts.admin')
@section('title', 'ویرایش کارواش')
@section('page-title', 'ویرایش کارواش')
@section('content')
<div class="mb-6 flex items-center justify-between gap-3">
    <div><h1 class="text-2xl font-extrabold text-gray-900 dark:text-white">ویرایش {{ $carWash->name }}</h1><p class="mt-1 text-sm text-gray-500">{{ $carWash->code }}</p></div>
    <a href="{{ route('admin.car-washes.show', $carWash) }}" class="btn-secondary">بازگشت</a>
</div>
<form method="POST" action="{{ route('admin.car-washes.update', $carWash) }}" class="panel-card">
    @csrf @method('PUT')
    <div class="panel-card-header"><h2 class="font-bold">اطلاعات پایه</h2></div>
    <div class="panel-card-body">
        @include('admin.car-washes._form')
        <div class="mt-6 flex justify-end gap-3">
            <a href="{{ route('admin.car-washes.show', $carWash) }}" class="btn-secondary">انصراف</a>
            <button class="btn-primary">ذخیره تغییرات</button>
        </div>
    </div>
</form>
@endsection
