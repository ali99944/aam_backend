<?php

use App\Models\DeliveryCompany;
use App\Models\DeliveryPersonnel;
use App\Models\Order;
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
        Schema::create('shipments', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Order::class);
            $table->foreignIdFor(DeliveryPersonnel::class)->nullable();
            $table->foreignIdFor(DeliveryCompany::class)->nullable();


            $table->string('tracking_number');
            $table->enum('status', ['pending', 'shipped', 'in_transit', 'out_for_delivery', 'delivered', 'failed', 'returned'])->default('pending');
            $table->timestamp('shipped_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->string('confirmation_image')->nullable();
            $table->enum('type', ['outbound', 'return'])->default('outbound');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shipments');
    }
};
