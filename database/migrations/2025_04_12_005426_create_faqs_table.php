<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\FaqCategory; // Use model

return new class extends Migration {
    public function up(): void {
        Schema::create('faqs', function (Blueprint $table) {
            $table->id();
            // Allow FAQs without category? Use nullable() if so.
            $table->foreignIdFor(FaqCategory::class)->nullable()->constrained()->onDelete('set null');
            $table->text('question');
            $table->longText('answer'); // Use longText for potentially long answers with HTML
            $table->unsignedInteger('display_order')->default(0); // For ordering within category / overall
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('faqs'); }
};