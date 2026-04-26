<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\Permission;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    /**
     * Display a listing of roles
     */
    public function index(Request $request)
    {
        $this->authorize('admin');

        $roles = Role::when($request->search, function ($q) use ($request) {
            $q->where('name', 'like', "%{$request->search}%")
                ->orWhere('slug', 'like', "%{$request->search}%");
        })
            ->withCount('users')
            ->paginate(15);

        return view('roles.index', compact('roles'));
    }

    /**
     * Show the form for creating a new role
     */
    public function create()
    {
        $this->authorize('admin');

        $permissionsByGroup = Permission::groupByCategory();
        return view('roles.create', compact('permissionsByGroup'));
    }

    /**
     * Store a newly created role in storage
     */
    public function store(Request $request)
    {
        $this->authorize('admin');

        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:roles',
            'slug' => 'required|string|max:255|unique:roles',
            'description' => 'nullable|string',
            'permissions' => 'array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        $role = Role::create([
            'name' => $validated['name'],
            'slug' => $validated['slug'],
            'description' => $validated['description'] ?? null,
            'is_active' => true,
        ]);

        if (!empty($validated['permissions'])) {
            $role->permissions()->sync($validated['permissions']);
        }

        return redirect()->route('roles.index')
            ->with('success', 'Role created successfully');
    }

    /**
     * Show the form for editing the specified role
     */
    public function edit(Role $role)
    {
        $this->authorize('admin');

        $permissionsByGroup = Permission::groupByCategory();
        $rolePermissions = $role->permissions()->pluck('id')->toArray();

        return view('roles.edit', compact('role', 'permissionsByGroup', 'rolePermissions'));
    }

    /**
     * Update the specified role in storage
     */
    public function update(Request $request, Role $role)
    {
        $this->authorize('admin');

        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:roles,name,' . $role->id,
            'slug' => 'required|string|max:255|unique:roles,slug,' . $role->id,
            'description' => 'nullable|string',
            'is_active' => 'boolean',
            'permissions' => 'array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        $role->update($validated);

        if (array_key_exists('permissions', $validated)) {
            $role->permissions()->sync($validated['permissions']);
        }

        return redirect()->route('roles.index')
            ->with('success', 'Role updated successfully');
    }

    /**
     * Remove the specified role from storage
     */
    public function destroy(Role $role)
    {
        $this->authorize('admin');

        // Prevent deletion if role is assigned to users
        if ($role->users()->count() > 0) {
            return back()->with('error', 'Cannot delete a role with assigned users');
        }

        $role->permissions()->detach();
        $role->delete();

        return redirect()->route('roles.index')
            ->with('success', 'Role deleted successfully');
    }

    protected function authorize($role)
    {
        if (!auth()->check() || !auth()->user()->hasRole($role)) {
            abort(403, 'Unauthorized action.');
        }
    }
}
