@extends('layouts.auth')
@section('title', 'انتخاب کارواش')
@section('hero-title', 'کارواش موردنظر را انتخاب کنید')
@section('hero-description', 'هر کارواش فضای مدیریتی و سطح دسترسی مستقل خود را دارد.')
@section('content')
<div class="mb-7">
    <h2 class="text-2xl font-extrabold text-gray-900 dark:text-white">انتخاب پنل کارواش</h2>
    <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">با حساب شما چند کارواش فعال مرتبط است.</p>
</div>
<div class="space-y-3">
    @forelse($carWashes as $carWash)
        <a href="{{ route('carwash.dashboard', $carWash) }}" class="panel-card group flex items-center gap-4 p-4 hover:border-primary">
            <span class="bg-primary-grad flex h-12 w-12 shrink-0 items-center justify-center rounded-xl text-white">
                <x-icon name="carwash" class="h-6 w-6"/>
            </span>
            <div class="min-w-0 flex-1">
                <div class="truncate font-bold text-gray-900 dark:text-white">{{ $carWash->name }}</div>
                <div class="mt-1 truncate text-sm text-gray-500">{{ $carWash->city }} · {{ $carWash->code }}</div>
            </div>
            <x-icon name="arrow-left" class="h-5 w-5 text-gray-400 group-hover:text-primary"/>
        </a>
    @empty
        <x-empty-state title="کارواش فعالی ندارید" description="برای بررسی عضویت با مدیر کارواش یا پشتیبانی تماس بگیرید." icon="carwash"/>
    @endforelse
</div>
<form method="POST" action="{{ route('carwash.logout') }}" class="mt-6">
    @csrf
    <button class="btn-secondary w-full">خروج از حساب</button>
</form>
@endsection
