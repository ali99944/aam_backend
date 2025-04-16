<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\FaqCategory; // Import model

class FaqCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'الاتصال بنا', // Contact Us
                'description' => 'أسئلة شائعة حول كيفية التواصل مع فريق خدمة عملاء متجر AAM.',
                'display_order' => 10,
                'is_active' => true,
                'key' => 'contact-us'
            ],
            [
                'name' => 'عن متجر AAM', // About AAM Store
                'description' => 'معلومات عامة حول متجر AAM، رؤيتنا، وما يميزنا.',
                'display_order' => 20,
                'is_active' => true,
                'key' => 'about-us'
            ],
            [
                'name' => 'الدعم والمساعدة', // Support & Help
                'description' => 'مساعدة بخصوص الطلبات، الحسابات، سياسات المتجر والمشاكل التقنية.',
                'display_order' => 30,
                'is_active' => true,
                'key' => 'support'
            ],
            // Add more categories if needed
        ];

        foreach ($categories as $categoryData) {
            FaqCategory::updateOrCreate(
                ['name' => $categoryData['name']], // Find by name
                $categoryData
            );
            $this->command->info("FAQ Category '{$categoryData['name']}' seeded.");
        }
    }
}