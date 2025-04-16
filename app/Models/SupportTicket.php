<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SupportTicket extends Model
{
    use HasFactory;
    protected $fillable = [
        'customer_id', 'subject', 'message', 'status',
        'priority', 'last_reply_at', 'assigned_admin_id'
    ];
    protected $casts = ['last_reply_at' => 'datetime'];

    // Statuses & Priorities
    const STATUS_OPEN = 'open';
    const STATUS_IN_PROGRESS = 'in_progress';
    const STATUS_CUSTOMER_REPLY = 'customer_reply';
    const STATUS_RESOLVED = 'resolved';
    const STATUS_CLOSED = 'closed';

    const PRIORITY_LOW = 'low';
    const PRIORITY_MEDIUM = 'medium';
    const PRIORITY_HIGH = 'high';

    public static function statuses(): array { return [ /*...*/ ]; } // Define as before
    public static function priorities(): array { return [ /*...*/ ]; } // Define as before

    // Relationships
    public function customer(): BelongsTo { return $this->belongsTo(Customer::class)->withDefault(['name' => 'Deleted Customer']); }
    public function assignedAdmin(): BelongsTo { return $this->belongsTo(User::class, 'assigned_admin_id')->withDefault(['name' => 'Unassigned']); }
    public function replies(): HasMany { return $this->hasMany(SupportTicketReply::class)->orderBy('created_at'); }

    // Accessors for labels
    public function getStatusLabelAttribute(): string { /* Return label based on status */ }
    public function getPriorityLabelAttribute(): string { /* Return label based on priority */ }
}