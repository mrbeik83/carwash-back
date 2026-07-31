<!doctype html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="color-scheme" content="light dark">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'پنل مدیریت کل') | {{ config('app.name', 'سامانه کارواش') }}</title>
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
    <div data-sidebar-overlay class="fixed inset-0 z-40 hidden bg-gray-950/50 backdrop-blur-sm lg:hidden"></div>

    <aside id="panel-sidebar" data-panel-sidebar class="fixed inset-y-0 right-0 z-50 flex w-72 translate-x-full flex-col border-l border-gray-200 bg-white shadow-xl transition-transform dark:border-gray-700 dark:bg-gray-800 lg:static lg:translate-x-0 lg:shadow-sm">
        @include('partials.admin.sidebar')
    </aside>

    <div class="min-w-0 flex-1">
        <header class="sticky top-0 z-30 flex h-20 items-center justify-between border-b border-gray-200 bg-white/90 px-4 backdrop-blur dark:border-gray-700 dark:bg-gray-800/90 sm:px-6">
            <div class="flex items-center gap-3">
                <button type="button" data-sidebar-open aria-label="بازکردن منوی پنل" class="rounded-xl border border-gray-200 p-2.5 text-gray-600 dark:border-gray-700 dark:text-gray-300 lg:hidden">
                    <x-icon name="menu"/>
                </button>
                <div>
                    <div class="text-sm text-gray-500 dark:text-gray-400">مدیریت پلتفرم</div>
                    <div class="font-bold text-gray-900 dark:text-white">@yield('page-title', 'پیشخوان')</div>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <button type="button" data-theme-toggle aria-label="تغییر حالت نمایش" class="rounded-xl border border-gray-200 bg-white p-2.5 text-gray-600 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-700 dark:text-gray-300">
                    <x-icon name="sun"/>
                </button>
                <div class="hidden items-center gap-3 rounded-xl border border-gray-200 px-3 py-2 dark:border-gray-700 sm:flex">
                    <img src="{{ asset('vendor/arino/images/profile.jpg') }}" class="h-8 w-8 rounded-full object-cover" alt="">
                    <div class="text-sm">
                        <div class="font-semibold text-gray-900 dark:text-white">{{ auth()->user()->full_name ?: 'مدیر کل' }}</div>
                        <div class="text-xs text-gray-500">مدیر اصلی سامانه</div>
                    </div>
                </div>
            </div>
        </header>

        <main id="main-content" tabindex="-1" class="p-4 sm:p-6 lg:p-8">
            @include('partials.flash')
            @yield('content')
        </main>
    </div>
</div>
<script src="{{ asset('vendor/arino/js/chart.js') }}"></script>
@stack('scripts')
</body>
</html>
