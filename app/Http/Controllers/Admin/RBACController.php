<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\Permission;
use App\Models\User;
use Illuminate\Http\Request;

class RBACController extends Controller
{
    /**
     * RBAC Dashboard - Overview of roles and permissions
     */
    public function dashboard()
    {
        $totalRoles = Role::count();
        $totalPermissions = Permission::count();
        $totalUsersWithRoles = User::whereHas('roles')->count();
        $rolesWithPermissions = Role::withCount('permissions')->get();

        return view('admin.rbac.dashboard', [
            'totalRoles' => $totalRoles,
            'totalPermissions' => $totalPermissions,
            'totalUsersWithRoles' => $totalUsersWithRoles,
            'rolesWithPermissions' => $rolesWithPermissions,
        ]);
    }

    /**
     * List all roles
     */
    public function listRoles()
    {
        $roles = Role::withCount('users', 'permissions')
            ->orderBy('name')
            ->paginate(15);

        return view('admin.rbac.roles.index', [
            'roles' => $roles,
        ]);
    }

    /**
     * Show role creation form
     */
    public function createRole()
    {
        $permissions = Permission::orderBy('name')->get();
        
        return view('admin.rbac.roles.create', [
            'permissions' => $permissions,
        ]);
    }

    /**
     * Store new role
     */
    public function storeRole(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|unique:roles,name|max:255',
            'description' => 'nullable|string|max:500',
            'permissions' => 'array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        $role = Role::create($validated);

        if ($request->has('permissions')) {
            $role->permissions()->sync($request->input('permissions', []));
        }

        return redirect()->route('admin.roles.index')
            ->with('success', "Role '{$role->name}' created successfully!");
    }

    /**
     * Show role edit form
     */
    public function editRole(Role $role)
    {
        $permissions = Permission::orderBy('name')->get();
        $rolePermissions = $role->permissions->pluck('id')->toArray();

        return view('admin.rbac.roles.edit', [
            'role' => $role,
            'permissions' => $permissions,
            'rolePermissions' => $rolePermissions,
        ]);
    }

    /**
     * Update role
     */
    public function updateRole(Request $request, Role $role)
    {
        $validated = $request->validate([
            'description' => 'nullable|string|max:500',
            'permissions' => 'array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        $role->update($validated);
        $role->permissions()->sync($request->input('permissions', []));

        return redirect()->route('admin.roles.index')
            ->with('success', "Role '{$role->name}' updated successfully!");
    }

    /**
     * Delete role
     */
    public function deleteRole(Role $role)
    {
        if ($role->users()->exists()) {
            return back()->with('error', 'Cannot delete role with assigned users!');
        }

        $role->delete();

        return redirect()->route('admin.roles.index')
            ->with('success', "Role deleted successfully!");
    }

    /**
     * List all permissions
     */
    public function listPermissions()
    {
        $permissions = Permission::withCount('roles')
            ->orderBy('name')
            ->paginate(20);

        return view('admin.rbac.permissions.index', [
            'permissions' => $permissions,
        ]);
    }

    /**
     * Show permission creation form
     */
    public function createPermission()
    {
        return view('admin.rbac.permissions.create');
    }

    /**
     * Store new permission
     */
    public function storePermission(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|unique:permissions,name|max:255',
            'description' => 'nullable|string|max:500',
        ]);

        // Generate slug from name
        $validated['slug'] = \Illuminate\Support\Str::slug($validated['name']);

        Permission::create($validated);

        return redirect()->route('admin.permissions.index')
            ->with('success', "Permission '{$validated['name']}' created successfully!");
    }

    /**
     * Show permission edit form
     */
    public function editPermission(Permission $permission)
    {
        return view('admin.rbac.permissions.edit', [
            'permission' => $permission,
        ]);
    }

    /**
     * Update permission
     */
    public function updatePermission(Request $request, Permission $permission)
    {
        $validated = $request->validate([
            'description' => 'nullable|string|max:500',
        ]);

        $permission->update($validated);

        return redirect()->route('admin.permissions.index')
            ->with('success', "Permission updated successfully!");
    }

    /**
     * Delete permission
     */
    public function deletePermission(Permission $permission)
    {
        if ($permission->roles()->exists()) {
            return back()->with('error', 'Cannot delete permission assigned to roles!');
        }

        $permission->delete();

        return redirect()->route('admin.permissions.index')
            ->with('success', "Permission deleted successfully!");
    }

    /**
     * Manage user roles
     */
    public function userRoles(Request $request)
    {
        $query = User::with('roles');
        
        // Search by name or email
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }
        
        $users = $query->orderBy('name')->paginate(15);

        $availableRoles = Role::orderBy('name')->get();

        return view('admin.rbac.user-roles.index', [
            'users' => $users,
            'availableRoles' => $availableRoles,
        ]);
    }

    /**
     * Show user role assignment form
     */
    public function editUserRoles(User $user)
    {
        $availableRoles = Role::orderBy('name')->get();
        $userRoles = $user->roles->pluck('id')->toArray();

        return view('admin.rbac.user-roles.edit', [
            'user' => $user,
            'availableRoles' => $availableRoles,
            'userRoles' => $userRoles,
        ]);
    }

    /**
     * Update user roles
     */
    public function updateUserRoles(Request $request, User $user)
    {
        $validated = $request->validate([
            'roles' => 'array',
            'roles.*' => 'exists:roles,id',
        ]);

        $user->roles()->sync($request->input('roles', []));

        return redirect()->route('admin.user-roles.index')
            ->with('success', "Roles for '{$user->name}' updated successfully!");
    }
}
