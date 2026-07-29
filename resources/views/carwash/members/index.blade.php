@extends('layouts.panel')
@section('title', 'کارکنان')
@section('navigation') @include('carwash.partials.navigation') @endsection
@section('content')
<div class="mb-6 flex items-center justify-between"><h1 class="text-2xl font-bold">کارکنان و دعوت‌ها</h1></div>
@can('carwash.members.invite')
<form method="POST" action="{{ route('carwash.members.invite',$carWash) }}" class="mb-6 grid gap-3 rounded-2xl bg-white p-5 shadow-sm md:grid-cols-4">@csrf
<input class="rounded-xl border p-3" name="mobile" placeholder="موبایل"><input class="rounded-xl border p-3" name="email" placeholder="ایمیل">
<select class="rounded-xl border p-3" name="role"><option value="carwash-manager">مدیر</option><option value="carwash-receptionist">پذیرش</option><option value="carwash-operator">اپراتور</option><option value="carwash-accountant">حسابدار</option></select>
<button class="rounded-xl bg-slate-900 p-3 text-white">ساخت دعوت</button></form>
@endcan
@if(session('invitation_url'))<div class="mb-5 rounded-xl bg-blue-50 p-4 text-blue-900">لینک دعوت: <span dir="ltr">{{ session('invitation_url') }}</span></div>@endif
<div class="overflow-x-auto rounded-2xl bg-white shadow-sm"><table class="w-full text-sm"><thead class="bg-slate-100"><tr><th class="p-3">نام</th><th>موبایل</th><th>وضعیت</th><th>عنوان</th><th>عملیات</th></tr></thead><tbody>
@forelse($members as $member)<tr class="border-t"><td class="p-3">{{ $member->full_name }}</td><td dir="ltr">{{ $member->mobile }}</td><td>{{ $member->pivot->status }}</td><td>{{ $member->pivot->job_title }}</td><td class="p-2"><div class="flex gap-2">@can('carwash.members.update')<form method="POST" action="{{ route('carwash.members.role',[$carWash,$member]) }}">@csrf @method('PUT')<select name="role" class="rounded-lg border p-2"><option value="carwash-manager">مدیر</option><option value="carwash-receptionist">پذیرش</option><option value="carwash-operator">اپراتور</option><option value="carwash-accountant">حسابدار</option></select><button class="rounded-lg bg-blue-600 px-3 py-2 text-white">تغییر</button></form>@endcan @can('carwash.members.remove')<form method="POST" action="{{ route('carwash.members.destroy',[$carWash,$member]) }}">@csrf @method('DELETE')<button class="rounded-lg bg-red-600 px-3 py-2 text-white">حذف</button></form>@endcan</div></td></tr>@empty<tr><td colspan="5" class="p-6 text-center text-slate-500">عضوی ثبت نشده است.</td></tr>@endforelse
</tbody></table></div><div class="mt-4">{{ $members->links() }}</div>
@endsection
