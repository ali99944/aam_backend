<?php

use App\Models\ExpenseCategory;
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
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(ExpenseCategory::class)->constrained()->onDelete('restrict'); // Prevent deleting category with expenses
            $table->decimal('amount', 10, 2); // Store amount with precision
            $table->date('entry_date'); // Date the expense occurred/was recorded
            $table->text('description'); // Details about the expense
            $table->string('receipt_image')->nullable(); // Path to optional receipt image
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expenses');
    }
};
