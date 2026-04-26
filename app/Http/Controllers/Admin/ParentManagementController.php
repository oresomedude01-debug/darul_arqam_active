<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class ParentManagementController extends Controller
{
    /**
     * Display list of parents
     */
    public function index(Request $request)
    {
        // Get users with parent role
        $query = User::whereHas('roles', function($q) {
            $q->where('slug', 'parent');
        });

        // Search by name or email
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $parents = $query->with('profile')
            ->orderBy('name')
            ->paginate(15);

        return view('admin.parents.index', [
            'parents' => $parents,
        ]);
    }

    /**
     * Show create parent form
     */
    public function create()
    {
        return view('admin.parents.create');
    }

    /**
     * Store new parent
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'occupation' => 'nullable|string|max:255',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $parent = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => bcrypt($validated['password']),
            'is_active' => true,
        ]);

        // Create user profile
        $parent->profile()->create([
            'phone' => $validated['phone'] ?? null,
            'address' => $validated['address'] ?? null,
            'occupation' => $validated['occupation'] ?? null,
        ]);

        // Assign parent role
        $parentRole = \App\Models\Role::where('slug', 'parent')->first();
        if ($parentRole) {
            $parent->roles()->attach($parentRole->id);
        }

        return redirect()->route('admin.parents.index')
            ->with('success', "Parent '{$parent->name}' created successfully!");
    }

    /**
     * Show edit parent form
     */
    public function edit(User $parent)
    {
        $parent->load('profile');
        return view('admin.parents.edit', [
            'parent' => $parent,
        ]);
    }

    /**
     * Show parent detail with children and payment info
     */
    public function show(User $parent)
    {
        $parent->load('profile');
        
        // Get all students to allow assignment
        $allStudents = User::whereHas('roles', function($q) {
            $q->where('slug', 'student');
        })->orderBy('name')->get();

        // Get children through user_profile.parent_id relationship
        $children = User::whereHas('profile', function($q) use ($parent) {
            $q->where('parent_id', $parent->id);
        })->with('profile', 'roles')->orderBy('name')->get();

        // Get outstanding bills for this parent's children
        $childrenIds = $children->pluck('id')->toArray();
        $outstandingBills = \App\Models\StudentBill::whereIn('student_id', $childrenIds)
            ->whereIn('status', ['pending', 'partial'])
            ->with('student', 'feeStructure')
            ->get();

        // Calculate total outstanding (balance_due)
        $totalOutstanding = $outstandingBills->sum('balance_due');

        return view('admin.parents.show', [
            'parent' => $parent,
            'children' => $children,
            'allStudents' => $allStudents,
            'outstandingBills' => $outstandingBills,
            'totalOutstanding' => $totalOutstanding,
        ]);
    }

    /**
     * Assign student to parent
     */
    public function assignChild(Request $request, User $parent)
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:users,id',
        ]);

        $student = User::findOrFail($validated['student_id']);
        
        // Update the student's profile with parent_id
        $student->profile()->updateOrCreate(
            ['user_id' => $student->id],
            ['parent_id' => $parent->id]
        );

        return redirect()->route('admin.parents.show', $parent)
            ->with('success', "Student '{$student->name}' assigned to parent successfully!");
    }

    /**
     * Unassign student from parent
     */
    public function unassignChild(User $parent, User $student)
    {
        // Check if student is assigned to this parent
        $studentProfile = $student->profile;
        if (!$studentProfile || $studentProfile->parent_id != $parent->id) {
            return redirect()->route('admin.parents.show', $parent)
                ->with('error', 'This student is not assigned to this parent.');
        }

        $studentName = $student->name;
        $studentProfile->update(['parent_id' => null]);

        return redirect()->route('admin.parents.show', $parent)
            ->with('success', "Student '{$studentName}' unassigned from parent successfully!");
    }

    /**
     * Update parent information
     */
    public function update(Request $request, User $parent)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $parent->id,
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'occupation' => 'nullable|string|max:255',
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        $parent->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
        ]);

        if ($validated['password'] ?? false) {
            $parent->update(['password' => bcrypt($validated['password'])]);
        }

        // Update or create profile
        $parent->profile()->updateOrCreate(
            ['user_id' => $parent->id],
            [
                'phone' => $validated['phone'] ?? null,
                'address' => $validated['address'] ?? null,
                'occupation' => $validated['occupation'] ?? null,
            ]
        );

        return redirect()->route('admin.parents.index')
            ->with('success', "Parent '{$parent->name}' updated successfully!");
    }

    /**
     * Delete parent
     */
    public function destroy(User $parent)
    {
        // Check if parent has children
        if ($parent->children()->count() > 0) {
            return redirect()->route('admin.parents.index')
                ->with('error', "Cannot delete parent '{$parent->name}' - has linked children. Remove children first.");
        }

        $name = $parent->name;
        $parent->profile()->delete();
        $parent->roles()->detach();
        $parent->delete();

        return redirect()->route('admin.parents.index')
            ->with('success', "Parent '{$name}' deleted successfully!");
    }

    /**
     * Show parent children
     */
    public function showChildren(User $parent)
    {
        $parent->load('children');
        return view('admin.parents.children', [
            'parent' => $parent,
        ]);
    }
}
