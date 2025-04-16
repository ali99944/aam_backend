<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\PaymentMethod; // Import the PaymentMethod model
use Illuminate\Support\Facades\DB; // Optional: For truncate/constraints
use Illuminate\Support\Facades\Schema; // Optional: For constraints

class PaymentMethodSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Optional: Clear the table before seeding (Use with caution!)
        // Schema::disableForeignKeyConstraints(); // Disable constraints if truncating
        // DB::table('payment_methods')->truncate();
        // Schema::enableForeignKeyConstraints(); // Re-enable constraints

        // --- 1. Cash on Delivery (Enabled by Default) ---
        PaymentMethod::updateOrCreate(
            ['code' => 'cod'], // Unique identifier to find or create
            [
                'name' => 'Cash on Delivery',
                'description' => 'Pay with cash when your order is delivered.',
                'image' => 'images/payment/cod.png', // Example path - create this image
                'slug' => 'cash-on-delivery',
                'gateway_provider' => null, // No specific provider for COD
                'supported_currencies' => null, // Supports all store currencies by default
                'is_default' => true, // Make it default initially
                'is_enabled' => true, // Enable it
                'is_test_mode' => false,
                'is_online' => false, // It's an offline method
                'credentials' => null, // No credentials needed
                'display_order' => 0, // Show first
                'instructions' => 'Please prepare the exact amount for payment upon delivery. / يرجى تجهيز المبلغ الصحيح للدفع عند استلام الطلب.', // Example instructions
                'api_key' => null,
                'api_secret' => null,
                'client_id' => null,
                'client_secret' => null,
                'merchant_id' => null,
                'merchant_key' => null,
                'redirect_url' => null,
            ]
        );

        /*
        // --- 2. PayTabs (Commented Out - Disabled by Default) ---
        PaymentMethod::updateOrCreate(
            ['code' => 'paytabs_mada_visa_mc'], // Unique code for this specific PayTabs setup
            [
                'name' => 'PayTabs - Mada / Visa / MC',
                'description' => 'Pay securely using Mada, Visa, or Mastercard via PayTabs.',
                'image' => 'images/payment/paytabs.png', // Example path - create this image
                'slug' => 'paytabs-cards',
                'gateway_provider' => 'PayTabs',
                'supported_currencies' => 'JOD,SAR,AED,USD', // Example supported currencies
                'is_default' => false,
                'is_enabled' => false, // Start disabled
                'is_test_mode' => true, // Start in test mode
                'is_online' => true,
                // Store credentials as JSON. The model's mutator will encrypt this.
                // IMPORTANT: Replace placeholder values AFTER seeding via Admin Panel, NOT here.
                'credentials' => [ // Stored as encrypted JSON string in DB
                    'profile_id' => 'YOUR_PAYTABS_PROFILE_ID_HERE',
                    'server_key' => 'YOUR_PAYTABS_SERVER_KEY_HERE',
                    'region' => 'ARE', // Example: Jordan='JOR', Saudi='SAU', Egypt='EGY', Global='GLOBAL', UAE='ARE'
                    // Add any other necessary config keys here
                ],
                'display_order' => 10,
                'instructions' => null,
                // Specific fields can also be used if preferred over JSON credentials, model encrypts these too
                'api_key' => null, // Paytabs might use server key instead
                'api_secret' => null,
                'client_id' => null,
                'client_secret' => null,
                'merchant_id' => null, // Paytabs might use profile id instead
                'merchant_key' => null, // Paytabs might use server key instead
                'redirect_url' => null, // Usually configured in PayTabs dashboard or dynamically generated
            ]
        );
        */

        /*
        // --- 3. PayPal (Commented Out - Disabled by Default) ---
        PaymentMethod::updateOrCreate(
            ['code' => 'paypal_express'],
            [
                'name' => 'PayPal',
                'description' => 'Pay using your PayPal account or credit/debit card.',
                'image' => 'images/payment/paypal.png', // Example path - create this image
                'slug' => 'paypal',
                'gateway_provider' => 'PayPal',
                'supported_currencies' => 'USD,EUR,GBP,AUD', // Example
                'is_default' => false,
                'is_enabled' => false, // Start disabled
                'is_test_mode' => true, // Start in test mode
                'is_online' => true,
                // Use specific fields for common PayPal credentials
                'credentials' => null, // Use specific fields below mainly
                'display_order' => 20,
                'instructions' => null,
                // IMPORTANT: Replace placeholder values AFTER seeding via Admin Panel, NOT here.
                'api_key' => null, // Often N/A for PayPal REST
                'api_secret' => null, // Often N/A for PayPal REST
                'client_id' => 'YOUR_PAYPAL_CLIENT_ID_HERE',
                'client_secret' => 'YOUR_PAYPAL_SECRET_HERE',
                'merchant_id' => null,
                'merchant_key' => null,
                'redirect_url' => null, // Usually handled by PayPal SDK/API calls
            ]
        );
        */

    }
}