@php($editing = isset($carWash))
<div class="grid gap-4 md:grid-cols-2">
    <label>نام کارواش
        <input name="name" value="{{ old('name', $carWash->name ?? '') }}" class="mt-1 w-full rounded-xl border p-3" required>
    </label>
    <label>نامک
        <input name="slug" value="{{ old('slug', $carWash->slug ?? '') }}" class="mt-1 w-full rounded-xl border p-3" dir="ltr">
    </label>
    <label>کد داخلی
        <input name="code" value="{{ old('code', $carWash->code ?? '') }}" class="mt-1 w-full rounded-xl border p-3" dir="ltr">
    </label>
    @unless($editing)
        <label>وضعیت
            <select name="status" class="mt-1 w-full rounded-xl border p-3">
                @foreach(['pending','active','suspended','rejected'] as $status)
                    <option value="{{ $status }}" @selected(old('status', 'pending') === $status)>{{ $status }}</option>
                @endforeach
            </select>
        </label>
    @endunless
    <label>تلفن
        <input name="phone" value="{{ old('phone', $carWash->phone ?? '') }}" class="mt-1 w-full rounded-xl border p-3" dir="ltr">
    </label>
    <label>موبایل مجموعه
        <input name="mobile" value="{{ old('mobile', $carWash->mobile ?? '') }}" class="mt-1 w-full rounded-xl border p-3" dir="ltr">
    </label>
    <label>ایمیل
        <input type="email" name="email" value="{{ old('email', $carWash->email ?? '') }}" class="mt-1 w-full rounded-xl border p-3" dir="ltr">
    </label>
    <label>منطقه زمانی
        <input name="timezone" value="{{ old('timezone', $carWash->timezone ?? 'Asia/Tehran') }}" class="mt-1 w-full rounded-xl border p-3" dir="ltr" required>
    </label>
    <label>استان
        <input name="province" value="{{ old('province', $carWash->province ?? '') }}" class="mt-1 w-full rounded-xl border p-3">
    </label>
    <label>شهر
        <input name="city" value="{{ old('city', $carWash->city ?? '') }}" class="mt-1 w-full rounded-xl border p-3">
    </label>
    <label>کد پستی
        <input name="postal_code" value="{{ old('postal_code', $carWash->postal_code ?? '') }}" class="mt-1 w-full rounded-xl border p-3" dir="ltr">
    </label>
    <label class="md:col-span-2">آدرس
        <textarea name="address" class="mt-1 w-full rounded-xl border p-3">{{ old('address', $carWash->address ?? '') }}</textarea>
    </label>

    @unless($editing)
        <div class="md:col-span-2 mt-3 border-t pt-5">
            <h2 class="font-bold">مالک اولیه کارواش</h2>
        </div>
        <label>نام مالک
            <input name="owner_name" value="{{ old('owner_name') }}" class="mt-1 w-full rounded-xl border p-3" required>
        </label>
        <label>موبایل مالک
            <input name="owner_mobile" value="{{ old('owner_mobile') }}" class="mt-1 w-full rounded-xl border p-3" dir="ltr" required>
        </label>
    @endunless
</div>
