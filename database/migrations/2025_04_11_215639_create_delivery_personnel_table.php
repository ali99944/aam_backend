<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\DeliveryCompany; // Use the model

return new class extends Migration {
    public function up(): void {
        Schema::create('delivery_personnel', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('phone')->unique();
            $table->timestamp('email_verified_at')->nullable(); // Optional standard verification
            $table->string('password'); // For potential future login
            $table->rememberToken();

            // Relationship to Company (Optional)
            $table->foreignIdFor(DeliveryCompany::class)->nullable()->constrained()->onDelete('set null');

            // Additional Useful Info
            $table->string('avatar')->nullable(); // Profile picture path
            $table->string('vehicle_type')->nullable(); // e.g., Motorcycle, Car, Van
            $table->string('vehicle_plate_number')->nullable();
            $table->string('national_id_or_iqama')->nullable()->unique(); // Optional but good for ID

            $table->boolean('is_active')->default(true); // Enable/disable driver
            // Optional: Add a more detailed status like 'on-duty', 'off-duty' later if needed
            // $table->enum('status', ['pending', 'active', 'inactive', 'on-duty', 'off-duty'])->default('active');

            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('delivery_personnel');
    }
};