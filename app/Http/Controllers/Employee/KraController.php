<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\Kra;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class KraController extends Controller
{
    /**
     * Display KRAs for the employee's department
     */
    public function index(Request $request): Response
    {
        $employee = Auth::guard('employee')->user();

        // Get KRAs for employee's department
        $query = Kra::with(['department', 'creator'])
            ->where('is_active', true)
            ->where('department_id', $employee->department_id);

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Filter by priority
        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        // Sorting
        $sortBy = $request->get('sort_by', 'priority');
        $sortOrder = $request->get('sort_order', 'asc');

        $allowedSorts = ['title', 'priority', 'created_at'];
        if (in_array($sortBy, $allowedSorts)) {
            // Custom sorting for priority to show high -> medium -> low
            if ($sortBy === 'priority') {
                $query->orderByRaw("FIELD(priority, 'high', 'medium', 'low')");
            } else {
                $query->orderBy($sortBy, $sortOrder);
            }
        }

        $kras = $query->paginate(15)->withQueryString();

        // Calculate statistics for employee's department
        $statistics = [
            'total' => Kra::where('is_active', true)
                ->where('department_id', $employee->department_id)
                ->count(),
            'high_priority' => Kra::where('is_active', true)
                ->where('department_id', $employee->department_id)
                ->where('priority', 'high')
                ->count(),
            'medium_priority' => Kra::where('is_active', true)
                ->where('department_id', $employee->department_id)
                ->where('priority', 'medium')
                ->count(),
            'low_priority' => Kra::where('is_active', true)
                ->where('department_id', $employee->department_id)
                ->where('priority', 'low')
                ->count(),
        ];

        return Inertia::render('Employee/Kra/Index', [
            'kras' => $kras,
            'statistics' => $statistics,
            'filters' => [
                'search' => $request->search,
                'priority' => $request->priority,
                'sort_by' => $sortBy,
                'sort_order' => $sortOrder,
            ],
            'departmentName' => $employee->department ? $employee->department->name : 'Your Department',
        ]);
    }
}
