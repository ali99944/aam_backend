<?php
namespace App\Models;

use App\Observers\PaymentMethodObserver;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt; // For encrypting credentials
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log; // For logging decryption errors

class PaymentMethod extends Model
{
    use HasFactory;

    // Be cautious with mass assignment, especially for credentials.
    // Consider handling sensitive fields explicitly in the controller instead.
    // Or use mutators with encryption as shown below.
    protected $fillable = [
        'name', 'code', 'description', 'image', 'slug',
        'gateway_provider', 'supported_currencies',
        'is_default', 'is_enabled', 'is_test_mode', 'is_online',
        'credentials', // Encrypted
        'display_order', 'instructions',
        'api_key', // Encrypted
        'api_secret', // Encrypted
        'client_id', // Encrypted
        'client_secret', // Encrypted
        'merchant_id', // Encrypted
        'merchant_key', // Encrypted
        'redirect_url',
    ];

    protected $casts = [
        'is_default' => 'boolean',
        'is_enabled' => 'boolean',
        'is_test_mode' => 'boolean',
        'is_online' => 'boolean',
        // 'credentials' => 'array', // Cast only AFTER decryption if storing structured data
    ];

    // IMPORTANT: Hide sensitive fields from default JSON serialization
    protected $hidden = [
        'credentials', 'api_key', 'api_secret', 'client_id', 'client_secret', 'merchant_id', 'merchant_key',
    ];

    // --- Accessors & Mutators for Encrypted Fields ---

    // Example for Credentials (can be JSON string or simple text)
    public function setCredentialsAttribute($value) {
        $this->attributes['credentials'] = $value ? Crypt::encryptString(json_encode($value)) : null; // Assuming $value is an array/object, store as encrypted JSON
        // If it's just a simple string: $this->attributes['credentials'] = $value ? Crypt::encryptString($value) : null;
    }
    public function getCredentialsAttribute($value) {
        try {
            // Try to decrypt and decode JSON
            return $value ? json_decode(Crypt::decryptString($value), true) : null;
            // If just simple string: return $value ? Crypt::decryptString($value) : null;
        } catch (\Illuminate\Contracts\Encryption\DecryptException $e) {
            Log::error("Could not decrypt credentials for Payment Method ID {$this->id}: " . $e->getMessage());
            return null; // Or return an error indicator like ['error' => 'Decryption failed']
        }
    }

    // Repeat pattern for other sensitive fields (adjust if they aren't JSON)
    public function setApiKeyAttribute($value) { $this->attributes['api_key'] = $value ? Crypt::encryptString($value) : null; }
    public function getApiKeyAttribute($value) { try { return $value ? Crypt::decryptString($value) : null; } catch (\Exception $e) { return null; }}
    public function setApiSecretAttribute($value) { $this->attributes['api_secret'] = $value ? Crypt::encryptString($value) : null; }
    public function getApiSecretAttribute($value) { try { return $value ? Crypt::decryptString($value) : null; } catch (\Exception $e) { return null; }}
    public function setClientIdAttribute($value) { $this->attributes['client_id'] = $value ? Crypt::encryptString($value) : null; }
    public function getClientIdAttribute($value) { try { return $value ? Crypt::decryptString($value) : null; } catch (\Exception $e) { return null; }}
    public function setClientSecretAttribute($value) { $this->attributes['client_secret'] = $value ? Crypt::encryptString($value) : null; }
    public function getClientSecretAttribute($value) { try { return $value ? Crypt::decryptString($value) : null; } catch (\Exception $e) { return null; }}
    public function setMerchantIdAttribute($value) { $this->attributes['merchant_id'] = $value ? Crypt::encryptString($value) : null; }
    public function getMerchantIdAttribute($value) { try { return $value ? Crypt::decryptString($value) : null; } catch (\Exception $e) { return null; }}
    public function setMerchantKeyAttribute($value) { $this->attributes['merchant_key'] = $value ? Crypt::encryptString($value) : null; }
    public function getMerchantKeyAttribute($value) { try { return $value ? Crypt::decryptString($value) : null; } catch (\Exception $e) { return null; }}


    // --- Image Accessor ---
    public function getImageUrlAttribute(): ?string {
        if ($this->image) {
            return Storage::disk('public')->url($this->image);
        }
        return asset('images/placeholder-payment.png'); // Provide a default placeholder
    }

    // --- Observer for is_default ---
    // (Register PaymentMethodObserver in AppServiceProvider)
    protected static function boot() {
        parent::boot();
        static::observe(PaymentMethodObserver::class);
    }
}