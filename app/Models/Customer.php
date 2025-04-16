<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Customer extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable; // Added HasApiTokens

    protected $guard = 'customer';

    // --- Status Constants ---
    public const STATUS_ACTIVE = 'active';
    public const STATUS_BANNED = 'banned';
    public const STATUS_VERIFICATION_REQUIRED = 'verification-required';


    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'status', // Added
        'is_banned', // Added
        'banned_at', // Added
        'ban_reason', // Added
        // Add other fields like phone, address if managed here
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        // 'password' => 'hashed',
        'is_banned' => 'boolean', // Added
        'banned_at' => 'datetime', // Added
    ];

    // --- Accessor for Email Verification Status ---
    public function getIsEmailVerifiedAttribute(): bool
    {
        return !is_null($this->email_verified_at);
    }


     // --- Static Helper ---
    public static function statuses(): array
    {
        return [
            self::STATUS_ACTIVE => 'Active',
            self::STATUS_BANNED => 'Banned',
            self::STATUS_VERIFICATION_REQUIRED => 'Verification Required',
        ];
    }

    public function favorites()
    {
        return $this->belongsToMany(Product::class, 'customer_favorites', 'customer_id', 'product_id')
                    ->withTimestamps(); // Track when favorited
    }

    /**
     * Check if customer has favorited a specific product.
     *
     * @param int $productId
     * @return bool
     */
    public function hasFavorited(int $productId): bool
    {
        // Check if the relationship exists for the given product ID
        // Load the relationship if not already loaded to check efficiently
         if (!$this->relationLoaded('favorites')) {
            $this->load('favorites:id'); // Load only favorite IDs for efficiency
        }
         return $this->favorites->contains($productId);
    }

    public function visitedProducts()
    {
        return $this->belongsToMany(Product::class, 'product_visits', 'customer_id', 'product_id')
                    ->withTimestamps(); // Track when visited
    }
}
