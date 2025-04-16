<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\SubCategory;
use App\Models\Brand;
use App\Models\Discount;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Optional: Clear the table first (Use with caution!)
        // Schema::disableForeignKeyConstraints();
        // DB::table('products')->truncate();
        // Schema::enableForeignKeyConstraints();

        $subCategories = SubCategory::where('is_active', true)->get();
        $brands = Brand::all(); // Assuming brands table is seeded
        $discounts = Discount::where('status', 'active')->get(); // Optional: get active discounts

        if ($subCategories->isEmpty()) {
            Log::warning('ProductSeeder: No active SubCategories found. Please run SubCategorySeeder first.');
            $this->command->warn('ProductSeeder: No active SubCategories found. Skipping.');
            return;
        }

        if ($brands->isEmpty()) {
            Log::warning('ProductSeeder: No Brands found. Please run BrandSeeder first. Assigning null brand_id.');
            $this->command->warn('ProductSeeder: No Brands found. Assigning null brand_id.');
             // Set brands to an empty collection to avoid errors in random()
            $brands = collect();
        }

        foreach ($subCategories as $subCategory) {
            for ($i = 1; $i <= 3; $i++) {
                $productName = $this->generateProductName($subCategory->name, $i);
                $description = $this->generateDescription($productName, $subCategory->name);
                $costPrice = round(rand(5, 500) + (rand(0, 99) / 100), 2); // Random cost price
                $sellPrice = round($costPrice * (rand(120, 200) / 100), 2); // Sell price 20% to 100% higher
                $stock = rand(0, 150); // Random stock, allowing some out-of-stock
                $brand = $brands->isNotEmpty() ? $brands->random() : null;
                $discount = $discounts->isNotEmpty() && rand(1, 6) == 1 ? $discounts->random() : null; // Assign discount ~1/6 chance
                $sku = $this->generateSku($subCategory, $brand, $i);

                Product::updateOrCreate(
                    ['sku_code' => $sku], // Unique identifier
                    [
                        'name' => $productName,
                        'description' => $description,
                        'main_image' => 'images/products/placeholder.jpg', // Use a generic placeholder
                        'cost_price' => $costPrice,
                        'sell_price' => $sellPrice,
                        'total_views' => rand(0, 500), // Dummy data
                        'favorites_views' => rand(0, 100), // Dummy data
                        'stock' => $stock,
                        'lower_stock_warn' => max(0, floor($stock * 0.1)), // Warn at 10% stock or 0
                        'favorites_count' => rand(0, 50), // Dummy data
                        // 'sku_code' => $sku, // Handled by updateOrCreate key
                        'overall_rating' => round(rand(300, 500) / 100, 1), // Dummy rating 3.0 - 5.0
                        'total_rating' => rand(5, 100), // Dummy number of ratings
                        'sub_category_id' => $subCategory->id,
                        'brand_id' => $brand?->id, // Use null safe operator
                        'discount_id' => $discount?->id, // Use null safe operator
                        'status' => ($stock > 0) ? 'active' : 'out-of-stock',
                        'is_public' => true, // Make seeded products public
                        'is_featured' => (rand(1, 10) == 1), // Feature ~10% of products randomly
                    ]
                );
            }
        }
    }

    /**
     * Helper to generate a product name.
     */
    private function generateProductName(string $subCategoryName, int $index): string
    {
        $variations = ['ممتاز', 'قياس '.$index * 10 .' سم', 'لون '.$this->randomColor(), 'درجة أولى', 'اقتصادي', 'احترافي', 'طقم كامل'];
        return $subCategoryName . ' - ' . $variations[array_rand($variations)];
    }

     /**
      * Helper to generate a random Arabic color.
      */
     private function randomColor(): string {
         $colors = ['أحمر', 'أزرق', 'أخضر', 'أصفر', 'أسود', 'أبيض', 'رمادي', 'برتقالي', 'بني'];
         return $colors[array_rand($colors)];
     }


    /**
     * Helper to generate a description.
     */
    private function generateDescription(string $productName, string $subCategoryName): string
    {
        return "منتج {$productName} عالي الجودة، مثالي للاستخدام في {$subCategoryName}. مصنوع من أفضل المواد لضمان المتانة والأداء الممتاز. سهل التركيب والاستخدام.";
    }

    /**
     * Helper to generate a unique SKU.
     */
    private function generateSku(SubCategory $subCategory, ?Brand $brand, int $index): string
    {
        $prefix = "AAM";
        $catPart = "SC" . str_pad($subCategory->id, 3, '0', STR_PAD_LEFT);
        $brandPart = $brand ? "BR" . str_pad($brand->id, 3, '0', STR_PAD_LEFT) : 'BR000';
        $randPart = strtoupper(Str::random(4)) . $index;
        return $prefix . '-' . $catPart . '-' . $brandPart . '-' . $randPart;
    }
}