<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    use HasFactory;
    protected $fillable = [
        'order_id', 'invoice_id', 'payment_method_id', // Add invoice_id
        'amount', 'status', 'transaction_id'
    ];
    protected $casts = ['amount' => 'decimal:2'];

    // Relationships
    public function order(): BelongsTo { return $this->belongsTo(Order::class); }
    public function paymentMethod(): BelongsTo { return $this->belongsTo(PaymentMethod::class)->withDefault(['name'=>'N/A']); }
    public function invoice(): BelongsTo { return $this->belongsTo(Invoice::class); } // Add relationship

     // --- Static Statuses ---
     public const STATUS_PENDING = 'pending';
     public const STATUS_COMPLETED = 'completed';
     public const STATUS_FAILED = 'failed';

     public static function statuses(): array {
         return [
             self::STATUS_PENDING => 'Pending',
             self::STATUS_COMPLETED => 'Completed',
             self::STATUS_FAILED => 'Failed',
         ];
     }
}