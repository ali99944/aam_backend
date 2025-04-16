<?php

use App\Models\City;
use App\Models\Customer;
use App\Models\DeliveryFee;
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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Customer::class);
            $table->enum('status', ['pending', 'processing', 'completed', 'cancelled', 'in-check'])->default('pending');
            $table->double('total');
            $table->double('delivery_fee');
            $table->string('payment_method_code')->nullable()->after('delivery_fee');
            $table->string('postal_code')->nullable()->after('city_id');
            $table->string('special_mark')->nullable()->after('postal_code'); // Landmark etc.
            $table->text('notes')->nullable()->after('special_mark'); // Customer notes for delivery

            // Add tracking code (unique identifier for customer tracking)
            $table->string('track_code')->unique()->nullable()->after('status');

            // Add subtotal, discount, tax if needed
            $table->decimal('subtotal', 10, 2)->default(0.00)->after('status');
            $table->decimal('discount_amount', 10, 2)->default(0.00)->after('subtotal');
            $table->string('phone_number')->after('customer_id'); // Contact for this specific order
            $table->string('address_line_1')->after('phone_number');
            $table->string('address_line_2')->nullable()->after('address_line_1');
            $table->foreignIdFor(City::class)->after('address_line_2')->constrained()->onDelete('restrict'); // Link to city
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
