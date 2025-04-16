<?php

use App\Models\Brand;
use App\Models\Discount;
use App\Models\SubCategory;
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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('description');
            $table->string('main_image');
            $table->double('cost_price');
            $table->double('sell_price');

            $table->integer('total_views')->default(0);
            $table->integer('favorites_views')->default(0);

            $table->integer('stock')->default(0);
            $table->integer('lower_stock_warn')->default(0);
            $table->integer('favorites_count')->default(0);
            $table->string('sku_code')->default(0);
            $table->double('overall_rating')->default(0);
            $table->double('total_rating')->default(0);
            $table->foreignIdFor(SubCategory::class);
            $table->foreignIdFor(Brand::class)->nullable();
            $table->foreignIdFor(Discount::class)->nullable();
            $table->enum('status', ['active', 'out-of-stock']);
            $table->boolean('is_public')->default(false);
            $table->boolean('is_featured')->default(false)->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
