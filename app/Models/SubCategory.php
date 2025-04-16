<?php

namespace App\Models;

use App\Traits\Translatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class SubCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'name',
        'description',
        'cover_image',
        'icon_image', // Optional
        'is_active',
        'total_products',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // Relationship: A SubCategory belongs to a Category
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    // Accessor for Cover Image URL
    public function getCoverImageUrlAttribute(): ?string
    {
        return $this->cover_image ? Storage::disk('public')->url($this->cover_image) : null;
    }

    // Accessor for Icon Image URL (Optional)
    public function getIconImageUrlAttribute(): ?string
    {
        return $this->icon_image ? Storage::disk('public')->url($this->icon_image) : null;
    }
}
