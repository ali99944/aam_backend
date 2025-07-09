<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo; // Import MorphTo

class ActionRequest extends Model
{
    use HasFactory;

    // --- Action Type Constants ---
    const TYPE_ORDER_CREATE_REQUEST = 'order_creation';
    const TYPE_ORDER_CANCEL_REQUEST = 'order_cancellation';
    // Add other types like 'product_update', etc.

    // --- Status Constants ---
    const STATUS_PENDING = 'pending';
    const STATUS_APPROVED = 'approved';
    const STATUS_REJECTED = 'rejected';

    protected $fillable = [
        'action_type',
        'actionable_id', // Part of morphs
        'actionable_type', // Part of morphs
        'data', // For extra info like rejection reason
        'status',
        'requested_by_user_id',
        'processed_by_user_id',
        'processed_at',
        'rejection_reason',
    ];

    protected $casts = [
        'data' => 'array',
        'processed_at' => 'datetime',
    ];

    // Polymorphic relationship
    public function actionable(): MorphTo
    {
        return $this->morphTo();
    }

    public function requestedBy(): BelongsTo {
         return $this->belongsTo(User::class, 'requested_by_user_id'); // Assuming requestor is an admin/staff
    }
    public function processedBy(): BelongsTo {
         return $this->belongsTo(User::class, 'processed_by_user_id'); // Assuming processor is an admin/staff
    }
}