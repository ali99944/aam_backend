<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'main_image',
        'cost_price',
        'sell_price',
        'stock',
        'lower_stock_warn',
        'sku_code',
        'sub_category_id',
        'is_featured',
        'brand_id',
        'discount_id',
        'status',
        'is_public',
        'overall_rating'
        // Non-fillable by mass assignment: total_views, favorites_views, favorites_count, overall_rating (managed internally)
    ];

    protected $casts = [
        'cost_price' => 'decimal:2',
        'sell_price' => 'decimal:2',
        'is_public' => 'boolean',
        'overall_rating' => 'decimal:2',
        'is_featured' => 'boolean', // Added
    ];

    // --- Relationships ---

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function subCategory(): BelongsTo
    {
        return $this->belongsTo(SubCategory::class);
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    public function discount(): BelongsTo
    {
        // Use withDefault to avoid errors if discount is null
        return $this->belongsTo(Discount::class)->withDefault();
    }

    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class);
    }

    public function specs(): HasMany
    {
        return $this->hasMany(ProductSpec::class);
    }

    public function addons(): HasMany
    {
        return $this->hasMany(ProductAddon::class);
    }

    // --- Accessors ---

    public function getMainImageUrlAttribute(): ?string
    {
        return $this->main_image ? Storage::disk('public')->url($this->main_image) : null;
         // Optional: return asset('images/placeholder-product.png');
    }

    // You might add accessors for formatted prices, status labels etc.
    public function getFormattedSellPriceAttribute(): string
    {
         // Replace 'AED' with your currency helper/config
        return 'AED ' . number_format($this->sell_price, 2);
    }

     // --- Static Data ---
    const STATUS_ACTIVE = 'active';
    const STATUS_OUT_OF_STOCK = 'out-of-stock';

    public static function statuses(): array
    {
        return [
            self::STATUS_ACTIVE => ucfirst(self::STATUS_ACTIVE),
            self::STATUS_OUT_OF_STOCK => 'Out of Stock',
        ];
    }
}