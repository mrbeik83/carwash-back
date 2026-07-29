@extends('layouts.panel')
@section('title', 'ویرایش کارواش')
@section('navigation')
<a class="block rounded-lg p-2 hover:bg-white/10" href="{{ route('admin.dashboard') }}">داشبورد</a>
<a class="block rounded-lg p-2 hover:bg-white/10" href="{{ route('admin.car-washes.index') }}">کارواش‌ها</a>
@endsection
@section('content')
<h1 class="mb-6 text-2xl font-bold">ویرایش {{ $carWash->name }}</h1>
<form method="POST" action="{{ route('admin.car-washes.update', $carWash) }}" class="rounded-2xl bg-white p-6 shadow-sm">
    @csrf
    @method('PUT')
    @include('admin.car-washes._form')
    <button class="mt-6 rounded-xl bg-slate-900 px-5 py-3 text-white">ذخیره تغییرات</button>
</form>
@endsection
