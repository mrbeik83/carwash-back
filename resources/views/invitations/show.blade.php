<!doctype html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>پذیرش دعوت کارواش</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-100 p-6 text-slate-900">
<div class="mx-auto max-w-xl rounded-3xl bg-white p-8 shadow-sm">
    <h1 class="text-2xl font-bold">دعوت به پنل کارواش</h1>
    <p class="mt-4">شما برای همکاری با <strong>{{ $invitation->carWash->name }}</strong> دعوت شده‌اید.</p>
    <p class="mt-2 text-slate-600">نقش: {{ $invitation->role_name }}</p>
    <p class="mt-2 text-slate-600">دعوت‌کننده: {{ $invitation->inviter->full_name }}</p>

    <form method="POST" action="{{ route('invitations.accept', $token) }}" class="mt-6">
        @csrf
        <button class="w-full rounded-xl bg-emerald-600 p-3 text-white">پذیرش دعوت</button>
    </form>
</div>
</body>
</html>
