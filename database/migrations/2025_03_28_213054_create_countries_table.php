<?php

use App\Models\Currency;
use App\Models\Timezone;
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
        Schema::create('countries', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->char('iso2', 2)->unique(); // Alpha-2 code
            $table->char('iso3', 3)->unique()->nullable(); // Alpha-3 code
            $table->string('phone_code')->nullable();
            $table->string('capital')->nullable();
            $table->foreignIdFor(Currency::class);
            $table->foreignIdFor(Timezone::class);
            $table->string('region')->nullable(); // e.g., Asia
            $table->string('subregion')->nullable(); // e.g., Western Asia
            $table->string('flag_image')->nullable(); // Path to flag image
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('countries');
    }
};
