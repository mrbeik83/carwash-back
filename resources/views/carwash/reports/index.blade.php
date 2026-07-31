@extends('layouts.carwash')
@section('title', 'گزارش‌ها')
@section('page-title', 'گزارش عملکرد')

@section('content')
<div class="mb-6 flex flex-wrap items-start justify-between gap-4">
    <div>
        <h1 class="panel-page-heading">گزارش عملکرد کارواش</h1>
        <p class="panel-page-description">تحلیل رزروها، لغوها، عدم مراجعه و فروش در بازه شمسی انتخابی.</p>
    </div>
</div>

<section class="panel-card mb-6">
    <form method="GET" action="{{ route('carwash.reports.index', $carWash) }}" class="panel-card-body grid gap-4 sm:grid-cols-2 lg:grid-cols-[1fr_1fr_auto]">
        <x-persian-date-input name="from" label="از تاریخ" :value="$from->format('Y-m-d')" required/>
        <x-persian-date-input name="to" label="تا تاریخ" :value="$to->format('Y-m-d')" required/>
        <div class="flex items-end"><button class="btn-primary w-full lg:w-auto"><x-icon name="reports"/> اعمال بازه</button></div>
    </form>
</section>

<div class="mb-6 grid gap-4 sm:grid-cols-2 xl:grid-cols-5">
    <x-panel.stat-card label="کل رزرو" :value="\App\Support\PersianDate::digits(number_format($summary['total_bookings']))" icon="bookings" tone="primary"/>
    <x-panel.stat-card label="تکمیل‌شده" :value="\App\Support\PersianDate::digits(number_format($summary['completed']))" icon="check" tone="green"/>
    <x-panel.stat-card label="لغوشده" :value="\App\Support\PersianDate::digits(number_format($summary['cancelled']))" icon="close" tone="red"/>
    <x-panel.stat-card label="عدم مراجعه" :value="\App\Support\PersianDate::digits(number_format($summary['no_show']))" icon="clock" tone="primary"/>
    <x-panel.stat-card label="فروش" :value="\App\Support\PersianDate::digits(number_format($summary['revenue'])).' ریال'" icon="finance" tone="violet"/>
</div>

<div class="grid gap-6 xl:grid-cols-[minmax(0,1.4fr)_minmax(320px,.6fr)]">
    <section class="panel-card">
        <div class="panel-card-header"><div><h2 class="font-extrabold text-gray-900 dark:text-white">روند روزانه</h2><p class="mt-1 text-xs text-gray-500">تعداد رزرو و فروش تکمیل‌شده</p></div></div>
        <div class="panel-card-body"><div class="h-80"><canvas id="daily-report-chart"></canvas></div></div>
    </section>

    <section class="table-shell">
        <div class="border-b border-gray-200 px-5 py-4 dark:border-gray-700"><h2 class="font-extrabold text-gray-900 dark:text-white">جزئیات روزانه</h2></div>
        <div class="max-h-[26rem] overflow-auto">
            <table class="data-table">
                <thead><tr><th>روز</th><th>رزرو</th><th>فروش</th></tr></thead>
                <tbody>
                @forelse($daily as $row)
                    <tr>
                        <td>{{ \App\Support\PersianDate::date($row->day, $carWash->timezone) }}</td>
                        <td>{{ \App\Support\PersianDate::digits(number_format($row->bookings)) }}</td>
                        <td>{{ \App\Support\PersianDate::digits(number_format($row->revenue)) }} ریال</td>
                    </tr>
                @empty
                    <tr><td colspan="3"><x-empty-state title="داده‌ای در این بازه وجود ندارد" icon="reports"/></td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </section>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const canvas = document.getElementById('daily-report-chart');
    if (!canvas || typeof Chart === 'undefined') return;
    Chart.defaults.font.family = 'IRANYekanXFaNum, Tahoma, sans-serif';
    Chart.defaults.color = document.documentElement.classList.contains('dark') ? '#d1d5db' : '#6b7280';

    new Chart(canvas, {
        type: 'line',
        data: {
            labels: @json($daily->map(fn ($row) => \App\Support\PersianDate::date($row->day, $carWash->timezone))->values()),
            datasets: [
                { label: 'تعداد رزرو', data: @json($daily->pluck('bookings')->values()), borderColor: '#ff8229', backgroundColor: 'rgba(255,130,41,.12)', tension: .35, fill: true, yAxisID: 'y' },
                { label: 'فروش (ریال)', data: @json($daily->pluck('revenue')->values()), borderColor: '#272c48', backgroundColor: 'rgba(39,44,72,.08)', tension: .35, yAxisID: 'y1' }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: { mode: 'index', intersect: false },
            plugins: { legend: { position: 'bottom', rtl: true } },
            scales: { y: { beginAtZero: true, position: 'right' }, y1: { beginAtZero: true, position: 'left', grid: { drawOnChartArea: false } } }
        }
    });
});
</script>
@endpush
