@if(session('success'))
    <div class="flash-message border-emerald-200 bg-emerald-50 text-emerald-800 dark:border-emerald-800 dark:bg-emerald-900/20 dark:text-emerald-200">
        <x-icon name="check" class="mt-0.5 h-5 w-5 shrink-0"/>
        <span>{{ session('success') }}</span>
    </div>
@endif

@if(session('invitation_url'))
    <div class="flash-message block border-blue-200 bg-blue-50 text-blue-800 dark:border-blue-800 dark:bg-blue-900/20 dark:text-blue-200">
        <div class="font-semibold">لینک دعوت ساخته شد</div>
        <div class="mt-2 break-all rounded-lg bg-white/70 p-3 font-latin text-xs dark:bg-gray-950/30" dir="ltr">{{ session('invitation_url') }}</div>
    </div>
@endif

@if($errors->any())
    <div class="flash-message block border-red-200 bg-red-50 text-red-800 dark:border-red-800 dark:bg-red-900/20 dark:text-red-200">
        <div class="mb-2 font-semibold">لطفاً موارد زیر را اصلاح کنید:</div>
        <ul class="list-disc space-y-1 pr-5">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
