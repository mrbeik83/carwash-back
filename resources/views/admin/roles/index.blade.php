@extends('layouts.admin')
@section('title', 'نقش‌ها و دسترسی‌ها')
@section('page-title', 'نقش‌ها و دسترسی‌ها')
@section('content')
<div class="mb-6"><h1 class="text-2xl font-extrabold text-gray-900 dark:text-white">نقش‌ها و سطح دسترسی</h1><p class="mt-1 text-sm text-gray-500">نقش‌های تیمی و مجوزهای دسترسی متصل به هر نقش</p></div>
<div class="grid gap-5 lg:grid-cols-2">
@foreach($roles as $role)
<section class="panel-card"><div class="panel-card-header"><div><h2 class="font-bold text-gray-900 dark:text-white">{{ $role->name }}</h2><p class="mt-1 text-xs text-gray-500">{{ $role->permissions->count() }} دسترسی</p></div><span class="rounded-full bg-primary-50 px-3 py-1 text-xs text-primary-700">{{ $role->guard_name }}</span></div>
<div class="panel-card-body flex flex-wrap gap-2">@forelse($role->permissions as $permission)<span class="rounded-lg bg-gray-100 px-2.5 py-1.5 font-latin text-xs text-gray-700 dark:bg-gray-700 dark:text-gray-200">{{ $permission->name }}</span>@empty<span class="text-sm text-gray-500">دسترسی‌ای تعریف نشده است.</span>@endforelse</div></section>
@endforeach
</div>
<section class="panel-card mt-6"><div class="panel-card-header"><h2 class="font-bold">گروه‌بندی مجوزهای دسترسی</h2></div><div class="panel-card-body grid gap-4 md:grid-cols-2">@foreach($permissionGroups as $group=>$permissions)<div class="rounded-xl border border-gray-200 p-4 dark:border-gray-700"><div class="font-bold text-primary">{{ $group }}</div><div class="mt-3 space-y-2">@foreach($permissions as $permission)<div class="font-latin text-xs text-gray-600 dark:text-gray-300">{{ $permission->name }}</div>@endforeach</div></div>@endforeach</div></section>
@endsection
