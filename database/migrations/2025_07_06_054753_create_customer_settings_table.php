<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('customer_settings', function (Blueprint $table) {
            $table->id();
            //notifications_settings
            $table->boolean('is_subscribed_to_newsletter')->default(false);
            $table->boolean('is_subscribed_to_order_status_updates')->default(false);
            $table->boolean('is_subscribed_to_promotional_emails')->default(false);
            $table->boolean('is_subscribed_to_abandoned_cart_reminders')->default(false);
            $table->boolean('is_subscribed_to_price_drop_notifications')->default(false);

            //other_notifications

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_settings');
    }
};
