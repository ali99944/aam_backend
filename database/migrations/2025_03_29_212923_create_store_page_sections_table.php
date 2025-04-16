<?php

use App\Models\StorePage;
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
        Schema::create('store_page_sections', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('key')->unique()->index();
            $table->json('content');
            $table->foreignIdFor(StorePage::class);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('store_page_sections');
    }
};
