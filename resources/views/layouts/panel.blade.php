<!doctype html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', config('app.name'))</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-50 text-slate-900">
<div class="min-h-screen lg:grid lg:grid-cols-[260px_1fr]">
    <aside class="bg-slate-900 p-5 text-white">
        <div class="mb-2 text-lg font-bold">{{ $carWash->name ?? 'مدیریت پلتفرم' }}</div>
        <div class="mb-6 text-xs text-slate-400">{{ auth()->user()?->full_name ?: auth()->user()?->mobile }}</div>

        <nav class="space-y-2 text-sm">
            @yield('navigation')
        </nav>

        <form method="POST" action="{{ route('logout') }}" class="mt-8 border-t border-white/10 pt-4">
            @csrf
            <button class="w-full rounded-lg bg-white/10 p-2 text-right hover:bg-white/20">خروج</button>
        </form>
    </aside>

    <main class="p-4 md:p-8">
        @if(session('success'))
            <div class="mb-4 rounded-xl bg-emerald-100 p-3 text-emerald-900">{{ session('success') }}</div>
        @endif

        @if(session('invitation_url'))
            <div class="mb-4 rounded-xl bg-blue-100 p-3 text-blue-900">
                لینک دعوت:
                <span class="break-all" dir="ltr">{{ session('invitation_url') }}</span>
            </div>
        @endif

        @if($errors->any())
            <div class="mb-4 rounded-xl bg-red-100 p-3 text-red-900">
                <ul class="list-disc pr-5">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @yield('content')
    </main>
</div>
</body>
</html>
