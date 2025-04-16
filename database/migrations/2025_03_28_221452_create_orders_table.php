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
            $table->string('payment_method_code')->nullable();
            $table->string('postal_code')->nullable();
            $table->string('special_mark')->nullable(); // Landmark etc.
            $table->text('notes')->nullable(); // Customer notes for delivery

            // Add tracking code (unique identifier for customer tracking)
            $table->string('track_code')->unique()->nullable();

            // Add subtotal, discount, tax if needed
            $table->decimal('subtotal', 10, 2)->default(0.00);
            $table->decimal('discount_amount', 10, 2)->default(0.00);
            $table->string('phone_number'); // Contact for this specific order
            $table->string('address_line_1');
            $table->string('address_line_2')->nullable();
            $table->foreignIdFor(City::class)->constrained()->onDelete('restrict'); // Link to city
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
