<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MarketerLocation extends Model
{
    use HasFactory;

    protected $fillable = [
        'marketer_id',
        'latitude',
        'longitude',
        'recorded_at',
    ];

    public $timestamps = false;

    public function marketer()
    {
        return $this->belongsTo(Marketer::class, 'marketer_id');
    }
}
