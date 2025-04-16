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
            ['name' => 'عمان', /* 'country_id' => JORDAN_ID */], // Amman
            ['name' => 'الزرقاء', /* 'country_id' => JORDAN_ID */], // Zarqa
            ['name' => 'إربد', /* 'country_id' => JORDAN_ID */], // Irbid
            ['name' => 'السلط', /* 'country_id' => JORDAN_ID */], // Salt
            ['name' => 'العقبة', /* 'country_id' => JORDAN_ID */], // Aqaba
            ['name' => 'المفرق', /* 'country_id' => JORDAN_ID */], // Mafraq
            ['name' => 'الكرك', /* 'country_id' => JORDAN_ID */], // Karak
            ['name' => 'معان', /* 'country_id' => JORDAN_ID */], // Ma'an
            ['name' => 'مادبا', /* 'country_id' => JORDAN_ID */], // Madaba
            ['name' => 'الطفيلة', /* 'country_id' => JORDAN_ID */], // Tafilah
            ['name' => 'جرش', /* 'country_id' => JORDAN_ID */], // Jerash
            ['name' => 'عجلون', /* 'country_id' => JORDAN_ID */], // Ajloun
            ['name' => 'الرصيفة', /* 'country_id' => JORDAN_ID */], // Russeifa (part of Zarqa Gov.)
            ['name' => 'وادي السير', /* 'country_id' => JORDAN_ID */], // Wadi Al-Seer (part of Amman Gov.)
            ['name' => 'الرمثا', /* 'country_id' => JORDAN_ID */], // Ramtha (part of Irbid Gov.)
        ];

        foreach ($cities as $cityData) {
            City::updateOrCreate(
                ['name' => $cityData['name']], // Use name as the unique key
                $cityData
            );
        }
    }
}