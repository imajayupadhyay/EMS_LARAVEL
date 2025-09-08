<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Employee extends Model
{
    // Add or merge with your existing $fillable
    protected $fillable = [
        'first_name',
        'middle_name',
        'last_name',
        'email',
        'password',               // <-- allow password mass assignment
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
        // payroll fields
        'monthly_salary',
        'salary_currency',
        'salary_type',
    ];

    /**
     * Hide sensitive fields when model is serialized
     */
    protected $hidden = [
        'password',
    ];

    protected $casts = [
        'dob' => 'date',
        'doj' => 'date',
        'monthly_salary' => 'decimal:2',
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

    // add other relationships (punches, leaves etc.) as needed
}
