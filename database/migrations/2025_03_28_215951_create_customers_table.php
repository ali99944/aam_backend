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
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('phone')->unique();
            $table->enum('status', ['active', 'banned', 'verification-required'])->default('active'); // Adjust default as needed
            $table->boolean('is_banned')->default(false);
            $table->timestamp('banned_at')->nullable();
            $table->text('ban_reason')->nullable();

            // Add index for faster lookups
            $table->index('status');
            $table->index('is_banned');
            $table->string('password');
            $table->decimal('balance', 10, 2)->default(0.00);
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
