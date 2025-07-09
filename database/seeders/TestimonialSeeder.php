<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Testimonial; // Import the Testimonial model

class TestimonialSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // IMPORTANT: These are placeholder image URLs from a free service.
        // For production, download your own images, place them in `public/images/testimonials/avatars/`,
        // and update the paths below to something like '/images/testimonials/avatars/avatar1.jpg'

        $testimonials = [
            [
                'name' => 'خالد الأحمدي',
                'title_or_company' => 'مهندس برمجيات',
                'quote' => 'تجربة شراء ممتازة من البداية للنهاية. الموقع سهل الاستخدام والمنتجات وصلتني في وقت قياسي وبجودة عالية جداً. أنصح به بشدة!',
                'avatar' => 'https://images.pexels.com/photos/220453/pexels-photo-220453.jpeg?auto=compress&cs=tinysrgb&w=1260&h=750&dpr=1',
                'rating' => 5,
                'is_active' => true,
                'sort_order' => 10,
            ],
            [
                'name' => 'فاطمة الزهراء',
                'title_or_company' => 'مصممة جرافيك',
                'quote' => 'كنت أبحث عن لابتوب بمواصفات معينة ووجدته في متجر AAM بسعر أفضل من أي مكان آخر. خدمة العملاء كانت متعاونة جداً وأجابت على كل استفساراتي.',
                'avatar' => 'https://images.pexels.com/photos/774909/pexels-photo-774909.jpeg?auto=compress&cs=tinysrgb&w=1260&h=750&dpr=1',
                'rating' => 5,
                'is_active' => true,
                'sort_order' => 20,
            ],
            [
                'name' => 'محمد عبدالله',
                'title_or_company' => 'طالب جامعي',
                'quote' => 'أفضل ما في المتجر هو تنوع المنتجات والعروض المستمرة. اشتريت سماعات رأس وكانت تجربة الصوت رائعة. التوصيل كان سريعاً والتغليف ممتاز.',
                'avatar' => 'https://images.pexels.com/photos/1043473/pexels-photo-1043473.jpeg?auto=compress&cs=tinysrgb&w=1260&h=750&dpr=1',
                'rating' => 4,
                'is_active' => true,
                'sort_order' => 30,
            ],
            [
                'name' => 'سارة إبراهيم',
                'title_or_company' => null, // Example with no title
                'quote' => 'الأسعار تنافسية والوصف المكتوب للمنتجات دقيق جداً وساعدني في اتخاذ قرار الشراء. شكراً لفريق متجر AAM على المصداقية.',
                'avatar' => 'https://images.pexels.com/photos/1036623/pexels-photo-1036623.jpeg?auto=compress&cs=tinysrgb&w=1260&h=750&dpr=1',
                'rating' => 5,
                'is_active' => true,
                'sort_order' => 40,
            ],
            [
                'name' => 'شركة النور للتجارة',
                'title_or_company' => 'قسم المشتريات',
                'quote' => 'تعاملنا مع متجر AAM لتجهيز مكاتبنا الجديدة بأجهزة الكمبيوتر. كانت العملية سلسة والأسعار ممتازة للطلبات بالجملة. خدمة احترافية وموثوقة.',
                'avatar' => null, // Example with no avatar, will use default
                'rating' => 5,
                'is_active' => true,
                'sort_order' => 50,
            ],
            // Example of an inactive testimonial
            [
                'name' => 'عميل قديم',
                'title_or_company' => null,
                'quote' => 'تقييم قديم سيتم مراجعته لاحقاً.',
                'avatar' => null,
                'rating' => 3,
                'is_active' => false,
                'sort_order' => 60,
            ],
        ];

        // Using updateOrCreate to prevent duplicates if seeder is run multiple times
        foreach ($testimonials as $testimonialData) {
            Testimonial::updateOrCreate(
                ['name' => $testimonialData['name']], // Find by name to avoid duplicates
                $testimonialData
            );
            $this->command->info("Testimonial for '{$testimonialData['name']}' seeded.");
        }
    }
}