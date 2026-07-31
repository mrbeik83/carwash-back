@extends('layouts.carwash')
@section('title', 'کارکنان')
@section('page-title', 'کارکنان و دسترسی‌ها')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-extrabold text-gray-900 dark:text-white">کارکنان و دعوت‌ها</h1>
    <p class="mt-1 text-sm text-gray-500">مدیریت اعضا و نقش هر شخص در همین کارواش</p>
</div>

@can('carwash.members.invite')
<section class="panel-card mb-6">
    <div class="panel-card-header"><div><h2 class="font-bold">دعوت عضو جدید</h2><p class="mt-1 text-xs text-gray-500">لینک دعوت پس از ثبت نمایش داده می‌شود.</p></div></div>
    <form method="POST" action="{{ route('carwash.members.invite',$carWash) }}" class="panel-card-body grid gap-4 md:grid-cols-4">
        @csrf
        <div><label class="form-label">شماره موبایل</label><input type="tel" class="form-control" name="mobile" value="{{ old('mobile') }}" dir="ltr" inputmode="tel" autocomplete="tel"></div>
        <div><label class="form-label">ایمیل</label><input class="form-control" name="email" value="{{ old('email') }}" dir="ltr" type="email"></div>
        <div><label class="form-label">نقش</label><select class="form-select" name="role">
            <option value="carwash-manager">مدیر کارواش</option>
            <option value="carwash-receptionist">پذیرش</option>
            <option value="carwash-operator">اپراتور</option>
            <option value="carwash-accountant">حسابدار</option>
            @if(auth()->user()->is_super_admin)<option value="carwash-owner">مالک</option>@endif
        </select></div>
        <div class="flex items-end"><button class="btn-primary w-full">ساخت دعوت</button></div>
    </form>
</section>
@endcan

<section class="table-shell">
    <div class="panel-card-header"><h2 class="font-bold">اعضای فعال و سابق</h2><span class="text-sm text-gray-500">{{ $members->total() }} نفر</span></div>
    <div class="overflow-x-auto">
        <table class="data-table">
            <thead><tr><th>عضو</th><th>وضعیت</th><th>عنوان شغلی</th><th>نقش</th><th>زمان عضویت</th><th>عملیات</th></tr></thead>
            <tbody>
            @forelse($members as $member)
                @php($currentRole = $member->getRoleNames()->first())
                <tr>
                    <td><div class="flex items-center gap-3"><img src="{{ asset('vendor/arino/images/profile.jpg') }}" class="h-10 w-10 rounded-full object-cover" alt=""><div><div class="font-semibold text-gray-900 dark:text-white">{{ $member->full_name ?: 'بدون نام' }}</div><div class="text-xs text-gray-500" dir="ltr">{{ $member->mobile ?: $member->email }}</div></div></div></td>
                    <td><x-status-badge :value="$member->pivot->status"/></td>
                    <td>{{ $member->pivot->job_title ?: '—' }}</td>
                    <td><span class="rounded-lg bg-gray-100 px-2.5 py-1.5 text-xs dark:bg-gray-700">{{ $currentRole ?: 'بدون نقش' }}</span></td>
                    <td>{{ $member->pivot->joined_at ? \App\Support\PersianDate::date($member->pivot->joined_at, $carWash->timezone) : '—' }}</td>
                    <td>
                        <div class="flex items-center gap-2">
                            @can('carwash.members.update')
                                <form method="POST" action="{{ route('carwash.members.role',[$carWash,$member]) }}" class="flex gap-2">
                                    @csrf @method('PUT')
                                    <select name="role" class="form-select min-w-40 py-2">
                                        @foreach([
                                            'carwash-manager'=>'مدیر',
                                            'carwash-receptionist'=>'پذیرش',
                                            'carwash-operator'=>'اپراتور',
                                            'carwash-accountant'=>'حسابدار',
                                        ] as $value=>$label)
                                            <option value="{{ $value }}" @selected($currentRole===$value)>{{ $label }}</option>
                                        @endforeach
                                        @if(auth()->user()->is_super_admin)<option value="carwash-owner" @selected($currentRole==='carwash-owner')>مالک</option>@endif
                                    </select>
                                    <button class="rounded-lg bg-blue-50 p-2 text-blue-600 hover:bg-blue-100"><x-icon name="check"/></button>
                                </form>
                            @endcan
                            @can('carwash.members.remove')
                                <form method="POST" action="{{ route('carwash.members.destroy',[$carWash,$member]) }}">
                                    @csrf @method('DELETE')
                                    <button data-confirm="این عضو از کارواش حذف شود؟" class="rounded-lg bg-red-50 p-2 text-red-600 hover:bg-red-100"><x-icon name="trash"/></button>
                                </form>
                            @endcan
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6"><x-empty-state title="عضوی ثبت نشده است" icon="users"/></td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</section>
<div class="mt-5">{{ $members->links() }}</div>

<section class="panel-card mt-6">
    <div class="panel-card-header"><h2 class="font-bold">دعوت‌نامه‌ها</h2></div>
    <div class="overflow-x-auto">
        <table class="data-table">
            <thead><tr><th>مخاطب</th><th>نقش</th><th>انقضا</th><th>وضعیت</th></tr></thead>
            <tbody>
            @forelse($invitations as $invitation)
                <tr><td dir="ltr">{{ $invitation->mobile ?: $invitation->email }}</td><td>{{ \App\Enums\RoleName::tryFrom($invitation->role_name)?->label() ?: $invitation->role_name }}</td><td>{{ \App\Support\PersianDate::dateTime($invitation->expires_at, $carWash->timezone) }}</td><td>@if($invitation->accepted_at)<x-status-badge value="active"/>@elseif($invitation->cancelled_at)<x-status-badge value="cancelled"/>@elseif($invitation->expires_at?->isPast())<x-status-badge value="rejected"/>@else<x-status-badge value="invited"/>@endif</td></tr>
            @empty
                <tr><td colspan="4"><x-empty-state title="دعوتی ثبت نشده است" icon="users"/></td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</section>
<div class="mt-5">{{ $invitations->links() }}</div>
@endsection
