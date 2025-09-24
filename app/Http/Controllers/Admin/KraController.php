<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kra;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class KraController extends Controller
{
    /**
     * Display a listing of KRAs.
     */
    public function index(Request $request): Response
    {
        $query = Kra::with(['department', 'creator']);

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhereHas('department', function($dept) use ($search) {
                      $dept->where('name', 'like', "%{$search}%");
                  });
            });
        }

        // Filter by department
        if ($request->filled('department_id')) {
            $query->where('department_id', $request->department_id);
        }

        // Filter by priority
        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        // Filter by status
        if ($request->filled('status')) {
            $isActive = $request->status === 'active';
            $query->where('is_active', $isActive);
        }

        // Sorting
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        
        $allowedSorts = ['title', 'priority', 'created_at', 'department_name'];
        if (in_array($sortBy, $allowedSorts)) {
            if ($sortBy === 'department_name') {
                $query->leftJoin('departments', 'kras.department_id', '=', 'departments.id')
                      ->orderBy('departments.name', $sortOrder)
                      ->select('kras.*');
            } else {
                $query->orderBy($sortBy, $sortOrder);
            }
        }

        $kras = $query->paginate(15)->withQueryString();

        // Get departments for filter dropdown
        $departments = Department::select('id', 'name')->orderBy('name')->get();

        // Calculate statistics
        $statistics = [
            'total' => Kra::count(),
            'active' => Kra::where('is_active', true)->count(),
            'high_priority' => Kra::where('priority', 'high')->where('is_active', true)->count(),
            'by_department' => Department::withCount(['kras' => function($query) {
                $query->where('is_active', true);
            }])->get(),
        ];

        return Inertia::render('Admin/Kras/Index', [
            'kras' => $kras,
            'departments' => $departments,
            'filters' => $request->only(['search', 'department_id', 'priority', 'status', 'sort_by', 'sort_order']),
            'statistics' => $statistics,
        ]);
    }

    /**
     * Store a newly created KRA.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:2000',
            'department_id' => 'required|exists:departments,id',
            'priority' => ['required', Rule::in(['low', 'medium', 'high'])],
            'is_active' => 'nullable|boolean',
        ], [
            'title.required' => 'KRA title is required.',
            'title.max' => 'KRA title cannot exceed 255 characters.',
            'department_id.required' => 'Department selection is required.',
            'department_id.exists' => 'Selected department does not exist.',
            'priority.required' => 'Priority selection is required.',
            'priority.in' => 'Priority must be low, medium, or high.',
            'description.max' => 'Description cannot exceed 2000 characters.',
        ]);

        $validated['created_by'] = Auth::id();
        $validated['is_active'] = $request->boolean('is_active', true);

        Kra::create($validated);

        return back()->with('success', 'KRA created successfully!');
    }

    /**
     * Display the specified KRA.
     */
    public function show(Kra $kra): Response
    {
        $kra->load(['department', 'creator']);
        
        return Inertia::render('Admin/Kras/Show', [
            'kra' => $kra,
        ]);
    }

    /**
     * Update the specified KRA.
     */
    public function update(Request $request, Kra $kra)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:2000',
            'department_id' => 'required|exists:departments,id',
            'priority' => ['required', Rule::in(['low', 'medium', 'high'])],
            'is_active' => 'nullable|boolean',
        ], [
            'title.required' => 'KRA title is required.',
            'title.max' => 'KRA title cannot exceed 255 characters.',
            'department_id.required' => 'Department selection is required.',
            'department_id.exists' => 'Selected department does not exist.',
            'priority.required' => 'Priority selection is required.',
            'priority.in' => 'Priority must be low, medium, or high.',
            'description.max' => 'Description cannot exceed 2000 characters.',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);

        $kra->update($validated);

        return back()->with('success', 'KRA updated successfully!');
    }

    /**
     * Remove the specified KRA.
     */
    public function destroy(Kra $kra)
    {
        $kra->delete();

        return back()->with('success', 'KRA deleted successfully!');
    }

    /**
     * Toggle KRA status.
     */
    public function toggleStatus(Kra $kra)
    {
        $kra->update([
            'is_active' => !$kra->is_active
        ]);

        $status = $kra->is_active ? 'activated' : 'deactivated';
        
        return back()->with('success', "KRA {$status} successfully!");
    }

    /**
     * Bulk operations for KRAs.
     */
    public function bulkAction(Request $request)
    {
        $request->validate([
            'action' => 'required|in:activate,deactivate,delete',
            'kra_ids' => 'required|array|min:1',
            'kra_ids.*' => 'exists:kras,id',
        ]);

        $kraIds = $request->kra_ids;
        $action = $request->action;

        switch ($action) {
            case 'activate':
                Kra::whereIn('id', $kraIds)->update(['is_active' => true]);
                return back()->with('success', 'Selected KRAs activated successfully!');

            case 'deactivate':
                Kra::whereIn('id', $kraIds)->update(['is_active' => false]);
                return back()->with('success', 'Selected KRAs deactivated successfully!');

            case 'delete':
                Kra::whereIn('id', $kraIds)->delete();
                return back()->with('success', 'Selected KRAs deleted successfully!');
        }
    }
}