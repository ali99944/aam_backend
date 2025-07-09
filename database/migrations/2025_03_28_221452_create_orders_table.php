<?php
// database/migrations/xxxx_xx_xx_xxxxxx_create_orders_table.php
use App\Models\City;
use App\Models\Customer;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            // Make customer_id nullable for guest orders
            $table->foreignIdFor(Customer::class)->nullable()->constrained()->onDelete('set null');

            // Add delivery status
            $table->enum('order_status', ['pending', 'processing', 'completed', 'cancelled', 'in-check'])->default('pending');

            // Customer info for guest orders
            $table->string('customer_name');
            $table->string('customer_email');
            $table->string('customer_phone');
            
            // Financials
            $table->decimal('subtotal', 10, 2);
            $table->decimal('delivery_fee', 10, 2);
            $table->decimal('discount_amount', 10, 2)->default(0.00);
            $table->decimal('total', 10, 2);

            // Tracking
            $table->string('tracking_number')->unique()->nullable();


            // Shipping Details (snapshot)
            $table->string('address_line_1');
            $table->string('address_line_2')->nullable();
            $table->foreignIdFor(City::class)->nullable()->constrained()->onDelete('set null');
            $table->string('postal_code')->nullable();
            $table->text('notes')->nullable();

            $table->timestamps();

            $table->index('order_status');
            $table->index('delivery_status');
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
