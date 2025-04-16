<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SupportTicketReply extends Model
{
    use HasFactory;
    protected $table = 'support_ticket_replies';
    protected $fillable = ['support_ticket_id', 'message', 'customer_id', 'admin_id'];

    // Relationships
    public function ticket(): BelongsTo { return $this->belongsTo(SupportTicket::class); }
    public function customer(): BelongsTo { return $this->belongsTo(Customer::class); }
    public function admin(): BelongsTo { return $this->belongsTo(User::class, 'admin_id'); } // Assuming admin uses User model

    // Helper to get the replier's user model (either Customer or Admin)
    public function getReplierAttribute(): ?Model {
        return $this->customer ?? $this->admin;
    }
     // Helper to get the replier's name
     public function getReplierNameAttribute(): string {
         return $this->customer?->name ?? $this->admin?->name ?? 'System';
     }
      // Helper to check if reply is from admin
     public function getIsAdminReplyAttribute(): bool {
         return !is_null($this->admin_id);
     }
}