<!doctype html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="color-scheme" content="light dark">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#272c48">
    <title>@yield('title', 'پنل کارواش') | {{ $carWash->name }}</title>
    <script>
        if (localStorage.getItem('theme') === 'dark' ||
            (!localStorage.getItem('theme') && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        }
    </script>
    <link rel="preload" href="{{ asset('fonts/iranyekanx/IRANYekanXVFaNumVF.woff2') }}" as="font" type="font/woff2" crossorigin>
    <link rel="preload" href="{{ asset('fonts/iranyekanx/IRANYekanXVF.woff2') }}" as="font" type="font/woff2" crossorigin>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('head')
</head>
<body class="min-h-screen">
<a href="#main-content" class="skip-link">رفتن به محتوای اصلی</a>
<div class="min-h-screen lg:flex">
    <div data-sidebar-overlay class="fixed inset-0 z-40 hidden bg-gray-950/55 backdrop-blur-sm lg:hidden"></div>

    <aside id="panel-sidebar" data-panel-sidebar class="fixed inset-y-0 right-0 z-50 flex w-72 translate-x-full flex-col border-l border-gray-200 bg-white shadow-2xl transition-transform dark:border-gray-700 dark:bg-gray-800 lg:static lg:translate-x-0 lg:shadow-sm">
        @include('partials.carwash.sidebar')
    </aside>

    <div class="min-w-0 flex-1">
        <header class="sticky top-0 z-30 border-b border-gray-200/80 bg-white/90 backdrop-blur-xl dark:border-gray-700 dark:bg-gray-900/90">
            <div class="flex h-20 items-center justify-between gap-4 px-4 sm:px-6 lg:px-8">
                <div class="flex min-w-0 items-center gap-3">
                    <button type="button" data-sidebar-open aria-label="بازکردن منوی پنل" class="rounded-xl border border-gray-200 bg-white p-2.5 text-gray-600 shadow-sm dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 lg:hidden">
                        <x-icon name="menu"/>
                    </button>
                    <div class="min-w-0">
                        <div class="flex items-center gap-2 text-xs font-semibold text-gray-500 dark:text-gray-400">
                            <span>{{ $carWash->city ?: 'پنل اختصاصی کارواش' }}</span>
                            <span class="h-1 w-1 rounded-full bg-gray-300"></span>
                            <span>{{ \App\Support\PersianDate::human(now($carWash->timezone), $carWash->timezone) }}</span>
                        </div>
                        <div class="mt-1 truncate text-base font-extrabold text-gray-950 dark:text-white">@yield('page-title', $carWash->name)</div>
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    @can('carwash.bookings.create')
                        <a href="{{ route('carwash.bookings.index', $carWash) }}#new-booking" class="hidden btn-primary py-2.5 sm:inline-flex">
                            <x-icon name="plus" class="h-4 w-4"/> رزرو سریع
                        </a>
                    @endcan
                    <button type="button" data-theme-toggle aria-label="تغییر حالت نمایش" class="rounded-xl border border-gray-200 bg-white p-2.5 text-gray-600 shadow-sm hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300">
                        <x-icon name="sun"/>
                    </button>
                    <div class="hidden items-center gap-3 rounded-2xl border border-gray-200 bg-white px-3 py-2 shadow-sm dark:border-gray-700 dark:bg-gray-800 sm:flex">
                        <span class="bg-primary-grad flex h-9 w-9 items-center justify-center rounded-xl text-sm font-extrabold text-white">
                            <x-icon name="profile" class="h-5 w-5" />
                        </span>
                        <div class="max-w-36 text-sm">
                            <div class="truncate font-bold text-gray-900 dark:text-white">{{ auth()->user()->full_name ?: auth()->user()->mobile }}</div>
                            <div class="truncate text-xs text-gray-500">همکار {{ $carWash->name }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <main id="main-content" tabindex="-1" class="mx-auto w-full max-w-[1700px] p-4 sm:p-6 lg:p-8">
            @include('partials.flash')
            @yield('content')
        </main>

        <footer class="px-6 pb-6 text-center text-xs text-gray-400">
            پنل مدیریت {{ $carWash->name }} · طراحی‌شده برای عملیات سریع کارواش
        </footer>
    </div>
</div>
<script src="{{ asset('vendor/arino/js/chart.js') }}"></script>
@stack('scripts')
</body>
</html>
