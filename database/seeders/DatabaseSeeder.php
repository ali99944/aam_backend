<?php

namespace Database\Seeders;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            BrandSeeder::class,
            OfferSeeder::class,
            LanguageSeeder::class,
            DiscountSeeder::class,
            CategorySeeder::class,
            SubCategorySeeder::class,
            PaymentMethodSeeder::class,
            ProductSeeder::class,
            JordanCitySeeder::class,
            DeliveryFeeSeeder::class,
            SeoSeeder::class,
            FaqCategorySeeder::class,
            FaqSeeder::class,
            PolicySeeder::class,
            BannerSeeder::class,
            TestimonialSeeder::class
        ]);
    }
}
