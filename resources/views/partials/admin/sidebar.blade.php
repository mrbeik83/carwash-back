<div class="flex h-20 items-center justify-between border-b border-gray-200 px-5 dark:border-gray-700">
    <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3">
        <span class="bg-primary-grad flex h-10 w-10 items-center justify-center rounded-xl text-white">
            <x-icon name="settings" class="h-6 w-6"/>
        </span>
        <div>
            <div class="font-extrabold text-gray-900 dark:text-white">پنل <span class="text-primary">مدیریت کل</span></div>
            <div class="text-xs text-gray-500 dark:text-gray-400">مدیریت پلتفرم</div>
        </div>
    </a>
    <button type="button" data-sidebar-close class="rounded-lg p-2 text-gray-500 lg:hidden">
        <x-icon name="close"/>
    </button>
</div>

<div class="sidebar-scrollbar flex-1 overflow-y-auto p-4">
    <nav class="space-y-1">
        <x-panel.nav-link :href="route('admin.dashboard')" icon="dashboard" :active="request()->routeIs('admin.dashboard')">پیشخوان</x-panel.nav-link>
        <x-panel.nav-link :href="route('admin.car-washes.index')" icon="carwash" :active="request()->routeIs('admin.car-washes.*')">مدیریت کارواش‌ها</x-panel.nav-link>
        <x-panel.nav-link :href="route('admin.users.index')" icon="users" :active="request()->routeIs('admin.users.*')">کاربران</x-panel.nav-link>
        <x-panel.nav-link :href="route('admin.bookings.index')" icon="bookings" :active="request()->routeIs('admin.bookings.*')">همه رزروها</x-panel.nav-link>
        <x-panel.nav-link :href="route('admin.finance.index')" icon="finance" :active="request()->routeIs('admin.finance.*')">مالی و تراکنش‌ها</x-panel.nav-link>
        <x-panel.nav-link :href="route('admin.reports.index')" icon="reports" :active="request()->routeIs('admin.reports.*')">گزارش‌ها</x-panel.nav-link>
        <x-panel.nav-link :href="route('admin.roles.index')" icon="roles" :active="request()->routeIs('admin.roles.*')">نقش‌ها و دسترسی‌ها</x-panel.nav-link>
        <x-panel.nav-link :href="route('admin.audit-logs.index')" icon="audit" :active="request()->routeIs('admin.audit-logs.*')">گزارش فعالیت‌ها</x-panel.nav-link>
        <x-panel.nav-link :href="route('admin.settings.index')" icon="settings" :active="request()->routeIs('admin.settings.*')">تنظیمات سامانه</x-panel.nav-link>
    </nav>
</div>

<div class="border-t border-gray-200 p-4 dark:border-gray-700">
    <div class="mb-3 flex items-center gap-3 rounded-xl bg-gray-50 p-3 dark:bg-gray-700/60">
        <img src="{{ asset('vendor/arino/images/profile.jpg') }}" alt="" class="h-10 w-10 rounded-full object-cover">
        <div class="min-w-0">
            <div class="truncate text-sm font-semibold text-gray-900 dark:text-white">{{ auth()->user()->full_name ?: 'مدیر کل' }}</div>
            <div class="truncate text-xs text-gray-500 dark:text-gray-400">{{ auth()->user()->mobile }}</div>
        </div>
    </div>
    <form method="POST" action="{{ route('admin.logout') }}">
        @csrf
        <button class="flex w-full items-center gap-3 rounded-xl px-4 py-3 text-sm font-medium text-red-600 hover:bg-red-50 dark:text-red-400 dark:hover:bg-red-900/20">
            <x-icon name="logout"/>
            خروج از پنل
        </button>
    </form>
</div>
