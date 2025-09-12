<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class Punch extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'punched_in_at',
        'punched_out_at',
        'location',
    ];

    protected $casts = [
        'punched_in_at' => 'datetime',
        'punched_out_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function getTypeAttribute()
    {
        return is_null($this->punched_out_at) ? 'in' : 'out';
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    /**
     * Scope for punches of today (based on punched_in_at)
     */
    public function scopeToday($query)
    {
        return $query->whereDate('punched_in_at', Carbon::today()->toDateString());
    }
}
