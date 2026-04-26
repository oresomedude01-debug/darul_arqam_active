@extends('layouts.spa')

@section('title', 'Assign Roles to ' . $user->name)

@section('content')
<div class="p-6">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Assign Roles to {{ $user->name }}</h1>
        <p class="mt-2 text-gray-600">{{ $user->email }}</p>
    </div>

    <!-- Form -->
    <div class="max-w-2xl">
        <form action="{{ route('admin.user-roles.update', $user) }}" method="POST" class="bg-white rounded-lg shadow-md p-8">
            @csrf
            @method('PUT')

            <!-- User Info (Read-only) -->
            <div class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm text-gray-600">Full Name</p>
                        <p class="font-semibold text-gray-900">{{ $user->name }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Email</p>
                        <p class="font-semibold text-gray-900">{{ $user->email }}</p>
                    </div>
                </div>
            </div>

            <!-- Roles -->
            <div class="mb-6">
                <label class="block text-sm font-semibold text-gray-900 mb-4">
                    Assign Roles
                </label>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 border border-gray-300 rounded-lg p-4 bg-gray-50">
                    @if($availableRoles->isEmpty())
                    <p class="col-span-2 text-gray-500 py-4">
                        No roles available. <a href="{{ route('admin.roles.create') }}" class="text-purple-600 hover:underline">Create one first</a>.
                    </p>
                    @else
                    @foreach($availableRoles as $role)
                    <label class="flex items-center space-x-3 cursor-pointer hover:bg-white p-2 rounded transition">
                        <input type="checkbox"
                               name="roles[]"
                               value="{{ $role->id }}"
                               {{ in_array($role->id, $userRoles) ? 'checked' : '' }}
                               class="w-4 h-4 text-purple-600 rounded">
                        <div>
                            <p class="font-medium text-gray-900">{{ $role->name }}</p>
                            @if($role->description)
                            <p class="text-xs text-gray-500">{{ $role->description }}</p>
                            @endif
                        </div>
                    </label>
                    @endforeach
                    @endif
                </div>
            </div>

            <!-- Current Roles Info -->
            <div class="mb-6 p-4 bg-purple-50 border border-purple-200 rounded-lg">
                <p class="text-sm text-gray-700">
                    <strong>{{ $user->roles->count() }}</strong> role(s) currently assigned.
                </p>
            </div>

            <!-- Actions -->
            <div class="flex gap-3">
                <button type="submit" class="bg-purple-600 hover:bg-purple-700 text-white font-medium py-2 px-6 rounded-lg transition">
                    <i class="fas fa-save mr-2"></i>Update Roles
                </button>
                <a href="{{ route('admin.user-roles.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-900 font-medium py-2 px-6 rounded-lg transition">
                    <i class="fas fa-times mr-2"></i>Cancel
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
