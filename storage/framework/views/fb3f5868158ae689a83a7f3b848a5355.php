<?php $__env->startSection('title', 'ایجاد کارواش'); ?>
<?php $__env->startSection('navigation'); ?>
<a class="block rounded-lg p-2 hover:bg-white/10" href="<?php echo e(route('admin.dashboard')); ?>">داشبورد</a>
<a class="block rounded-lg p-2 hover:bg-white/10" href="<?php echo e(route('admin.car-washes.index')); ?>">کارواش‌ها</a>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>
<h1 class="mb-6 text-2xl font-bold">ایجاد کارواش</h1>
<form method="POST" action="<?php echo e(route('admin.car-washes.store')); ?>" class="rounded-2xl bg-white p-6 shadow-sm">
    <?php echo csrf_field(); ?>
    <?php echo $__env->make('admin.car-washes._form', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <button class="mt-6 rounded-xl bg-slate-900 px-5 py-3 text-white">ایجاد کارواش</button>
</form>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.panel', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\carwash-laravel-management-v2\carwash\carwash-app-back\resources\views/admin/car-washes/create.blade.php ENDPATH**/ ?>