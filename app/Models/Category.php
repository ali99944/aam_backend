<?php

namespace App\Models;

use App\Traits\Translatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Category extends Model
{
    /** @use HasFactory<\Database\Factories\CategoryFactory> */
    use HasFactory;
    use Translatable;

    protected $table = 'categories';
    protected $translatable = ['name', 'description'];

    protected $fillable = [
        'name',
        'description',
        'is_active',
        'is_featured',
        'total_sub_categories',
        'cover_image',
        'icon_image',
    ];

    /**
     * Get all of the subCategories for the Category
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function subCategories()
    {
        return $this->hasMany(SubCategory::class, 'category_id');
    }

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_active' => 'boolean', // Important for handling 'true'/'false' or 1/0
    ];



    // Accessor for Cover Image URL
    public function getCoverImageUrlAttribute(): ?string
    {
        if ($this->cover_image) {
            // Ensure you have run `php artisan storage:link`
            return Storage::disk('public')->url($this->cover_image);
        }
        // Optional: Return a default placeholder URL
        // return asset('images/placeholder-cover.jpg');
        return null;
    }

    // Accessor for Icon Image URL
    public function getIconImageUrlAttribute(): ?string
    {
        if ($this->icon_image) {
            return Storage::disk('public')->url($this->icon_image);
        }
        // Optional: Return a default placeholder URL
        // return asset('images/placeholder-icon.svg');
        return null;
    }


    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($category) {
            // Delete related translations when a category is deleted
            $category->translations()->delete();
            // Also delete images (if not handled elsewhere or by observers)
            if ($category->cover_image) {
                Storage::disk('public')->delete($category->cover_image);
            }
            if ($category->icon_image) {
                 Storage::disk('public')->delete($category->icon_image);
            }
        });
    }
}
