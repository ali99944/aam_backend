<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Relations\Pivot;

class CustomerFavorite extends Pivot
{
    public $incrementing = true;
    protected $table = 'customer_favorites';
}