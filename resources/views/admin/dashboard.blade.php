@extends('layouts.admin')
@section('title', 'داشبورد مدیریت')
@section('page-title', 'پیشخوان مدیریت کل')

@section('content')
<div class="mb-6 flex flex-wrap items-center justify-between gap-3">
    <div>
        <h1 class="text-2xl font-extrabold text-gray-900 dark:text-white">داشبورد مدیریت کل</h1>
        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">نمای کلی وضعیت پلتفرم و آخرین فعالیت‌ها</p>
    </div>
    <a href="{{ route('admin.car-washes.create') }}" class="btn-primary">
        <x-icon name="plus"/>
        افزودن کارواش
    </a>
</div>

<div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-5">
    <x-panel.stat-card label="کل کارواش‌ها" :value="number_format($summary['car_washes'])" icon="carwash" hint="مجموع مراکز ثبت‌شده"/>
    <x-panel.stat-card label="در انتظار تأیید" :value="number_format($summary['pending_car_washes'])" icon="clock" tone="red" hint="نیازمند بررسی مدیر"/>
    <x-panel.stat-card label="کاربران" :value="number_format($summary['users'])" icon="users" tone="blue" hint="مشتری و کارکنان"/>
    <x-panel.stat-card label="رزرو امروز" :value="number_format($summary['bookings_today'])" icon="bookings" tone="violet" hint="در تمام کارواش‌ها"/>
    <x-panel.stat-card label="دریافتی امروز" :value="number_format($summary['revenue_today']).' ریال'" icon="finance" tone="green" hint="پرداخت‌های موفق"/>
</div>

<div class="mt-6 grid gap-6 xl:grid-cols-[minmax(0,1.5fr)_minmax(320px,.8fr)]">
    <section class="panel-card">
        <div class="panel-card-header">
            <div>
                <h2 class="font-bold text-gray-900 dark:text-white">روند رزروهای هفت روز اخیر</h2>
                <p class="mt-1 text-xs text-gray-500">تعداد رزروهای ثبت‌شده در کل سامانه</p>
            </div>
        </div>
        <div class="panel-card-body h-80">
            <canvas id="adminBookingsChart"></canvas>
        </div>
    </section>

    <section class="panel-card">
        <div class="panel-card-header">
            <h2 class="font-bold text-gray-900 dark:text-white">آخرین کارواش‌ها</h2>
            <a href="{{ route('admin.car-washes.index') }}" class="text-sm font-medium text-primary">مشاهده همه</a>
        </div>
        <div class="divide-y divide-gray-100 dark:divide-gray-700">
            @forelse($recentCarWashes as $wash)
                <a href="{{ route('admin.car-washes.show', $wash) }}" class="flex items-center gap-3 p-4 hover:bg-gray-50 dark:hover:bg-gray-700/50">
                    <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-primary-50 text-primary dark:bg-primary-900/30">
                        <x-icon name="carwash"/>
                    </span>
                    <div class="min-w-0 flex-1">
                        <div class="truncate text-sm font-semibold text-gray-900 dark:text-white">{{ $wash->name }}</div>
                        <div class="mt-1 text-xs text-gray-500">{{ $wash->city ?: 'شهر ثبت نشده' }} · {{ $wash->members_count }} عضو</div>
                    </div>
                    <x-status-badge :value="$wash->status"/>
                </a>
            @empty
                <x-empty-state title="هنوز کارواشی ثبت نشده" icon="carwash"/>
            @endforelse
        </div>
    </section>
</div>

<section class="panel-card mt-6">
    <div class="panel-card-header">
        <h2 class="font-bold text-gray-900 dark:text-white">آخرین رزروها</h2>
        <a href="{{ route('admin.bookings.index') }}" class="text-sm font-medium text-primary">مشاهده همه رزروها</a>
    </div>
    <div class="overflow-x-auto">
        <table class="data-table">
            <thead><tr><th>کد پیگیری</th><th>کارواش</th><th>مشتری</th><th>مبلغ</th><th>وضعیت</th><th>زمان ثبت</th></tr></thead>
            <tbody>
            @forelse($recentBookings as $booking)
                <tr>
                    <td class="font-latin text-xs">{{ $booking->tracking_code }}</td>
                    <td>{{ $booking->carWash?->name }}</td>
                    <td><div class="font-medium">{{ $booking->customer_name }}</div><div class="text-xs text-gray-500" dir="ltr">{{ $booking->customer_mobile }}</div></td>
                    <td>{{ number_format($booking->payable_amount) }} ریال</td>
                    <td><x-status-badge :value="$booking->status"/></td>
                    <td>{{ \App\Support\PersianDate::dateTime($booking->created_at, $booking->carWash?->timezone ?? 'Asia/Tehran') }}</td>
                </tr>
            @empty
                <tr><td colspan="6"><x-empty-state title="رزروی ثبت نشده است"/></td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</section>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const canvas = document.getElementById('adminBookingsChart');
    if (!canvas || typeof Chart === 'undefined') return;
    Chart.defaults.font.family = 'IRANYekanXFaNum, Tahoma, sans-serif';
    Chart.defaults.color = document.documentElement.classList.contains('dark') ? '#d1d5db' : '#6b7280';
    new Chart(canvas, {
        type: 'line',
        data: {
            labels: @json($chart['labels']),
            datasets: [{
                label: 'رزرو',
                data: @json($chart['values']),
                borderColor: '#ff8229',
                backgroundColor: 'rgba(255,130,41,.12)',
                fill: true,
                tension: .4,
                pointRadius: 4,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: true, ticks: { precision: 0 } } }
        }
    });
});
</script>
@endpush
