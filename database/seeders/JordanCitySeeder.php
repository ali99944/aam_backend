<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\City; // Adjust namespace if needed
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class JordanCitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Optional: Truncate if needed
        // Schema::disableForeignKeyConstraints();
        // City::truncate();
        // Schema::enableForeignKeyConstraints();

        $cities = [
            // Major Governorates/Cities - Add more specific cities if needed
            ['name' => 'عمان'], // Amman
            ['name' => 'الزرقاء'], // Zarqa
            ['name' => 'إربد'], // Irbid
            ['name' => 'السلط'], // Salt
            ['name' => 'العقبة'], // Aqaba
            ['name' => 'المفرق'], // Mafraq
            ['name' => 'الكرك'], // Karak
            ['name' => 'معان'], // Ma'an
            ['name' => 'مادبا'], // Madaba
            ['name' => 'الطفيلة'], // Tafilah
            ['name' => 'جرش'], // Jerash
            ['name' => 'عجلون'], // Ajloun
            ['name' => 'الرصيفة'], // Russeifa (part of Zarqa Gov.)
            ['name' => 'وادي السير'], // Wadi Al-Seer (part of Amman Gov.)
            ['name' => 'الرمثا'], // Ramtha (part of Irbid Gov.)
        ];

        foreach ($cities as $cityData) {
            City::updateOrCreate(
                ['name' => $cityData['name']], // Use name as the unique key
                $cityData
            );
        }
    }
}
