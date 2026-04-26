@extends('layouts.app')

@section('title', 'Role Management')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Role Management</h1>
                    <p class="mt-2 text-sm text-gray-600">Manage system roles and their permissions</p>
                </div>
                <a href="{{ route('roles.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-primary-600 hover:bg-primary-700">
                    <i class="fas fa-plus mr-2"></i> New Role
                </a>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        @if (session('success'))
            <div class="mb-4 p-4 bg-green-50 border border-green-200 rounded-lg text-green-700">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-lg text-red-700">
                {{ session('error') }}
            </div>
        @endif

        <!-- Search Form -->
        <div class="mb-6">
            <form method="GET" action="{{ route('roles.index') }}" class="flex gap-4">
                <input
                    type="text"
                    name="search"
                    placeholder="Search roles..."
                    value="{{ request('search') }}"
                    class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                >
                <button type="submit" class="px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700">
                    <i class="fas fa-search mr-2"></i> Search
                </button>
                @if (request('search'))
                    <a href="{{ route('roles.index') }}" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400">
                        Clear
                    </a>
                @endif
            </form>
        </div>

        <!-- Roles Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse ($roles as $role)
                <div class="bg-white shadow-md rounded-lg p-6">
                    <div class="flex justify-between items-start mb-4">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">{{ $role->name }}</h3>
                            <p class="text-sm text-gray-500 mt-1">{{ $role->slug }}</p>
                        </div>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                            :class="{'bg-green-100 text-green-800': {{ $role->is_active ? 'true' : 'false' }}, 'bg-red-100 text-red-800': {{ $role->is_active ? 'false' : 'true' }}}">
                            {{ $role->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </div>

                    @if ($role->description)
                        <p class="text-sm text-gray-600 mb-4">{{ $role->description }}</p>
                    @endif

                    <div class="mb-4">
                        <p class="text-xs font-medium text-gray-700 mb-2">Permissions ({{ $role->permissions()->count() }})</p>
                        <div class="flex flex-wrap gap-1">
                            @forelse ($role->permissions()->limit(3)->get() as $permission)
                                <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-gray-100 text-gray-700">
                                    {{ $permission->slug }}
                                </span>
                            @empty
                                <span class="text-xs text-gray-500">No permissions</span>
                            @endforelse
                            @if ($role->permissions()->count() > 3)
                                <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-gray-100 text-gray-700">
                                    +{{ $role->permissions()->count() - 3 }} more
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="mb-4 text-xs text-gray-600">
                        <i class="fas fa-users mr-1"></i> {{ $role->users_count }} users
                    </div>

                    <div class="flex gap-2">
                        <a href="{{ route('roles.edit', $role) }}" class="flex-1 text-center px-3 py-2 bg-primary-100 text-primary-700 rounded hover:bg-primary-200 text-sm font-medium">
                            <i class="fas fa-edit mr-1"></i> Edit
                        </a>
                        <form method="POST" action="{{ route('roles.destroy', $role) }}" style="flex: 1;" onsubmit="return confirm('Are you sure?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="w-full px-3 py-2 bg-red-100 text-red-700 rounded hover:bg-red-200 text-sm font-medium">
                                <i class="fas fa-trash mr-1"></i> Delete
                            </button>
                        </form>
                    </div>
                </div>
            @empty
                <div class="col-span-3 text-center py-12">
                    <i class="fas fa-inbox text-4xl text-gray-400 mb-4"></i>
                    <p class="text-gray-500">No roles found</p>
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        <div class="mt-6">
            {{ $roles->links() }}
        </div>
    </div>
</div>
@endsection
