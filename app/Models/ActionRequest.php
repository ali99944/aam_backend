<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActionRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'action_type',
        'data',
        'status',
        'requested_by_user_id', // Added
        'processed_by_user_id', // Added
        'processed_at',         // Added
        'rejection_reason',     // Added
    ];

    protected $casts = [
        'data' => 'array', // Automatically cast JSON to/from array
        'processed_at' => 'datetime', // Added
    ];

    // --- Relationships (Added) ---
    public function requestor(): BelongsTo {
        return $this->belongsTo(User::class, 'requested_by_user_id')->withDefault(['name' => 'System/Unknown']);
    }
    public function processor(): BelongsTo {
        return $this->belongsTo(User::class, 'processed_by_user_id')->withDefault(['name' => 'N/A']);
    }

     // --- Static Statuses ---
     const STATUS_PENDING = 'pending';
     const STATUS_APPROVED = 'approved';
     const STATUS_REJECTED = 'rejected';

     public static function statuses(): array {
         return [
             self::STATUS_PENDING => 'Pending',
             self::STATUS_APPROVED => 'Approved',
             self::STATUS_REJECTED => 'Rejected',
         ];
     }

     // Define your action types here or fetch from config/service
     // This helps with validation and potentially displaying friendly names
     const TYPE_PRODUCT_UPDATE = 'product_update';
     const TYPE_ORDER_CANCEL = 'order_cancel';
     const TYPE_USER_VERIFY = 'user_verify';

     const TYPE_ORDER_CREATE_REQUEST = 'order_create_request'; // Added
     // Add more action types as needed...

     public static function availableActionTypes(): array {
          return [
             self::TYPE_PRODUCT_UPDATE => 'Product Update Request',
             self::TYPE_ORDER_CANCEL => 'Order Cancellation Request',
             self::TYPE_USER_VERIFY => 'Manual User Verification',
             self::TYPE_ORDER_CREATE_REQUEST => 'Order Creation Request',
             // ... map other types
         ];
     }

     // Accessor for friendly action type name
     public function getActionTypeNameAttribute(): string {
        return self::availableActionTypes()[$this->action_type] ?? ucfirst(str_replace('_', ' ', $this->action_type));
     }
}