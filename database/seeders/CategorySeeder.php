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
                'image' => 'https://img.freepik.com/premium-vector/construction-repair-work-tools-vector-icons_8071-27164.jpg?uid=R160151452&ga=GA1.1.963014397.1747021839&semt=ais_hybrid&w=740', // Placeholder
                'is_featured' => true,
                'slug' => 'plumbing',
            ],
            [
                'name' => 'إضاءة', // Lighting
                'description' => 'حلول إضاءة داخلية وخارجية، لمبات ومستلزماتها.',
                'is_active' => true,
                'total_sub_categories' => 5,
                'image' => 'https://img.freepik.com/premium-photo/close-up-lighting-equipments-against-white-background_1048944-10574142.jpg?uid=R160151452&ga=GA1.1.963014397.1747021839&semt=ais_hybrid&w=740',
                'is_featured' => true,
                'slug' => 'lighting',
            ],
            [
                'name' => 'عدد كهربائية', // Electrical Tools
                'description' => 'أدوات ومعدات كهربائية متنوعة للحرفيين والهواة.',
                'is_active' => true,
                'total_sub_categories' => 5,
                'image' => 'https://img.freepik.com/free-photo/different-electrical-tools-wooden-background-flat-lay_169016-24825.jpg?uid=R160151452&ga=GA1.1.963014397.1747021839&semt=ais_hybrid&w=740',
                'is_featured' => true,
                'slug' => 'electrical-tools',
            ],
            [
                'name' => 'مواد بناء', // Building Materials
                'description' => 'مواد أساسية للإنشاءات والتشطيبات.',
                'is_active' => true,
                'total_sub_categories' => 5,
                'image' => 'https://img.freepik.com/premium-photo/house-arrangement-from-yellow-repair-tools-top-view_23-2148393070.jpg?uid=R160151452&ga=GA1.1.963014397.1747021839&semt=ais_hybrid&w=740',
                'is_featured' => true,
                'slug' => 'building-materials',
            ],
            [
                'name' => 'مواد زراعية', // Agricultural Materials
                'description' => 'مستلزمات الزراعة المنزلية والحدائق والبساتين.',
                'is_active' => true,
                'total_sub_categories' => 5,
                'image' => 'https://img.freepik.com/free-photo/tidy-gardening-elements_23-2147997158.jpg?uid=R160151452&ga=GA1.1.963014397.1747021839&semt=ais_hybrid&w=740',
                'is_featured' => true,
                'slug' => 'agricultural-materials',
            ],
            [
                'name' => 'ديكورات', // Decorations
                'description' => 'ديكورات منزلية، مستلزمات وملحقات.',
                'is_active' => true,
                'total_sub_categories' => 5,
                'image' => 'https://img.freepik.com/free-photo/arrangement-repair-utensils-paint-brush_23-2148393105.jpg?uid=R160151452&ga=GA1.1.963014397.1747021839&semt=ais_hybrid&w=740',
                'is_featured' => false,
                'slug' => 'decorations',
            ],
            [
                'name' => 'أدوات ميكانيكية', // Mechanical Tools
                'description' => 'أدوات ومعدات ميكانيكية متنوعة.',
                'is_active' => true,
                'total_sub_categories' => 5,
                'image' => 'https://img.freepik.com/free-photo/top-view-mechanical-tools-arrangement_23-2149552380.jpg?uid=R160151452&ga=GA1.1.963014397.1747021839&semt=ais_hybrid&w=740',
                'is_featured' => false,
                'slug' => 'mechanical-tools',
            ],
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