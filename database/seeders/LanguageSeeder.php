<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Language; // Import Language model

class LanguageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $languages = [
            [
                'name' => 'Arabic',
                'name_native' => 'العربية',
                'direction' => 'rtl',
                'locale' => 'ar',
                'flag_svg' => '/images/seeders/flags/sa.svg', // Example Saudi Arabia flag
                'is_active' => true,
            ],
            [
                'name' => 'English',
                'name_native' => 'English',
                'direction' => 'ltr',
                'locale' => 'en',
                'flag_svg' => '/images/seeders/flags/us.svg', // Example US flag
                'is_active' => true,
            ],
            [
                'name' => 'French',
                'name_native' => 'Français',
                'direction' => 'ltr',
                'locale' => 'fr',
                'flag_svg' => '/images/seeders/flags/fr.svg',
                'is_active' => false, // Example inactive
            ],
            [
                'name' => 'German',
                'name_native' => 'Deutsch',
                'direction' => 'ltr',
                'locale' => 'de',
                'flag_svg' => '/images/seeders/flags/de.svg',
                'is_active' => false,
            ],
             [
                'name' => 'Spanish',
                'name_native' => 'Español',
                'direction' => 'ltr',
                'locale' => 'es',
                'flag_svg' => '/images/seeders/flags/es.svg',
                'is_active' => false,
            ],
             [
                'name' => 'Urdu',
                'name_native' => 'اردو',
                'direction' => 'rtl',
                'locale' => 'ur',
                'flag_svg' => '/images/seeders/flags/pk.svg', // Example Pakistan flag
                'is_active' => false,
            ],
            [
                'name' => 'Hindi',
                'name_native' => 'हिन्दी',
                'direction' => 'ltr',
                'locale' => 'hi',
                'flag_svg' => '/images/seeders/flags/in.svg', // Example India flag
                'is_active' => false,
            ],
             [
                'name' => 'Turkish',
                'name_native' => 'Türkçe',
                'direction' => 'ltr',
                'locale' => 'tr',
                'flag_svg' => '/images/seeders/flags/tr.svg',
                'is_active' => false,
            ],
             [
                'name' => 'Chinese',
                'name_native' => '中文',
                'direction' => 'ltr',
                'locale' => 'zh',
                'flag_svg' => '/images/seeders/flags/cn.svg',
                'is_active' => false,
            ],
              [
                'name' => 'Russian',
                'name_native' => 'Русский',
                'direction' => 'ltr',
                'locale' => 'ru',
                'flag_svg' => '/images/seeders/flags/ru.svg',
                'is_active' => false,
            ],
        ];

         foreach ($languages as $langData) {
            Language::updateOrCreate(
                ['locale' => $langData['locale']], // Find by locale
                $langData
            );
             $this->command->info("Language '{$langData['name']}' seeded.");
        }
    }
}