<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany; // For Cities later maybe
use Illuminate\Support\Facades\Storage;

class Country extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'iso2',
        'iso3',
        'phone_code',
        'capital',
        'currency_id', // Foreign key
        'timezone_id', // Foreign key
        'region',
        'subregion',
        'flag_image',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // --- Relationships ---

    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class)->withDefault(); // Provide default if null
    }

    public function timezone(): BelongsTo
    {
        return $this->belongsTo(Timezone::class)->withDefault(); // Provide default if null
    }

     public function cities(): HasMany // If you have a City model linked to Country
     {
         return $this->hasMany(City::class); // Adjust App\Models\City if needed
     }

    // --- Accessors ---

    public function getFlagImageUrlAttribute(): ?string
    {
        if ($this->flag_image) {
            return Storage::disk('public')->url($this->flag_image);
        }
        // Optional: Return a default placeholder or maybe generate from iso2?
        // return "https://flagcdn.com/w40/{$this->iso2}.png"; // Example using external service
        return asset('images/placeholder-flag.png'); // Local placeholder
    }
}