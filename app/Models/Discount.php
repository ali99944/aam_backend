<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon; // For date handling

class Discount extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'type',
        'value',
        'status',
        'expiration_type',
        'duration_days',
        'start_date',
        'end_date',
        'description',
    ];

    protected $casts = [
        'value' => 'decimal:2',
        'is_active' => 'boolean', // We might use an accessor instead
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'duration_days' => 'integer',
    ];

    // --- Constants for ENUM values ---
    const TYPE_FIXED = 'fixed';
    const TYPE_PERCENTAGE = 'percentage';

    const STATUS_ACTIVE = 'active';
    const STATUS_INACTIVE = 'inactive';
    const STATUS_EXPIRED = 'expired';

    const EXPIRATION_NONE = 'none';
    const EXPIRATION_DURATION = 'duration';
    const EXPIRATION_PERIOD = 'period';


    // --- Accessors & Mutators ---

    /**
     * Get the formatted discount value (e.g., "15%" or "AED 10.00").
     * Assumes you have a currency helper or config.
     */
    public function getFormattedValueAttribute(): string
    {
        if ($this->type === self::TYPE_PERCENTAGE) {
            return rtrim(rtrim(number_format($this->value, 2), '0'), '.') . '%';
        } else {
            // Replace 'AED' with your currency symbol/code from config or helper
            return 'AED ' . number_format($this->value, 2);
        }
    }

    /**
     * Get the formatted expiration details.
     */
    public function getExpirationDetailsAttribute(): string
    {
        switch ($this->expiration_type) {
            case self::EXPIRATION_DURATION:
                return $this->duration_days ? "{$this->duration_days} days after activation" : 'Duration not set';
            case self::EXPIRATION_PERIOD:
                $start = $this->start_date ? $this->start_date->format('d M Y') : 'N/A';
                $end = $this->end_date ? $this->end_date->format('d M Y') : 'N/A';
                return "Period: {$start} - {$end}";
            case self::EXPIRATION_NONE:
            default:
                return 'No Expiration';
        }
    }

    /**
     * Check if the discount is currently valid (active and not expired).
     * Note: 'duration' type validity depends on when it was 'activated' (which isn't tracked here yet).
     * This accessor focuses on the 'period' type and general status.
     */
    public function getIsValidAttribute(): bool
    {
        if ($this->status !== self::STATUS_ACTIVE) {
            return false;
        }

        if ($this->expiration_type === self::EXPIRATION_PERIOD) {
            $now = Carbon::now();
            if ($this->start_date && $this->start_date->gt($now)) {
                return false; // Not started yet
            }
            if ($this->end_date && $this->end_date->lt($now)) {
                // Optionally: Automatically set status to 'expired' here or via a scheduled job
                // if ($this->status === self::STATUS_ACTIVE) {
                //     $this->update(['status' => self::STATUS_EXPIRED]);
                // }
                return false; // Expired
            }
        }
        // Add logic for 'duration' if activation timestamp is added later

        return true; // Active and within period (or no period defined)
    }

    // --- Scopes ---

    /**
     * Scope a query to only include currently valid discounts.
     */
    public function scopeValid($query)
    {
        $now = Carbon::now();
        return $query->where('status', self::STATUS_ACTIVE)
                     ->where(function($q) use ($now) {
                         $q->where('expiration_type', self::EXPIRATION_NONE)
                           ->orWhere(function($q2) use ($now) { // Valid period
                               $q2->where('expiration_type', self::EXPIRATION_PERIOD)
                                  ->where(function($q3) use ($now){
                                      $q3->whereNull('start_date')->orWhere('start_date', '<=', $now);
                                  })
                                  ->where(function($q4) use ($now){
                                     $q4->whereNull('end_date')->orWhere('end_date', '>=', $now);
                                  });
                           });
                         // Add logic for 'duration' type if needed
                     });
    }

    // --- Relationships (Example) ---
    // public function products() {
    //     return $this->belongsToMany(Product::class, 'product_discount'); // If using pivot table
    // }
}