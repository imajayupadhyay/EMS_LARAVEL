<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Models\Employee;
use App\Models\Department;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Inertia\Inertia;

class TaskController extends Controller
{
    public function index(Request $request)
    {
        // Get filter parameters
        $dateFrom = $request->input('date_from');
        $dateTo = $request->input('date_to');
        $employeeName = $request->input('employee');
        $departmentId = $request->input('department_id');
        $perPage = $request->input('per_page', 15);
        $sortBy = $request->input('sort_by', 'task_date');
        $sortOrder = $request->input('sort_order', 'desc');
        
        // Default date range (last 30 days if no filters provided)
        if (!$dateFrom && !$dateTo) {
            $dateTo = Carbon::today()->toDateString();
            $dateFrom = Carbon::today()->subDays(30)->toDateString();
        } elseif ($dateFrom && !$dateTo) {
            $dateTo = Carbon::today()->toDateString();
        } elseif (!$dateFrom && $dateTo) {
            $dateFrom = Carbon::parse($dateTo)->subDays(30)->toDateString();
        }

        // Build query with relationships
        $query = Task::with(['employee.department', 'employee.designation'])
            ->select('tasks.*');

        // Apply date range filter
        if ($dateFrom) {
            $query->whereDate('task_date', '>=', $dateFrom);
        }
        if ($dateTo) {
            $query->whereDate('task_date', '<=', $dateTo);
        }

        // Apply employee name filter
        if ($employeeName) {
            $query->whereHas('employee', function ($q) use ($employeeName) {
                $q->where(function ($subQ) use ($employeeName) {
                    $subQ->where('first_name', 'like', "%{$employeeName}%")
                        ->orWhere('last_name', 'like', "%{$employeeName}%")
                        ->orWhere(\DB::raw("CONCAT(first_name, ' ', last_name)"), 'like', "%{$employeeName}%");
                });
            });
        }

        // Apply department filter
        if ($departmentId) {
            $query->whereHas('employee', function ($q) use ($departmentId) {
                $q->where('department_id', $departmentId);
            });
        }

        // Apply sorting
        switch ($sortBy) {
            case 'employee_name':
                $query->join('employees', 'tasks.employee_id', '=', 'employees.id')
                    ->orderBy('employees.first_name', $sortOrder)
                    ->orderBy('employees.last_name', $sortOrder);
                break;
            case 'department':
                $query->join('employees', 'tasks.employee_id', '=', 'employees.id')
                    ->join('departments', 'employees.department_id', '=', 'departments.id')
                    ->orderBy('departments.name', $sortOrder);
                break;
            default:
                $query->orderBy($sortBy, $sortOrder);
                break;
        }

        // Add secondary sort by task_date desc
        if ($sortBy !== 'task_date') {
            $query->orderBy('task_date', 'desc');
        }

        // Paginate results
        $tasks = $query->paginate($perPage)->withQueryString();

        // Get statistics
        $stats = $this->getTaskStats($dateFrom, $dateTo, $employeeName, $departmentId);

        // Get departments for filter dropdown
        $departments = Department::orderBy('name')->get(['id', 'name']);

        return Inertia::render('Admin/Tasks/Index', [
            'tasks' => $tasks,
            'departments' => $departments,
            'stats' => $stats,
            'filters' => [
                'date_from' => $dateFrom,
                'date_to' => $dateTo,
                'employee' => $employeeName,
                'department_id' => $departmentId,
                'per_page' => $perPage,
                'sort_by' => $sortBy,
                'sort_order' => $sortOrder,
            ]
        ]);
    }

    private function getTaskStats($dateFrom = null, $dateTo = null, $employeeName = null, $departmentId = null)
    {
        $query = Task::query();

        // Apply same filters as main query
        if ($dateFrom) {
            $query->whereDate('task_date', '>=', $dateFrom);
        }
        if ($dateTo) {
            $query->whereDate('task_date', '<=', $dateTo);
        }
        if ($employeeName) {
            $query->whereHas('employee', function ($q) use ($employeeName) {
                $q->where(function ($subQ) use ($employeeName) {
                    $subQ->where('first_name', 'like', "%{$employeeName}%")
                        ->orWhere('last_name', 'like', "%{$employeeName}%")
                        ->orWhere(\DB::raw("CONCAT(first_name, ' ', last_name)"), 'like', "%{$employeeName}%");
                });
            });
        }
        if ($departmentId) {
            $query->whereHas('employee', function ($q) use ($departmentId) {
                $q->where('department_id', $departmentId);
            });
        }

        $totalTasks = $query->count();
        $uniqueEmployees = $query->distinct('employee_id')->count('employee_id');
        $todayTasks = (clone $query)->whereDate('task_date', Carbon::today())->count();
        $avgTasksPerDay = 0;

        if ($dateFrom && $dateTo) {
            $daysDiff = Carbon::parse($dateTo)->diffInDays(Carbon::parse($dateFrom)) + 1;
            $avgTasksPerDay = $daysDiff > 0 ? round($totalTasks / $daysDiff, 1) : 0;
        }

        return [
            'total_tasks' => $totalTasks,
            'unique_employees' => $uniqueEmployees,
            'today_tasks' => $todayTasks,
            'avg_tasks_per_day' => $avgTasksPerDay,
        ];
    }

    public function export(Request $request)
    {
        // Export functionality can be added here
        // For now, return the same data as JSON
        $tasks = $this->getTasksForExport($request);
        
        return response()->json([
            'success' => true,
            'data' => $tasks,
            'message' => 'Export functionality to be implemented'
        ]);
    }

    private function getTasksForExport(Request $request)
    {
        $dateFrom = $request->input('date_from');
        $dateTo = $request->input('date_to');
        $employeeName = $request->input('employee');
        $departmentId = $request->input('department_id');

        $query = Task::with(['employee.department', 'employee.designation']);

        if ($dateFrom) {
            $query->whereDate('task_date', '>=', $dateFrom);
        }
        if ($dateTo) {
            $query->whereDate('task_date', '<=', $dateTo);
        }
        if ($employeeName) {
            $query->whereHas('employee', function ($q) use ($employeeName) {
                $q->where('first_name', 'like', "%{$employeeName}%")
                  ->orWhere('last_name', 'like', "%{$employeeName}%");
            });
        }
        if ($departmentId) {
            $query->whereHas('employee', function ($q) use ($departmentId) {
                $q->where('department_id', $departmentId);
            });
        }

        return $query->orderBy('task_date', 'desc')->get();
    }
}