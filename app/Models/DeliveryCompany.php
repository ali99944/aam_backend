<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laratrust\Contracts\LaratrustUser;
use Laratrust\Traits\HasRolesAndPermissions;
use Laravel\Sanctum\HasApiTokens;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class DeliveryCompany extends Authenticatable implements LaratrustUser
{
    use HasFactory, Notifiable;
    use HasRolesAndPermissions;
    use HasApiTokens;

    protected $fillable = [
        'name',
        'description',
        'logo',
        'contact_phone',
        'contact_email',
        'address',
        'tracking_url_pattern',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // Accessor for Logo URL
    public function getLogoUrlAttribute(): ?string
    {
        if ($this->logo) {
            return Storage::disk('public')->url($this->logo);
        }
        // Optional: Return a default placeholder
        return asset('images/placeholder-logo.png'); // Create this placeholder
    }

    // --- Relationships (Future) ---
    // public function orders(): HasMany { return $this->hasMany(Order::class); }
    // public function deliveryPersonnel(): HasMany { return $this->hasMany(DeliveryPerson::class); }

}