<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    /** @use HasFactory<\Database\Factories\CategoryFactory> */
    use HasFactory;

    protected $table = 'categories';

    protected $fillable = [
        'name',
        'description',
        'is_active',
        'is_featured',
        'total_sub_categories',
        'image',
        'slug'
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




    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($category) {
            $category->translations()->delete();
        });
    }
}
