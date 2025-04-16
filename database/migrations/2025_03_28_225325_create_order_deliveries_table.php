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
        Schema::create('order_deliveries', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Order::class);
            $table->foreignIdFor(DeliveryPersonnel::class);
            $table->foreignIdFor(DeliveryCompany::class);
            $table->string('tracking_number');
            $table->enum('status', ['pending', 'completed', 'failed'])->default('pending');
            $table->string('delivery_date');
            $table->string('confirmation_image')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_deliveries');
    }
};
