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

    <!-- Roles Table -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <table class="w-full">
            <thead class="bg-gray-100 border-b">
                <tr>
                    <th class="px-6 py-4 text-left text-sm font-semibold text-gray-900">Role Name</th>
                    <th class="px-6 py-4 text-left text-sm font-semibold text-gray-900">Description</th>
                    <th class="px-6 py-4 text-left text-sm font-semibold text-gray-900">Users</th>
                    <th class="px-6 py-4 text-left text-sm font-semibold text-gray-900">Permissions</th>
                    <th class="px-6 py-4 text-left text-sm font-semibold text-gray-900">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($roles as $role)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-6 py-4">
                        <div class="font-medium text-gray-900">{{ $role->name }}</div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="text-gray-600 text-sm">{{ $role->description ?? '-' }}</div>
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
                <tr>
                    <td colspan="5" class="px-6 py-8 text-center text-gray-500">
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
