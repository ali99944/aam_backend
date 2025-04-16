<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StorePage extends Model
{
    use HasFactory;
    protected $fillable = ['key', 'name'];

    // Relationship: A Page has many Sections
    public function sections(): HasMany
    {
        // Optionally order sections by name or a display_order column if added
        return $this->hasMany(StorePageSection::class)->orderBy('name');
    }
}