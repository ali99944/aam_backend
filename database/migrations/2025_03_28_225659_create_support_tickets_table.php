<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Customer; // Assuming App\Models\Customer
use App\Models\User; // Assuming App\Models\User for Admin

return new class extends Migration {
    public function up(): void {
        Schema::create('support_tickets', function (Blueprint $table) {
            $table->id();
            // Link to customer - nullable if guests can submit via API later? Required for now.
            $table->foreignIdFor(Customer::class)->constrained()->onDelete('cascade');
            $table->string('subject');
            $table->longText('message'); // Initial message
            $table->enum('status', ['open', 'in_progress', 'customer_reply', 'resolved', 'closed'])->default('open');
            $table->enum('priority', ['low', 'medium', 'high'])->default('medium');
            $table->timestamp('last_reply_at')->nullable(); // Timestamp of the last reply
            // Optional: Link to the admin currently assigned
            $table->foreignId('assigned_admin_id')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('support_tickets'); }
};