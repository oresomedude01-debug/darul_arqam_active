@extends('layouts.spa')

@section('title', 'Edit Subject - ' . $subject->name)

@section('breadcrumb')
    <span class="text-gray-400">Academics</span>
    <span class="text-gray-400">/</span>
    <a href="{{ route('subjects.index') }}" class="text-primary-600 hover:text-primary-700">Subjects</a>
    <span class="text-gray-400">/</span>
    <a href="{{ route('subjects.show', $subject) }}" class="text-primary-600 hover:text-primary-700">{{ $subject->name }}</a>
    <span class="text-gray-400">/</span>
    <span class="font-semibold text-gray-900">Edit</span>
@endsection

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Edit Subject</h1>
            <p class="text-sm text-gray-600 mt-1">Update {{ $subject->name }}'s information</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('subjects.show', $subject) }}" class="btn btn-outline">
                <i class="fas fa-eye mr-2"></i>View Subject
            </a>
            <a href="{{ route('subjects.index') }}" class="btn btn-outline">
                <i class="fas fa-arrow-left mr-2"></i>Back to List
            </a>
        </div>
    </div>

    <form action="{{ route('subjects.update', $subject) }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')

        <!-- Basic Information -->
        <div class="card">
            <div class="card-header">
                <h3 class="text-lg font-semibold text-gray-900">
                    <i class="fas fa-info-circle mr-2 text-primary-600"></i>Basic Information
                </h3>
            </div>
            <div class="card-body space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="form-group">
                        <label class="form-label">Subject Name <span class="text-red-500">*</span></label>
                        <input
                            type="text"
                            name="name"
                            value="{{ old('name', $subject->name) }}"
                            placeholder="e.g., Mathematics, English Language"
                            class="form-input @error('name') border-red-500 @enderror"
                            required
                        >
                        @error('name')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label">Subject Code <span class="text-red-500">*</span></label>
                        <input
                            type="text"
                            name="code"
                            value="{{ old('code', $subject->code) }}"
                            placeholder="e.g., MATH, ENG, SCI"
                            class="form-input font-mono @error('code') border-red-500 @enderror"
                            required
                        >
                        <p class="text-xs text-gray-500 mt-1">Unique identifier for the subject</p>
                        @error('code')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label">Category</label>
                        <input
                            type="text"
                            name="category"
                            value="{{ old('category', $subject->category) }}"
                            placeholder="e.g., Core, Elective, Vocational"
                            class="form-input @error('category') border-red-500 @enderror"
                        >
                        <p class="text-xs text-gray-500 mt-1">Subject classification</p>
                        @error('category')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label">Color <span class="text-red-500">*</span></label>
                        <select name="color" class="form-select @error('color') border-red-500 @enderror" required>
                            <option value="blue" {{ old('color', $subject->color) === 'blue' ? 'selected' : '' }}>Blue</option>
                            <option value="green" {{ old('color', $subject->color) === 'green' ? 'selected' : '' }}>Green</option>
                            <option value="purple" {{ old('color', $subject->color) === 'purple' ? 'selected' : '' }}>Purple</option>
                            <option value="red" {{ old('color', $subject->color) === 'red' ? 'selected' : '' }}>Red</option>
                            <option value="yellow" {{ old('color', $subject->color) === 'yellow' ? 'selected' : '' }}>Yellow</option>
                            <option value="indigo" {{ old('color', $subject->color) === 'indigo' ? 'selected' : '' }}>Indigo</option>
                            <option value="pink" {{ old('color', $subject->color) === 'pink' ? 'selected' : '' }}>Pink</option>
                            <option value="teal" {{ old('color', $subject->color) === 'teal' ? 'selected' : '' }}>Teal</option>
                            <option value="orange" {{ old('color', $subject->color) === 'orange' ? 'selected' : '' }}>Orange</option>
                        </select>
                        <p class="text-xs text-gray-500 mt-1">Color for UI display</p>
                        @error('color')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Description</label>
                    <textarea
                        name="description"
                        rows="4"
                        class="form-textarea @error('description') border-red-500 @enderror"
                        placeholder="Brief description of the subject..."
                    >{{ old('description', $subject->description) }}</textarea>
                    @error('description')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="flex items-center">
                        <input
                            type="checkbox"
                            name="is_active"
                            value="1"
                            class="form-checkbox"
                            {{ old('is_active', $subject->is_active) ? 'checked' : '' }}
                        >
                        <span class="ml-2 text-sm text-gray-700">Active (available for assignment to classes)</span>
                    </label>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex items-center justify-between">
            <a href="{{ route('subjects.show', $subject) }}" class="btn btn-outline">
                <i class="fas fa-times mr-2"></i>Cancel
            </a>
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save mr-2"></i>Update Subject
            </button>
        </div>
    </form>
</div>
@endsection
