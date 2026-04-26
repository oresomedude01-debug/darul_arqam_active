@extends('layouts.spa')

@section('title', 'Edit Permission: ' . $permission->name)

@section('content')
<div class="p-6">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Edit Permission: {{ $permission->name }}</h1>
        <p class="mt-2 text-gray-600">Update permission details</p>
    </div>

    <!-- Form -->
    <div class="max-w-2xl">
        <form action="{{ route('admin.permissions.update', $permission) }}" method="POST" class="bg-white rounded-lg shadow-md p-8">
            @csrf
            @method('PUT')

            <!-- Permission Name (Read-only) -->
            <div class="mb-6">
                <label for="name" class="block text-sm font-semibold text-gray-900 mb-2">
                    Permission Name
                </label>
                <input type="text"
                       id="name"
                       value="{{ $permission->name }}"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-100 text-gray-600"
                       disabled>
                <p class="text-gray-500 text-xs mt-1">Permission name cannot be changed</p>
            </div>

            <!-- Description -->
            <div class="mb-6">
                <label for="description" class="block text-sm font-semibold text-gray-900 mb-2">
                    Description
                </label>
                <textarea id="description"
                          name="description"
                          rows="4"
                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-green-500">{{ old('description', $permission->description) }}</textarea>
            </div>

            <!-- Actions -->
            <div class="flex gap-3">
                <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-medium py-2 px-6 rounded-lg transition">
                    <i class="fas fa-save mr-2"></i>Update Permission
                </button>
                <a href="{{ route('admin.permissions.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-900 font-medium py-2 px-6 rounded-lg transition">
                    <i class="fas fa-times mr-2"></i>Cancel
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
