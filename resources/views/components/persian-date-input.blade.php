@props([
    'name',
    'value' => null,
    'label' => null,
    'placeholder' => 'انتخاب تاریخ',
    'required' => false,
    'min' => null,
    'max' => null,
])
@php
    $resolvedValue = old($name, $value);
    if ($resolvedValue instanceof \DateTimeInterface) {
        $resolvedValue = $resolvedValue->format('Y-m-d');
    } elseif (is_string($resolvedValue) && preg_match('/^\d{4}-\d{2}-\d{2}/', $resolvedValue, $dateMatch)) {
        $resolvedValue = $dateMatch[0];
    }
    $displayValue = $resolvedValue ? \App\Support\PersianDate::human($resolvedValue) : '';
    $inputId = $attributes->get('id') ?: 'persian-date-'.preg_replace('/[^a-zA-Z0-9_-]/', '-', $name).'-'.uniqid();
    $hasError = $errors->has($name);
@endphp
<div
    class="persian-date-field"
    data-persian-date-field
    data-placeholder="{{ $placeholder }}"
>
    @if($label)
        <label for="{{ $inputId }}-display" class="form-label">
            {{ $label }}
            @if($required)<span class="text-red-500" aria-hidden="true">*</span>@endif
        </label>
    @endif

    <input
        type="hidden"
        id="{{ $inputId }}"
        name="{{ $name }}"
        value="{{ $resolvedValue }}"
        data-persian-date-value
        @if($required) required @endif
        @if($min) min="{{ $min }}" @endif
        @if($max) max="{{ $max }}" @endif
        @if($hasError) aria-invalid="true" aria-describedby="{{ $inputId }}-error" @endif
    >

    <button
        type="button"
        id="{{ $inputId }}-display"
        data-persian-date-trigger
        class="form-control flex items-center justify-between gap-3 text-right {{ $displayValue ? '' : 'text-gray-400' }} {{ $hasError ? 'border-red-500' : '' }}"
        aria-haspopup="dialog"
        aria-expanded="false"
        @if($hasError) aria-invalid="true" aria-describedby="{{ $inputId }}-error" @endif
    >
        <span class="min-w-0 truncate" data-persian-date-label>{{ $displayValue ?: $placeholder }}</span>
        <x-icon name="schedule" class="h-4 w-4 shrink-0 text-gray-400"/>
    </button>

    @error($name)
        <span id="{{ $inputId }}-error" class="form-error">{{ $message }}</span>
    @enderror
</div>
