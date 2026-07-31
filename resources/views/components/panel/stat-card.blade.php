@props(['label', 'value', 'icon', 'hint' => null, 'tone' => 'primary'])
@php
    $tones = [
        'primary' => 'bg-primary-100 text-primary-700 dark:bg-primary-900/30 dark:text-primary-300',
        'blue' => 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300',
        'green' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300',
        'red' => 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-300',
        'violet' => 'bg-violet-100 text-violet-700 dark:bg-violet-900/30 dark:text-violet-300',
    ];
@endphp
<div class="panel-card p-5">
    <div class="flex items-start justify-between gap-4">
        <div>
            <p class="text-sm text-gray-500 dark:text-gray-400">{{ $label }}</p>
            <p class="mt-2 text-2xl font-bold text-gray-900 dark:text-white">{{ $value }}</p>
        </div>
        <span class="rounded-xl p-3 {{ $tones[$tone] ?? $tones['primary'] }}">
            <x-icon :name="$icon" class="h-6 w-6"/>
        </span>
    </div>
    @if($hint)
        <p class="mt-4 border-t border-gray-100 pt-3 text-xs text-gray-500 dark:border-gray-700 dark:text-gray-400">{{ $hint }}</p>
    @endif
</div>
