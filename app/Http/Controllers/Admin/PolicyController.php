<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Policy;
use App\Models\Department;
use App\Models\Designation;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class PolicyController extends Controller
{
    // No controller-level middleware here; middleware is applied via routes.

    /**
     * List policies (admin)
     */
    public function index(Request $request)
    {
        $q = (string) $request->input('q', '');

        $policies = Policy::with('author')
            ->when($q !== '', fn($qB) => $qB->where('title', 'like', "%{$q}%"))
            ->orderBy('created_at', 'desc')
            ->paginate(12)
            ->withQueryString();

        return Inertia::render('Admin/Policies/Index', [
            'policies' => $policies,
            'filters' => ['q' => $q],
        ]);
    }

    /**
     * Show create form
     */
    public function create()
    {
        $departments = Department::orderBy('name')->get(['id','name']);
        $designations = Designation::orderBy('name')->get(['id','name']);
        $employees = Employee::orderBy('first_name')->get(['id','first_name','last_name','email']);

        return Inertia::render('Admin/Policies/Create', [
            'departments' => $departments,
            'designations' => $designations,
            'employees' => $employees,
        ]);
    }

    /**
     * Store a new policy
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => ['nullable','string','max:255','alpha_dash', Rule::unique('policies','slug')],
            'content' => 'required|string',
            'audience' => ['required', Rule::in(['all','department','designation','employee','custom'])],
            'audience_value' => 'nullable|array',
            'is_active' => 'nullable|in:0,1,true,false',
        ]);

        try {
            $slug = $data['slug'] ?? Str::slug($data['title']);
            $orig = $slug; $i = 1;
            while (Policy::where('slug', $slug)->exists()) {
                $slug = "{$orig}-{$i}"; $i++;
            }

            $policy = Policy::create([
                'title' => $data['title'],
                'slug' => $slug,
                'content' => $data['content'],
                'audience' => $data['audience'],
                // store as JSON or null
                'audience_value' => $data['audience_value'] ?? null,
                'is_active' => isset($data['is_active']) ? (bool) $data['is_active'] : true,
                'created_by' => Auth::id(),
            ]);
        } catch (\Throwable $e) {
            Log::error('Policy store failed: '.$e->getMessage(), ['trace' => $e->getTraceAsString(), 'payload' => $request->all()]);
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Failed to create policy.'], 500);
            }
            return back()->with('error', 'Failed to create policy.');
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => true, 'policy' => $policy], 201);
        }

        return redirect()->route('admin.policies.index')->with('success', 'Policy created.');
    }

    /**
     * Edit form
     */
    public function edit(Policy $policy)
    {
        $departments = Department::orderBy('name')->get(['id','name']);
        $designations = Designation::orderBy('name')->get(['id','name']);
        $employees = Employee::orderBy('first_name')->get(['id','first_name','last_name','email']);

        return Inertia::render('Admin/Policies/Edit', [
            'policy' => $policy,
            'departments' => $departments,
            'designations' => $designations,
            'employees' => $employees,
        ]);
    }

    /**
     * Update a policy
     */
    public function update(Request $request, Policy $policy)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => ['nullable','string','max:255','alpha_dash', Rule::unique('policies','slug')->ignore($policy->id)],
            'content' => 'required|string',
            'audience' => ['required', Rule::in(['all','department','designation','employee','custom'])],
            'audience_value' => 'nullable|array',
            'is_active' => 'nullable|in:0,1,true,false',
        ]);

        try {
            $slug = $data['slug'] ?? Str::slug($data['title']);
            if ($slug !== $policy->slug) {
                $orig = $slug; $i = 1;
                while (Policy::where('slug', $slug)->where('id','!=',$policy->id)->exists()) {
                    $slug = "{$orig}-{$i}"; $i++;
                }
            }

            $policy->update([
                'title' => $data['title'],
                'slug' => $slug,
                'content' => $data['content'],
                'audience' => $data['audience'],
                'audience_value' => $data['audience_value'] ?? null,
                'is_active' => isset($data['is_active']) ? (bool) $data['is_active'] : true,
            ]);
        } catch (\Throwable $e) {
            Log::error('Policy update failed: '.$e->getMessage(), ['id'=>$policy->id, 'trace' => $e->getTraceAsString(), 'payload'=>$request->all()]);
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Failed to update policy.'], 500);
            }
            return back()->with('error', 'Failed to update policy.');
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => true, 'policy' => $policy], 200);
        }

        return redirect()->route('admin.policies.index')->with('success', 'Policy updated.');
    }

    /**
     * Soft delete
     */
/**
 * Permanently delete (hard delete) a policy
 */
public function destroy(Request $request, Policy $policy)
{
    try {
        // Permanently remove the record from DB.
        // If your Policy model uses SoftDeletes, this forces permanent removal.
        $policy->forceDelete();
    } catch (\Throwable $e) {
        \Log::error('Policy hard-delete failed: '.$e->getMessage(), [
            'id' => $policy->id,
            'trace' => $e->getTraceAsString(),
        ]);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => false, 'message' => 'Failed to delete policy.'], 500);
        }
        return back()->with('error', 'Failed to delete policy.');
    }

    if ($request->ajax() || $request->wantsJson()) {
        return response()->json(['success' => true], 200);
    }

    return redirect()->route('admin.policies.index')->with('success', 'Policy permanently deleted.');
}

    /**
     * Toggle active state via XHR (optional)
     */
    public function toggle(Request $request, Policy $policy)
    {
        $policy->is_active = ! $policy->is_active;
        $policy->save();
        return response()->json(['success' => true, 'is_active' => $policy->is_active]);
    }
}
