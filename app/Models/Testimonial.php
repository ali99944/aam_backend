<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Testimonial extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'title_or_company',
        'quote',
        'avatar',
        'rating',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'rating' => 'integer',
    ];

    /**
     * Get the full public URL for the avatar image.
     */
    public function getAvatarUrlAttribute(): ?string
    {
        if ($this->avatar) {
            return Storage::disk('public')->url($this->avatar);
        }
        // Return a default placeholder if you have one
        return asset('images/default-avatar.png');
    }

    /**
     * Scope a query to only include active testimonials.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}