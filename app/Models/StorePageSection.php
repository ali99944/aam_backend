<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StorePageSection extends Model
{
    use HasFactory;
    protected $fillable = ['store_page_id', 'name', 'key', 'content'];

    // IMPORTANT: Cast the JSON column to an array/object
    protected $casts = [
        'content' => 'array', // Use 'array' (or 'object') for automatic JSON handling
    ];

    // Relationship: A Section belongs to a Page
    public function storePage(): BelongsTo
    {
        return $this->belongsTo(StorePage::class);
    }

    // Add accessors for specific content fields if frequently needed, e.g.:
    // public function getTitleAttribute() { return $this->content['title'] ?? null; }
}