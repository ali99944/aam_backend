<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Policy extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'name',
        'content',
    ];

    // No specific casts needed unless content needs special handling (e.g., DOMPurifier on save)
}