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
        Schema::create('discounts', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // e.g., "Summer Sale", "New User Discount"
            $table->string('code')->unique()->nullable(); // Optional unique code for coupon-like usage
            $table->enum('type', ['fixed', 'percentage'])->default('percentage'); // Type of discount
            $table->decimal('value', 10, 2); // Amount (if fixed) or percentage (e.g., 15.00 for 15%)
            $table->enum('status', ['active', 'inactive', 'expired'])->default('inactive');

            // Expiration Handling
            $table->enum('expiration_type', ['none', 'duration', 'period'])->default('none'); // How expiration is defined
            $table->integer('duration_days')->nullable(); // Number of days after activation (if expiration_type is 'duration')
            $table->timestamp('start_date')->nullable(); // Start date (if expiration_type is 'period')
            $table->timestamp('end_date')->nullable(); // End date (if expiration_type is 'period')
            // $table->timestamp('end_date')->nullable(); // End date (if expiration_type is 'period')

            $table->text('description')->nullable(); // Internal description
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('discounts');
    }
};
