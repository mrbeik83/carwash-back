@php($editing = isset($carWash))
<div class="grid gap-5 md:grid-cols-2">
    <div>
        <label class="form-label">نام کارواش <span class="text-red-500">*</span></label>
        <input name="name" value="{{ old('name', $carWash->name ?? '') }}" class="form-control" required>
    </div>
    <div>
        <label class="form-label">نامک انگلیسی</label>
        <input name="slug" value="{{ old('slug', $carWash->slug ?? '') }}" class="form-control" dir="ltr" placeholder="arya-carwash">
    </div>
    <div>
        <label class="form-label">کد داخلی</label>
        <input name="code" value="{{ old('code', $carWash->code ?? '') }}" class="form-control" dir="ltr" placeholder="CW-001">
    </div>
    @unless($editing)
        <div>
            <label class="form-label">وضعیت اولیه</label>
            <select name="status" class="form-select">
                @foreach(['pending' => 'در انتظار', 'active' => 'فعال', 'suspended' => 'تعلیق', 'rejected' => 'ردشده'] as $value => $label)
                    <option value="{{ $value }}" @selected(old('status', 'pending') === $value)>{{ $label }}</option>
                @endforeach
            </select>
        </div>
    @endunless
    <div>
        <label class="form-label">شماره تلفن</label>
        <input type="tel" inputmode="tel" autocomplete="tel" name="phone" value="{{ old('phone', $carWash->phone ?? '') }}" class="form-control" dir="ltr">
    </div>
    <div>
        <label class="form-label">شماره موبایل مجموعه</label>
        <input type="tel" inputmode="tel" autocomplete="tel" name="mobile" value="{{ old('mobile', $carWash->mobile ?? '') }}" class="form-control" dir="ltr">
    </div>
    <div>
        <label class="form-label">ایمیل</label>
        <input type="email" name="email" value="{{ old('email', $carWash->email ?? '') }}" class="form-control" dir="ltr">
    </div>
    <div>
        <label class="form-label">منطقه زمانی <span class="text-red-500">*</span></label>
        <input name="timezone" value="{{ old('timezone', $carWash->timezone ?? 'Asia/Tehran') }}" class="form-control" dir="ltr" required>
    </div>
    <div>
        <label class="form-label">استان</label>
        <input name="province" value="{{ old('province', $carWash->province ?? '') }}" class="form-control">
    </div>
    <div>
        <label class="form-label">شهر</label>
        <input name="city" value="{{ old('city', $carWash->city ?? '') }}" class="form-control">
    </div>
    <div>
        <label class="form-label">کد پستی</label>
        <input inputmode="numeric" autocomplete="postal-code" name="postal_code" value="{{ old('postal_code', $carWash->postal_code ?? '') }}" class="form-control" dir="ltr">
    </div>
    <div class="md:col-span-2">
        <label class="form-label">آدرس کامل</label>
        <textarea name="address" rows="3" class="form-control">{{ old('address', $carWash->address ?? '') }}</textarea>
    </div>

    @unless($editing)
        <div class="md:col-span-2 mt-2 border-t border-gray-200 pt-5 dark:border-gray-700">
            <h3 class="font-bold text-gray-900 dark:text-white">مالک اولیه کارواش</h3>
            <p class="mt-1 text-sm text-gray-500">حساب مالک ساخته یا با شماره موبایل موجود متصل می‌شود.</p>
        </div>
        <div>
            <label class="form-label">نام و نام خانوادگی مالک <span class="text-red-500">*</span></label>
            <input name="owner_name" value="{{ old('owner_name') }}" class="form-control" required>
        </div>
        <div>
            <label class="form-label">موبایل مالک <span class="text-red-500">*</span></label>
            <input type="tel" inputmode="tel" autocomplete="tel" name="owner_mobile" value="{{ old('owner_mobile') }}" class="form-control" dir="ltr" required>
        </div>
    @endunless
</div>
