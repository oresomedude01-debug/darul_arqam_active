<?php

namespace App\Http\Controllers;

use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SubjectController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Subject::query();

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%")
                    ->orWhere('category', 'like', "%{$search}%");
            });
        }

        // Filter by category
        if ($request->filled('category')) {
            $query->byCategory($request->category);
        }

        // Filter by status
        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->where('is_active', true);
            } else {
                $query->where('is_active', false);
            }
        }

        // Sorting
        $sortField = $request->get('sort', 'name');
        $sortDirection = $request->get('direction', 'asc');

        $allowedSorts = ['name', 'code', 'category', 'created_at'];
        if (in_array($sortField, $allowedSorts)) {
            $query->orderBy($sortField, $sortDirection);
        }

        // Pagination
        $subjects = $query->paginate(15)->withQueryString();

        // Calculate stats
        $stats = [
            'total' => Subject::count(),
            'active' => Subject::where('is_active', true)->count(),
            'inactive' => Subject::where('is_active', false)->count(),
        ];

        // Get unique categories
        $categories = Subject::distinct()->pluck('category')->filter()->sort()->values();

        return view('subjects.index', compact('subjects', 'stats', 'categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('subjects.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:255|unique:subjects,code',
            'category' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'color' => 'required|string|max:50',
            'is_active' => 'boolean',
        ]);

        // Set default is_active if not provided
        if (!isset($validated['is_active'])) {
            $validated['is_active'] = true;
        }

        $subject = Subject::create($validated);

        return redirect()
            ->route('subjects.show', $subject)
            ->with('success', 'Subject created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Subject $subject)
    {
        $subject->load(['classes.teacher.profile', 'classes.students']);

        return view('subjects.show', compact('subject'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Subject $subject)
    {
        return view('subjects.edit', compact('subject'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Subject $subject)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => ['required', 'string', 'max:255', Rule::unique('subjects', 'code')->ignore($subject->id)],
            'category' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'color' => 'required|string|max:50',
            'is_active' => 'boolean',
        ]);

        $subject->update($validated);

        return redirect()
            ->route('subjects.show', $subject)
            ->with('success', 'Subject updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Subject $subject)
    {
        $subject->delete();

        return redirect()
            ->route('subjects.index')
            ->with('success', 'Subject deleted successfully!');
    }

    /**
     * Assign subject to multiple classes
     */
    public function assignClasses(Request $request, Subject $subject)
    {
        $validated = $request->validate([
            'class_ids' => 'required|array',
            'class_ids.*' => 'integer|exists:school_classes,id'
        ]);

        // Get current assignments
        $currentClassIds = $subject->classes->pluck('id')->toArray();
        $newClassIds = $validated['class_ids'];

        // Find classes to remove and add
        $toRemove = array_diff($currentClassIds, $newClassIds);
        $toAdd = array_diff($newClassIds, $currentClassIds);

        // Remove classes
        if (!empty($toRemove)) {
            $subject->classes()->detach($toRemove);
        }

        // Add new classes
        foreach ($toAdd as $classId) {
            $subject->classes()->attach($classId, [
                'periods_per_week' => 3, // Default value
                'teacher_id' => null
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Classes assigned successfully'
        ]);
    }
}

