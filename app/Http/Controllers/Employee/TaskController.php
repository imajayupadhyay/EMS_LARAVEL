<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\Task;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Carbon\Carbon;

class TaskController extends Controller
{
    public function index(Request $request)
    {
        $today = now()->toDateString();
        $employeeId = auth()->user()->id;

        $query = Task::where('employee_id', $employeeId);

        // Filters
        if ($request->filled('date')) {
            $query->whereDate('task_date', $request->date);
        }

        if ($request->filled('keyword')) {
            $query->where('task_content', 'like', '%' . $request->keyword . '%');
        }

        $tasks = $query->orderByDesc('task_date')->paginate(10)->withQueryString();

        // Calculate statistics
        $statistics = [
            'total' => Task::where('employee_id', $employeeId)->count(),
            'this_week' => Task::where('employee_id', $employeeId)
                              ->whereBetween('task_date', [
                                  now()->startOfWeek()->toDateString(),
                                  now()->endOfWeek()->toDateString()
                              ])
                              ->count(),
            'this_month' => Task::where('employee_id', $employeeId)
                               ->whereMonth('task_date', now()->month)
                               ->whereYear('task_date', now()->year)
                               ->count(),
            'streak' => $this->calculateStreak($employeeId),
        ];

        return Inertia::render('Employee/Tasks/Index', [
            'today'   => $today,
            'tasks'   => $tasks,
            'filters' => $request->only(['date', 'keyword']),
            'statistics' => $statistics,
        ]);
    }

    public function save(Request $request)
    {
        $request->validate([
            'task_content' => 'required|string',
        ]);

        $today = Carbon::now()->toDateString();
        $employeeId = auth()->user()->id;

        if ($request->task_id) {
            // Update
            $task = Task::where('id', $request->task_id)
                        ->where('employee_id', $employeeId)
                        ->where('task_date', $today)
                        ->firstOrFail();

            $task->update([
                'task_content' => $request->task_content,
            ]);

            return redirect()->back()->with('success', 'Task updated successfully.');
        } else {
            // Create
            $exists = Task::where('employee_id', $employeeId)
                          ->where('task_date', $today)
                          ->exists();

            if ($exists) {
                return redirect()->back()->with('error', 'Task already exists for today.');
            }

            Task::create([
                'employee_id'  => $employeeId,
                'task_date'    => $today,
                'task_content' => $request->task_content,
            ]);

            return redirect()->back()->with('success', 'Task created successfully.');
        }
    }

    public function destroy(Task $task)
    {
        $employeeId = auth()->user()->id;

        if ($task->employee_id !== $employeeId || $task->task_date !== now()->toDateString()) {
            return back()->with('error', 'Unauthorized action.');
        }

        $task->delete();

        return back()->with('success', 'Task deleted successfully.');
    }

    /**
     * Calculate the current streak of consecutive days with tasks
     */
    private function calculateStreak($employeeId)
    {
        $streak = 0;
        $currentDate = Carbon::now()->subDay(); // Start from yesterday
        
        while (true) {
            $hasTask = Task::where('employee_id', $employeeId)
                          ->where('task_date', $currentDate->toDateString())
                          ->exists();
            
            if (!$hasTask) {
                break;
            }
            
            $streak++;
            $currentDate->subDay();
            
            // Limit to prevent infinite loops
            if ($streak > 365) {
                break;
            }
        }
        
        // Check if there's a task for today to include it in the streak
        $hasTodayTask = Task::where('employee_id', $employeeId)
                            ->where('task_date', now()->toDateString())
                            ->exists();
        
        if ($hasTodayTask) {
            $streak++;
        }
        
        return $streak;
    }
}