@extends('layouts.admin')
@section('title', 'ایجاد کارواش')
@section('page-title', 'ایجاد کارواش')
@section('content')
<div class="mb-6 flex items-center justify-between gap-3">
    <div><h1 class="text-2xl font-extrabold text-gray-900 dark:text-white">ثبت کارواش جدید</h1><p class="mt-1 text-sm text-gray-500">اطلاعات مرکز و مالک اولیه را وارد کنید.</p></div>
    <a href="{{ route('admin.car-washes.index') }}" class="btn-secondary">بازگشت</a>
</div>
<form method="POST" action="{{ route('admin.car-washes.store') }}" class="panel-card">
    @csrf
    <div class="panel-card-header"><h2 class="font-bold">اطلاعات کارواش</h2></div>
    <div class="panel-card-body">
        @include('admin.car-washes._form')
        <div class="mt-6 flex justify-end gap-3">
            <a href="{{ route('admin.car-washes.index') }}" class="btn-secondary">انصراف</a>
            <button class="btn-primary">ایجاد کارواش</button>
        </div>
    </div>
</form>
@endsection
