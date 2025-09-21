<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Hash;

class Employee extends Authenticatable
{
    use Notifiable;

    protected $fillable = [
        'first_name',
        'middle_name',
        'last_name',
        'email',
        'password',
        'contact',
        'emergency_contact',
        'gender',
        'dob',
        'doj',
        'marital_status',
        'address',
        'zip',
        'pay_scale',
        'work_location',
        'department_id',
        'designation_id',
        'shift_id', 
        // payroll fields
        'monthly_salary',
        'salary_currency',
        'salary_type',
        // NEW: Warning counter fields
        'late_warning_count',
        'overtime_appreciation_count',
    ];

    /**
     * Hide sensitive fields when model is serialized
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'dob' => 'date',
        'doj' => 'date',
        'monthly_salary' => 'decimal:2',
        'late_warning_count' => 'integer',              // NEW: Cast to integer
        'overtime_appreciation_count' => 'integer',     // NEW: Cast to integer
    ];

    /**
     * Department relationship (each employee belongs to one department).
     */
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    /**
     * Designation relationship (each employee belongs to one designation).
     */
    public function designation(): BelongsTo
    {
        return $this->belongsTo(Designation::class);
    }

    public function shift()
    {
        return $this->belongsTo(Shift::class);
    }
    
    /**
     * Defensive mutator: hash password only when required.
     * If you already Hash::make() in controller, this will not double-hash
     * because Hash::needsRehash() returns false for already hashed values.
     */
    public function setPasswordAttribute($value)
    {
        if ($value === null) return;

        if (Hash::needsRehash($value)) {
            $this->attributes['password'] = Hash::make($value);
        } else {
            $this->attributes['password'] = $value;
        }
    }
}