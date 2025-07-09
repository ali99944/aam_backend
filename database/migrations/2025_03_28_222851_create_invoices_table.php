<?php

// database/migrations/xxxx_xx_xx_xxxxxx_create_invoices_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Order;
use App\Models\Customer; // If needed

return new class extends Migration {
    public function up(): void {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Order::class)->unique()->constrained()->onDelete('cascade'); // One invoice per order
            // $table->foreignIdFor(Customer::class)->constrained()->onDelete('cascade'); // Link to customer too?
            $table->string('invoice_number')->unique(); // e.g., INV-2023-00123
            $table->date('invoice_date');
            $table->date('due_date')->nullable(); // If applicable
            $table->decimal('subtotal', 10, 2);
            $table->decimal('tax_amount', 10, 2)->default(0);
            $table->decimal('discount_amount', 10, 2)->default(0);
            $table->decimal('delivery_fee', 10, 2);
            $table->decimal('total_amount', 10, 2);
            $table->enum('status', ['draft', 'sent', 'paid', 'partially_paid', 'overdue', 'void'])->default('draft');
            $table->string('pdf_path')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('invoices'); }
};