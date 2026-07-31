@props(['value'])
@php
    $raw = $value instanceof \BackedEnum ? $value->value : (string) $value;
    $labels = [
        'active' => 'فعال', 'inactive' => 'غیرفعال', 'pending' => 'در انتظار', 'suspended' => 'تعلیق‌شده', 'rejected' => 'ردشده',
        'confirmed' => 'تأییدشده', 'checked_in' => 'مراجعه کرده', 'in_progress' => 'در حال انجام',
        'completed' => 'تکمیل‌شده', 'cancelled' => 'لغوشده', 'no_show' => 'عدم مراجعه',
        'paid' => 'پرداخت‌شده', 'unpaid' => 'پرداخت‌نشده', 'partial' => 'پرداخت ناقص',
        'processing' => 'در حال پردازش', 'failed' => 'ناموفق', 'refunded' => 'بازگشت وجه',
        'partially_refunded' => 'بازگشت بخشی',
        'invited' => 'دعوت‌شده', 'removed' => 'حذف‌شده',
        'open' => 'باز', 'full' => 'تکمیل ظرفیت', 'closed' => 'بسته', 'blocked' => 'مسدود',
    ];
    $classes = match ($raw) {
        'active', 'completed', 'paid', 'open' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300',
        'pending', 'processing', 'invited', 'partial' => 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-300',
        'confirmed', 'checked_in', 'in_progress' => 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300',
        'inactive', 'suspended', 'cancelled', 'rejected', 'failed', 'removed', 'blocked', 'closed' => 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-300',
        default => 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300',
    };
@endphp
<span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold {{ $classes }}">{{ $labels[$raw] ?? $raw }}</span>
