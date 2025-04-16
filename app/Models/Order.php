<?php
namespace App\Models;
// ... other uses
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;// Add BelongsTo

class Order extends Model
{
    use HasFactory;

    // --- Status Constants ---
    const STATUS_PENDING = 'pending';
    const STATUS_IN_CHECK = 'in-check'; // Added
    const STATUS_PROCESSING = 'processing';
    const STATUS_COMPLETED = 'completed';
    const STATUS_CANCELLED = 'cancelled';

    public static function statuses(): array {
        return [
            self::STATUS_PENDING => 'Pending',
            self::STATUS_IN_CHECK => 'In Check (Pending Approval)', // Added
            self::STATUS_PROCESSING => 'Processing',
            self::STATUS_COMPLETED => 'Completed',
            self::STATUS_CANCELLED => 'Cancelled',
        ];
    }

    protected $fillable = [
        'customer_id', // Nullable now?
        'phone_number', // Added
        'address_line_1', // Added
        'address_line_2', // Added
        'city_id', // Added
        'postal_code', // Added
        'special_mark', // Added
        'notes', // Added
        'status',
        'subtotal', // Added
        'discount_amount', // Added
        'delivery_fee_id',
        'total',
        'payment_method_code', // Added (replaces payment_method string)
        'track_code', // Added
    ];

    protected $casts = [
        'total' => 'decimal:2',
        'delivery_fee' => 'decimal:2',
        'subtotal' => 'decimal:2', // Added
        'discount_amount' => 'decimal:2', // Added
        // 'tax_amount' => 'decimal:2', // Added
    ];

    // --- Relationships ---
    public function customer(): BelongsTo {
        return $this->belongsTo(Customer::class)->withDefault(['name' => 'Guest Customer']); // Allow null/guest
    }
    public function city(): BelongsTo { // Added
        return $this->belongsTo(City::class);
    }
    public function deliveryFee() { // Convenience relationship
        // Assumes City model has hasOne(DeliveryFee::class) relationship named 'deliveryFee'
         return $this->hasOneThrough(DeliveryFee::class, City::class, 'id', 'city_id', 'city_id', 'id');
    }
    public function paymentMethod(): BelongsTo { // Added (based on code)
        return $this->belongsTo(PaymentMethod::class, 'payment_method_code', 'code')->withDefault(['name' => $this->payment_method_code ?? 'N/A']); // Fallback to code if not found
    }
    // ... existing items, payments, delivery, invoice relationships ...


    // --- Accessors ---
    // ... existing formatted_total ...

    // --- Helper to generate Track Code ---
    public static function generateTrackCode(): string {
        // Simple example: AAM- + timestamp + random string
        $timestamp = now()->format('yd'); // Year, Day of year
        do {
            $random = strtoupper(Str::random(6));
            $code = 'AAM-' . $timestamp . '-' . $random;
        } while (self::where('track_code', $code)->exists());
        return $code;
    }
}