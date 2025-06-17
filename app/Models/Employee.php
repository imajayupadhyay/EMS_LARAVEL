<?php
namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;  // change from Model to Authenticatable
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Hash;

class Employee extends Authenticatable
{
    use HasFactory;

    protected $fillable = [
        'first_name',
        'middle_name',
        'last_name',
        'email',
        'password',
        'gender',
        'dob',
        'doj',
        'marital_status',
        'contact',
        'emergency_contact',
        'address',
        'zip',
        'pay_scale',
        'work_location',
        'department_id',
        'designation_id',
    ];

    public function setPasswordAttribute($value)
    {
        if ($value) {
            $this->attributes['password'] = Hash::make($value);
        }
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function designation()
    {
        return $this->belongsTo(Designation::class);
    }
}


