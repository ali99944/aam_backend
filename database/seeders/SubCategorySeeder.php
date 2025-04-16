<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\SubCategory; // Import SubCategory model
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log; // For logging errors

class SubCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Optional: Clear table before seeding
        // DB::table('sub_categories')->truncate(); // Be careful with truncate if FK constraints exist

        // Structure: [ Parent Category Name => [ Array of SubCategory data ] ]
        $categorySubCategories = [
            'سباكة' => [ // Plumbing
                ['name' => 'أنابيب ومواسير', 'description' => 'مواسير PVC، حراري، حديد ولوازمها.'],
                ['name' => 'حنفيات وخلاطات', 'description' => 'خلاطات مطابخ وحمامات ودوشات.'],
                ['name' => 'أدوات صحية', 'description' => 'مراحيض، مغاسل، بانيوهات وشاورات.'],
                ['name' => 'سخانات مياه', 'description' => 'سخانات كهربائية وغاز فورية وتخزين.'],
                ['name' => 'مضخات مياه', 'description' => 'مضخات منزلية وصناعية لرفع المياه.'],
            ],
            'إضاءة' => [ // Lighting
                ['name' => 'لمبات LED', 'description' => 'لمبات موفرة للطاقة بمختلف الأشكال والألوان.'],
                ['name' => 'وحدات إضاءة سقفية', 'description' => 'ثريات، سبوت لايت، إضاءة مخفية.'],
                ['name' => 'إضاءة خارجية', 'description' => 'كشافات، إضاءة حدائق وأسوار.'],
                ['name' => 'شرائط إضاءة LED', 'description' => 'شرائط ليد للإضاءة المخفية والديكور.'],
                ['name' => 'إضاءة طوارئ', 'description' => 'وحدات إضاءة تعمل عند انقطاع التيار.'],
            ],
            'عدد كهربائية' => [ // Electrical Tools
                ['name' => 'مثاقب كهربائية', 'description' => 'دريلات عادية، شحن، وهمر.'],
                ['name' => 'مناشير كهربائية', 'description' => 'مناشير خشب ومعادن، اركت، صينية.'],
                ['name' => 'مفكات كهربائية', 'description' => 'مفكات شحن لفك وربط البراغي.'],
                ['name' => 'جلاخات وصواريخ', 'description' => 'جلاخات لقص وتلميع المعادن.'],
                ['name' => 'أجهزة قياس', 'description' => 'أفوميتر، أجهزة فحص كهرباء وليزر.'],
            ],
            'مواد بناء' => [ // Building Materials
                ['name' => 'أسمنت ومواد خلط', 'description' => 'أسمنت، رمل، بحص، مواد لاصقة.'],
                ['name' => 'طوب وبلوك', 'description' => 'طوب بناء، بلوك إسمنتي بأنواعه.'],
                ['name' => 'مواد عزل', 'description' => 'عزل مائي وحراري، فوم وبوليسترين.'],
                ['name' => 'حديد تسليح', 'description' => 'أسياخ حديد بأقطار مختلفة للبناء.'],
                ['name' => 'أخشاب بناء', 'description' => 'ألواح خشبية وقوائم للدعم والإنشاء.'],
            ],
            'مواد زراعية' => [ // Agricultural Materials
                ['name' => 'بذور وأسمدة', 'description' => 'بذور خضروات وزهور، أسمدة عضوية وكيماوية.'],
                ['name' => 'أدوات زراعية يدوية', 'description' => 'مجارف، فؤوس، مقصات تقليم.'],
                ['name' => 'أنظمة ري', 'description' => 'خراطيم، رشاشات، أنظمة تنقيط.'],
                ['name' => 'مبيدات وآفات', 'description' => 'مبيدات حشرية، فطرية، ومكافحة قوارض.'],
                ['name' => 'أحواض زراعية', 'description' => 'أصص بلاستيكية وفخارية، أحواض جاهزة.'],
            ],
        ];

        foreach ($categorySubCategories as $categoryName => $subCategories) {
            $parentCategory = Category::where('name', $categoryName)->first();

            if (!$parentCategory) {
                Log::warning("CategorySeeder: Parent category '{$categoryName}' not found, skipping its sub-categories.");
                continue; // Skip if parent category doesn't exist
            }

            foreach ($subCategories as $subCategoryData) {
                 // Add default values and parent category ID
                $fullData = array_merge([
                    'category_id' => $parentCategory->id,
                    'description' => $subCategoryData['description'] ?? null,
                    'cover_image' => null, // Add placeholder paths if desired
                    'icon_image' => null,  // Add placeholder paths if desired
                    'is_active' => true,
                    'total_products' => 0,
                ], $subCategoryData); // Merge ensures name is included

                // Use updateOrCreate based on name *within* the parent category
                SubCategory::updateOrCreate(
                    [
                        'category_id' => $parentCategory->id,
                        'name' => $fullData['name']
                    ],
                    $fullData
                );
            }
        }
    }
}