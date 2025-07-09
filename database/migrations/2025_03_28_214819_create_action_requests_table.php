<?php
// database/migrations/xxxx_xx_xx_xxxxxx_create_action_requests_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('action_requests', function (Blueprint $table) {
            $table->id();
            $table->string('action_type')->index(); // e.g., 'order_creation', 'order_cancellation'
            $table->morphs('actionable'); // Polymorphic relation: links to Order, Product, etc.
            $table->json('data')->nullable(); // For additional context, like cancellation reason

            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->foreignId('requested_by_user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('processed_by_user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('processed_at')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->timestamps();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('action_requests');
    }
};
