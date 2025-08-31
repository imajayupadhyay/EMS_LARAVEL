<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MarketerPunch extends Model
{
    use HasFactory;

    protected $fillable = [
        'marketer_id',
        'punched_in_at',
        'punched_out_at',
        'status',
    ];

    // ✅ This ensures punched_in_at/out are Carbon dates
    protected $casts = [
        'punched_in_at'  => 'datetime',
        'punched_out_at' => 'datetime',
        'created_at'     => 'datetime',
        'updated_at'     => 'datetime',
    ];
}
