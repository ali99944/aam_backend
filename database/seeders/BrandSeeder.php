<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Brand; // Import Brand model

class BrandSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $brands = [
            ['name' => 'سامسونج', 'image' => '/images/seeders/brands/samsung_logo.png'],
            ['name' => 'آبل', 'image' => '/images/seeders/brands/apple_logo.png'],
            ['name' => 'هواوي', 'image' => '/images/seeders/brands/huawei_logo.png'],
            ['name' => 'إل جي', 'image' => '/images/seeders/brands/lg_logo.png'],
            ['name' => 'سوني', 'image' => '/images/seeders/brands/sony_logo.png'],
            ['name' => 'شاومي', 'image' => '/images/seeders/brands/xiaomi_logo.png'],
            ['name' => 'توشيبا', 'image' => '/images/seeders/brands/toshiba_logo.png'],
            ['name' => 'ديل', 'image' => '/images/seeders/brands/dell_logo.png'],
            ['name' => 'إتش بي', 'image' => '/images/seeders/brands/hp_logo.png'],
            ['name' => 'لينوفو', 'image' => '/images/seeders/brands/lenovo_logo.png'],
        ];

        foreach ($brands as $brandData) {
            Brand::updateOrCreate(
                ['name' => $brandData['name']], // Find by name
                $brandData // Data to update or create
            );
            $this->command->info("Brand '{$brandData['name']}' seeded.");
        }
    }
}