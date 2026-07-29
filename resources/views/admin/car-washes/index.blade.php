@extends('layouts.panel')
@section('title', 'کارواش‌ها')
@section('navigation')
<a class="block rounded-lg p-2 hover:bg-white/10" href="{{ route('admin.dashboard') }}">داشبورد</a>
<a class="block rounded-lg p-2 hover:bg-white/10" href="{{ route('admin.car-washes.index') }}">کارواش‌ها</a>
<a class="block rounded-lg p-2 hover:bg-white/10" href="{{ route('admin.users.index') }}">کاربران</a>
@endsection
@section('content')
<div class="mb-6 flex flex-wrap items-center justify-between gap-3">
    <h1 class="text-2xl font-bold">مدیریت کارواش‌ها</h1>
    @can('platform.car-washes.create')
        <a class="rounded-xl bg-slate-900 px-5 py-3 text-white" href="{{ route('admin.car-washes.create') }}">ایجاد کارواش</a>
    @endcan
</div>

<form class="mb-5 flex flex-wrap gap-3">
    <input name="q" value="{{ request('q') }}" class="rounded-xl border p-3" placeholder="نام یا کد">
    <select name="status" class="rounded-xl border p-3">
        <option value="">همه</option>
        @foreach(['pending','active','suspended','rejected'] as $status)
            <option value="{{ $status }}" @selected(request('status') === $status)>{{ $status }}</option>
        @endforeach
    </select>
    <button class="rounded-xl bg-slate-900 px-5 text-white">فیلتر</button>
</form>

<div class="overflow-x-auto rounded-2xl bg-white shadow-sm">
    <table class="w-full">
        <thead class="bg-slate-100">
        <tr>
            <th class="p-3">نام</th>
            <th>کد</th>
            <th>شهر</th>
            <th>وضعیت</th>
            <th>تاریخ</th>
        </tr>
        </thead>
        <tbody>
        @forelse($carWashes as $wash)
            <tr class="border-t">
                <td class="p-3">
                    <a class="text-blue-600" href="{{ route('admin.car-washes.show', $wash) }}">{{ $wash->name }}</a>
                </td>
                <td>{{ $wash->code }}</td>
                <td>{{ $wash->city }}</td>
                <td>{{ $wash->status->value }}</td>
                <td>{{ $wash->created_at }}</td>
            </tr>
        @empty
            <tr><td colspan="5" class="p-6 text-center text-slate-500">کارواشی ثبت نشده است.</td></tr>
        @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">{{ $carWashes->links() }}</div>
@endsection
