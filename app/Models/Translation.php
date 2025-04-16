<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Translation extends Model
{
    protected $fillable = ['locale', 'field', 'value'];

    public function translatable()
    {
        return $this->morphTo();
    }
}