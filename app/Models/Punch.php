<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
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

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function scopeToday($query)
    {
        return $query->whereDate('created_at', Carbon::today());
    }
}
