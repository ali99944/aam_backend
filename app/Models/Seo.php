<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Seo extends Model
{
    use HasFactory;

    // Define ENUM constants
    const TYPE_PAGE = 'page';
    const TYPE_RECORD = 'record'; // Keep for potential future use

    protected $fillable = [
        'name',
        'key',
        'type',
        'title',
        'description',
        'keywords',
        'robots_meta',
        'canonical_url',
        'og_title',
        'og_description',
        'og_image',
        'og_image_alt',
        'og_locale',
        'og_site_name',
        'twitter_title',
        'twitter_description',
        'twitter_image',
        'twitter_alt',
        'custom_meta_tags',
    ];

    // No casts needed unless you store JSON in custom_meta_tags

    /**
     * Accessor for Open Graph Image URL.
     */
    public function getOgImageUrlAttribute(): ?string
    {
        // Assuming og_image stores the path relative to storage/app/public/seo/og
        return $this->og_image ? Storage::disk('public')->url($this->og_image) : null;
    }

     /**
     * Accessor for Twitter Image URL.
      * Note: Reusing og_image path structure, adjust if needed
     */
    public function getTwitterImageUrlAttribute(): ?string
    {
        return $this->twitter_image ? Storage::disk('public')->url($this->twitter_image) : null;
    }
}