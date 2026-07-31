<div class="flex h-20 items-center justify-between border-b border-gray-200 px-5 dark:border-gray-700">
    <a href="{{ route('carwash.dashboard', $carWash) }}" class="flex min-w-0 items-center gap-3">
        <span class="bg-primary-grad flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl text-white shadow-lg shadow-primary/20">
            <x-icon name="carwash" class="h-6 w-6"/>
        </span>
        <div class="min-w-0">
            <div class="truncate font-extrabold text-gray-950 dark:text-white">{{ $carWash->name }}</div>
            <div class="mt-0.5 text-xs text-gray-500 dark:text-gray-400">سامانه مدیریت و رزرو</div>
        </div>
    </a>
    <button type="button" data-sidebar-close class="rounded-xl p-2 text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-700 lg:hidden">
        <x-icon name="close"/>
    </button>
</div>

<div class="sidebar-scrollbar flex-1 overflow-y-auto p-4">
    <div class="mb-3 px-3 text-[11px] font-extrabold tracking-wide text-gray-400">عملیات روزانه</div>
    <nav class="space-y-1">
        @can('carwash.dashboard.view')
            <x-panel.nav-link :href="route('carwash.dashboard', $carWash)" icon="dashboard" :active="request()->routeIs('carwash.dashboard')">پیشخوان امروز</x-panel.nav-link>
        @endcan
        @can('carwash.bookings.view')
            <x-panel.nav-link :href="route('carwash.bookings.index', $carWash)" icon="bookings" :active="request()->routeIs('carwash.bookings.*')">رزروها و صف کاری</x-panel.nav-link>
        @endcan
        @can('carwash.schedule.view')
            <x-panel.nav-link :href="route('carwash.schedule.index', $carWash)" icon="schedule" :active="request()->routeIs('carwash.schedule.*')">تقویم کاری و ظرفیت</x-panel.nav-link>
        @endcan
        @can('carwash.customers.view')
            <x-panel.nav-link :href="route('carwash.customers.index', $carWash)" icon="customers" :active="request()->routeIs('carwash.customers.*')">مشتریان</x-panel.nav-link>
        @endcan
    </nav>

    <div class="mb-3 mt-7 px-3 text-[11px] font-extrabold tracking-wide text-gray-400">مدیریت کسب‌وکار</div>
    <nav class="space-y-1">
        @can('carwash.services.view')
            <x-panel.nav-link :href="route('carwash.services.index', $carWash)" icon="services" :active="request()->routeIs('carwash.services.*')">خدمات و قیمت‌ها</x-panel.nav-link>
        @endcan
        @can('carwash.members.view')
            <x-panel.nav-link :href="route('carwash.members.index', $carWash)" icon="users" :active="request()->routeIs('carwash.members.*')">کارکنان و دسترسی‌ها</x-panel.nav-link>
        @endcan
        @can('carwash.payments.view')
            <x-panel.nav-link :href="route('carwash.payments.index', $carWash)" icon="finance" :active="request()->routeIs('carwash.payments.*')">پرداخت‌ها</x-panel.nav-link>
        @endcan
        @can('carwash.reports.view')
            <x-panel.nav-link :href="route('carwash.reports.index', $carWash)" icon="reports" :active="request()->routeIs('carwash.reports.*')">گزارش عملکرد</x-panel.nav-link>
        @endcan
        @can('carwash.qr.view')
            <x-panel.nav-link :href="route('carwash.qr.index', $carWash)" icon="qr" :active="request()->routeIs('carwash.qr.*')">QR و کمپین‌ها</x-panel.nav-link>
        @endcan
    </nav>

    <div class="mb-3 mt-7 px-3 text-[11px] font-extrabold tracking-wide text-gray-400">تنظیمات</div>
    <nav class="space-y-1">
        @can('carwash.profile.view')
            <x-panel.nav-link :href="route('carwash.profile.edit', $carWash)" icon="profile" :active="request()->routeIs('carwash.profile.*')">پروفایل کارواش</x-panel.nav-link>
        @endcan
        @can('carwash.settings.view')
            <x-panel.nav-link :href="route('carwash.settings.edit', $carWash)" icon="settings" :active="request()->routeIs('carwash.settings.*')">تنظیمات رزرو</x-panel.nav-link>
        @endcan
    </nav>
</div>

<div class="border-t border-gray-200 p-4 dark:border-gray-700">
    <div class="mb-3 rounded-2xl bg-secondary p-4 text-white">
        <div class="text-xs text-gray-300">وضعیت کارواش</div>
        <div class="mt-2 flex items-center justify-between gap-2">
            <span class="font-bold">فعال و آماده رزرو</span>
            <span class="h-2.5 w-2.5 rounded-full bg-emerald-400 shadow-[0_0_0_5px_rgb(52_211_153_/_0.15)]"></span>
        </div>
    </div>
    @if(auth()->user()->activeCarWashes()->where('car_washes.status', 'active')->count() > 1)
        <a href="{{ route('carwash.select') }}" class="mb-2 flex items-center gap-3 rounded-xl px-4 py-3 text-sm text-gray-600 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700">
            <x-icon name="carwash"/> تغییر کارواش
        </a>
    @endif
    <form method="POST" action="{{ route('carwash.logout') }}">
        @csrf
        <button class="flex w-full items-center gap-3 rounded-xl px-4 py-3 text-sm font-medium text-red-600 hover:bg-red-50 dark:text-red-400 dark:hover:bg-red-900/20">
            <x-icon name="logout"/> خروج از پنل
        </button>
    </form>
</div>
