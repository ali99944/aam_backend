<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Discount; // Import Discount model
use Illuminate\Support\Carbon; // For dates
use Illuminate\Support\Str; // For codes

class DiscountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $discounts = [
            // --- Period Based ---
            [
                'name' => 'خصم العيد الوطني',
                'code' => 'EID20',
                'type' => Discount::TYPE_PERCENTAGE,
                'value' => 20.00,
                'status' => Discount::STATUS_ACTIVE,
                'expiration_type' => Discount::EXPIRATION_PERIOD,
                'start_date' => Carbon::now()->subDays(2), // Example: Started 2 days ago
                'end_date' => Carbon::now()->addDays(5),   // Example: Ends in 5 days
                'description' => 'خصم خاص بمناسبة احتفالات العيد الوطني.',
            ],
            [
                'name' => 'تخفيضات نهاية الأسبوع',
                'code' => 'WEEKEND50',
                'type' => Discount::TYPE_FIXED,
                'value' => 50.00, // AED 50 fixed discount
                'status' => Discount::STATUS_ACTIVE,
                'expiration_type' => Discount::EXPIRATION_PERIOD,
                'start_date' => Carbon::now()->next('Thursday')->setTime(18, 0, 0), // Starts next Thursday 6 PM
                'end_date' => Carbon::now()->next('Saturday')->setTime(23, 59, 59), // Ends next Saturday night
                'description' => 'وفر 50 درهم إماراتي على طلبك خلال عطلة نهاية الأسبوع.',
            ],
             // --- Duration Based (Needs logic to "activate") ---
            [
                'name' => 'خصم ترحيبي للمستخدم الجديد',
                'code' => null, // Maybe applied automatically, not via code
                'type' => Discount::TYPE_PERCENTAGE,
                'value' => 15.00,
                'status' => Discount::STATUS_ACTIVE, // Active in general, but applicability depends on user status
                'expiration_type' => Discount::EXPIRATION_DURATION,
                'duration_days' => 7, // Valid for 7 days after registration/first activation
                'description' => 'خصم 15% للعملاء الجدد، صالح لمدة أسبوع من تاريخ التسجيل.',
            ],
            // --- No Expiration ---
            [
                'name' => 'خصم دائم 5% للأعضاء المميزين',
                'code' => 'VIPCLUB5',
                'type' => Discount::TYPE_PERCENTAGE,
                'value' => 5.00,
                'status' => Discount::STATUS_ACTIVE,
                'expiration_type' => Discount::EXPIRATION_NONE,
                'description' => 'خصم خاص لأعضاء برنامج الولاء المميزين.',
            ],
            [
                'name' => 'شحن مجاني للطلبات فوق 300 درهم',
                'code' => 'FREESHIP300',
                'type' => Discount::TYPE_FIXED, // Technically affects shipping cost, represent as fixed for simplicity? Or needs separate type.
                'value' => 0.00, // Value might represent the shipping cost waived, requires logic adjustment
                'status' => Discount::STATUS_ACTIVE,
                'expiration_type' => Discount::EXPIRATION_NONE,
                'description' => 'احصل على شحن مجاني عند الشراء بقيمة 300 درهم أو أكثر.',
            ],
             // --- Inactive / Expired Examples ---
             [
                'name' => 'تخفيضات رمضان الماضية',
                'code' => 'RAMADAN23',
                'type' => Discount::TYPE_PERCENTAGE,
                'value' => 25.00,
                'status' => Discount::STATUS_EXPIRED, // Expired status
                'expiration_type' => Discount::EXPIRATION_PERIOD,
                'start_date' => Carbon::parse('2023-03-22'), // Example past date
                'end_date' => Carbon::parse('2023-04-21'),   // Example past date
                'description' => 'خصم رمضان للعام الماضي.',
            ],
             [
                'name' => 'خصم الصيف المبكر (غير نشط)',
                'code' => 'EARLYSUMMER',
                'type' => Discount::TYPE_PERCENTAGE,
                'value' => 10.00,
                'status' => Discount::STATUS_INACTIVE, // Currently inactive
                'expiration_type' => Discount::EXPIRATION_PERIOD,
                'start_date' => Carbon::now()->addMonth()->startOfMonth(), // Example: Starts next month
                'end_date' => Carbon::now()->addMonth()->addWeeks(2), // Example: Ends 2 weeks after start
                'description' => 'خصم سيتم تفعيله قريباً لموسم الصيف.',
            ],
            // --- More Examples ---
            [
                'name' => 'خصم 25 درهم للدعوة',
                'code' => null, // Likely generated per user referral
                'type' => Discount::TYPE_FIXED,
                'value' => 25.00,
                'status' => Discount::STATUS_ACTIVE,
                'expiration_type' => Discount::EXPIRATION_NONE,
                'description' => 'مكافأة لدعوة صديق للتسجيل والشراء.',
            ],
             [
                'name' => 'عرض اليوم الواحد',
                'code' => 'FLASH'.date('md'), // e.g., FLASH1026
                'type' => Discount::TYPE_PERCENTAGE,
                'value' => 30.00,
                'status' => Discount::STATUS_ACTIVE,
                'expiration_type' => Discount::EXPIRATION_PERIOD,
                'start_date' => Carbon::now()->startOfDay(),
                'end_date' => Carbon::now()->endOfDay(),
                'description' => 'خصم كبير ليوم واحد فقط!',
            ],
             [
                'name' => 'كوبون تعويض',
                'code' => 'SORRY'.Str::random(6),
                'type' => Discount::TYPE_FIXED,
                'value' => 15.00,
                'status' => Discount::STATUS_ACTIVE,
                'expiration_type' => Discount::EXPIRATION_DURATION,
                 'duration_days' => 30,
                'description' => 'كوبون مخصص يتم إصداره من خدمة العملاء.',
            ],
        ];

         foreach ($discounts as $discountData) {
            // Ensure code uniqueness if generated automatically or nullable
            if (isset($discountData['code']) && Discount::where('code', $discountData['code'])->exists()) {
                 $this->command->warn("Discount code '{$discountData['code']}' already exists, skipping '{$discountData['name']}'.");
                 continue;
            }

            Discount::updateOrCreate(
                // Use name as unique identifier for seeding? Or code if always present?
                // Using name might overwrite descriptions if names aren't unique in practice
                ['name' => $discountData['name']],
                $discountData
            );
             $this->command->info("Discount '{$discountData['name']}' seeded.");
        }
    }
}