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
        Schema::create('translations', function (Blueprint $table) {
            $table->id();
            $table->string('locale')->index();
            $table->string('field');
            $table->text('value');

            // Polymorphic relationship fields
            $table->unsignedBigInteger('translatable_id');
            $table->string('translatable_type');

            $table->unique(['locale', 'field', 'translatable_id', 'translatable_type'], 'translation_unique');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('translations');
    }
};
