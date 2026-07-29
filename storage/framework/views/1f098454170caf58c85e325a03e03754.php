<?php ($editing = isset($carWash)); ?>
<div class="grid gap-4 md:grid-cols-2">
    <label>نام کارواش
        <input name="name" value="<?php echo e(old('name', $carWash->name ?? '')); ?>" class="mt-1 w-full rounded-xl border p-3" required>
    </label>
    <label>نامک
        <input name="slug" value="<?php echo e(old('slug', $carWash->slug ?? '')); ?>" class="mt-1 w-full rounded-xl border p-3" dir="ltr">
    </label>
    <label>کد داخلی
        <input name="code" value="<?php echo e(old('code', $carWash->code ?? '')); ?>" class="mt-1 w-full rounded-xl border p-3" dir="ltr">
    </label>
    <?php if (! ($editing)): ?>
        <label>وضعیت
            <select name="status" class="mt-1 w-full rounded-xl border p-3">
                <?php $__currentLoopData = ['pending','active','suspended','rejected']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $status): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($status); ?>" <?php if(old('status', 'pending') === $status): echo 'selected'; endif; ?>><?php echo e($status); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </label>
    <?php endif; ?>
    <label>تلفن
        <input name="phone" value="<?php echo e(old('phone', $carWash->phone ?? '')); ?>" class="mt-1 w-full rounded-xl border p-3" dir="ltr">
    </label>
    <label>موبایل مجموعه
        <input name="mobile" value="<?php echo e(old('mobile', $carWash->mobile ?? '')); ?>" class="mt-1 w-full rounded-xl border p-3" dir="ltr">
    </label>
    <label>ایمیل
        <input type="email" name="email" value="<?php echo e(old('email', $carWash->email ?? '')); ?>" class="mt-1 w-full rounded-xl border p-3" dir="ltr">
    </label>
    <label>منطقه زمانی
        <input name="timezone" value="<?php echo e(old('timezone', $carWash->timezone ?? 'Asia/Tehran')); ?>" class="mt-1 w-full rounded-xl border p-3" dir="ltr" required>
    </label>
    <label>استان
        <input name="province" value="<?php echo e(old('province', $carWash->province ?? '')); ?>" class="mt-1 w-full rounded-xl border p-3">
    </label>
    <label>شهر
        <input name="city" value="<?php echo e(old('city', $carWash->city ?? '')); ?>" class="mt-1 w-full rounded-xl border p-3">
    </label>
    <label>کد پستی
        <input name="postal_code" value="<?php echo e(old('postal_code', $carWash->postal_code ?? '')); ?>" class="mt-1 w-full rounded-xl border p-3" dir="ltr">
    </label>
    <label class="md:col-span-2">آدرس
        <textarea name="address" class="mt-1 w-full rounded-xl border p-3"><?php echo e(old('address', $carWash->address ?? '')); ?></textarea>
    </label>

    <?php if (! ($editing)): ?>
        <div class="md:col-span-2 mt-3 border-t pt-5">
            <h2 class="font-bold">مالک اولیه کارواش</h2>
        </div>
        <label>نام مالک
            <input name="owner_name" value="<?php echo e(old('owner_name')); ?>" class="mt-1 w-full rounded-xl border p-3" required>
        </label>
        <label>موبایل مالک
            <input name="owner_mobile" value="<?php echo e(old('owner_mobile')); ?>" class="mt-1 w-full rounded-xl border p-3" dir="ltr" required>
        </label>
    <?php endif; ?>
</div>
<?php /**PATH D:\carwash-laravel-management-v2\carwash\carwash-app-back\resources\views/admin/car-washes/_form.blade.php ENDPATH**/ ?>