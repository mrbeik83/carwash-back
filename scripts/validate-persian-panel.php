<?php

declare(strict_types=1);

use App\Support\PersianDate;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\View\Compilers\BladeCompiler;

require dirname(__DIR__).'/vendor/autoload.php';

$root = dirname(__DIR__);
$app = require $root.'/bootstrap/app.php';
$app->make(Kernel::class)->bootstrap();
$failures = [];
$passes = [];

$assert = static function (bool $condition, string $message) use (&$failures, &$passes): void {
    if ($condition) {
        $passes[] = $message;
        return;
    }

    $failures[] = $message;
};

$datePairs = [
    ['2026-07-31', '1405/05/09'],
    ['2026-08-01', '1405/05/10'],
    ['2025-03-20', '1403/12/30'],
    ['2025-03-21', '1404/01/01'],
];

foreach ($datePairs as [$gregorian, $jalali]) {
    $displayJalali = PersianDate::digits($jalali);
    $assert(PersianDate::date($gregorian) === $displayJalali, "تبدیل {$gregorian} به {$jalali}");

    [$jy, $jm, $jd] = array_map('intval', explode('/', $jalali));
    [$gy, $gm, $gd] = PersianDate::jalaliToGregorian($jy, $jm, $jd);
    $roundTrip = sprintf('%04d-%02d-%02d', $gy, $gm, $gd);
    $assert($roundTrip === $gregorian, "تبدیل برگشتی {$jalali} به {$gregorian}");
}

$carWashViews = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($root.'/resources/views/carwash'),
);

foreach ($carWashViews as $file) {
    if (! $file->isFile() || $file->getExtension() !== 'php') {
        continue;
    }

    $contents = file_get_contents($file->getPathname());
    $assert(
        ! preg_match('/type=["\'](?:date|datetime-local)["\']/i', $contents),
        'نبود ورودی تاریخ میلادی در '.str_replace($root.'/', '', $file->getPathname()),
    );
}

/** @var BladeCompiler $compiler */
$compiler = $app->make('blade.compiler');
$viewFiles = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($root.'/resources/views'),
);

$compiledCount = 0;
foreach ($viewFiles as $file) {
    if (! $file->isFile() || ! str_ends_with($file->getFilename(), '.blade.php')) {
        continue;
    }

    try {
        $compiler->compileString((string) file_get_contents($file->getPathname()));
        $compiledCount++;
    } catch (Throwable $exception) {
        $failures[] = 'کامپایل Blade شکست خورد: '.str_replace($root.'/', '', $file->getPathname()).' — '.$exception->getMessage();
    }
}
$assert($compiledCount > 0, "کامپایل نحوی {$compiledCount} فایل Blade");

$requiredFiles = [
    'app/Support/PersianDate.php',
    'app/Http/Requests/CarWashPanel/SaveWeeklyScheduleRequest.php',
    'app/Http/Resources/Api/V1/BookingResource.php',
    'database/migrations/2026_07_31_000100_add_slot_capacities_to_capacity_rules_table.php',
    'docs/15-FRONTEND-INTEGRATION-FA.md',
    'docs/openapi.yaml',
];

foreach ($requiredFiles as $requiredFile) {
    $assert(is_file($root.'/'.$requiredFile), 'وجود فایل '.$requiredFile);
}

echo "نتایج اعتبارسنجی پنل فارسی کارواش\n";
echo str_repeat('=', 52)."\n";
foreach ($passes as $pass) {
    echo "[OK] {$pass}\n";
}

if ($failures !== []) {
    echo "\nخطاها:\n";
    foreach ($failures as $failure) {
        echo "[FAIL] {$failure}\n";
    }
    exit(1);
}

echo "\nهمه بررسی‌های استاتیک با موفقیت انجام شدند.\n";
