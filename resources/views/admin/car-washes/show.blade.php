@extends('layouts.panel')
@section('title', 'جزئیات کارواش')
@section('navigation')
<a class="block rounded-lg p-2 hover:bg-white/10" href="{{ route('admin.dashboard') }}">داشبورد</a>
<a class="block rounded-lg p-2 hover:bg-white/10" href="{{ route('admin.car-washes.index') }}">کارواش‌ها</a>
<a class="block rounded-lg p-2 hover:bg-white/10" href="{{ route('admin.users.index') }}">کاربران</a>
@endsection
@section('content')
<div class="mb-6 flex flex-wrap items-start justify-between gap-3">
    <div>
        <h1 class="text-2xl font-bold">{{ $carWash->name }}</h1>
        <p class="text-slate-500">{{ $carWash->code }} - {{ $carWash->status->value }}</p>
    </div>
    @can('platform.car-washes.update')
        <a class="rounded-xl bg-blue-600 px-5 py-3 text-white" href="{{ route('admin.car-washes.edit', $carWash) }}">ویرایش</a>
    @endcan
</div>

<div class="grid gap-5 lg:grid-cols-2">
    <div class="rounded-2xl bg-white p-5 shadow-sm">
        <h2 class="mb-3 font-bold">اطلاعات</h2>
        <p>{{ $carWash->city }} - {{ $carWash->address }}</p>
        <p>{{ $carWash->mobile }} / {{ $carWash->phone }}</p>
        <p>اعضا: {{ $carWash->members->count() }}</p>
        @if($carWash->status->value === 'active')
            <a class="mt-4 inline-block text-blue-600" href="{{ route('carwash.dashboard', $carWash) }}">ورود به پنل کارواش</a>
        @endif
    </div>

    <div class="rounded-2xl bg-white p-5 shadow-sm">
        <h2 class="mb-3 font-bold">تغییر وضعیت</h2>
        <form method="POST" action="{{ route('admin.car-washes.status', $carWash) }}" class="space-y-3">
            @csrf
            <select name="status" class="w-full rounded-xl border p-3">
                @foreach(['pending','active','suspended','rejected'] as $status)
                    <option value="{{ $status }}" @selected($carWash->status->value === $status)>{{ $status }}</option>
                @endforeach
            </select>
            <textarea name="reason" class="w-full rounded-xl border p-3" placeholder="دلیل">{{ old('reason', $carWash->suspension_reason) }}</textarea>
            <button class="rounded-xl bg-slate-900 px-5 py-3 text-white">ثبت وضعیت</button>
        </form>
    </div>
</div>
@endsection
