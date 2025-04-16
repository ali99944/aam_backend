<?php // app/Models/Brand.php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Brand extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'image', 'total_products'];

    public function getImageUrlAttribute(): ?string {
        return $this->image ? Storage::disk('public')->url($this->image) : null;
        // Optional: return asset('images/placeholder-brand.png');
    }
    // Optional: Relationship to Products (if a Product belongs to a Brand)
    // public function products(): HasMany { return $this->hasMany(Product::class); }
}