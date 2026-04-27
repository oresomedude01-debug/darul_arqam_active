@extends('layouts.spa')

@section('title', 'Manage Roles')

@section('content')
<div class="p-6">
    <!-- Header -->
    <div class="mb-8 flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Manage Roles</h1>
            <p class="mt-2 text-gray-600">Create and manage system roles</p>
        </div>
        <a href="{{ route('admin.roles.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-3 px-6 rounded-lg transition">
            <i class="fas fa-plus mr-2"></i>Create New Role
        </a>
    </div>

    <!-- Alerts -->
    @if($message = session('success'))
    <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-4 rounded-lg">
        <i class="fas fa-check-circle mr-2"></i>{{ $message }}
    </div>
    @endif

    @if($message = session('error'))
    <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-4 rounded-lg">
        <i class="fas fa-exclamation-circle mr-2"></i>{{ $message }}
    </div>
    @endif

    <!-- Roles Table/Grid -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <table class="w-full block md:table">
            <thead class="bg-gray-100 border-b hidden md:table-header-group">
                <tr class="block md:table-row">
                    <th class="px-6 py-4 text-left text-sm font-semibold text-gray-900 block md:table-cell">Role Name</th>
                    <th class="px-6 py-4 text-left text-sm font-semibold text-gray-900 block md:table-cell">Description</th>
                    <th class="px-6 py-4 text-left text-sm font-semibold text-gray-900 block md:table-cell">Users</th>
                    <th class="px-6 py-4 text-left text-sm font-semibold text-gray-900 block md:table-cell">Permissions</th>
                    <th class="px-6 py-4 text-left text-sm font-semibold text-gray-900 block md:table-cell">Actions</th>
                </tr>
            </thead>
            <tbody class="block md:table-row-group divide-y md:divide-y-0 divide-gray-200">
                @forelse($roles as $role)
                <tr class="block md:table-row hover:bg-gray-50 transition p-4 md:p-0 border-b md:border-b-0 last:border-b-0">
                    <td class="flex flex-col md:table-cell px-2 py-2 md:px-6 md:py-4 border-b md:border-b border-gray-100 md:border-gray-200">
                        <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1 md:hidden">Role Name</span>
                        <div class="font-medium text-gray-900">{{ $role->name }}</div>
                    </td>
                    <td class="flex flex-col md:table-cell px-2 py-2 md:px-6 md:py-4 border-b md:border-b border-gray-100 md:border-gray-200">
                        <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1 md:hidden">Description</span>
                        <div class="text-gray-600 text-sm">{{ $role->description ?? '-' }}</div>
                    </td>
                    <td class="flex flex-col md:table-cell px-2 py-2 md:px-6 md:py-4 border-b md:border-b border-gray-100 md:border-gray-200">
                        <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1 md:hidden">Users</span>
                        <div>
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                                {{ $role->users_count }}
                            </span>
                        </div>
                    </td>
                    <td class="flex flex-col md:table-cell px-2 py-2 md:px-6 md:py-4 border-b md:border-b border-gray-100 md:border-gray-200">
                        <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1 md:hidden">Permissions</span>
                        <div>
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                {{ $role->permissions_count }}
                            </span>
                        </div>
                    </td>
                    <td class="flex flex-col md:table-cell px-2 py-3 md:px-6 md:py-4 md:border-b md:border-gray-200">
                        <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2 md:hidden">Actions</span>
                        <div class="flex gap-2">
                            <a href="{{ route('admin.roles.edit', $role) }}" class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-2 rounded transition text-sm">
                                <i class="fas fa-edit mr-1"></i>Edit
                            </a>
                            @if(!$role->users()->exists())
                            <form action="{{ route('admin.roles.destroy', $role) }}" method="POST" class="inline" onsubmit="return confirm('Delete this role?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="bg-red-500 hover:bg-red-600 text-white px-3 py-2 rounded transition text-sm">
                                    <i class="fas fa-trash mr-1"></i>Delete
                                </button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr class="block md:table-row">
                    <td colspan="5" class="block md:table-cell px-6 py-8 text-center text-gray-500">
                        <i class="fas fa-inbox text-4xl mb-2 block opacity-50"></i>
                        No roles found
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    @if($roles->hasPages())
    <div class="mt-6">
        {{ $roles->links() }}
    </div>
    @endif
</div>
@endsection
