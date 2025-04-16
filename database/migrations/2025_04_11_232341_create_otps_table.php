<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::create('otps', function (Blueprint $table) {
            $table->id();
            $table->string('identifier'); // Email or Phone number used
            $table->string('code');       // The OTP code
            $table->timestamp('expires_at'); // When the OTP is no longer valid
            $table->timestamp('verified_at')->nullable(); // When it was successfully verified
            $table->string('purpose')->nullable(); // e.g., 'password_reset', 'email_verification', 'phone_verification'
            $table->timestamps();

            $table->index(['identifier', 'purpose', 'expires_at']); // Index for efficient lookups
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::dropIfExists('otps');
    }
};