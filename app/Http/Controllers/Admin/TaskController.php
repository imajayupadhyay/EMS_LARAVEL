<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Models\Employee;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Inertia\Inertia;

class TaskController extends Controller
{
    public function index(Request $request)
    {
        $date = $request->input('date') ?? Carbon::today()->toDateString();
        $employeeName = $request->input('employee');

        $query = Task::with('employee')
            ->whereDate('task_date', $date);

        if ($employeeName) {
            $query->whereHas('employee', function ($q) use ($employeeName) {
                $q->where('first_name', 'like', "%{$employeeName}%")
                  ->orWhere('last_name', 'like', "%{$employeeName}%");
            });
        }

        $tasks = $query->orderBy('task_date', 'desc')->paginate(10);

        return Inertia::render('Admin/Tasks/Index', [
            'tasks' => $tasks,
            'filters' => [
                'date' => $date,
                'employee' => $employeeName,
            ]
        ]);
    }
}
