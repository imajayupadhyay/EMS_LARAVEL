<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeaveAssignment extends Model
{
    use HasFactory;

    protected $fillable = ['employee_id', 'leave_type_id', 'total_assigned', 'balance'];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function leaveType()
    {
        return $this->belongsTo(LeaveType::class);
    }
}
