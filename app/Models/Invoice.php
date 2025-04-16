<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne; // For Payment

class Invoice extends Model
{
    use HasFactory;
    // Adjust fillable as needed for creation logic
    protected $fillable = [
        'order_id', 'invoice_number', 'invoice_date', 'due_date',
        'subtotal', 'tax_amount', 'discount_amount', 'delivery_fee', 'total_amount',
        'status', 'notes',
    ];
    protected $casts = [
        'invoice_date' => 'date',
        'due_date' => 'date',
        'subtotal' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'delivery_fee' => 'decimal:2',
        'total_amount' => 'decimal:2',
    ];

    public function order(): BelongsTo {
        return $this->belongsTo(Order::class);
    }

    // Relationship to Payment
    public function payment(): HasOne {
        // Assumes Payment table has invoice_id nullable foreign key
         return $this->hasOne(Payment::class);
    }

     // --- Static Statuses ---
     const STATUS_DRAFT = 'draft';
     const STATUS_SENT = 'sent';
     const STATUS_PAID = 'paid';
     const STATUS_PARTIALLY_PAID = 'partially_paid';
     const STATUS_OVERDUE = 'overdue';
     const STATUS_VOID = 'void';

     public static function statuses(): array {
         return [
             self::STATUS_DRAFT => 'Draft',
             self::STATUS_SENT => 'Sent',
             self::STATUS_PAID => 'Paid',
             self::STATUS_PARTIALLY_PAID => 'Partially Paid',
             self::STATUS_OVERDUE => 'Overdue',
             self::STATUS_VOID => 'Void',
         ];
     }

}