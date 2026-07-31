<!doctype html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="color-scheme" content="light dark">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'ورود') | {{ config('app.name', 'سامانه کارواش') }}</title>
    <script>
        if (localStorage.getItem('theme') === 'dark' ||
            (!localStorage.getItem('theme') && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        }
    </script>
    <link rel="preload" href="{{ asset('fonts/iranyekanx/IRANYekanXVFaNumVF.woff2') }}" as="font" type="font/woff2" crossorigin>
    <link rel="preload" href="{{ asset('fonts/iranyekanx/IRANYekanXVF.woff2') }}" as="font" type="font/woff2" crossorigin>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen">
<a href="#main-content" class="skip-link">رفتن به محتوای اصلی</a>
<div class="grid min-h-screen lg:grid-cols-[minmax(0,1fr)_minmax(420px,560px)]">
    <section class="auth-background relative hidden overflow-hidden p-10 text-white lg:flex lg:flex-col lg:justify-between">
        <div class="relative z-10 flex items-center gap-3">
            <span class="bg-primary-grad flex h-12 w-12 items-center justify-center rounded-xl shadow-lg">
                <x-icon name="carwash" class="h-7 w-7"/>
            </span>
            <div>
                <div class="text-xl font-extrabold">سامانه مدیریت کارواش</div>
                <div class="text-sm text-white/70">مدیریت یکپارچه رزرو و عملیات روزانه</div>
            </div>
        </div>

        <div class="relative z-10 max-w-xl">
            <span class="inline-flex rounded-full border border-white/20 bg-white/10 px-4 py-2 text-sm backdrop-blur">
                طراحی‌شده بر پایه قالب آرینو
            </span>
            <h1 class="mt-6 text-4xl font-extrabold leading-relaxed text-balance">@yield('hero-title', 'مدیریت دقیق‌تر، تجربه بهتر برای مشتری')</h1>
            <p class="mt-4 text-base leading-8 text-white/75">@yield('hero-description', 'رزروها، ظرفیت، خدمات، کارکنان و گزارش‌های مالی را از یک پنل سریع و منظم مدیریت کنید.')</p>
        </div>

        <div class="relative z-10 text-sm text-white/60">© {{ \App\Support\PersianDate::digits(now()->year) }} تمامی حقوق محفوظ است.</div>
    </section>

    <main id="main-content" tabindex="-1" class="flex min-h-screen items-center justify-center bg-gray-50 p-4 dark:bg-gray-950 sm:p-8">
        <div class="w-full max-w-md">
            <div class="mb-8 flex items-center justify-between lg:hidden">
                <a href="{{ route('home') }}" class="flex items-center gap-3">
                    <span class="bg-primary-grad flex h-11 w-11 items-center justify-center rounded-xl text-white">
                        <x-icon name="carwash" class="h-6 w-6"/>
                    </span>
                    <span class="font-extrabold text-gray-900 dark:text-white">سامانه کارواش</span>
                </a>
                <button type="button" data-theme-toggle aria-label="تغییر حالت نمایش" class="rounded-xl border border-gray-200 bg-white p-2.5 text-gray-600 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300">
                    <x-icon name="sun"/>
                </button>
            </div>

            @include('partials.flash')
            @yield('content')
        </div>
    </main>
</div>
</body>
</html>
