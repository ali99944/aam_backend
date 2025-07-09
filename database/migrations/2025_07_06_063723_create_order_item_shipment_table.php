<?php

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
        Schema::create('order_item_shipment', function (Blueprint $table) {
            $table->foreignIdFor(\App\Models\OrderItem::class)->constrained()->onDelete('cascade');
            $table->foreignIdFor(\App\Models\Shipment::class)->constrained()->onDelete('cascade');
            $table->integer('quantity'); // How many of the order_item's quantity are in this shipment
            $table->primary(['order_item_id', 'shipment_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_item_shipment');
    }
};
