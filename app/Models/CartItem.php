<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CartItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'guest_cart_token',
        'product_id',
        'quantity',
        // 'price_at_add',
        // 'addons_data',
    ];

    protected $casts = [
        'quantity' => 'integer',
        // 'price_at_add' => 'decimal:2',
        // 'addons_data' => 'array',
    ];

    public function customer(): BelongsTo {
        return $this->belongsTo(Customer::class);
    }

    public function product(): BelongsTo {
        return $this->belongsTo(Product::class);
    }

    // Scope to find items by customer OR guest token
    public function scopeForUserOrGuest($query, ?Customer $customer, ?string $guestToken)
    {
        if ($customer) {
            return $query->where('customer_id', $customer->id)->whereNull('guest_cart_token');
        } elseif ($guestToken) {
            return $query->where('guest_cart_token', $guestToken)->whereNull('customer_id');
        }
        // If neither is provided, return an empty query to prevent accidental data exposure
        return $query->whereRaw('1 = 0'); // Or throw an exception
    }
}