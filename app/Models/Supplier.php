<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Supplier extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'image',
        'contact_person',
        'email',
        'phone',
        'address',
        'website',
        // 'balance' should NOT be fillable - managed via transactions/updates
        'is_active',
        'notes',
    ];

    protected $casts = [
        'balance' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    // Accessor for Image URL
    public function getImageUrlAttribute(): ?string
    {
        if ($this->image) {
            return Storage::disk('public')->url($this->image);
        }
        // Optional: Default placeholder
        return asset('images/placeholder-supplier.png'); // Create this placeholder
    }

     // Accessor for formatted balance
     public function getFormattedBalanceAttribute(): string
     {
         $amount = number_format(abs($this->balance), 2);
         $currency = 'AED'; // Replace with config/helper
         if ($this->balance == 0) {
             return "{$currency} 0.00";
         } elseif ($this->balance > 0) {
             return "{$currency} {$amount} (Owed)"; // We owe them
         } else {
              return "{$currency} {$amount} (Credit)"; // They owe us / Prepayment
         }
     }

    // --- Relationships (Future) ---
    // public function purchaseOrders(): HasMany { return $this->hasMany(PurchaseOrder::class); }
    // public function products(): BelongsToMany { return $this->belongsToMany(Product::class, 'product_supplier'); } // If tracking which products come from which supplier
    // public function paymentsMade(): HasMany { return $this->hasMany(SupplierPayment::class); } // For tracking payments TO suppliers
}