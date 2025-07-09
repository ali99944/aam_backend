<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('testimonials', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Name of the person giving the testimonial
            $table->string('title_or_company')->nullable(); // Their title or company name (e.g., "CEO, Tech Corp")
            $table->text('quote'); // The testimonial content
            $table->string('avatar')->nullable(); // Path to their profile picture
            $table->unsignedTinyInteger('rating')->nullable()->comment('Rating from 1 to 5'); // Optional star rating
            $table->boolean('is_active')->default(true)->index(); // To control visibility
            $table->unsignedInteger('sort_order')->default(0)->index(); // For ordering display
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('testimonials');
    }
};