<!doctype html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>عدم دسترسی</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-100 p-6 text-slate-900">
<div class="mx-auto max-w-xl rounded-3xl bg-white p-8 shadow-sm">
    <h1 class="text-2xl font-bold">پنل فعالی برای شما پیدا نشد</h1>
    <p class="mt-3 text-slate-600">برای ورود به پنل کارواش باید دعوت‌نامه را بپذیرید یا عضویت فعال داشته باشید.</p>
    <form method="POST" action="{{ route('logout') }}" class="mt-6">
        @csrf
        <button class="rounded-xl bg-slate-900 px-5 py-3 text-white">خروج</button>
    </form>
</div>
</body>
</html>
