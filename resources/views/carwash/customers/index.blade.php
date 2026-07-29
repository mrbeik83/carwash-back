@extends('layouts.panel')
@section('title', 'مشتریان')
@section('navigation') @include('carwash.partials.navigation') @endsection
@section('content')
<h1 class="mb-6 text-2xl font-bold">مشتریان</h1><form class="mb-5 flex gap-3"><input name="q" value="{{ request('q') }}" class="w-full rounded-xl border p-3" placeholder="نام یا موبایل"><button class="rounded-xl bg-slate-900 px-5 text-white">جستجو</button></form><div class="grid gap-3">@foreach($customers as $customer)<div class="rounded-xl bg-white p-4 shadow-sm"><b>{{ $customer->full_name }}</b><span class="mr-3" dir="ltr">{{ $customer->mobile }}</span><div class="mt-2 text-sm text-slate-500">خودروها: {{ $customer->vehicles()->count() }} | رزروها: {{ $customer->bookings()->where('car_wash_id',$carWash->id)->count() }}</div></div>@endforeach</div><div class="mt-4">{{ $customers->links() }}</div>
@endsection
