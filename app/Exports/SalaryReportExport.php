<?php

namespace App\Exports;

use App\Models\Salary;
use App\Models\PayrollPeriod;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class SalaryReportExport implements FromCollection, WithHeadings, WithStyles
{
    protected $month;
    protected $year;

    public function __construct($month, $year)
    {
        $this->month = (int) $month;
        $this->year = (int) $year;
    }

    public function collection()
    {
        try {
            // Increase memory limit and execution time for export
            ini_set('memory_limit', '512M');
            ini_set('max_execution_time', '300');

            $period = PayrollPeriod::where('year', $this->year)
                ->where('month', $this->month)
                ->first();

            if (!$period) {
                Log::warning("No payroll period found for {$this->year}-{$this->month}");
                return collect([[
                    'No payroll data found for the selected month.',
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                    ''
                ]]);
            }

            $salaries = Salary::with(['employee.department', 'employee.designation'])
                ->where('payroll_period_id', $period->id)
                ->get();

            if ($salaries->isEmpty()) {
                return collect([[
                    'No salary records found for the selected period.',
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                    ''
                ]]);
            }

            return $salaries->map(function ($s) {
                $employee = $s->employee;

                return [
                    'employee_id' => $s->employee_id ?? '',
                    'employee_name' => $employee ? trim($employee->first_name . ' ' . $employee->last_name) : 'N/A',
                    'department' => $employee && $employee->department ? $employee->department->name : '-',
                    'designation' => $employee && $employee->designation ? $employee->designation->name : '-',
                    'gross_salary' => number_format($s->gross_salary ?? 0, 2),
                    'total_earnings' => number_format($s->total_earnings ?? 0, 2),
                    'total_deductions' => number_format($s->total_deductions ?? 0, 2),
                    'net_salary' => number_format($s->net_salary ?? 0, 2),
                    'present_days' => $s->meta['present_days'] ?? 0,
                    'approved_leaves' => $s->meta['approved_leaves'] ?? 0,
                    'paid_leave_days' => $s->meta['paid_leave_days'] ?? 0,
                    'unpaid_leave_days' => $s->meta['unpaid_leave_days'] ?? 0,
                    'absent_days' => $s->meta['absent_days'] ?? 0,
                    'working_days' => $s->meta['working_days'] ?? 0,
                    'status' => ucfirst($s->status ?? 'draft'),
                ];
            });

        } catch (\Exception $e) {
            Log::error('Salary Export Error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'month' => $this->month,
                'year' => $this->year
            ]);

            return collect([[
                'Error generating report: ' . $e->getMessage(),
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                ''
            ]]);
        }
    }

    public function headings(): array
    {
        return [
            'Employee ID',
            'Employee Name',
            'Department',
            'Designation',
            'Gross Salary',
            'Total Earnings',
            'Total Deductions',
            'Net Salary',
            'Present Days',
            'Approved Leaves',
            'Paid Leave Days',
            'Unpaid Leave Days',
            'Absent Days',
            'Working Days',
            'Status'
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Style the first row (header)
            1 => [
                'font' => ['bold' => true, 'size' => 12],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '8B5CF6']
                ],
                'font' => ['color' => ['rgb' => 'FFFFFF'], 'bold' => true]
            ],
        ];
    }
}
