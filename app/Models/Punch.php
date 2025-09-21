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
        'is_auto_punchout',
        'late_warning_shown',              // NEW: Track if late warning was shown
        'overtime_appreciation_shown',     // NEW: Track if overtime appreciation was shown
    ];

    protected $casts = [
        'punched_in_at' => 'datetime',
        'punched_out_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'is_auto_punchout' => 'boolean',
        'late_warning_shown' => 'boolean',              // NEW: Cast to boolean
        'overtime_appreciation_shown' => 'boolean',     // NEW: Cast to boolean
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
    
    /**
     * Scope for open punches (not punched out yet)
     */
    public function scopeOpen($query)
    {
        return $query->whereNull('punched_out_at');
    }
    
    /**
     * Scope for auto punch-outs
     */
    public function scopeAutoPunchedOut($query)
    {
        return $query->where('is_auto_punchout', true);
    }
}