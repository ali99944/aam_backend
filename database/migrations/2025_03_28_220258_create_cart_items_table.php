<?php

use App\Models\Cart;
use App\Models\Customer;
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
        Schema::create('cart_items', function (Blueprint $table) {
            $table->id();
            // Link to customer if logged in
            $table->foreignIdFor(Customer::class)->nullable()->constrained()->onDelete('cascade');
            // Unique token for guest carts
            $table->string('guest_cart_token')->nullable()->index(); // UUID for better uniqueness
            // Link to the product
            $table->foreignIdFor(Product::class)->constrained()->onDelete('cascade');

            $table->unsignedInteger('quantity')->default(1);
            // Optional: Store price at time of adding
            // $table->decimal('price_at_add', 10, 2)->nullable();
            // Optional: Store selected addons as JSON
            // $table->json('addons_data')->nullable();
            $table->timestamps();

            // Ensure a cart item belongs to either a customer or a guest token
            $table->unique(['customer_id', 'product_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cart_items');
    }
};
