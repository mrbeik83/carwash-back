@extends('layouts.admin')
@section('title', 'گزارش‌ها')
@section('page-title', 'گزارش‌های مدیریتی')

@section('content')
<div class="mb-6 flex flex-wrap items-start justify-between gap-4">
    <div>
        <h1 class="panel-page-heading">گزارش عملکرد پلتفرم</h1>
        <p class="panel-page-description">تحلیل رزرو، درآمد و رشد سامانه در بازه زمانی دلخواه</p>
    </div>

    <form class="panel-card-soft grid w-full gap-3 p-3 sm:w-auto sm:grid-cols-[190px_190px_auto] sm:items-end">
        <x-persian-date-input name="from" label="از تاریخ" :value="$from->format('Y-m-d')"/>
        <x-persian-date-input name="to" label="تا تاریخ" :value="$to->format('Y-m-d')"/>
        <button class="btn-primary whitespace-nowrap">اعمال بازه</button>
    </form>
</div>

<div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
    <x-panel.stat-card label="کل رزرو" :value="number_format($summary['bookings'])" icon="bookings"/>
    <x-panel.stat-card label="رزرو تکمیل‌شده" :value="number_format($summary['completed'])" icon="check" tone="green"/>
    <x-panel.stat-card label="لغو و رد" :value="number_format($summary['cancelled'])" icon="close" tone="red"/>
    <x-panel.stat-card label="درآمد" :value="number_format($summary['revenue']).' ریال'" icon="finance" tone="green"/>
    <x-panel.stat-card label="کاربر جدید" :value="number_format($summary['new_users'])" icon="users" tone="blue"/>
    <x-panel.stat-card label="کارواش جدید" :value="number_format($summary['new_car_washes'])" icon="carwash" tone="violet"/>
</div>

<div class="mt-6 grid gap-6 xl:grid-cols-2">
    <section class="panel-card">
        <div class="panel-card-header"><h2 class="font-bold">روند رزروها</h2></div>
        <div class="panel-card-body h-80"><canvas id="reportChart"></canvas></div>
    </section>

    <section class="panel-card overflow-hidden">
        <div class="panel-card-header"><h2 class="font-bold">کارواش‌های برتر</h2></div>
        <div class="overflow-x-auto">
            <table class="data-table">
                <thead><tr><th>کارواش</th><th>رزرو</th><th>ارزش رزروهای تکمیل</th></tr></thead>
                <tbody>
                @forelse($topCarWashes as $wash)
                    <tr>
                        <td class="font-semibold">{{ $wash->name }}</td>
                        <td>{{ \App\Support\PersianDate::digits(number_format($wash->bookings_count)) }}</td>
                        <td>{{ \App\Support\PersianDate::digits(number_format($wash->revenue)) }} ریال</td>
                    </tr>
                @empty
                    <tr><td colspan="3"><x-empty-state title="داده‌ای در این بازه نیست" icon="reports"/></td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </section>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const canvas = document.getElementById('reportChart');
    if (!canvas || typeof Chart === 'undefined') return;

    Chart.defaults.font.family = 'IRANYekanXFaNum, Tahoma, sans-serif';
    Chart.defaults.color = document.documentElement.classList.contains('dark') ? '#d1d5db' : '#6b7280';

    new Chart(canvas, {
        type: 'bar',
        data: {
            labels: @json($daily->pluck('day')),
            datasets: [{
                data: @json($daily->pluck('bookings')),
                backgroundColor: '#ff8229',
                borderRadius: 8,
                borderSkipped: false,
            }],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: true, ticks: { precision: 0 } } },
        },
    });
});
</script>
@endpush
