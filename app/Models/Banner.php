<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Banner extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'image',
        'button_text',
        'button_url',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get the full public URL for the banner image.
     */
    public function getImageUrlAttribute(): ?string
    {
        if ($this->image) {
            return Storage::disk('public')->url($this->image);
        }
        // Return a default placeholder if you have one
        return asset('images/placeholder-banner.jpg');
    }

    /**
     * Scope a query to only include active banners.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}