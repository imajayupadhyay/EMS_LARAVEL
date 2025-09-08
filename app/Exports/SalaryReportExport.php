<?php

namespace App\Exports;

use App\Models\Salary;
use App\Models\PayrollPeriod;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class SalaryReportExport implements FromCollection, WithHeadings
{
    protected $month;
    protected $year;

    public function __construct(int $month, int $year)
    {
        $this->month = $month;
        $this->year = $year;
    }

    public function collection()
    {
        $period = PayrollPeriod::where('year', $this->year)->where('month', $this->month)->first();
        if (! $period) {
            return collect();
        }

        return Salary::with('employee')->where('payroll_period_id', $period->id)->get()->map(function ($s) {
            // friendly flattened row
            return [
                'employee_id' => $s->employee_id,
                'employee_name' => optional($s->employee)->first_name . ' ' . optional($s->employee)->last_name,
                'gross_salary' => $s->gross_salary,
                'total_earnings' => $s->total_earnings,
                'total_deductions' => $s->total_deductions,
                'net_salary' => $s->net_salary,
                'status' => $s->status,
                'present_days' => $s->meta['present_days'] ?? null,
                'approved_leaves' => $s->meta['approved_leaves'] ?? null,
                'paid_leave_days' => $s->meta['paid_leave_days'] ?? null,
                'unpaid_leave_days' => $s->meta['unpaid_leave_days'] ?? null,
                'absent_days' => $s->meta['absent_days'] ?? null,
            ];
        });
    }

    public function headings(): array
    {
        return [
            'employee_id','employee_name','gross_salary','total_earnings','total_deductions','net_salary','status','present_days','approved_leaves','paid_leave_days','unpaid_leave_days','absent_days'
        ];
    }
}
