<?php

namespace Database\Seeders;

use App\Models\VehicleType;
use Illuminate\Database\Seeder;

class VehicleTypeSeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            ['name' => 'سواری', 'slug' => 'sedan', 'size_class' => 'medium', 'sort_order' => 10],
            ['name' => 'شاسی‌بلند', 'slug' => 'suv', 'size_class' => 'large', 'sort_order' => 20],
            ['name' => 'وانت', 'slug' => 'pickup', 'size_class' => 'large', 'sort_order' => 30],
            ['name' => 'ون', 'slug' => 'van', 'size_class' => 'large', 'sort_order' => 40],
            ['name' => 'موتورسیکلت', 'slug' => 'motorcycle', 'size_class' => 'small', 'sort_order' => 50],
        ];

        foreach ($items as $item) {
            VehicleType::query()->updateOrCreate(['slug' => $item['slug']], [...$item, 'is_active' => true]);
        }
    }
}
