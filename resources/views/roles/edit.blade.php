@extends('layouts.app')

@section('title', 'Edit Role')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow-sm">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="flex items-center gap-4">
                <a href="{{ route('roles.index') }}" class="text-primary-600 hover:text-primary-900">
                    <i class="fas fa-arrow-left mr-2"></i> Back
                </a>
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Edit Role</h1>
                    <p class="mt-2 text-sm text-gray-600">{{ $role->name }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Form -->
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="bg-white shadow-md rounded-lg p-6">
            <form action="{{ route('roles.update', $role) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <!-- Name -->
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700">Role Name</label>
                        <input type="text" id="name" name="name" value="{{ old('name', $role->name) }}" required
                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 @error('name') border-red-500 @enderror">
                        @error('name')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>

                    <!-- Slug -->
                    <div>
                        <label for="slug" class="block text-sm font-medium text-gray-700">Slug</label>
                        <input type="text" id="slug" name="slug" value="{{ old('slug', $role->slug) }}" required
                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 @error('slug') border-red-500 @enderror">
                        @error('slug')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>
                </div>

                <!-- Description -->
                <div class="mb-6">
                    <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                    <textarea id="description" name="description" rows="3"
                        class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 @error('description') border-red-500 @enderror">{{ old('description', $role->description) }}</textarea>
                    @error('description')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <!-- Active Status -->
                <div class="mb-6">
                    <label class="flex items-center">
                        <input type="checkbox" name="is_active" value="1"
                            @if(old('is_active', $role->is_active)) checked @endif
                            class="rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                        <span class="ml-2 text-sm font-medium text-gray-700">Active Role</span>
                    </label>
                </div>

                <!-- Permissions -->
                <div class="mb-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Permissions</h3>

                    @forelse ($permissionsByGroup as $group => $groupPermissions)
                        <fieldset class="mb-6 p-4 border border-gray-200 rounded-lg">
                            <legend class="text-sm font-semibold text-gray-900 px-2 mb-3 capitalize">{{ $group }}</legend>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                @foreach ($groupPermissions as $permission)
                                    <label class="flex items-center">
                                        <input type="checkbox" name="permissions[]" value="{{ $permission->id }}"
                                            @if(in_array($permission->id, old('permissions', $rolePermissions))) checked @endif
                                            class="rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                                        <span class="ml-2 text-sm text-gray-700">
                                            <strong>{{ $permission->name }}</strong>
                                            @if ($permission->description)
                                                <span class="text-gray-500 block text-xs mt-1">{{ $permission->description }}</span>
                                            @endif
                                        </span>
                                    </label>
                                @endforeach
                            </div>
                        </fieldset>
                    @empty
                        <p class="text-gray-500">No permissions available</p>
                    @endforelse
                    @error('permissions')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <!-- Buttons -->
                <div class="flex gap-2">
                    <button type="submit" class="px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700">
                        <i class="fas fa-save mr-2"></i> Save Changes
                    </button>
                    <a href="{{ route('roles.index') }}" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
