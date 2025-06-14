<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;

class TaskController extends Controller
{
    public function index(Request $request)
    {
        $date = $request->input('date') ?? Carbon::today()->toDateString();
        $employee = $request->input('employee');

        $query = Task::with('user')
            ->whereDate('task_date', $date);

        if ($employee) {
            $query->whereHas('user', function($q) use ($employee) {
                $q->where('name', 'like', "%{$employee}%");
            });
        }

        $tasks = $query->orderBy('task_date', 'desc')->paginate(10);

        return inertia('Admin/Tasks/Index', [
            'tasks' => $tasks,
            'filters' => [
                'date' => $date,
                'employee' => $employee
            ]
        ]);
    }
}
