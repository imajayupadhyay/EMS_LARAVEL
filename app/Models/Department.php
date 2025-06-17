<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Department extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    /**
     * Get all designations under this department.
     */
    public function designations()
    {
        return $this->hasMany(Designation::class);
    }

    /**
     * Get all employees under this department.
     */
    public function employees()
    {
        return $this->hasMany(Employee::class);
    }
}
