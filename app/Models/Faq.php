<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Faq extends Model
{
    use HasFactory;
    protected $fillable = [
        'faq_category_id',
        'question',
        'answer',
        'display_order',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function category(): BelongsTo {
        return $this->belongsTo(FaqCategory::class, 'faq_category_id')
                    ->withDefault(['name' => 'Uncategorized']); // Default if category is null/deleted
    }
}