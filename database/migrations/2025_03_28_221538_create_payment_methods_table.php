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
        Schema::create('payment_methods', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique()->index();
            $table->string('code')->unique()->index();
            $table->string('description')->nullable();
            $table->string('image');
            $table->string('slug')->unique()->index();
            $table->string('gateway_provider')->nullable();
            $table->string('supported_currencies')->nullable();
            $table->boolean('is_default')->default(false);
            $table->boolean('is_enabled')->default(false);
            $table->boolean('is_test_mode')->default(false);
            $table->boolean('is_online')->default(true);
            $table->text('credentials')->nullable();
            $table->unsignedInteger('display_order')->default(0);
            $table->text('instructions')->nullable();
            $table->string('api_key')->nullable();
            $table->string('api_secret')->nullable();
            $table->string('client_id')->nullable();
            $table->string('client_secret')->nullable();
            $table->string('merchant_id')->nullable();
            $table->string('merchant_key')->nullable();
            $table->string('redirect_url')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_methods');
    }
};
