<?php

use App\Models\City;
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
        Schema::create('delivery_fees', function (Blueprint $table) {
            $table->id();
            // Unique link to the city. One fee setting per city.
            $table->foreignIdFor(City::class)->unique();
            $table->decimal('amount', 8, 2); // The delivery fee amount
            $table->string('estimated_delivery_time')->nullable(); // e.g., "1-2 days", "Same day if ordered before 12 PM"
            $table->boolean('is_active')->default(true); // Easily enable/disable fee for a city
            $table->text('notes')->nullable(); // Internal notes if needed
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('delivery_fees');
    }
};
