<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserProfile;
use App\Models\Student;
use App\Mail\ParentEnrollmentCredentials;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ParentManagementController extends Controller
{
    /**
     * Display a listing of parents
     */
    public function index(Request $request)
    {
        if (!auth()->user()->hasPermission('manage-parents')) {
            abort(403, 'Unauthorized to manage parents');
        }

        $query = UserProfile::whereHas('user', function($q) {
            $q->whereHas('roles', function($r) {
                $r->where('slug', 'parent');
            });
        });

        // Search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        // Status filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $parents = $query->with('user')
            ->latest('created_at')
            ->paginate(15);

        return view('parents.index', compact('parents'));
    }

    /**
     * Show the form for creating a new parent
     */
    public function create()
    {
        if (!auth()->user()->hasPermission('manage-parents')) {
            abort(403, 'Unauthorized to create parents');
        }

        return view('parents.create');
    }

    /**
     * Store a newly created parent
     */
    public function store(Request $request)
    {
        if (!auth()->user()->hasPermission('manage-parents')) {
            abort(403, 'Unauthorized to create parents');
        }

        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'phone' => 'nullable|string|max:20',
            'occupation' => 'nullable|string|max:255',
            'address' => 'nullable|string',
            'status' => 'nullable|in:active,inactive',
        ]);

        // Create user
        $user = User::create([
            'name' => $validated['first_name'] . ' ' . $validated['last_name'],
            'email' => $validated['email'],
            'password' => bcrypt('password123'),
        ]);

        // Assign parent role
        $user->assignRole('parent');

        // Create profile
        UserProfile::create([
            'user_id' => $user->id,
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'phone' => $validated['phone'] ?? null,
            'occupation' => $validated['occupation'] ?? null,
            'address' => $validated['address'] ?? null,
            'status' => $validated['status'] ?? 'active',
        ]);

        // Send welcome email with credentials
        Mail::to($user->email)->send(new ParentEnrollmentCredentials($user, 'New Parent Account'));

        return redirect()->route('admin.parents.index')
            ->with('success', 'Parent created successfully!');
    }

    /**
     * Display the specified parent
     */
    public function show(string $id)
    {
        if (!auth()->user()->hasPermission('manage-parents')) {
            abort(403, 'Unauthorized to view parents');
        }

        $parent = UserProfile::with('user')->findOrFail($id);

        return view('parents.show', compact('parent'));
    }

    /**
     * Show the form for editing a parent
     */
    public function edit(string $id)
    {
        if (!auth()->user()->hasPermission('manage-parents')) {
            abort(403, 'Unauthorized to edit parents');
        }

        $parent = UserProfile::with('user')->findOrFail($id);

        return view('parents.edit', compact('parent'));
    }

    /**
     * Update the specified parent
     */
    public function update(Request $request, string $id)
    {
        if (!auth()->user()->hasPermission('manage-parents')) {
            abort(403, 'Unauthorized to edit parents');
        }

        $parent = UserProfile::findOrFail($id);

        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $parent->user_id,
            'phone' => 'nullable|string|max:20',
            'occupation' => 'nullable|string|max:255',
            'address' => 'nullable|string',
            'status' => 'nullable|in:active,inactive',
        ]);

        // Update user
        $parent->user->update([
            'name' => $validated['first_name'] . ' ' . $validated['last_name'],
            'email' => $validated['email'],
        ]);

        // Update profile
        $parent->update([
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'phone' => $validated['phone'] ?? null,
            'occupation' => $validated['occupation'] ?? null,
            'address' => $validated['address'] ?? null,
            'status' => $validated['status'] ?? 'active',
        ]);

        return redirect()->route('admin.parents.index')
            ->with('success', 'Parent updated successfully!');
    }

    /**
     * Delete a parent
     */
    public function destroy(string $id)
    {
        if (!auth()->user()->hasPermission('manage-parents')) {
            abort(403, 'Unauthorized to delete parents');
        }

        $parent = UserProfile::findOrFail($id);
        $parent->delete();

        return redirect()->route('admin.parents.index')
            ->with('success', 'Parent deleted successfully!');
    }

    /**
     * Show parent's children
     */
    public function showChildren(string $id)
    {
        if (!auth()->user()->hasPermission('manage-parents')) {
            abort(403, 'Unauthorized to manage parents');
        }

        $parent = UserProfile::with('children')->findOrFail($id);

        return view('parents.children', compact('parent'));
    }

    /**
     * Assign a student to a parent
     */
    public function assignChild(Request $request, string $id)
    {
        if (!auth()->user()->hasPermission('manage-parents')) {
            abort(403, 'Unauthorized to manage parents');
        }

        $parent = UserProfile::findOrFail($id);

        $validated = $request->validate([
            'student_id' => 'required|exists:users,id',
        ]);

        $student = User::findOrFail($validated['student_id']);

        // Update student's parent_id with parent's user_id (not parent profile id)
        $student->profile()->updateOrCreate(
            ['user_id' => $student->id],
            ['parent_id' => $parent->user_id]
        );

        return redirect()->route('admin.parents.children', $parent->id)
            ->with('success', 'Student assigned to parent successfully!');
    }

    /**
     * Unassign a student from a parent
     */
    public function unassignChild(string $parentId, string $studentId)
    {
        if (!auth()->user()->hasPermission('manage-parents')) {
            abort(403, 'Unauthorized to manage parents');
        }

        $student = UserProfile::findOrFail($studentId);
        $student->update(['parent_id' => null]);

        return redirect()->route('admin.parents.children', $parentId)
            ->with('success', 'Student unassigned from parent successfully!');
    }
}
