@extends('layouts.admin')
@section('title', 'گزارش فعالیت‌ها')
@section('page-title', 'لاگ‌های مدیریتی')
@section('content')
<div class="mb-6"><h1 class="text-2xl font-extrabold text-gray-900 dark:text-white">گزارش فعالیت‌ها</h1><p class="mt-1 text-sm text-gray-500">تغییرات حساس و عملیات مدیران سامانه</p></div>
<form class="panel-card mb-5 grid gap-3 p-4 sm:grid-cols-[minmax(0,1fr)_auto]"><input name="q" value="{{ request('q') }}" class="form-control" placeholder="نام عملیات یا نوع موجودیت"><button class="btn-primary">جست‌وجو</button></form>
<div class="table-shell"><div class="overflow-x-auto"><table class="data-table">
<thead><tr><th>زمان</th><th>انجام‌دهنده</th><th>کارواش</th><th>عملیات</th><th>موجودیت</th><th>IP</th></tr></thead>
<tbody>@forelse($logs as $log)<tr>
<td>{{ \App\Support\PersianDate::dateTime($log->created_at, $log->carWash?->timezone ?? 'Asia/Tehran') }}</td><td>{{ $log->actor?->full_name ?: $log->actor?->mobile ?: 'سیستم' }}</td><td>{{ $log->carWash?->name ?: '—' }}</td><td class="font-latin text-xs">{{ $log->action }}</td><td class="font-latin text-xs">{{ class_basename($log->subject_type) }} #{{ $log->subject_id }}</td><td dir="ltr">{{ $log->ip_address ?: '—' }}</td>
</tr>@empty<tr><td colspan="6"><x-empty-state title="لاگی ثبت نشده است" icon="audit"/></td></tr>@endforelse</tbody>
</table></div></div><div class="mt-5">{{ $logs->links() }}</div>
@endsection
