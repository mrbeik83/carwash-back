@props(['title' => 'اطلاعاتی وجود ندارد', 'description' => null, 'icon' => 'bookings'])
<div class="py-12 text-center">
    <span class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-gray-100 text-gray-400 dark:bg-gray-700">
        <x-icon :name="$icon" class="h-7 w-7"/>
    </span>
    <h3 class="mt-4 font-bold text-gray-800 dark:text-white">{{ $title }}</h3>
    @if($description)
        <p class="mx-auto mt-2 max-w-md text-sm text-gray-500 dark:text-gray-400">{{ $description }}</p>
    @endif
    @if(trim($slot))
        <div class="mt-5">{{ $slot }}</div>
    @endif
</div>
