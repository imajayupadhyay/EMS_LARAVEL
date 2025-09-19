<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
}