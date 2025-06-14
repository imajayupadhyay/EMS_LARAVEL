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
    $query = Task::where('user_id', auth()->id());

    // Apply filters
    if ($request->filled('date')) {
        $query->whereDate('task_date', $request->date);
    }

    if ($request->filled('keyword')) {
        $query->where('task_content', 'like', '%' . $request->keyword . '%');
    }

    $tasks = $query->orderByDesc('task_date')->paginate(5)->withQueryString();

    return Inertia::render('Employee/Tasks/Index', [
        'today' => $today,
        'tasks' => $tasks,
        'filters' => $request->only(['date', 'keyword']),
    ]);
}

    public function save(Request $request)
    {
        $request->validate([
            'task_content' => 'required|string',
        ]);

        $today = Carbon::now()->toDateString();

        if ($request->task_id) {
            // Update
            $task = Task::where('id', $request->task_id)
                        ->where('user_id', auth()->id())
                        ->where('task_date', $today)
                        ->firstOrFail();

            $task->update([
                'task_content' => $request->task_content,
            ]);

            return redirect()->back()->with('success', 'Task updated successfully.');
        } else {
            // Create
            $exists = Task::where('user_id', auth()->id())
                          ->where('task_date', $today)
                          ->exists();

            if ($exists) {
                return redirect()->back()->with('error', 'Task already exists for today.');
            }

            Task::create([
                'user_id' => auth()->id(),
                'task_date' => $today,
                'task_content' => $request->task_content,
            ]);

            return redirect()->back()->with('success', 'Task created successfully.');
        }
    }

    public function destroy(Task $task)
    {
        if ($task->user_id !== auth()->id() || $task->task_date !== now()->toDateString()) {
            return back()->with('error', 'Unauthorized action.');
        }

        $task->delete();

        return back()->with('success', 'Task deleted successfully.');
    }
}
