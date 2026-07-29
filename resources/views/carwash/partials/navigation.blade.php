<a class="block rounded-lg p-2 hover:bg-white/10" href="{{ route('carwash.dashboard', $carWash) }}">داشبورد</a>
@can('carwash.bookings.view')<a class="block rounded-lg p-2 hover:bg-white/10" href="{{ route('carwash.bookings.index', $carWash) }}">رزروها</a>@endcan
@can('carwash.services.view')<a class="block rounded-lg p-2 hover:bg-white/10" href="{{ route('carwash.services.index', $carWash) }}">خدمات و قیمت‌ها</a>@endcan
@can('carwash.schedule.view')<a class="block rounded-lg p-2 hover:bg-white/10" href="{{ route('carwash.schedule.index', $carWash) }}">ظرفیت و زمان‌بندی</a>@endcan
@can('carwash.members.view')<a class="block rounded-lg p-2 hover:bg-white/10" href="{{ route('carwash.members.index', $carWash) }}">کارکنان</a>@endcan
@can('carwash.customers.view')<a class="block rounded-lg p-2 hover:bg-white/10" href="{{ route('carwash.customers.index', $carWash) }}">مشتریان</a>@endcan
@can('carwash.payments.view')<a class="block rounded-lg p-2 hover:bg-white/10" href="{{ route('carwash.payments.index', $carWash) }}">پرداخت‌ها</a>@endcan
@can('carwash.reports.view')<a class="block rounded-lg p-2 hover:bg-white/10" href="{{ route('carwash.reports.index', $carWash) }}">گزارش‌ها</a>@endcan
@can('carwash.qr.view')<a class="block rounded-lg p-2 hover:bg-white/10" href="{{ route('carwash.qr.index', $carWash) }}">QR و کمپین</a>@endcan
@can('carwash.profile.view')<a class="block rounded-lg p-2 hover:bg-white/10" href="{{ route('carwash.profile.edit', $carWash) }}">پروفایل کارواش</a>@endcan
@can('carwash.settings.view')<a class="block rounded-lg p-2 hover:bg-white/10" href="{{ route('carwash.settings.edit', $carWash) }}">تنظیمات</a>@endcan
