<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FaqCategory extends Model
{
    use HasFactory;
    protected $table = 'faq_categories'; // Explicit table name
    protected $fillable = ['name', 'description', 'display_order', 'is_active', 'key'];
    protected $casts = ['is_active' => 'boolean'];

    public function faqs(): HasMany {
        return $this->hasMany(Faq::class, 'faq_category_id');
    }
}