<?php

use App\Models\Country;
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
        Schema::create('timezones', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique(); // e.g., "America/New_York"
            $table->string('offset')->nullable(); // e.g., "UTC-05:00"
            $table->integer('gmt_offset')->nullable(); // e.g., -18000 (seconds from GMT)
            $table->string('abbreviation')->nullable(); // e.g., EST
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('timezones');
    }
};
