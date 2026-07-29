@extends('layouts.panel')
@section('title', 'خدمات و قیمت‌ها')
@section('navigation') @include('carwash.partials.navigation') @endsection
@section('content')
<h1 class="mb-6 text-2xl font-bold">خدمات و قیمت‌ها</h1>
@can('carwash.services.create')
<form method="POST" action="{{ route('carwash.services.store',$carWash) }}" class="mb-6 grid gap-3 rounded-2xl bg-white p-5 shadow-sm md:grid-cols-3">@csrf
<input name="name" class="rounded-xl border p-3" placeholder="نام خدمت"><input name="slug" class="rounded-xl border p-3" placeholder="slug"><input type="number" name="base_price" class="rounded-xl border p-3" placeholder="قیمت پایه ریال"><input type="number" name="default_duration_minutes" class="rounded-xl border p-3" value="30" placeholder="مدت"><input type="number" name="sort_order" class="rounded-xl border p-3" value="0" placeholder="ترتیب"><textarea name="description" class="rounded-xl border p-3 md:col-span-3" placeholder="توضیحات"></textarea>
<input type="hidden" name="is_active" value="1"><input type="hidden" name="is_featured" value="0">
<div class="md:col-span-3 grid gap-3 md:grid-cols-2">@foreach($vehicleTypes as $i=>$type)<div class="grid grid-cols-4 gap-2 rounded-xl border p-3"><div class="col-span-4 font-bold">{{ $type->name }}</div><input type="hidden" name="prices[{{ $i }}][vehicle_type_id]" value="{{ $type->id }}"><input class="col-span-2 rounded-lg border p-2" type="number" name="prices[{{ $i }}][price]" placeholder="قیمت ریال"><input class="rounded-lg border p-2" type="number" name="prices[{{ $i }}][duration_minutes]" value="30"><input type="hidden" name="prices[{{ $i }}][is_active]" value="1"></div>@endforeach</div>
<button class="rounded-xl bg-slate-900 p-3 text-white md:col-span-3">افزودن خدمت</button></form>
@endcan
<div class="grid gap-4">@foreach($services as $service)<div class="rounded-2xl bg-white p-5 shadow-sm"><div class="flex justify-between"><div><h2 class="font-bold">{{ $service->name }}</h2><p class="text-sm text-slate-500">{{ $service->description }}</p></div><span class="rounded-full bg-slate-100 px-3 py-1 text-sm">{{ $service->is_active ? 'فعال':'غیرفعال' }}</span></div><div class="mt-4 grid gap-2 sm:grid-cols-3">@foreach($service->vehiclePrices as $price)<div class="rounded-xl border p-3"><b>{{ $price->vehicleType->name }}</b><div>{{ number_format($price->price) }} ریال</div><div>{{ $price->duration_minutes }} دقیقه</div></div>@endforeach</div>@can('carwash.services.delete')<form method="POST" class="mt-4" action="{{ route('carwash.services.destroy',[$carWash,$service]) }}">@csrf @method('DELETE')<button class="rounded-lg bg-red-600 px-3 py-2 text-white">غیرفعال کردن</button></form>@endcan</div>@endforeach</div><div class="mt-4">{{ $services->links() }}</div>
@endsection
