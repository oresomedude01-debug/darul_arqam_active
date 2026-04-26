@extends('layouts.spa')

@section('title', 'Role & Permission Management')

@section('content')
<div class="p-6">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Role & Permission Management</h1>
        <p class="mt-2 text-gray-600">Manage roles, permissions, and user access control</p>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Total Roles -->
        <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-blue-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm font-medium">Total Roles</p>
                    <p class="text-3xl font-bold text-gray-900 mt-2">{{ $totalRoles }}</p>
                </div>
                <div class="bg-blue-100 rounded-full p-4">
                    <i class="fas fa-user-tag text-2xl text-blue-600"></i>
                </div>
            </div>
        </div>

        <!-- Total Permissions -->
        <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-green-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm font-medium">Total Permissions</p>
                    <p class="text-3xl font-bold text-gray-900 mt-2">{{ $totalPermissions }}</p>
                </div>
                <div class="bg-green-100 rounded-full p-4">
                    <i class="fas fa-key text-2xl text-green-600"></i>
                </div>
            </div>
        </div>

        <!-- Users with Roles -->
        <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-purple-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm font-medium">Users with Roles</p>
                    <p class="text-3xl font-bold text-gray-900 mt-2">{{ $totalUsersWithRoles }}</p>
                </div>
                <div class="bg-purple-100 rounded-full p-4">
                    <i class="fas fa-users text-2xl text-purple-600"></i>
                </div>
            </div>
        </div>

        <!-- Average Permissions per Role -->
        <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-orange-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm font-medium">Avg Permissions/Role</p>
                    <p class="text-3xl font-bold text-gray-900 mt-2">
                        {{ $totalRoles > 0 ? round($totalPermissions / $totalRoles, 1) : 0 }}
                    </p>
                </div>
                <div class="bg-orange-100 rounded-full p-4">
                    <i class="fas fa-chart-bar text-2xl text-orange-600"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Management Sections -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        <!-- Roles Management -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition">
            <div class="bg-gradient-to-r from-blue-500 to-blue-600 px-6 py-8 text-white">
                <i class="fas fa-user-tag text-4xl mb-4"></i>
                <h3 class="text-xl font-bold">Manage Roles</h3>
                <p class="mt-2 text-blue-100 text-sm">Create, edit, and manage system roles</p>
            </div>
            <div class="p-6">
                <div class="flex gap-3">
                    <a href="{{ route('admin.roles.index') }}" class="flex-1 bg-blue-500 hover:bg-blue-600 text-white font-medium py-2 px-4 rounded-lg transition text-center">
                        <i class="fas fa-list mr-2"></i>View All
                    </a>
                    <a href="{{ route('admin.roles.create') }}" class="flex-1 bg-blue-100 hover:bg-blue-200 text-blue-600 font-medium py-2 px-4 rounded-lg transition text-center">
                        <i class="fas fa-plus mr-2"></i>Create
                    </a>
                </div>
            </div>
        </div>

        <!-- Permissions Management -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition">
            <div class="bg-gradient-to-r from-green-500 to-green-600 px-6 py-8 text-white">
                <i class="fas fa-key text-4xl mb-4"></i>
                <h3 class="text-xl font-bold">Manage Permissions</h3>
                <p class="mt-2 text-green-100 text-sm">Create, edit, and manage permissions</p>
            </div>
            <div class="p-6">
                <div class="flex gap-3">
                    <a href="{{ route('admin.permissions.index') }}" class="flex-1 bg-green-500 hover:bg-green-600 text-white font-medium py-2 px-4 rounded-lg transition text-center">
                        <i class="fas fa-list mr-2"></i>View All
                    </a>
                    <a href="{{ route('admin.permissions.create') }}" class="flex-1 bg-green-100 hover:bg-green-200 text-green-600 font-medium py-2 px-4 rounded-lg transition text-center">
                        <i class="fas fa-plus mr-2"></i>Create
                    </a>
                </div>
            </div>
        </div>

        <!-- User Roles Assignment -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition">
            <div class="bg-gradient-to-r from-purple-500 to-purple-600 px-6 py-8 text-white">
                <i class="fas fa-users text-4xl mb-4"></i>
                <h3 class="text-xl font-bold">Assign User Roles</h3>
                <p class="mt-2 text-purple-100 text-sm">Assign roles to users and manage access</p>
            </div>
            <div class="p-6">
                <a href="{{ route('admin.user-roles.index') }}" class="w-full bg-purple-500 hover:bg-purple-600 text-white font-medium py-2 px-4 rounded-lg transition text-center block">
                    <i class="fas fa-edit mr-2"></i>Manage Users
                </a>
            </div>
        </div>
    </div>

    <!-- Roles Overview Table -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
            <h3 class="text-lg font-semibold text-gray-900">Roles Overview</h3>
            <a href="{{ route('admin.roles.create') }}" class="bg-blue-500 hover:bg-blue-600 text-white font-medium py-2 px-4 rounded-lg transition">
                <i class="fas fa-plus mr-2"></i>New Role
            </a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Role Name</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Users</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Permissions</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($rolesWithPermissions as $role)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">
                            <span class="font-medium text-gray-900">{{ $role->name }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                                {{ $role->users_count }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                {{ $role->permissions_count }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <a href="{{ route('admin.roles.edit', $role) }}" class="text-blue-600 hover:text-blue-900 font-medium text-sm">
                                <i class="fas fa-edit mr-2"></i>Edit
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-6 py-8 text-center text-gray-500">
                            <i class="fas fa-inbox text-3xl mb-2 block opacity-50"></i>
                            No roles found
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
