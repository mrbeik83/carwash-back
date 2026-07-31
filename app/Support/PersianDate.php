<?php

namespace App\Support;

use Carbon\CarbonImmutable;
use Carbon\CarbonInterface;
use DateTimeInterface;

final class PersianDate
{
    private const MONTHS = [
        1 => 'فروردین', 'اردیبهشت', 'خرداد', 'تیر', 'مرداد', 'شهریور',
        'مهر', 'آبان', 'آذر', 'دی', 'بهمن', 'اسفند',
    ];

    private const WEEKDAYS = [
        0 => 'یکشنبه',
        1 => 'دوشنبه',
        2 => 'سه‌شنبه',
        3 => 'چهارشنبه',
        4 => 'پنجشنبه',
        5 => 'جمعه',
        6 => 'شنبه',
    ];

    public static function date(DateTimeInterface|string|null $date, string $timezone = 'Asia/Tehran'): string
    {
        if ($date === null || $date === '') {
            return '—';
        }

        $carbon = self::carbon($date, $timezone);
        [$year, $month, $day] = self::gregorianToJalali(
            (int) $carbon->format('Y'),
            (int) $carbon->format('n'),
            (int) $carbon->format('j'),
        );

        return self::digits(sprintf('%04d/%02d/%02d', $year, $month, $day));
    }

    public static function dateTime(DateTimeInterface|string|null $date, string $timezone = 'Asia/Tehran'): string
    {
        if ($date === null || $date === '') {
            return '—';
        }

        $carbon = self::carbon($date, $timezone);

        return self::date($carbon, $timezone).'، '.self::digits($carbon->format('H:i'));
    }

    public static function human(DateTimeInterface|string|null $date, string $timezone = 'Asia/Tehran', bool $withTime = false): string
    {
        if ($date === null || $date === '') {
            return '—';
        }

        $carbon = self::carbon($date, $timezone);
        [$year, $month, $day] = self::gregorianToJalali(
            (int) $carbon->format('Y'),
            (int) $carbon->format('n'),
            (int) $carbon->format('j'),
        );

        $result = self::WEEKDAYS[$carbon->dayOfWeek].'، '
            .self::digits((string) $day).' '.self::MONTHS[$month].' '.self::digits((string) $year);

        if ($withTime) {
            $result .= ' ساعت '.self::digits($carbon->format('H:i'));
        }

        return $result;
    }

    public static function short(DateTimeInterface|string|null $date, string $timezone = 'Asia/Tehran'): string
    {
        if ($date === null || $date === '') {
            return '—';
        }

        $carbon = self::carbon($date, $timezone);
        [, $month, $day] = self::gregorianToJalali(
            (int) $carbon->format('Y'),
            (int) $carbon->format('n'),
            (int) $carbon->format('j'),
        );

        return self::digits((string) $day).' '.self::MONTHS[$month];
    }

    public static function weekday(DateTimeInterface|string|null $date, string $timezone = 'Asia/Tehran'): string
    {
        if ($date === null || $date === '') {
            return '—';
        }

        return self::WEEKDAYS[self::carbon($date, $timezone)->dayOfWeek];
    }

    public static function digits(string|int|float|null $value): string
    {
        return strtr((string) $value, [
            '0' => '۰', '1' => '۱', '2' => '۲', '3' => '۳', '4' => '۴',
            '5' => '۵', '6' => '۶', '7' => '۷', '8' => '۸', '9' => '۹',
        ]);
    }

    /** @return array{0:int,1:int,2:int} */
    public static function gregorianToJalali(int $gy, int $gm, int $gd): array
    {
        $gDayNo = 365 * ($gy - 1600)
            + intdiv($gy - 1600 + 3, 4)
            - intdiv($gy - 1600 + 99, 100)
            + intdiv($gy - 1600 + 399, 400);

        $gMonthDays = [31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31];
        for ($i = 0; $i < $gm - 1; $i++) {
            $gDayNo += $gMonthDays[$i];
        }

        if ($gm > 2 && self::isGregorianLeap($gy)) {
            $gDayNo++;
        }

        $gDayNo += $gd - 1;
        $jDayNo = $gDayNo - 79;
        $jNp = intdiv($jDayNo, 12053);
        $jDayNo %= 12053;

        $jy = 979 + 33 * $jNp + 4 * intdiv($jDayNo, 1461);
        $jDayNo %= 1461;

        if ($jDayNo >= 366) {
            $jy += intdiv($jDayNo - 1, 365);
            $jDayNo = ($jDayNo - 1) % 365;
        }

        $jMonthDays = [31, 31, 31, 31, 31, 31, 30, 30, 30, 30, 30, 29];
        for ($i = 0; $i < 11 && $jDayNo >= $jMonthDays[$i]; $i++) {
            $jDayNo -= $jMonthDays[$i];
        }

        return [$jy, $i + 1, $jDayNo + 1];
    }

    /** @return array{0:int,1:int,2:int} */
    public static function jalaliToGregorian(int $jy, int $jm, int $jd): array
    {
        $jy -= 979;
        $jDayNo = 365 * $jy + intdiv($jy, 33) * 8 + intdiv(($jy % 33) + 3, 4);

        for ($i = 0; $i < $jm - 1; $i++) {
            $jDayNo += $i < 6 ? 31 : 30;
        }
        $jDayNo += $jd - 1;

        $gDayNo = $jDayNo + 79;
        $gy = 1600 + 400 * intdiv($gDayNo, 146097);
        $gDayNo %= 146097;

        $leap = true;
        if ($gDayNo >= 36525) {
            $gDayNo--;
            $gy += 100 * intdiv($gDayNo, 36524);
            $gDayNo %= 36524;

            if ($gDayNo >= 365) {
                $gDayNo++;
            } else {
                $leap = false;
            }
        }

        $gy += 4 * intdiv($gDayNo, 1461);
        $gDayNo %= 1461;

        if ($gDayNo >= 366) {
            $leap = false;
            $gDayNo--;
            $gy += intdiv($gDayNo, 365);
            $gDayNo %= 365;
        }

        $gMonthDays = [31, ($leap ? 29 : 28), 31, 30, 31, 30, 31, 31, 30, 31, 30, 31];
        for ($i = 0; $gDayNo >= $gMonthDays[$i]; $i++) {
            $gDayNo -= $gMonthDays[$i];
        }

        return [$gy, $i + 1, $gDayNo + 1];
    }

    private static function isGregorianLeap(int $year): bool
    {
        return ($year % 4 === 0 && $year % 100 !== 0) || $year % 400 === 0;
    }

    private static function carbon(DateTimeInterface|string $date, string $timezone): CarbonImmutable
    {
        if ($date instanceof CarbonInterface) {
            return CarbonImmutable::instance($date)->setTimezone($timezone);
        }

        if ($date instanceof DateTimeInterface) {
            return CarbonImmutable::instance($date)->setTimezone($timezone);
        }

        return CarbonImmutable::parse($date, $timezone);
    }
}
