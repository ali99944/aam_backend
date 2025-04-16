<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Language extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'name_native',
        'direction',
        'locale',
        'flag_svg',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // Constants for direction
    const DIRECTION_LTR = 'ltr';
    const DIRECTION_RTL = 'rtl';

    public static function directions(): array
    {
        return [
            self::DIRECTION_LTR => 'LTR (Left-to-Right)',
            self::DIRECTION_RTL => 'RTL (Right-to-Left)',
        ];
    }

    // Accessor for Flag SVG URL
    public function getFlagSvgUrlAttribute(): ?string
    {
        return $this->flag_svg ? Storage::disk('public')->url($this->flag_svg) : null;
    }
}
