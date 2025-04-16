<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\SupportTicket;
use App\Models\User; // For Admin reply
use App\Models\Customer; // For Customer reply

return new class extends Migration {
    public function up(): void {
        Schema::create('support_ticket_replies', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(SupportTicket::class)->constrained()->onDelete('cascade');
            $table->longText('message');
            // Identify replier type: Customer or Admin (User)
            // Option 1: Separate Foreign Keys (Clearer)
            $table->foreignId('customer_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('admin_id')->nullable()->constrained('users')->onDelete('cascade'); // Assuming admins are in 'users' table

            // Option 2: Morphic Relationship (More complex if needed later)
            // $table->morphs('replier'); // Creates replier_id, replier_type

            $table->timestamps();

            // Add check constraint if using Option 1 to ensure one ID is non-null
            // DB::statement('ALTER TABLE support_ticket_replies ADD CONSTRAINT chk_replier CHECK ((customer_id IS NOT NULL AND admin_id IS NULL) OR (customer_id IS NULL AND admin_id IS NOT NULL));'); // Syntax might vary
        });
    }
    public function down(): void { Schema::dropIfExists('support_ticket_replies'); }
};