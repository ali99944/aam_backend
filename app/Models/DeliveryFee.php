<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DeliveryFee extends Model
{
    use HasFactory;

    protected $fillable = [
        'city_id',
        'amount',
        'estimated_delivery_time',
        'is_active',
        'notes',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    // Relationship: A DeliveryFee belongs to a City
    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class); // Adjust App\Models\City if needed
    }

    // Accessor for formatted amount
    public function getFormattedAmountAttribute(): string
    {
         // Replace 'AED' with your currency helper/config
        return 'AED ' . number_format($this->amount, 2);
    }
}