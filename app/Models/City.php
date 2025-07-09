<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    use HasFactory;
    protected $table = 'cities';

    protected $fillable = [
        'state_id',
        'country_id',
        'name',
        'is_active',

    ];


    protected $casts = [
        'is_active' => 'boolean',
        'latitude' => 'decimal:7', // Specify precision for casting
        'longitude' => 'decimal:7', // Specify precision for casting
    ];


    public function deliveryFee()
    {
        return $this->hasOne(DeliveryFee::class);
    }

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function state()
    {
        return $this->belongsTo(State::class)->withDefault(['name' => 'N/A']); // Default if state_id is null
    }
}
