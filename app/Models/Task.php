<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'task_date',
        'task_content',
    ];

    /**
     * Relationship: Task belongs to a user.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
