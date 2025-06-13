<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Punch extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'punched_in_at',
        'punched_out_at',
        'location',
    ];

    protected $dates = [
        'punched_in_at',
        'punched_out_at',
        'created_at',
        'updated_at',
    ];

    // Accessor to get type (in/out) dynamically
    public function getTypeAttribute()
    {
        return is_null($this->punched_out_at) ? 'in' : 'out';
    }

    // Relation to user
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Optional: Scope to get today's punches
    public function scopeToday($query)
    {
        return $query->whereDate('created_at', Carbon::today());
    }
}
