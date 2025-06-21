<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeaveApplication extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'leave_type_id',
        'start_date',
        'end_date',
        'reason',
        'status',
        'day_type'
    ];

    /**
     * Relationship: LeaveApplication belongs to an employee.
     */
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    /**
     * Relationship: LeaveApplication belongs to a leave type.
     */
    public function leaveType()
    {
        return $this->belongsTo(LeaveType::class);
    }
}
