@extends('layouts.spa')

@section('title', 'Create New Role')

@section('content')
<div class="p-6">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Create New Role</h1>
        <p class="mt-2 text-gray-600">Define a new role with permissions</p>
    </div>

    <!-- Form -->
    <div class="max-w-2xl">
        <form action="{{ route('admin.roles.store') }}" method="POST" class="bg-white rounded-lg shadow-md p-8">
            @csrf

            <!-- Role Name -->
            <div class="mb-6">
                <label for="name" class="block text-sm font-semibold text-gray-900 mb-2">
                    Role Name <span class="text-red-600">*</span>
                </label>
                <input type="text"
                       id="name"
                       name="name"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500 @error('name') border-red-500 @enderror"
                       value="{{ old('name') }}"
                       placeholder="e.g., Superadmin, Editor, Moderator"
                       required>
                @error('name')
                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Description -->
            <div class="mb-6">
                <label for="description" class="block text-sm font-semibold text-gray-900 mb-2">
                    Description
                </label>
                <textarea id="description"
                          name="description"
                          rows="4"
                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500"
                          placeholder="Describe the purpose of this role">{{ old('description') }}</textarea>
            </div>

            <!-- Permissions -->
            <div class="mb-6">
                <label class="block text-sm font-semibold text-gray-900 mb-4">
                    Assign Permissions
                </label>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 border border-gray-300 rounded-lg p-4 bg-gray-50">
                    @if($permissions->isEmpty())
                    <p class="col-span-2 text-gray-500 py-4">
                        No permissions available. <a href="{{ route('admin.permissions.create') }}" class="text-blue-600 hover:underline">Create one first</a>.
                    </p>
                    @else
                    @foreach($permissions as $permission)
                    <label class="flex items-center space-x-3 cursor-pointer hover:bg-white p-2 rounded transition">
                        <input type="checkbox"
                               name="permissions[]"
                               value="{{ $permission->id }}"
                               {{ in_array($permission->id, old('permissions', [])) ? 'checked' : '' }}
                               class="w-4 h-4 text-blue-600 rounded">
                        <div>
                            <p class="font-medium text-gray-900">{{ $permission->name }}</p>
                            @if($permission->description)
                            <p class="text-xs text-gray-500">{{ $permission->description }}</p>
                            @endif
                        </div>
                    </label>
                    @endforeach
                    @endif
                </div>
            </div>

            <!-- Actions -->
            <div class="flex gap-3">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-6 rounded-lg transition">
                    <i class="fas fa-save mr-2"></i>Create Role
                </button>
                <a href="{{ route('admin.roles.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-900 font-medium py-2 px-6 rounded-lg transition">
                    <i class="fas fa-times mr-2"></i>Cancel
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
