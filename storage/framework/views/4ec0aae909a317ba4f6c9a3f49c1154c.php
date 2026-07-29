<?php $__env->startSection('title', 'کارواش‌ها'); ?>
<?php $__env->startSection('navigation'); ?>
<a class="block rounded-lg p-2 hover:bg-white/10" href="<?php echo e(route('admin.dashboard')); ?>">داشبورد</a>
<a class="block rounded-lg p-2 hover:bg-white/10" href="<?php echo e(route('admin.car-washes.index')); ?>">کارواش‌ها</a>
<a class="block rounded-lg p-2 hover:bg-white/10" href="<?php echo e(route('admin.users.index')); ?>">کاربران</a>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>
<div class="mb-6 flex flex-wrap items-center justify-between gap-3">
    <h1 class="text-2xl font-bold">مدیریت کارواش‌ها</h1>
    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('platform.car-washes.create')): ?>
        <a class="rounded-xl bg-slate-900 px-5 py-3 text-white" href="<?php echo e(route('admin.car-washes.create')); ?>">ایجاد کارواش</a>
    <?php endif; ?>
</div>

<form class="mb-5 flex flex-wrap gap-3">
    <input name="q" value="<?php echo e(request('q')); ?>" class="rounded-xl border p-3" placeholder="نام یا کد">
    <select name="status" class="rounded-xl border p-3">
        <option value="">همه</option>
        <?php $__currentLoopData = ['pending','active','suspended','rejected']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $status): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <option value="<?php echo e($status); ?>" <?php if(request('status') === $status): echo 'selected'; endif; ?>><?php echo e($status); ?></option>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </select>
    <button class="rounded-xl bg-slate-900 px-5 text-white">فیلتر</button>
</form>

<div class="overflow-x-auto rounded-2xl bg-white shadow-sm">
    <table class="w-full">
        <thead class="bg-slate-100">
        <tr>
            <th class="p-3">نام</th>
            <th>کد</th>
            <th>شهر</th>
            <th>وضعیت</th>
            <th>تاریخ</th>
        </tr>
        </thead>
        <tbody>
        <?php $__empty_1 = true; $__currentLoopData = $carWashes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $wash): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <tr class="border-t">
                <td class="p-3">
                    <a class="text-blue-600" href="<?php echo e(route('admin.car-washes.show', $wash)); ?>"><?php echo e($wash->name); ?></a>
                </td>
                <td><?php echo e($wash->code); ?></td>
                <td><?php echo e($wash->city); ?></td>
                <td><?php echo e($wash->status->value); ?></td>
                <td><?php echo e($wash->created_at); ?></td>
            </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <tr><td colspan="5" class="p-6 text-center text-slate-500">کارواشی ثبت نشده است.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>

<div class="mt-4"><?php echo e($carWashes->links()); ?></div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.panel', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\carwash-laravel-management-v2\carwash\carwash-app-back\resources\views/admin/car-washes/index.blade.php ENDPATH**/ ?>