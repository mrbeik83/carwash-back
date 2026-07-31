@props(['href', 'icon', 'active' => false])
<a href="{{ $href }}"
   {{ $attributes->class([
       'flex items-center gap-3 rounded-xl px-4 py-3 text-sm font-medium',
       'bg-primary-50 text-primary-700 dark:bg-primary-900/30 dark:text-primary-300' => $active,
       'text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700' => ! $active,
   ]) }}>
    <x-icon :name="$icon" class="h-5 w-5 shrink-0"/>
    <span>{{ $slot }}</span>
    @if($active)
        <span class="mr-auto h-2 w-2 rounded-full bg-primary"></span>
    @endif
</a>
