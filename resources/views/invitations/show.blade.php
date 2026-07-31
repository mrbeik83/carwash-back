@extends('layouts.auth')
@section('title', 'پذیرش دعوت همکاری')
@section('hero-title', 'دعوت همکاری با یک کارواش')
@section('hero-description', 'پس از پذیرش، نقش و دسترسی شما فقط در همان کارواش فعال می‌شود.')

@section('content')
<div class="rounded-2xl border border-gray-200 bg-white p-7 shadow-sm dark:border-gray-700 dark:bg-gray-800">
    <div class="flex items-start gap-4">
        <span class="bg-primary-grad flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl text-white">
            <x-icon name="users" class="h-7 w-7"/>
        </span>
        <div>
            <span class="inline-flex rounded-full bg-primary-50 px-3 py-1 text-xs font-semibold text-primary-700 dark:bg-primary-950/40 dark:text-primary-300">دعوت‌نامه همکاری</span>
            <h1 class="mt-3 text-xl font-extrabold text-gray-900 dark:text-white">{{ $invitation->carWash->name }}</h1>
            <p class="mt-2 text-sm leading-7 text-gray-500 dark:text-gray-400">
                شما برای همکاری در پنل این کارواش دعوت شده‌اید.
            </p>
        </div>
    </div>

    <dl class="mt-6 space-y-3 rounded-2xl bg-gray-50 p-4 text-sm dark:bg-gray-700/50">
        <div class="flex items-center justify-between gap-4">
            <dt class="text-gray-500">نقش پیشنهادی</dt>
            <dd class="font-semibold text-gray-900 dark:text-white">{{ $invitation->role_name }}</dd>
        </div>
        <div class="flex items-center justify-between gap-4">
            <dt class="text-gray-500">دعوت‌کننده</dt>
            <dd class="font-semibold text-gray-900 dark:text-white">{{ $invitation->inviter?->full_name ?: $invitation->inviter?->mobile ?: 'مدیر کارواش' }}</dd>
        </div>
        <div class="flex items-center justify-between gap-4">
            <dt class="text-gray-500">اعتبار دعوت</dt>
            <dd class="font-semibold text-gray-900 dark:text-white">{{ \App\Support\PersianDate::dateTime($invitation->expires_at, $invitation->carWash?->timezone ?? 'Asia/Tehran') }}</dd>
        </div>
    </dl>

    <form method="POST" action="{{ route('invitations.accept', $token) }}" class="mt-6">
        @csrf
        <button class="btn-primary w-full"><x-icon name="check"/> پذیرش دعوت و فعال‌سازی عضویت</button>
    </form>
</div>
@endsection
