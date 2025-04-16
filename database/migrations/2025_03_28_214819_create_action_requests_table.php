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
        Schema::create('action_requests', function (Blueprint $table) {
            $table->id();
            $table->string('action_type');
            $table->json('data');
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->foreignId('requested_by_user_id')->nullable()->after('id')->constrained('users')->onDelete('set null');
             // User who approved/rejected the request
             $table->foreignId('processed_by_user_id')->nullable()->after('status')->constrained('users')->onDelete('set null');
             $table->timestamp('processed_at')->nullable();
             $table->text('rejection_reason')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('action_requests');
    }
};
