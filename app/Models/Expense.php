<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class Expense extends Model
{
    use HasFactory;
    protected $fillable = [
        'expense_category_id',
        'amount',
        'entry_date',
        'description',
        'receipt_image',
        'user_id'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'entry_date' => 'date',
    ];

    public function category(): BelongsTo { // Renamed for clarity
        return $this->belongsTo(ExpenseCategory::class, 'expense_category_id');
    }

    public function getReceiptImageUrlAttribute(): ?string {
        return $this->receipt_image ? Storage::disk('public')->url($this->receipt_image) : null;
    }

    public function getFormattedAmountAttribute(): string {
         // Replace 'AED' with your currency helper/config
        return 'AED ' . number_format($this->amount, 2);
    }
}