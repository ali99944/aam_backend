<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Currency extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'code', 'symbol', 'exchange_rate', 'is_active'];
    protected $casts = ['exchange_rate' => 'decimal:6', 'is_active' => 'boolean'];

    public function countries() {
        return $this->hasMany(Country::class);
    }
}
