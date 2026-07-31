@extends('layouts.admin')
@section('title', 'مدیریت کارواش‌ها')
@section('page-title', 'کارواش‌ها')
@section('content')
<div class="mb-6 flex flex-wrap items-center justify-between gap-3">
    <div><h1 class="text-2xl font-extrabold text-gray-900 dark:text-white">مدیریت کارواش‌ها</h1><p class="mt-1 text-sm text-gray-500">ثبت، تأیید و مدیریت تمام مراکز</p></div>
    <a class="btn-primary" href="{{ route('admin.car-washes.create') }}"><x-icon name="plus"/> ایجاد کارواش</a>
</div>

<form method="GET" class="panel-card mb-5 grid gap-3 p-4 md:grid-cols-[minmax(0,1fr)_220px_auto]">
    <div class="relative">
        <x-icon name="search" class="absolute right-3 top-1/2 h-5 w-5 -translate-y-1/2 text-gray-400"/>
        <input name="q" value="{{ request('q') }}" class="form-control pr-10" placeholder="جست‌وجو با نام یا کد">
    </div>
    <select name="status" class="form-select">
        <option value="">همه وضعیت‌ها</option>
        @foreach(['pending'=>'در انتظار','active'=>'فعال','suspended'=>'تعلیق‌شده','rejected'=>'ردشده'] as $value=>$label)
            <option value="{{ $value }}" @selected(request('status') === $value)>{{ $label }}</option>
        @endforeach
    </select>
    <button class="btn-primary">اعمال فیلتر</button>
</form>

<div class="table-shell">
    <div class="overflow-x-auto">
        <table class="data-table">
            <thead><tr><th>کارواش</th><th>کد</th><th>موقعیت</th><th>وضعیت</th><th>تاریخ ثبت</th><th>عملیات</th></tr></thead>
            <tbody>
            @forelse($carWashes as $wash)
                <tr>
                    <td><div class="flex items-center gap-3"><span class="flex h-10 w-10 items-center justify-center rounded-xl bg-primary-50 text-primary dark:bg-primary-900/30"><x-icon name="carwash"/></span><div><div class="font-semibold text-gray-900 dark:text-white">{{ $wash->name }}</div><div class="text-xs text-gray-500">{{ $wash->mobile ?: $wash->phone }}</div></div></div></td>
                    <td class="font-latin text-xs">{{ $wash->code }}</td>
                    <td>{{ collect([$wash->province,$wash->city])->filter()->join('، ') ?: '—' }}</td>
                    <td><x-status-badge :value="$wash->status"/></td>
                    <td>{{ \App\Support\PersianDate::date($wash->created_at, $wash->timezone) }}</td>
                    <td><div class="flex gap-2"><a href="{{ route('admin.car-washes.show',$wash) }}" class="rounded-lg p-2 text-blue-600 hover:bg-blue-50"><x-icon name="eye"/></a><a href="{{ route('admin.car-washes.edit',$wash) }}" class="rounded-lg p-2 text-amber-600 hover:bg-amber-50"><x-icon name="edit"/></a></div></td>
                </tr>
            @empty
                <tr><td colspan="6"><x-empty-state title="کارواشی ثبت نشده است" description="اولین کارواش را ایجاد کنید." icon="carwash"><a href="{{ route('admin.car-washes.create') }}" class="btn-primary">ایجاد کارواش</a></x-empty-state></td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
<div class="mt-5">{{ $carWashes->links() }}</div>
@endsection
