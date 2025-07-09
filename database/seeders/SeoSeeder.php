<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Seo; // Assuming your model is App\Models\Seo
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class SeoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Optional: Clear table first
        // Schema::disableForeignKeyConstraints();
        // DB::table('seos')->truncate();
        // Schema::enableForeignKeyConstraints();

        // --- Common Data ---
        $siteName = "متجر AAM"; // Replace with your actual store name
        $baseUrl = rtrim(config('app.url', 'http://localhost'), '/'); // Get base URL
        $defaultOgImage = $baseUrl . '/images/seo/default_og_image.jpg'; // Path to your default OG image
        $defaultTwitterImage = $baseUrl . '/images/seo/default_twitter_image.jpg'; // Path to your default Twitter image (can be same as OG)
        $defaultLocale = 'ar_JO'; // Arabic - Jordan

        $seoEntries = [
            // --- Core Pages ---
            [
                'name' => 'Homepage',
                'key' => 'home',
                'type' => 'page',
                'title' => "{$siteName} | مستلزمات بناء وكهرباء وسباكة", // Main title
                'description' => "تسوق أفضل العدد الكهربائية ومواد البناء والسباكة والزراعة اونلاين في الأردن. توصيل سريع وأسعار تنافسية.",
                'keywords' => 'عدد كهربائية, مواد بناء, سباكة, مواد زراعية, تسوق اونلاين, الأردن, AAM',
                'robots_meta' => 'index, follow',
                'canonical_url' => $baseUrl . '/',
                'og_title' => "{$siteName} | متجرك الأول للمستلزمات",
                'og_description' => "اكتشف تشكيلة واسعة من العدد ومواد البناء والسباكة والمزيد.",
                'og_image' => $defaultOgImage,
                'og_image_alt' => "{$siteName} Logo",
                'og_locale' => $defaultLocale,
                'og_site_name' => $siteName,
                'twitter_title' => "{$siteName} | تسوق مستلزماتك بسهولة",
                'twitter_description' => "أفضل الأسعار وأسرع توصيل لجميع مستلزماتك في الأردن.",
                'twitter_image' => $defaultTwitterImage,
                'twitter_alt' => "{$siteName}",
                'custom_meta_tags' => null,
            ],
            [
                'name' => 'Contact Us',
                'key' => 'contact_us',
                'type' => 'page',
                'title' => "تواصل معنا | {$siteName}",
                'description' => "تواصل مع خدمة عملاء {$siteName}. نحن هنا للمساعدة في استفساراتك حول المنتجات أو الطلبات.",
                'keywords' => 'تواصل, خدمة عملاء, استفسار, دعم فني, {$siteName}',
                'robots_meta' => 'index, follow',
                'canonical_url' => $baseUrl . '/contact', // Adjust URL path
                'og_title' => "تواصل مع {$siteName}",
                'og_description' => "نحن هنا لمساعدتك. اتصل بنا أو أرسل لنا رسالة.",
                'og_image' => $defaultOgImage,
                'og_image_alt' => "{$siteName} Contact",
                'og_locale' => $defaultLocale,
                'og_site_name' => $siteName,
                'twitter_title' => "تواصل مع {$siteName}",
                'twitter_description' => "لديك سؤال؟ فريقنا جاهز للمساعدة.",
                'twitter_image' => $defaultTwitterImage,
                'twitter_alt' => "{$siteName} Contact",
                'custom_meta_tags' => null,
            ],
            [
                'name' => 'About Us',
                'key' => 'about_us',
                'type' => 'page',
                'title' => "عن {$siteName} | رؤيتنا وقيمنا",
                'description' => "تعرف على قصة {$siteName}، المتجر الرائد في توفير مستلزمات البناء والأدوات عالية الجودة في الأردن.",
                'keywords' => "عن الشركة, رؤية, قيم, تاريخ, {$siteName}, الأردن",
                'robots_meta' => 'index, follow',
                'canonical_url' => $baseUrl . '/about', // Adjust URL path
                'og_title' => "قصة نجاح {$siteName}",
                'og_description' => "تعرف على فريقنا، مهمتنا، والتزامنا بالجودة والخدمة.",
                'og_image' => $defaultOgImage,
                'og_image_alt' => "About {$siteName}",
                'og_locale' => $defaultLocale,
                'og_site_name' => $siteName,
                'twitter_title' => "اكتشف قصة {$siteName}",
                'twitter_description' => "من نحن وماذا نقدم لعملائنا الكرام.",
                'twitter_image' => $defaultTwitterImage,
                'twitter_alt' => "About {$siteName}",
                'custom_meta_tags' => null,
            ],


            // --- Functional Pages ---
            [
                'name' => 'Support Chat', // Or FAQ page?
                'key' => 'support_chat', // Or 'faq'
                'type' => 'page',
                'title' => "الدعم والمساعدة | {$siteName}",
                'description' => "تحتاج مساعدة؟ تواصل مع فريق الدعم أو ابحث عن إجابات لأسئلتك الشائعة.",
                'keywords' => "دعم, مساعدة, أسئلة شائعة, دردشة, {$siteName}",
                'robots_meta' => 'index, follow',
                'canonical_url' => $baseUrl . '/support', // Adjust URL path
                'og_title' => "مركز المساعدة | {$siteName}",
                'og_description' => "نحن هنا لمساعدتك في أي وقت.",
                'og_image' => $defaultOgImage,
                'og_image_alt' => "Support",
                'og_locale' => $defaultLocale,
                'og_site_name' => $siteName,
                'twitter_title' => "الدعم والمساعدة | {$siteName}",
                'twitter_description' => "ابحث عن إجابات أو تواصل معنا.",
                'twitter_image' => $defaultTwitterImage,
                'twitter_alt' => "Support",
                'custom_meta_tags' => null,
            ],
            [
                'name' => 'Login',
                'key' => 'login',
                'type' => 'page',
                'title' => "تسجيل الدخول | {$siteName}",
                'description' => "قم بتسجيل الدخول إلى حسابك في {$siteName} للوصول إلى طلباتك ومفضلاتك.",
                'keywords' => "تسجيل دخول, حسابي, {$siteName}",
                'robots_meta' => 'noindex, follow', // Don't index login page typically
                'canonical_url' => $baseUrl . '/login', // Adjust URL path
                'og_title' => "تسجيل الدخول | {$siteName}",
                'og_description' => "الوصول إلى حسابك.",
                'og_image' => $defaultOgImage,
                'og_image_alt' => "Login",
                'og_locale' => $defaultLocale,
                'og_site_name' => $siteName,
                'twitter_title' => "تسجيل الدخول | {$siteName}",
                'twitter_description' => "الوصول إلى حسابك.",
                'twitter_image' => $defaultTwitterImage,
                'twitter_alt' => "Login",
                'custom_meta_tags' => null,
            ],
             [
                'name' => 'Register',
                'key' => 'register',
                'type' => 'page',
                'title' => "إنشاء حساب جديد | {$siteName}",
                'description' => "أنشئ حساباً جديداً في {$siteName} للاستمتاع بتجربة تسوق أسرع وحفظ منتجاتك المفضلة.",
                'keywords' => "تسجيل, حساب جديد, إنشاء حساب, {$siteName}",
                'robots_meta' => 'noindex, follow', // Usually no need to index register page
                'canonical_url' => $baseUrl . '/register', // Adjust URL path
                'og_title' => "إنشاء حساب جديد | {$siteName}",
                'og_description' => "انضم إلينا اليوم!",
                'og_image' => $defaultOgImage,
                'og_image_alt' => "Register",
                'og_locale' => $defaultLocale,
                'og_site_name' => $siteName,
                'twitter_title' => "إنشاء حساب | {$siteName}",
                'twitter_description' => "انضم إلينا اليوم!",
                'twitter_image' => $defaultTwitterImage,
                'twitter_alt' => "Register",
                'custom_meta_tags' => null,
            ],
            [
                'name' => 'Forgot Password',
                'key' => 'forgot_password',
                'type' => 'page',
                'title' => "نسيت كلمة المرور | {$siteName}",
                'description' => "اطلب إعادة تعيين كلمة المرور لحسابك في {$siteName}.",
                'keywords' => "نسيت كلمة المرور, إعادة تعيين, استعادة حساب, {$siteName}",
                'robots_meta' => 'noindex, nofollow',
                'canonical_url' => $baseUrl . '/forgot-password', // Adjust URL path
                 // OG/Twitter usually not needed for these utility pages
                'og_title' => "نسيت كلمة المرور", 'og_description' => '', 'og_image' => '', 'og_image_alt' => '', 'og_locale' => $defaultLocale, 'og_site_name' => $siteName,
                'twitter_title' => '', 'twitter_description' => '', 'twitter_image' => '', 'twitter_alt' => '',
                'custom_meta_tags' => null,
            ],
            [
                'name' => 'Verify OTP',
                'key' => 'verify_otp',
                'type' => 'page',
                'title' => "تأكيد الرمز | {$siteName}",
                'description' => "أدخل رمز التحقق المرسل إليك لإكمال العملية.",
                'keywords' => "رمز تحقق, OTP, تأكيد, {$siteName}",
                'robots_meta' => 'noindex, nofollow',
                'canonical_url' => $baseUrl . '/verify-otp', // Adjust URL path
                 'og_title' => "تأكيد الرمز", 'og_description' => '', 'og_image' => '', 'og_image_alt' => '', 'og_locale' => $defaultLocale, 'og_site_name' => $siteName,
                'twitter_title' => '', 'twitter_description' => '', 'twitter_image' => '', 'twitter_alt' => '',
                'custom_meta_tags' => null,
            ],
            [
                'name' => 'Reset Password',
                'key' => 'reset_password',
                'type' => 'page',
                'title' => "إعادة تعيين كلمة المرور | {$siteName}",
                'description' => "أدخل كلمة المرور الجديدة لحسابك.",
                'keywords' => "إعادة تعيين, كلمة مرور جديدة, {$siteName}",
                'robots_meta' => 'noindex, nofollow',
                'canonical_url' => $baseUrl . '/reset-password', // Adjust URL path (might include token)
                'og_title' => "إعادة تعيين كلمة المرور", 'og_description' => '', 'og_image' => '', 'og_image_alt' => '', 'og_locale' => $defaultLocale, 'og_site_name' => $siteName,
                'twitter_title' => '', 'twitter_description' => '', 'twitter_image' => '', 'twitter_alt' => '',
                'custom_meta_tags' => null,
            ],

             // --- User Account Pages ---
             [
                'name' => 'Profile',
                'key' => 'profile',
                'type' => 'page',
                'title' => "ملفي الشخصي | {$siteName}",
                'description' => "عرض وتعديل معلومات حسابك الشخصي، عناوينك، وطلباتك.",
                'keywords' => "حسابي, ملفي الشخصي, معلومات, طلبات, عناوين, {$siteName}",
                'robots_meta' => 'noindex, nofollow', // User-specific pages usually not indexed
                'canonical_url' => $baseUrl . '/profile', // Adjust URL path
                 'og_title' => "ملفي الشخصي", 'og_description' => '', 'og_image' => '', 'og_image_alt' => '', 'og_locale' => $defaultLocale, 'og_site_name' => $siteName,
                'twitter_title' => '', 'twitter_description' => '', 'twitter_image' => '', 'twitter_alt' => '',
                'custom_meta_tags' => null,
            ],
             [
                'name' => 'Cart',
                'key' => 'cart',
                'type' => 'page',
                'title' => "سلة التسوق | {$siteName}",
                'description' => "مراجعة المنتجات في سلة التسوق الخاصة بك والمتابعة لإتمام الطلب.",
                'keywords' => "سلة التسوق, عربة التسوق, منتجات, شراء, {$siteName}",
                'robots_meta' => 'noindex, nofollow', // Cart contents are dynamic
                'canonical_url' => $baseUrl . '/cart', // Adjust URL path
                'og_title' => "سلة التسوق", 'og_description' => '', 'og_image' => '', 'og_image_alt' => '', 'og_locale' => $defaultLocale, 'og_site_name' => $siteName,
                'twitter_title' => '', 'twitter_description' => '', 'twitter_image' => '', 'twitter_alt' => '',
                'custom_meta_tags' => null,
            ],
            [
                'name' => 'Checkout',
                'key' => 'checkout',
                'type' => 'page',
                'title' => "إتمام الطلب | {$siteName}",
                'description' => "أدخل معلومات الشحن والدفع لإكمال عملية الشراء.",
                'keywords' => "إتمام الطلب, دفع, شراء, شحن, عنوان, {$siteName}",
                'robots_meta' => 'noindex, nofollow',
                'canonical_url' => $baseUrl . '/checkout', // Adjust URL path
                 'og_title' => "إتمام الطلب", 'og_description' => '', 'og_image' => '', 'og_image_alt' => '', 'og_locale' => $defaultLocale, 'og_site_name' => $siteName,
                'twitter_title' => '', 'twitter_description' => '', 'twitter_image' => '', 'twitter_alt' => '',
                'custom_meta_tags' => null,
            ],
             [
                'name' => 'Orders History', // Page listing all orders
                'key' => 'orders_history',
                'type' => 'page',
                'title' => "طلباتي | {$siteName}",
                'description' => "عرض سجل طلباتك السابقة وتتبع حالة الطلبات الحالية.",
                'keywords' => "طلباتي, سجل الطلبات, تتبع, {$siteName}",
                'robots_meta' => 'noindex, nofollow',
                'canonical_url' => $baseUrl . '/orders', // Adjust URL path
                'og_title' => "طلباتي", 'og_description' => '', 'og_image' => '', 'og_image_alt' => '', 'og_locale' => $defaultLocale, 'og_site_name' => $siteName,
                'twitter_title' => '', 'twitter_description' => '', 'twitter_image' => '', 'twitter_alt' => '',
                'custom_meta_tags' => null,
            ],
            [
                'name' => 'Track Order', // Single order tracking page
                'key' => 'track_order',
                'type' => 'page',
                'title' => "تتبع الطلب | {$siteName}",
                'description' => "أدخل رقم الطلب أو رمز التتبع الخاص بك لمعرفة حالة شحنتك.",
                'keywords' => "تتبع, شحنة, طلب, حالة الطلب, {$siteName}",
                'robots_meta' => 'noindex, nofollow',
                'canonical_url' => $baseUrl . '/track-order', // Adjust URL path
                 'og_title' => "تتبع طلبك", 'og_description' => '', 'og_image' => '', 'og_image_alt' => '', 'og_locale' => $defaultLocale, 'og_site_name' => $siteName,
                'twitter_title' => '', 'twitter_description' => '', 'twitter_image' => '', 'twitter_alt' => '',
                'custom_meta_tags' => null,
            ],
        ];

        // --- Insert into database ---
        foreach ($seoEntries as $entry) {
            Seo::updateOrCreate(
                ['key' => $entry['key']], // Unique key to check
                $entry                   // Data to insert/update
            );
        }
    }
}