<?php $__env->startSection('title', 'مدیریت کل'); ?>
<?php $__env->startSection('navigation'); ?>
<a class="block rounded-lg p-2 hover:bg-white/10" href="<?php echo e(route('admin.dashboard')); ?>">داشبورد</a>
<a class="block rounded-lg p-2 hover:bg-white/10" href="<?php echo e(route('admin.car-washes.index')); ?>">کارواش‌ها</a>
<a class="block rounded-lg p-2 hover:bg-white/10" href="<?php echo e(route('admin.users.index')); ?>">کاربران</a>
<a class="block rounded-lg p-2 hover:bg-white/10" href="<?php echo e(route('admin.bookings.index')); ?>">همه رزروها</a>
<a class="block rounded-lg p-2 hover:bg-white/10" href="<?php echo e(route('admin.finance.index')); ?>">مالی</a>
<a class="block rounded-lg p-2 hover:bg-white/10" href="<?php echo e(route('admin.audit-logs.index')); ?>">لاگ‌ها</a>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>
<h1 class="mb-6 text-2xl font-bold">داشبورد مدیریت کل</h1>
<div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-5">
<?php $__currentLoopData = [
    'کارواش‌ها' => $summary['car_washes'],
    'در انتظار تایید' => $summary['pending_car_washes'],
    'کاربران' => $summary['users'],
    'رزرو امروز' => $summary['bookings_today'],
    'پرداخت امروز (ریال)' => number_format($summary['revenue_today']),
]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $label => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
<div class="rounded-2xl bg-white p-5 shadow-sm"><div class="text-sm text-slate-500"><?php echo e($label); ?></div><div class="mt-2 text-2xl font-bold"><?php echo e($value); ?></div></div>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.panel', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\carwash-laravel-management-v2\carwash\carwash-app-back\resources\views/admin/dashboard.blade.php ENDPATH**/ ?>