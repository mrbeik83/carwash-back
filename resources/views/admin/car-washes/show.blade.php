@extends('layouts.admin')
@section('title', $carWash->name)
@section('page-title', 'جزئیات کارواش')
@section('content')
<div class="mb-6 flex flex-wrap items-start justify-between gap-3">
    <div class="flex items-center gap-4">
        <span class="bg-primary-grad flex h-16 w-16 items-center justify-center rounded-2xl text-white"><x-icon name="carwash" class="h-8 w-8"/></span>
        <div><div class="flex items-center gap-2"><h1 class="text-2xl font-extrabold text-gray-900 dark:text-white">{{ $carWash->name }}</h1><x-status-badge :value="$carWash->status"/></div><p class="mt-1 text-sm text-gray-500">{{ $carWash->code }} · {{ $carWash->city ?: 'شهر ثبت نشده' }}</p></div>
    </div>
    <div class="flex gap-2">
        @if($carWash->status->value === 'active')
            <a href="{{ route('carwash.dashboard',$carWash) }}" class="btn-secondary">مشاهده پنل کارواش</a>
        @endif
        <a href="{{ route('admin.car-washes.edit',$carWash) }}" class="btn-primary"><x-icon name="edit"/> ویرایش</a>
    </div>
</div>

<div class="grid gap-6 xl:grid-cols-3">
    <section class="panel-card xl:col-span-2">
        <div class="panel-card-header"><h2 class="font-bold">اطلاعات مرکز</h2></div>
        <div class="panel-card-body grid gap-5 sm:grid-cols-2">
            @foreach([
                'شماره تماس' => $carWash->phone ?: '—',
                'موبایل مجموعه' => $carWash->mobile ?: '—',
                'ایمیل' => $carWash->email ?: '—',
                'منطقه زمانی' => $carWash->timezone,
                'استان و شهر' => collect([$carWash->province,$carWash->city])->filter()->join('، ') ?: '—',
                'کد پستی' => $carWash->postal_code ?: '—',
                'تعداد اعضا' => number_format($carWash->members->count()),
                'تاریخ تأیید' => \App\Support\PersianDate::dateTime($carWash->approved_at, $carWash->timezone),
            ] as $label=>$value)
                <div><div class="text-xs text-gray-500">{{ $label }}</div><div class="mt-1 font-medium text-gray-900 dark:text-white">{{ $value }}</div></div>
            @endforeach
            <div class="sm:col-span-2"><div class="text-xs text-gray-500">آدرس</div><div class="mt-1 leading-7 text-gray-800 dark:text-gray-200">{{ $carWash->address ?: '—' }}</div></div>
        </div>
    </section>

    <section class="panel-card">
        <div class="panel-card-header"><h2 class="font-bold">تغییر وضعیت</h2></div>
        <form method="POST" action="{{ route('admin.car-washes.status',$carWash) }}" class="panel-card-body space-y-4">
            @csrf
            <div><label class="form-label">وضعیت</label><select name="status" class="form-select">@foreach(['pending'=>'در انتظار','active'=>'فعال','suspended'=>'تعلیق‌شده','rejected'=>'ردشده'] as $value=>$label)<option value="{{ $value }}" @selected($carWash->status->value===$value)>{{ $label }}</option>@endforeach</select></div>
            <div><label class="form-label">دلیل تغییر یا تعلیق</label><textarea name="reason" rows="4" class="form-control">{{ old('reason',$carWash->suspension_reason) }}</textarea></div>
            <button class="btn-primary w-full">ثبت وضعیت</button>
        </form>
    </section>
</div>

<section class="panel-card mt-6">
    <div class="panel-card-header"><h2 class="font-bold">اعضای کارواش</h2><span class="text-sm text-gray-500">{{ $carWash->members->count() }} نفر</span></div>
    <div class="overflow-x-auto">
        <table class="data-table"><thead><tr><th>نام</th><th>موبایل</th><th>عنوان شغلی</th><th>وضعیت عضویت</th></tr></thead><tbody>
        @forelse($carWash->members as $member)
            <tr><td class="font-medium text-gray-900 dark:text-white">{{ $member->full_name ?: 'بدون نام' }}</td><td dir="ltr">{{ $member->mobile }}</td><td>{{ $member->pivot->job_title ?: '—' }}</td><td><x-status-badge :value="$member->pivot->status"/></td></tr>
        @empty
            <tr><td colspan="4"><x-empty-state title="عضوی ثبت نشده است" icon="users"/></td></tr>
        @endforelse
        </tbody></table>
    </div>
</section>
@endsection
