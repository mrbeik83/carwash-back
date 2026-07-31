@props(['value', 'timezone' => 'Asia/Tehran', 'time' => false, 'human' => false])
@if($human)
    {{ \App\Support\PersianDate::human($value, $timezone, $time) }}
@elseif($time)
    {{ \App\Support\PersianDate::dateTime($value, $timezone) }}
@else
    {{ \App\Support\PersianDate::date($value, $timezone) }}
@endif
