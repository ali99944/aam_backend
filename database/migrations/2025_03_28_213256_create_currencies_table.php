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
        Schema::create('currencies', function (Blueprint $table) {
            $table->id();
            $table->string('name');                 // e.g., US Dollar, UAE Dirham
            $table->string('code', 5)->unique();   // e.g., USD, AED
            $table->string('symbol', 5)->nullable(); // e.g., $, د.إ
            $table->decimal('exchange_rate', 15, 6)->default(1.000000); // Rate against base currency (e.g., USD or AED)
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('currencies');
    }
};
