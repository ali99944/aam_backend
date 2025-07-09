<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Otp extends Model
{
    use HasFactory;

    protected $fillable = [
        'identifier',
        'code',
        'expires_at',
        'verified_at',
        'purpose',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'verified_at' => 'datetime',
    ];

    /**
     * Check if the OTP is still valid (not expired and not verified).
     */
    public function isValid(): bool
    {
        return $this->verified_at && $this->expires_at && $this->expires_at->isFuture();
    }
}