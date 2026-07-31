@extends('layouts.admin')
@section('title', 'کاربران')
@section('page-title', 'مدیریت کاربران')
@section('content')
<div class="mb-6"><h1 class="text-2xl font-extrabold text-gray-900 dark:text-white">کاربران سامانه</h1><p class="mt-1 text-sm text-gray-500">مشتریان، کارکنان کارواش و حساب‌های مدیریتی</p></div>
<form class="panel-card mb-5 grid gap-3 p-4 sm:grid-cols-[minmax(0,1fr)_auto]">
    <div class="relative flex-1"><x-icon name="search" class="absolute right-3 top-1/2 h-5 w-5 -translate-y-1/2 text-gray-400"/><input name="q" value="{{ request('q') }}" class="form-control pr-10" placeholder="نام، موبایل یا ایمیل"></div>
    <button class="btn-primary">جست‌وجو</button>
</form>
<div class="table-shell"><div class="overflow-x-auto"><table class="data-table">
<thead><tr><th>کاربر</th><th>موبایل</th><th>ایمیل</th><th>نوع حساب</th><th>وضعیت</th><th>آخرین ورود</th></tr></thead>
<tbody>
@forelse($users as $user)
<tr>
<td><div class="flex items-center gap-3"><img src="{{ asset('vendor/arino/images/profile.jpg') }}" class="h-10 w-10 rounded-full object-cover" alt=""><div><div class="font-semibold text-gray-900 dark:text-white">{{ $user->full_name ?: 'بدون نام' }}</div><div class="text-xs text-gray-500">{{ $user->public_id }}</div></div></div></td>
<td dir="ltr">{{ $user->mobile ?: '—' }}</td><td dir="ltr">{{ $user->email ?: '—' }}</td>
<td>@if($user->is_super_admin)<span class="rounded-full bg-secondary px-3 py-1 text-xs text-white">مدیر کل</span>@elseif($user->carWashes()->exists())<span class="rounded-full bg-primary-50 px-3 py-1 text-xs text-primary-700">عضو کارواش</span>@else<span class="rounded-full bg-gray-100 px-3 py-1 text-xs text-gray-600">مشتری</span>@endif</td>
<td><x-status-badge :value="$user->status"/></td><td>{{ \App\Support\PersianDate::dateTime($user->last_login_at) }}</td>
</tr>
@empty<tr><td colspan="6"><x-empty-state title="کاربری پیدا نشد" icon="users"/></td></tr>@endforelse
</tbody></table></div></div>
<div class="mt-5">{{ $users->links() }}</div>
@endsection
