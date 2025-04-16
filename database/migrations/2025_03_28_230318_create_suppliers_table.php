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
        Schema::create('suppliers', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique(); // Vendor/Company Name
            $table->text('description')->nullable();
            $table->string('image')->nullable(); // Logo or image

            // Contact Information
            $table->string('contact_person')->nullable();
            $table->string('email')->nullable()->unique(); // Primary contact email
            $table->string('phone')->nullable();
            $table->text('address')->nullable();
            $table->string('website')->nullable();

             // Financial Information
            $table->decimal('balance', 12, 2)->default(0.00); // Current balance (positive = we owe them, negative = they owe us / credit) - Managed via transactions later

            $table->boolean('is_active')->default(true);
            $table->text('notes')->nullable(); // Internal admin notes
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('suppliers');
    }
};
