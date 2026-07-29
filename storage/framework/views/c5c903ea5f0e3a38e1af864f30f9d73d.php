<!doctype html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title><?php echo $__env->yieldContent('title', config('app.name')); ?></title>
    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>
</head>
<body class="bg-slate-50 text-slate-900">
<div class="min-h-screen lg:grid lg:grid-cols-[260px_1fr]">
    <aside class="bg-slate-900 p-5 text-white">
        <div class="mb-2 text-lg font-bold"><?php echo e($carWash->name ?? 'مدیریت پلتفرم'); ?></div>
        <div class="mb-6 text-xs text-slate-400"><?php echo e(auth()->user()?->full_name ?: auth()->user()?->mobile); ?></div>

        <nav class="space-y-2 text-sm">
            <?php echo $__env->yieldContent('navigation'); ?>
        </nav>

        <form method="POST" action="<?php echo e(route('logout')); ?>" class="mt-8 border-t border-white/10 pt-4">
            <?php echo csrf_field(); ?>
            <button class="w-full rounded-lg bg-white/10 p-2 text-right hover:bg-white/20">خروج</button>
        </form>
    </aside>

    <main class="p-4 md:p-8">
        <?php if(session('success')): ?>
            <div class="mb-4 rounded-xl bg-emerald-100 p-3 text-emerald-900"><?php echo e(session('success')); ?></div>
        <?php endif; ?>

        <?php if(session('invitation_url')): ?>
            <div class="mb-4 rounded-xl bg-blue-100 p-3 text-blue-900">
                لینک دعوت:
                <span class="break-all" dir="ltr"><?php echo e(session('invitation_url')); ?></span>
            </div>
        <?php endif; ?>

        <?php if($errors->any()): ?>
            <div class="mb-4 rounded-xl bg-red-100 p-3 text-red-900">
                <ul class="list-disc pr-5">
                    <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <li><?php echo e($error); ?></li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </ul>
            </div>
        <?php endif; ?>

        <?php echo $__env->yieldContent('content'); ?>
    </main>
</div>
</body>
</html>
<?php /**PATH D:\carwash-laravel-management-v2\carwash\carwash-app-back\resources\views/layouts/panel.blade.php ENDPATH**/ ?>