@extends('layouts.panel')
@section('title', 'پرداخت‌ها')
@section('navigation') @include('carwash.partials.navigation') @endsection
@section('content')
<h1 class="mb-6 text-2xl font-bold">پرداخت‌ها</h1><div class="overflow-x-auto rounded-2xl bg-white shadow-sm"><table class="w-full text-sm"><thead class="bg-slate-100"><tr><th class="p-3">رزرو</th><th>مبلغ ریال</th><th>روش</th><th>وضعیت</th><th>مرجع</th><th>زمان</th></tr></thead><tbody>@foreach($payments as $payment)<tr class="border-t"><td class="p-3">{{ $payment->booking->tracking_code }}</td><td>{{ number_format($payment->amount) }}</td><td>{{ $payment->method->value }}</td><td>{{ $payment->status->value }}</td><td>{{ $payment->reference_id }}</td><td>{{ $payment->paid_at }}</td></tr>@endforeach</tbody></table></div><div class="mt-4">{{ $payments->links() }}</div>
@endsection
