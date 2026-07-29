<!doctype html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title>ورود به سامانه کارواش</title>
    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>
</head>
<body class="min-h-screen bg-slate-100 text-slate-900">
<div class="mx-auto grid min-h-screen max-w-5xl items-center gap-6 p-4 lg:grid-cols-2">
    <section class="rounded-3xl bg-slate-900 p-8 text-white">
        <h1 class="text-3xl font-bold">سامانه مدیریت کارواش</h1>
        <p class="mt-4 text-slate-300">ورود مدیر کل، مالک، مدیر و کارکنان کارواش از یک صفحه انجام می‌شود.</p>
    </section>

    <section class="space-y-5">
        <?php if(session('success')): ?>
            <div class="rounded-xl bg-emerald-100 p-3 text-emerald-900"><?php echo e(session('success')); ?></div>
        <?php endif; ?>

        <?php if($errors->any()): ?>
            <div class="rounded-xl bg-red-100 p-3 text-red-900">
                <ul class="list-disc pr-5">
                    <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <li><?php echo e($error); ?></li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </ul>
            </div>
        <?php endif; ?>

        <form method="POST" action="<?php echo e(route('login.store')); ?>" class="space-y-4 rounded-3xl bg-white p-6 shadow-sm">
            <?php echo csrf_field(); ?>
            <h2 class="text-xl font-bold">ورود با رمز عبور</h2>
            <label class="block">
                <span>شماره موبایل</span>
                <input name="mobile" value="<?php echo e(old('mobile')); ?>" class="mt-1 w-full rounded-xl border p-3" dir="ltr" required>
            </label>
            <label class="block">
                <span>رمز عبور</span>
                <input type="password" name="password" class="mt-1 w-full rounded-xl border p-3" required>
            </label>
            <label class="flex items-center gap-2">
                <input type="checkbox" name="remember" value="1">
                <span>مرا به خاطر بسپار</span>
            </label>
            <button class="w-full rounded-xl bg-slate-900 p-3 text-white">ورود</button>
        </form>

        <form method="POST" action="<?php echo e(route('login.otp.request')); ?>" class="space-y-4 rounded-3xl bg-white p-6 shadow-sm">
            <?php echo csrf_field(); ?>
            <h2 class="text-xl font-bold">ورود با کد یک‌بارمصرف</h2>
            <label class="block">
                <span>شماره موبایل</span>
                <input name="mobile" value="<?php echo e(session('otp_mobile', old('mobile'))); ?>" class="mt-1 w-full rounded-xl border p-3" dir="ltr" required>
            </label>
            <button class="w-full rounded-xl bg-blue-600 p-3 text-white">ارسال کد</button>
        </form>

        <form method="POST" action="<?php echo e(route('login.otp.verify')); ?>" class="space-y-4 rounded-3xl bg-white p-6 shadow-sm">
            <?php echo csrf_field(); ?>
            <h2 class="text-xl font-bold">تایید کد ورود</h2>
            <input name="mobile" value="<?php echo e(session('otp_mobile', old('mobile'))); ?>" class="w-full rounded-xl border p-3" dir="ltr" placeholder="شماره موبایل" required>
            <input name="code" class="w-full rounded-xl border p-3" dir="ltr" inputmode="numeric" maxlength="6" placeholder="کد ۶ رقمی" required>
            <button class="w-full rounded-xl bg-emerald-600 p-3 text-white">ورود با کد</button>
        </form>
    </section>
</div>
</body>
</html>
<?php /**PATH D:\carwash-laravel-management-v2\carwash\carwash-app-back\resources\views/auth/login.blade.php ENDPATH**/ ?>