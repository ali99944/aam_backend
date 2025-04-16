<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class State extends Model
{
    use HasFactory;

    protected $fillable = [
        'country_id',
        'name',
        'state_code',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // Relationship: State belongs to a Country
    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    // Relationship: State has many Cities
    public function cities(): HasMany
    {
        // Assuming City model exists and has 'state_id'
        return $this->hasMany(City::class);
    }
}