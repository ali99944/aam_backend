<?php
namespace App\Models;
// ... other uses
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphOne; // Import MorphOne
use Illuminate\Support\Str;

class Order extends Model
{
    use HasFactory;

    // --- Status Constants ---
    const ORDER_STATUS_PENDING = 'pending';
    const ORDER_STATUS_IN_CHECK = 'in-check';
    const ORDER_STATUS_PROCESSING = 'processing';
    const ORDER_STATUS_COMPLETED = 'completed';
    const ORDER_STATUS_CANCELLED = 'cancelled';

    const DELIVERY_STATUS_PENDING = 'pending';
    const DELIVERY_STATUS_SHIPPED = 'shipped';
    const DELIVERY_STATUS_OUT_FOR_DELIVERY = 'out_for_delivery';
    const DELIVERY_STATUS_DELIVERED = 'delivered';
    const DELIVERY_STATUS_FAILED = 'failed_attempt';

    protected $fillable = [
        'customer_id',
        'guest_name',
        'guest_email',
        'order_status',
        'delivery_status',
        'subtotal',
        'discount_amount',
        'delivery_fee',
        'total',
        'payment_method_code',
        'track_code',
        'phone_number',
        'address_line_1',
        'address_line_2',
        'city_id',
        'postal_code',
        'special_mark',
        'notes',
    ];

    protected $casts = [
        'total' => 'decimal:2',
        'delivery_fee' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'discount_amount' => 'decimal:2',
    ];

    // --- Relationships ---
    public function customer(): BelongsTo {
        return $this->belongsTo(Customer::class)->withDefault(['name' => $this->guest_name ?? 'Guest']);
    }

    // Polymorphic relationship to ActionRequest
    public function creationRequest(): MorphOne
    {
        return $this->morphOne(ActionRequest::class, 'actionable')
                    ->where('action_type', ActionRequest::TYPE_ORDER_CREATE_REQUEST); // Filter by type
    }

    public static function generateTrackCode(): string {
        // Simple example: AAM- + timestamp + random string
        $timestamp = now()->format('yd'); // Year, Day of year
        do {
            $random = strtoupper(Str::random(6));
            $code = 'AAM-' . $timestamp . '-' . $random;
        } while (self::where('track_code', $code)->exists());
        return $code;
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function payment()
    {
        return $this->hasMany(Payment::class);
    }

    // public function delivery()
    // {
    //     return $this->hasMany(Delivery::class);
    // }
}