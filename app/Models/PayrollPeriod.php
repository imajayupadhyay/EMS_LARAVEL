<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PayrollPeriod extends Model
{
    protected $fillable = ['year','month','start_date','end_date','status','meta','created_by','finalized_by','finalized_at','processed_at'];

    protected $casts = [
        'meta' => 'array',
        'finalized_at' => 'datetime',
        'processed_at' => 'datetime'
    ];

    public function getLabelAttribute()
    {
        return $this->month . '/' . $this->year;
    }
}
