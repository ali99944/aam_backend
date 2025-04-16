<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Carbon\Carbon;

class Offer extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'description',
        'image',
        'type',
        'linked_id',
        'target_url',
        'start_date',
        'end_date',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
        'linked_id' => 'integer',
    ];

    // --- Constants for Types ---
    const TYPE_GENERIC = 'generic';
    const TYPE_CATEGORY = 'category';
    const TYPE_PRODUCT = 'product';
    const TYPE_BRAND = 'brand';

    // --- Automatically Generate Slug ---
    protected static function boot()
    {
        parent::boot();
        static::creating(function ($offer) {
            if (empty($offer->slug)) {
                $offer->slug = Str::slug($offer->title);
                // Ensure slug is unique
                $count = static::where('slug', $offer->slug)->count();
                if ($count > 0) {
                    $offer->slug .= '-' . ($count + 1); // Append number if duplicate
                }
            }
        });
         static::updating(function ($offer) {
             if ($offer->isDirty('title') && empty($offer->slug)) { // Re-generate slug if title changes and slug is empty/reset
                 $offer->slug = Str::slug($offer->title);
                 // Ensure slug is unique (excluding self)
                 $count = static::where('slug', $offer->slug)->where('id', '!=', $offer->id)->count();
                 if ($count > 0) {
                     $originalSlug = Str::slug($offer->getOriginal('title'));
                     // Check if original title's slug is different before appending count
                      if (static::where('slug', $originalSlug)->where('id', '!=', $offer->id)->doesntExist()) {
                          $offer->slug = $originalSlug; // Try original first if available
                      } else {
                           $offer->slug .= '-' . ($count + 1);
                      }
                 }
             }
         });
    }

    // --- Accessor for Image URL ---
    public function getImageUrlAttribute(): ?string
    {
        return $this->image ? Storage::disk('public')->url($this->image) : null;
        // Optional: Placeholder return asset('images/placeholder-offer.png');
    }

    // --- Polymorphic Relationship (Optional but clean) ---
    // If you want to easily fetch the related Category/Product/Brand
    // public function linkable()
    // {
    //     // This requires the linked models (Category, Product, Brand) to also have a morphMany relationship back if needed
    //     // The `type` column would need to store the fully namespaced model class name (e.g., App\Models\Category) instead of 'category' string
    //     // return $this->morphTo(__FUNCTION__, 'type', 'linked_id');
    // }

    // --- Getters for related models (Simpler approach without morph) ---
    public function getLinkedCategoryAttribute() {
        return ($this->type === self::TYPE_CATEGORY && $this->linked_id) ? Category::find($this->linked_id) : null;
    }
    public function getLinkedProductAttribute() {
        return ($this->type === self::TYPE_PRODUCT && $this->linked_id) ? Product::find($this->linked_id) : null;
    }
    public function getLinkedBrandAttribute() {
         return ($this->type === self::TYPE_BRAND && $this->linked_id) ? Brand::find($this->linked_id) : null;
    }


    // --- Scope for Active Offers ---
    public function scopeActive($query)
    {
        $now = Carbon::now();
        return $query->where('is_active', true)
                     ->where(function ($q) use ($now) {
                         $q->whereNull('start_date')->orWhere('start_date', '<=', $now);
                     })
                     ->where(function ($q) use ($now) {
                         $q->whereNull('end_date')->orWhere('end_date', '>=', $now);
                     });
    }

     // --- Static Helper ---
    public static function types(): array
    {
        return [
            self::TYPE_GENERIC => 'Generic / Custom URL',
            self::TYPE_CATEGORY => 'Link to Category',
            self::TYPE_PRODUCT => 'Link to Product',
            self::TYPE_BRAND => 'Link to Brand',
        ];
    }

}