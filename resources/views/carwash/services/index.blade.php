@extends('layouts.carwash')
@section('title', 'خدمات و قیمت‌ها')
@section('page-title', 'خدمات و قیمت‌ها')

@section('content')
<div class="mb-6"><h1 class="text-2xl font-extrabold text-gray-900 dark:text-white">خدمات و قیمت‌ها</h1><p class="mt-1 text-sm text-gray-500">قیمت و مدت هر خدمت برای انواع خودرو</p></div>

@can('carwash.services.create')
<section class="panel-card mb-6">
    <div class="panel-card-header"><h2 class="font-bold">افزودن خدمت جدید</h2></div>
    <form method="POST" action="{{ route('carwash.services.store',$carWash) }}" class="panel-card-body">
        @csrf
        <div class="grid gap-4 md:grid-cols-3">
            <div><label class="form-label">نام خدمت</label><input name="name" value="{{ old('name') }}" class="form-control" required></div>
            <div><label class="form-label">نامک</label><input name="slug" value="{{ old('slug') }}" class="form-control" dir="ltr" required></div>
            <div><label class="form-label">قیمت پایه (ریال)</label><input type="number" name="base_price" value="{{ old('base_price',0) }}" class="form-control" min="0" required></div>
            <div><label class="form-label">مدت پایه (دقیقه)</label><input type="number" name="default_duration_minutes" value="{{ old('default_duration_minutes',30) }}" class="form-control" min="5" required></div>
            <div><label class="form-label">ترتیب نمایش</label><input type="number" name="sort_order" value="{{ old('sort_order',0) }}" class="form-control" min="0"></div>
            <div class="flex items-end gap-5 rounded-xl border border-gray-200 px-4 py-3 dark:border-gray-700">
                <label class="flex items-center gap-2 text-sm"><input type="hidden" name="is_active" value="0"><input type="checkbox" name="is_active" value="1" checked class="h-4 w-4 rounded text-primary"> فعال</label>
                <label class="flex items-center gap-2 text-sm"><input type="hidden" name="is_featured" value="0"><input type="checkbox" name="is_featured" value="1" class="h-4 w-4 rounded text-primary"> ویژه</label>
            </div>
            <div class="md:col-span-3"><label class="form-label">توضیحات</label><textarea name="description" rows="3" class="form-control">{{ old('description') }}</textarea></div>
        </div>

        <div class="mt-6">
            <h3 class="mb-3 font-semibold text-gray-900 dark:text-white">قیمت بر اساس نوع خودرو</h3>
            <div class="grid gap-3 md:grid-cols-2 xl:grid-cols-3">
                @foreach($vehicleTypes as $i=>$type)
                    <div class="rounded-xl border border-gray-200 p-4 dark:border-gray-700">
                        <div class="mb-3 font-bold">{{ $type->name }}</div>
                        <input type="hidden" name="prices[{{ $i }}][vehicle_type_id]" value="{{ $type->id }}">
                        <input type="hidden" name="prices[{{ $i }}][is_active]" value="1">
                        <div class="grid grid-cols-2 gap-3">
                            <div><label class="form-label">قیمت ریال</label><input type="number" name="prices[{{ $i }}][price]" value="{{ old("prices.$i.price",0) }}" class="form-control" min="0" required></div>
                            <div><label class="form-label">مدت</label><input type="number" name="prices[{{ $i }}][duration_minutes]" value="{{ old("prices.$i.duration_minutes",30) }}" class="form-control" min="5" required></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        <div class="mt-6 flex justify-end"><button class="btn-primary">افزودن خدمت</button></div>
    </form>
</section>
@endcan

<div class="grid gap-5 lg:grid-cols-2">
@forelse($services as $service)
<section class="panel-card">
    <div class="panel-card-header">
        <div><div class="flex items-center gap-2"><h2 class="font-bold text-gray-900 dark:text-white">{{ $service->name }}</h2><x-status-badge :value="$service->is_active ? 'active':'suspended'"/></div><p class="mt-1 text-xs text-gray-500">{{ $service->slug }} · {{ $service->default_duration_minutes }} دقیقه</p></div>
        <div class="font-bold text-primary">{{ number_format($service->base_price) }} ریال</div>
    </div>
    <div class="panel-card-body">
        @if($service->description)<p class="mb-4 text-sm leading-7 text-gray-600 dark:text-gray-300">{{ $service->description }}</p>@endif
        <div class="grid gap-3 sm:grid-cols-2">
            @foreach($service->vehiclePrices as $price)
                <div class="rounded-xl bg-gray-50 p-3 dark:bg-gray-700/60">
                    <div class="font-semibold text-gray-900 dark:text-white">{{ $price->vehicleType?->name }}</div>
                    <div class="mt-2 flex justify-between text-sm"><span>{{ number_format($price->price) }} ریال</span><span class="text-gray-500">{{ $price->duration_minutes }} دقیقه</span></div>
                </div>
            @endforeach
        </div>
        <div class="mt-5 flex flex-wrap gap-2 border-t border-gray-100 pt-4 dark:border-gray-700">
            @can('carwash.services.update')
                <details class="w-full">
                    <summary class="btn-secondary w-fit cursor-pointer list-none"><x-icon name="edit"/> ویرایش خدمت</summary>
                    <form method="POST" action="{{ route('carwash.services.update', [$carWash, $service]) }}" class="mt-4 rounded-2xl bg-gray-50 p-4 dark:bg-gray-700/50">
                        @csrf
                        @method('PUT')
                        <div class="grid gap-4 md:grid-cols-2">
                            <div><label class="form-label">نام خدمت</label><input name="name" value="{{ $service->name }}" class="form-control" required></div>
                            <div><label class="form-label">نامک</label><input name="slug" value="{{ $service->slug }}" class="form-control" dir="ltr" required></div>
                            <div><label class="form-label">قیمت پایه (ریال)</label><input type="number" name="base_price" value="{{ $service->base_price }}" class="form-control" min="0" required></div>
                            <div><label class="form-label">مدت پایه (دقیقه)</label><input type="number" name="default_duration_minutes" value="{{ $service->default_duration_minutes }}" class="form-control" min="5" required></div>
                            <div><label class="form-label">ترتیب نمایش</label><input type="number" name="sort_order" value="{{ $service->sort_order }}" class="form-control" min="0"></div>
                            <div class="flex items-center gap-5 rounded-xl border border-gray-200 px-4 py-3 dark:border-gray-600">
                                <label class="flex items-center gap-2 text-sm">
                                    <input type="hidden" name="is_active" value="0">
                                    <input type="checkbox" name="is_active" value="1" @checked($service->is_active) class="h-4 w-4 rounded border-gray-300 text-primary">
                                    فعال
                                </label>
                                <label class="flex items-center gap-2 text-sm">
                                    <input type="hidden" name="is_featured" value="0">
                                    <input type="checkbox" name="is_featured" value="1" @checked($service->is_featured) class="h-4 w-4 rounded border-gray-300 text-primary">
                                    ویژه
                                </label>
                            </div>
                            <div class="md:col-span-2"><label class="form-label">توضیحات</label><textarea name="description" rows="3" class="form-control">{{ $service->description }}</textarea></div>
                        </div>

                        <div class="mt-5 grid gap-3 md:grid-cols-2">
                            @foreach($vehicleTypes as $priceIndex => $vehicleType)
                                @php($currentPrice = $service->vehiclePrices->firstWhere('vehicle_type_id', $vehicleType->id))
                                <div class="rounded-xl border border-gray-200 bg-white p-4 dark:border-gray-600 dark:bg-gray-800">
                                    <div class="mb-3 font-semibold text-gray-900 dark:text-white">{{ $vehicleType->name }}</div>
                                    <input type="hidden" name="prices[{{ $priceIndex }}][vehicle_type_id]" value="{{ $vehicleType->id }}">
                                    <input type="hidden" name="prices[{{ $priceIndex }}][is_active]" value="0">
                                    <div class="mb-3 flex items-center gap-2">
                                        <input type="checkbox" name="prices[{{ $priceIndex }}][is_active]" value="1" @checked($currentPrice?->is_active ?? true) class="h-4 w-4 rounded border-gray-300 text-primary">
                                        <span class="text-sm">قابل ارائه</span>
                                    </div>
                                    <div class="grid grid-cols-2 gap-3">
                                        <div><label class="form-label">قیمت</label><input type="number" name="prices[{{ $priceIndex }}][price]" value="{{ $currentPrice?->price ?? $service->base_price }}" class="form-control" min="0" required></div>
                                        <div><label class="form-label">مدت</label><input type="number" name="prices[{{ $priceIndex }}][duration_minutes]" value="{{ $currentPrice?->duration_minutes ?? $service->default_duration_minutes }}" class="form-control" min="5" required></div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <div class="mt-5 flex justify-end"><button class="btn-primary"><x-icon name="check"/> ذخیره تغییرات</button></div>
                    </form>
                </details>
            @endcan

            @can('carwash.services.delete')
                <form method="POST" action="{{ route('carwash.services.destroy',[$carWash,$service]) }}">
                    @csrf @method('DELETE')
                    <button data-confirm="این خدمت غیرفعال شود؟" class="btn-danger"><x-icon name="trash"/> غیرفعال کردن</button>
                </form>
            @endcan
        </div>
    </div>
</section>
@empty
    <div class="panel-card lg:col-span-2"><x-empty-state title="خدمتی تعریف نشده است" description="برای شروع، خدمات قابل ارائه و قیمت هر نوع خودرو را ثبت کنید." icon="services"/></div>
@endforelse
</div>
<div class="mt-5">{{ $services->links() }}</div>
@endsection
