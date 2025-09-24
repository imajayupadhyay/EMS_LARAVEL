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

    /**
     * Get all KRAs that belong to this department.
     */
    public function kras()
    {
        return $this->hasMany(Kra::class);
    }

      /**
     * Get only active KRAs for this department.
     */
    public function activeKras()
    {
        return $this->hasMany(Kra::class)->where('is_active', true);
    }

     /**
     * Get the count of active KRAs in this department.
     */
    public function getActiveKrasCountAttribute()
    {
        return $this->activeKras()->count();
    }
    /**
     * Get KRAs by priority for this department.
     * 
     * @param string $priority ('high', 'medium', 'low')
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function krasByPriority($priority)
    {
        return $this->hasMany(Kra::class)
                    ->where('priority', $priority)
                    ->where('is_active', true);
    }
     /**
     * Get high priority active KRAs for this department.
     */
    public function highPriorityKras()
    {
        return $this->krasByPriority('high');
    }
    /**
     * Get medium priority active KRAs for this department.
     */
    public function mediumPriorityKras()
    {
        return $this->krasByPriority('medium');
    }

    /**
     * Get low priority active KRAs for this department.
     */
    public function lowPriorityKras()
    {
        return $this->krasByPriority('low');
    }

}
