<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Offer; // Import Offer model
use Illuminate\Support\Str; // For slug generation
use Illuminate\Support\Carbon; // For dates

class OfferSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $offers = [
            [
                'title' => 'خصومات الإلكترونيات الكبرى',
                'description' => 'وفر حتى 40% على أحدث الأجهزة الإلكترونية والهواتف الذكية.',
                'image' => '/images/seeders/offers/electronics_sale.jpg', // Replace with your image path
                'type' => 'category', // Links to an electronics category
                'linked_id' => 1, // <<< IMPORTANT: Change to your actual Electronics Category ID
                'start_date' => Carbon::now()->subDays(5),
                'end_date' => Carbon::now()->addDays(15),
                'is_active' => true,
                'sort_order' => 10,
            ],
            [
                'title' => 'تخفيضات الصيف على الأجهزة المنزلية',
                'description' => 'جهز منزلك بأفضل الأجهزة بخصومات رائعة لفترة محدودة.',
                'image' => '/images/seeders/offers/home_appliances.jpg',
                'type' => 'generic', // Generic offer, maybe links to a custom landing page
                'linked_id' => null,
                'target_url' => '/offers/summer-appliances', // Example custom link
                'start_date' => Carbon::now(),
                'end_date' => Carbon::now()->addMonth(),
                'is_active' => true,
                'sort_order' => 20,
            ],
            [
                'title' => 'عرض خاص على هواتف سامسونج',
                'description' => 'احصل على هدية مجانية عند شراء أي هاتف سامسونج جالاكسي جديد.',
                'image' => '/images/seeders/offers/samsung_phones.jpg',
                'type' => 'brand', // Links to the Samsung brand page/filter
                'linked_id' => 1, // <<< IMPORTANT: Change to your actual Samsung Brand ID
                'start_date' => Carbon::now()->subWeek(),
                'end_date' => Carbon::now()->addWeek(),
                'is_active' => true,
                'sort_order' => 30,
            ],
            [
                'title' => 'خصم إضافي على منتج الأسبوع',
                'description' => 'استخدم الكود WEEKLY10 للحصول على خصم 10% إضافي على هذا المنتج الرائع.',
                'image' => '/images/seeders/offers/product_of_week.jpg',
                'type' => 'product', // Links directly to a specific product
                'linked_id' => 5, // <<< IMPORTANT: Change to an actual Product ID
                'start_date' => Carbon::now()->startOfWeek(),
                'end_date' => Carbon::now()->endOfWeek(),
                'is_active' => true,
                'sort_order' => 40,
            ],
        ];

        foreach ($offers as $offerData) {
            // Generate slug if needed, or ensure uniqueness manually/via validation
            $offerData['slug'] = Str::slug($offerData['title']) . '-' . Str::random(4);

            Offer::updateOrCreate(
                ['slug' => $offerData['slug']], // Find by slug (or maybe title?)
                $offerData
            );
            $this->command->info("Offer '{$offerData['title']}' seeded.");
        }
    }
}