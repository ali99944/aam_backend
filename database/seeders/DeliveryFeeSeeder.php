<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\City; // Adjust namespace if needed
use App\Models\DeliveryFee;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;

class DeliveryFeeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Optional: Truncate if needed
        // Schema::disableForeignKeyConstraints();
        // DeliveryFee::truncate();
        // Schema::enableForeignKeyConstraints();

        // Define fees per city name
        $fees = [
            'عمان' => ['amount' => 2.50, 'time' => '1-2 أيام عمل'],
            'وادي السير' => ['amount' => 2.50, 'time' => '1-2 أيام عمل'], // Example: Same as Amman center
            'الزرقاء' => ['amount' => 3.00, 'time' => '1-2 أيام عمل'],
            'الرصيفة' => ['amount' => 3.00, 'time' => '1-2 أيام عمل'],
            'إربد' => ['amount' => 3.50, 'time' => '2-3 أيام عمل'],
            'الرمثا' => ['amount' => 3.50, 'time' => '2-3 أيام عمل'],
            'السلط' => ['amount' => 3.00, 'time' => '1-2 أيام عمل'],
            'العقبة' => ['amount' => 5.00, 'time' => '3-4 أيام عمل'], // Further away
            'المفرق' => ['amount' => 4.00, 'time' => '2-3 أيام عمل'],
            'الكرك' => ['amount' => 4.50, 'time' => '2-4 أيام عمل'],
            'معان' => ['amount' => 5.00, 'time' => '3-4 أيام عمل'],
            'مادبا' => ['amount' => 3.00, 'time' => '1-2 أيام عمل'],
            'الطفيلة' => ['amount' => 4.50, 'time' => '2-4 أيام عمل'],
            'جرش' => ['amount' => 3.50, 'time' => '2-3 أيام عمل'],
            'عجلون' => ['amount' => 3.50, 'time' => '2-3 أيام عمل'],
        ];

        foreach ($fees as $cityName => $feeData) {
            $city = City::where('name', $cityName)->first();

            if ($city) {
                DeliveryFee::updateOrCreate(
                    ['city_id' => $city->id], // Unique key
                    [
                        'amount' => $feeData['amount'],
                        'estimated_delivery_time' => $feeData['time'],
                        'is_active' => true,
                    ]
                );
            } else {
                Log::warning("DeliveryFeeSeeder: City '{$cityName}' not found in the database. Skipping fee creation.");
                 $this->command->warn("City '{$cityName}' not found. Skipping delivery fee.");
            }
        }

        // Optional: Set a default/fallback fee in your application settings/config
        // config(['app.default_delivery_fee' => 4.00]);
    }
}