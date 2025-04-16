<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('offers', function (Blueprint $table) {
            $table->id();
            $table->string('title'); // e.g., "Eid Electronics Sale"
            $table->string('slug')->unique(); // URL-friendly identifier
            $table->text('description')->nullable(); // Short description shown with the offer
            $table->string('image'); // Path to the promotional image/banner
            $table->enum('type', ['generic', 'category', 'product', 'brand'])->default('generic'); // What the offer links to/applies to
            $table->unsignedBigInteger('linked_id')->nullable(); // ID of Category, Product, or Brand if type isn't generic
            $table->string('target_url')->nullable(); // Optional: Custom URL to link to instead of type/linked_id logic

            $table->timestamp('start_date')->nullable();
            $table->timestamp('end_date')->nullable();
            $table->boolean('is_active')->default(false); // Control visibility
            $table->integer('sort_order')->default(0); // For ordering display on frontend
            $table->timestamps();

            // Add index for quicker lookup of active offers by type
            $table->index(['is_active', 'type', 'start_date', 'end_date']);
        });
    }
    public function down(): void { Schema::dropIfExists('offers'); }
};