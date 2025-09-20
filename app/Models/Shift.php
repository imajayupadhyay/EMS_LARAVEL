<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
class Shift extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'time_from',
        'time_to',
    ];

    protected $casts = [
        'time_from' => 'datetime:H:i',
        'time_to' => 'datetime:H:i',
    ];

     /**
     * Get the employees assigned to this shift.
     */
    public function employees(): HasMany
    {
        return $this->hasMany(Employee::class);
    }

    /**
     * Get the count of employees in this shift.
     */
    public function getEmployeeCountAttribute(): int
    {
        return $this->employees()->count();
    }
}