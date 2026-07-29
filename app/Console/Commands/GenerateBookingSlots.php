<?php

namespace App\Console\Commands;

use App\Models\CarWash;
use App\Services\SlotGenerationService;
use Carbon\CarbonImmutable;
use Illuminate\Console\Command;

class GenerateBookingSlots extends Command
{
    protected $signature = 'carwash:generate-slots {--days=45} {--car-wash=}';
    protected $description = 'Generate future booking slots from weekly capacity rules';

    public function handle(SlotGenerationService $service): int
    {
        $query = CarWash::query()->where('status', 'active');

        if ($id = $this->option('car-wash')) {
            $query->whereKey($id);
        }

        $total = 0;
        $from = CarbonImmutable::now('UTC');
        $to = $from->addDays((int) $this->option('days'));

        $query->each(function (CarWash $carWash) use ($service, $from, $to, &$total): void {
            $count = $service->generate($carWash, $from, $to);
            $total += $count;
            $this->line("{$carWash->name}: {$count} slot(s)");
        });

        $this->info("Created {$total} slot(s).");
        return self::SUCCESS;
    }
}
