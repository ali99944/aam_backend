<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Category; // Import the Category model
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Optional: Clear table before seeding
        // Schema::disableForeignKeyConstraints();
        // DB::table('categories')->truncate();
        // Schema::enableForeignKeyConstraints();

        $categories = [
            [
                'name' => 'سباكة', // Plumbing
                'description' => 'جميع مستلزمات وأدوات السباكة للمنزل والمشاريع.',
                'is_active' => true,
                'total_sub_categories' => 5, // We will create 5 sub-categories
                'cover_image' => 'images/categories/covers/plumbing_cover.jpg', // Placeholder
                'icon_image' => 'images/categories/icons/plumbing.svg', // Placeholder
                'is_featured' => true, // Set this category as featured
            ],
            [
                'name' => 'إضاءة', // Lighting
                'description' => 'حلول إضاءة داخلية وخارجية، لمبات ومستلزماتها.',
                'is_active' => true,
                'total_sub_categories' => 5,
                'cover_image' => 'images/categories/covers/lighting_cover.jpg',
                'icon_image' => 'images/categories/icons/lighting.svg',
                'is_featured' => true,
            ],
            [
                'name' => 'عدد كهربائية', // Electrical Tools
                'description' => 'أدوات ومعدات كهربائية متنوعة للحرفيين والهواة.',
                'is_active' => true,
                'total_sub_categories' => 5,
                'cover_image' => 'images/categories/covers/electrical_tools_cover.jpg',
                'icon_image' => 'images/categories/icons/electrical_tools.svg',
                'is_featured' => true,
            ],
            [
                'name' => 'مواد بناء', // Building Materials
                'description' => 'مواد أساسية للإنشاءات والتشطيبات.',
                'is_active' => true,
                'total_sub_categories' => 5,
                'cover_image' => 'images/categories/covers/building_materials_cover.jpg',
                'icon_image' => 'images/categories/icons/building_materials.svg',
                'is_featured' => true,
            ],
            [
                'name' => 'مواد زراعية', // Agricultural Materials
                'description' => 'مستلزمات الزراعة المنزلية والحدائق والبساتين.',
                'is_active' => true,
                'total_sub_categories' => 5,
                'cover_image' => 'images/categories/covers/agricultural_cover.jpg',
                'icon_image' => 'images/categories/icons/agricultural.svg',
                'is_featured' => true,
            ],
            [
                'name' => 'ديكورات', // Decorations
                'description' => 'ديكورات منزلية، مستلزمات وملحقات.',
                'is_active' => true,
                'total_sub_categories' => 5,
                'cover_image' => 'images/categories/covers/decorations_cover.jpg',
                'icon_image' => 'images/categories/icons/decorations.svg',
                'is_featured' => false,
            ],
            [
                'name' => 'أدوات ميكانيكية', // Mechanical Tools
                'description' => 'أدوات ومعدات ميكانيكية متنوعة.',
                'is_active' => true,
                'total_sub_categories' => 5,
                'cover_image' => 'images/categories/covers/mechanical_tools_cover.jpg',
                'icon_image' => 'images/categories/icons/mechanical_tools.svg',
                'is_featured' => false,
            ],
            [
                'name' => 'أدوات حرفية', // Hand Tools
                'description' => 'أدوات يدويّة متنوعة للحرفيين.',
                'is_active' => true,
                'total_sub_categories' => 5,
                'cover_image' => 'images/categories/covers/hand_tools_cover.jpg',
                'icon_image' => 'images/categories/icons/hand_tools.svg',
                'is_featured' => false,
            ],
            [
                'name' => 'أدوات كهربائية', // Electrical Tools
                'description' => 'أدوات كهربائية متنوعة للحرفيين.',
                'is_active' => true,
                'total_sub_categories' => 5,
                'cover_image' => 'images/categories/covers/electrical_tools_cover.jpg',
                'icon_image' => 'images/categories/icons/electrical_tools.svg',
                'is_featured' => false,
            ],
            [
                'name' => 'أدوات بلاستيكية', // Plastic Tools
                'description' => 'أدوات بلاستيكية متنوعة للحرفيين.',
                'is_active' => true,
                'total_sub_categories' => 5,
                'cover_image' => 'images/categories/covers/plastic_tools_cover.jpg',
                'icon_image' => 'images/categories/icons/plastic_tools.svg',
                'is_featured' => false,
            ],
            // Add more categories if needed following the same structure
        ];

        foreach ($categories as $categoryData) {
            // Use updateOrCreate to avoid duplicates if seeder runs multiple times
            // It checks based on the first array ['name' => ...], if found it updates, otherwise creates.
            Category::updateOrCreate(
                ['name' => $categoryData['name']], // Unique key to check against
                $categoryData                       // Data to insert or update with
            );
        }
    }
}