<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContactMessage extends Model
{
    use HasFactory;
    // Typically not mass assignable from admin panel
    protected $guarded = ['id']; // Guard ID, allow others if needed

    protected $casts = [
        'is_read' => 'boolean',
    ];

    // Scope for unread messages
    public function scopeUnread($query) {
        return $query->where('is_read', false);
    }
}