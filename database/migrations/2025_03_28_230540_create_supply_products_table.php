<?php

use App\Models\Product;
use App\Models\Supplier;
use App\Models\SupplyOrder;
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
        Schema::create('supply_products', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Supplier::class);
            $table->foreignIdFor(SupplyOrder::class);
            $table->foreignIdFor(Product::class);
            $table->integer('quantity');
            $table->double('price');
            $table->double('total');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('supply_products');
    }
};
