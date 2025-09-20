<?php

namespace App\Exports;

use App\Models\Punch;
use App\Models\Employee;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Illuminate\Http\Request;

class ManagerAttendanceExport implements FromCollection, WithHeadings, WithMapping
{
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function collection()
    {
        $selectedEmployee = $this->request->get('employee_id');
        $selectedDepartment = $this->request->get('department_id');
        $selectedDesignation = $this->request->get('designation_id');
        $selectedDate = $this->request->get('date');
        $fromDate = $this->request->get('from_date');
        $toDate = $this->request->get('to_date');
        $selectedMonth = $this->request->get('month') ?? now()->format('Y-m');

        // Build query with filters
        $query = Punch::with(['employee.designation', 'employee.department'])
            ->when($selectedEmployee, fn ($q) => $q->where('employee_id', $selectedEmployee))
            ->when($selectedDate, fn ($q) => $q->whereDate('punched_in_at', $selectedDate))
            ->when($fromDate && $toDate, function ($q) use ($fromDate, $toDate) {
                $q->whereBetween('punched_in_at', [$fromDate, $toDate]);
            })
            ->when($selectedMonth && !$fromDate && !$toDate, function ($q) use ($selectedMonth) {
                $m = Carbon::parse($selectedMonth);
                $q->whereMonth('punched_in_at', $m->month)
                  ->whereYear('punched_in_at', $m->year);
            });

        // Apply department/designation filters
        if ($selectedDepartment || $selectedDesignation) {
            $query->whereHas('employee', function ($q) use ($selectedDepartment, $selectedDesignation) {
                if ($selectedDepartment) {
                    $q->where('department_id', $selectedDepartment);
                }
                if ($selectedDesignation) {
                    $q->where('designation_id', $selectedDesignation);
                }
            });
        }

        $punches = $query->get();

        // Group by employee + date
        return $punches->groupBy(function ($punch) {
            $istDate = Carbon::parse($punch->punched_in_at)->timezone('Asia/Kolkata')->format('Y-m-d');
            return $punch->employee_id . '-' . $istDate;
        })->map(function ($group) {
            $first = $group->first();

            $firstInAt = $group->min('punched_in_at');
            $lastOutAt = $group->filter(fn($p) => !is_null($p->punched_out_at))->max('punched_out_at');

            $firstIn = $firstInAt ? Carbon::parse($firstInAt)->timezone('Asia/Kolkata') : null;
            $lastOut = $lastOutAt ? Carbon::parse($lastOutAt)->timezone('Asia/Kolkata') : null;

            $totalSeconds = 0;
            foreach ($group as $p) {
                if ($p->punched_in_at && $p->punched_out_at) {
                    $in = Carbon::parse($p->punched_in_at)->timezone('Asia/Kolkata');
                    $out = Carbon::parse($p->punched_out_at)->timezone('Asia/Kolkata');
                    $totalSeconds += $in->diffInSeconds($out);
                }
            }

            $emp = $first->employee ?? null;

            return [
                'employee' => $emp ? trim(($emp->first_name ?? '') . ' ' . ($emp->last_name ?? '')) : '',
                'department' => $emp && $emp->department ? $emp->department->name : '—',
                'designation' => $emp && $emp->designation ? $emp->designation->name : '—',
                'date' => Carbon::parse($first->punched_in_at)->timezone('Asia/Kolkata')->format('Y-m-d'),
                'first_in' => $firstIn ? $firstIn->format('h:i:s A') : '',
                'last_out' => $lastOut ? $lastOut->format('h:i:s A') : '',
                'hours' => gmdate('H:i:s', $totalSeconds),
            ];
        })->values();
    }

    public function headings(): array
    {
        return [
            'Employee Name',
            'Department',
            'Designation',
            'Date',
            'First In',
            'Last Out',
            'Total Hours',
        ];
    }

    public function map($row): array
    {
        return [
            $row['employee'],
            $row['department'],
            $row['designation'],
            $row['date'],
            $row['first_in'],
            $row['last_out'],
            $row['hours'],
        ];
    }
}