<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('banners', function (Blueprint $table) {
            $table->id();
            $table->string('title'); // Main heading, e.g., "أفضل التخفيضات 2022"
            $table->text('description')->nullable(); // Sub-heading text
            $table->string('image'); // Path to the main background image
            $table->string('button_text')->nullable(); // Text for the CTA button, e.g., "اكتشف المزيد"
            $table->string('button_url')->nullable(); // The URL the button links to
            $table->boolean('is_active')->default(true)->index(); // To control visibility
            $table->unsignedInteger('sort_order')->default(0)->index(); // For ordering the slides
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('banners'); }
};