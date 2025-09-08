<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Salary extends Model
{
    protected $fillable = [
        'employee_id',
        'payroll_period_id',
        'gross_salary',
        'total_earnings',
        'total_deductions',
        'net_salary',
        'status',
        'meta',
        'created_by',
        'finalized_by',
        'finalized_at',
        'paid_at'
    ];

    protected $casts = [
        'meta' => 'array',
        'finalized_at' => 'datetime',
        'paid_at' => 'datetime'
    ];

    public function items(): HasMany
    {
        return $this->hasMany(SalaryItem::class);
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function payrollPeriod(): BelongsTo
    {
        return $this->belongsTo(PayrollPeriod::class);
    }
}
