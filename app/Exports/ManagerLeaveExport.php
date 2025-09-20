<?php

namespace App\Exports;

use App\Models\LeaveApplication;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Illuminate\Http\Request;

class ManagerLeaveExport implements FromCollection, WithHeadings, WithMapping
{
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function collection()
    {
        $selectedEmployee = $this->request->get('employee_id');
        $selectedStatus = $this->request->get('status');
        $selectedLeaveType = $this->request->get('leave_type_id');
        $selectedDate = $this->request->get('date');
        $fromDate = $this->request->get('from_date');
        $toDate = $this->request->get('to_date');

        $query = LeaveApplication::with(['employee.department', 'employee.designation', 'leaveType'])
            ->when($selectedEmployee, fn ($q) => $q->where('employee_id', $selectedEmployee))
            ->when($selectedStatus, fn ($q) => $q->where('status', $selectedStatus))
            ->when($selectedLeaveType, fn ($q) => $q->where('leave_type_id', $selectedLeaveType))
            ->when($selectedDate, fn ($q) => $q->whereDate('start_date', $selectedDate))
            ->when($fromDate && $toDate, function ($q) use ($fromDate, $toDate) {
                $q->where(function($query) use ($fromDate, $toDate) {
                    $query->whereBetween('start_date', [$fromDate, $toDate])
                          ->orWhereBetween('end_date', [$fromDate, $toDate])
                          ->orWhere(function($q) use ($fromDate, $toDate) {
                              $q->where('start_date', '<=', $fromDate)
                                ->where('end_date', '>=', $toDate);
                          });
                });
            });

        return $query->latest()->get();
    }

    public function headings(): array
    {
        return [
            'Employee Name',
            'Department',
            'Designation',
            'Leave Type',
            'Start Date',
            'End Date',
            'Days',
            'Day Type',
            'Status',
            'Reason',
            'Applied Date',
        ];
    }

    public function map($leave): array
    {
        $employee = $leave->employee;
        $days = Carbon::parse($leave->start_date)->diffInDays(Carbon::parse($leave->end_date)) + 1;

        return [
            $employee ? trim($employee->first_name . ' ' . $employee->last_name) : '',
            $employee && $employee->department ? $employee->department->name : '',
            $employee && $employee->designation ? $employee->designation->name : '',
            $leave->leaveType->name ?? '',
            Carbon::parse($leave->start_date)->format('d M Y'),
            Carbon::parse($leave->end_date)->format('d M Y'),
            $days,
            ucfirst(str_replace('_', ' ', $leave->day_type ?? 'full day')),
            ucfirst($leave->status),
            $leave->reason,
            $leave->created_at->format('d M Y'),
        ];
    }
}