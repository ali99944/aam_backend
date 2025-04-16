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
        Schema::create('states', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Country::class)->constrained()->onDelete('restrict'); // States belong to Countries, prevent deleting country with states
            $table->string('name');
            $table->string('state_code')->nullable()->index(); // Optional state code (e.g., CA, TX)
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            // Optional: Unique constraint for name within a country
            $table->unique(['country_id', 'name']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('states');
    }
};
