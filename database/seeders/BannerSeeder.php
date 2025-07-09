<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Banner; // Import the Banner model

class BannerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // IMPORTANT: These are placeholder image URLs.
        // For production, download your own images and place them in `public/images/banners/`
        // Then update the paths below to something like '/images/banners/my_image.jpg'

        $banners = [
            [
                'title' => 'أقوى التخفيضات وصلت',
                'description' => 'تصفح أحدث أجهزة اللابتوب والإكسسوارات بأسعار لا مثيل لها. خصومات حصرية لفترة محدودة.',
                'image' => 'https://img.freepik.com/premium-photo/young-woman-holding-laptop-with-excited-expression_1157-39248.jpg?uid=R160151452&ga=GA1.1.963014397.1747021839&semt=ais_hybrid&w=740',
                'button_text' => 'تسوق الآن',
                'button_url' => '/products?category=electronics',
                'is_active' => true,
                'sort_order' => 10,
            ],
            [
                'title' => 'عالم الهواتف الذكية بين يديك',
                'description' => 'اكتشف مجموعتنا المختارة من أحدث الهواتف الذكية من أفضل العلامات التجارية العالمية.',
                'image' => 'https://img.freepik.com/premium-photo/smartphone-technology-apps-mobile-phone-tablet_1157-39245.jpg?uid=R160151452&ga=GA1.1.963014397.1747021839&semt=ais_hybrid&w=740',
                'button_text' => 'استكشف الهواتف',
                'button_url' => '/products?category=smartphones',
                'is_active' => true,
                'sort_order' => 20,
            ],
            [
                'title' => 'تجربة صوتية لا تُنسى',
                'description' => 'ارتقِ بتجربتك الموسيقية مع أفضل السماعات ومكبرات الصوت. جودة ونقاء يفوقان الخيال.',
                'image' => 'https://img.freepik.com/premium-photo/woman-listening-music-with-headphones-on-white-background_1157-39246.jpg?uid=R160151452&ga=GA1.1.963014397.1747021839&semt=ais_hybrid&w=740',
                'button_text' => 'عرض المنتجات الصوتية',
                'button_url' => '/products?category=audio',
                'is_active' => true,
                'sort_order' => 30,
            ],
            [
                'title' => 'شحن مجاني لجميع الطلبات!',
                'description' => 'استمتع بخدمة التوصيل المجاني عند الشراء بقيمة 200 درهم أو أكثر. تسوق براحة تصلك مشترياتك لباب بيتك.',
                'image' => 'https://img.freepik.com/premium-photo/shopping-online-with-free-shipping_1157-39247.jpg?uid=R160151452&ga=GA1.1.963014397.1747021839&semt=ais_hybrid&w=740',
                'button_text' => 'ابدأ التسوق',
                'button_url' => '/', // Link to the homepage
                'is_active' => true,
                'sort_order' => 40,
            ],
            // Example of an inactive banner for future use
            [
                'title' => 'تخفيضات الشتاء قادمة',
                'description' => 'استعد للموسم القادم مع عروضنا الحصرية على الأجهزة الشتوية.',
                'image' => 'https://img.freepik.com/premium-photo/winter-sale-banner-with-snowflakes_1157-39249.jpg?uid=R160151452&ga=GA1.1.963014397.1747021839&semt=ais_hybrid&w=740',
                'button_text' => 'قريباً',
                'button_url' => '#',
                'is_active' => false, // This banner will not be shown on the API
                'sort_order' => 50,
            ],
        ];

        // Using updateOrCreate to prevent duplicates if seeder is run multiple times
        foreach ($banners as $bannerData) {
            Banner::updateOrCreate(
                ['title' => $bannerData['title']], // Find by title to avoid duplicates
                $bannerData
            );
            $this->command->info("Banner '{$bannerData['title']}' seeded.");
        }
    }
}
