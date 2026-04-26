@extends('layouts.spa')

@section('title', 'Assign User Roles')

@section('content')
<div class="p-6">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Assign User Roles</h1>
        <p class="mt-2 text-gray-600">Manage user role assignments</p>
    </div>

    <!-- Alerts -->
    @if($message = session('success'))
    <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-4 rounded-lg">
        <i class="fas fa-check-circle mr-2"></i>{{ $message }}
    </div>
    @endif

    <!-- Search Bar -->
    <div class="mb-6">
        <form action="{{ route('admin.user-roles.index') }}" method="GET" class="flex gap-3">
            <input type="text" 
                   name="search" 
                   value="{{ request('search') }}" 
                   placeholder="Search by name or email..."
                   class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500">
            <button type="submit" class="bg-purple-600 hover:bg-purple-700 text-white px-6 py-2 rounded-lg transition">
                <i class="fas fa-search mr-2"></i>Search
            </button>
            @if(request('search'))
            <a href="{{ route('admin.user-roles.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-900 px-6 py-2 rounded-lg transition">
                <i class="fas fa-times mr-2"></i>Clear
            </a>
            @endif
        </form>
    </div>

    <!-- Users Table -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <table class="w-full">
            <thead class="bg-gray-100 border-b">
                <tr>
                    <th class="px-6 py-4 text-left text-sm font-semibold text-gray-900">User Name</th>
                    <th class="px-6 py-4 text-left text-sm font-semibold text-gray-900">Email</th>
                    <th class="px-6 py-4 text-left text-sm font-semibold text-gray-900">Roles</th>
                    <th class="px-6 py-4 text-left text-sm font-semibold text-gray-900">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($users as $user)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-6 py-4">
                        <div class="font-medium text-gray-900">{{ $user->name }}</div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="text-gray-600 text-sm">{{ $user->email }}</div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex flex-wrap gap-2">
                            @forelse($user->roles as $role)
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                {{ $role->name }}
                            </span>
                            @empty
                            <span class="text-gray-500 text-sm">No roles assigned</span>
                            @endforelse
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <a href="{{ route('admin.user-roles.edit', $user) }}" class="bg-purple-500 hover:bg-purple-600 text-white px-3 py-2 rounded transition text-sm">
                            <i class="fas fa-edit mr-1"></i>Assign Roles
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="px-6 py-8 text-center text-gray-500">
                        <i class="fas fa-inbox text-4xl mb-2 block opacity-50"></i>
                        No users found
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    @if($users->hasPages())
    <div class="mt-6">
        {{ $users->links() }}
    </div>
    @endif
</div>
@endsection
