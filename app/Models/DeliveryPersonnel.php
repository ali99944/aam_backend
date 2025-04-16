<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
// Use Illuminate\Foundation\Auth\User as Authenticatable if they need to log in
use Illuminate\Database\Eloquent\Model; // Or extend Authenticatable
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Notifications\Notifiable; // If they need notifications

// class DeliveryPersonnel extends Authenticatable // Use if login needed
class DeliveryPersonnel extends Model
{
    use HasFactory, Notifiable; // Add Notifiable if needed

    protected $table = 'delivery_personnel'; // Explicitly state table name

    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'delivery_company_id',
        'avatar',
        'vehicle_type',
        'vehicle_plate_number',
        'national_id_or_iqama',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_active' => 'boolean',
        'password' => 'hashed', // Automatically hash using mutator below
    ];

    // Relationship: Belongs to a Delivery Company (or null)
    public function deliveryCompany(): BelongsTo
    {
        return $this->belongsTo(DeliveryCompany::class)->withDefault(['name' => 'Independent']); // Provide default for null
    }

    // Accessor for Avatar URL
    public function getAvatarUrlAttribute(): ?string
    {
        if ($this->avatar) {
            return Storage::disk('public')->url($this->avatar);
        }
        // Default generic avatar
        return asset('images/default-avatar.png'); // Use same default as navbar?
    }

    // Mutator to hash password automatically
    // public function setPasswordAttribute($value) // uncomment/use if extending Authenticatable and need hashing
    // {
    //     if ($value) { // Only hash if a value is provided
    //         $this->attributes['password'] = Hash::make($value);
    //     }
    // }
}