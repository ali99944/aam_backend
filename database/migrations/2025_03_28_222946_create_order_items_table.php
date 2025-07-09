<?php

use App\Models\Order;
use App\Models\Product;
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
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Order::class);
            $table->foreignIdFor(Product::class)->nullable()->constrained()->onDelete('set null'); // Allow product deletion

            
            $table->string('product_name');     // ADDED: Snapshot of the name
            $table->string('product_sku')->nullable(); // ADDED: Snapshot of the SKU
            $table->integer('quantity');
            $table->decimal('price', 10, 2);    // Price at time of purchase
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
