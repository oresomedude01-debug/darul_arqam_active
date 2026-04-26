@extends('layouts.spa')

@section('title', 'Add Subject')

@section('breadcrumb')
    <span class="text-gray-400">Academics</span>
    <span class="text-gray-400">/</span>
    <a href="{{ route('subjects.index') }}" class="text-primary-600 hover:text-primary-700">Subjects</a>
    <span class="text-gray-400">/</span>
    <span class="font-semibold text-gray-900">Add New</span>
@endsection

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Page Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Add New Subject</h1>
            <p class="text-sm text-gray-600 mt-1">Create a new academic subject</p>
        </div>
        <a href="{{ route('subjects.index') }}" class="btn btn-outline">
            <i class="fas fa-arrow-left mr-2"></i>Back to List
        </a>
    </div>

    <form action="{{ route('subjects.store') }}" method="POST" class="space-y-6">
        @csrf

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
                            value="{{ old('name') }}"
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
                            value="{{ old('code') }}"
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
                            value="{{ old('category') }}"
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
                            <option value="blue" {{ old('color', 'blue') === 'blue' ? 'selected' : '' }}>Blue</option>
                            <option value="green" {{ old('color') === 'green' ? 'selected' : '' }}>Green</option>
                            <option value="purple" {{ old('color') === 'purple' ? 'selected' : '' }}>Purple</option>
                            <option value="red" {{ old('color') === 'red' ? 'selected' : '' }}>Red</option>
                            <option value="yellow" {{ old('color') === 'yellow' ? 'selected' : '' }}>Yellow</option>
                            <option value="indigo" {{ old('color') === 'indigo' ? 'selected' : '' }}>Indigo</option>
                            <option value="pink" {{ old('color') === 'pink' ? 'selected' : '' }}>Pink</option>
                            <option value="teal" {{ old('color') === 'teal' ? 'selected' : '' }}>Teal</option>
                            <option value="orange" {{ old('color') === 'orange' ? 'selected' : '' }}>Orange</option>
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
                    >{{ old('description') }}</textarea>
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
                            {{ old('is_active', true) ? 'checked' : '' }}
                        >
                        <span class="ml-2 text-sm text-gray-700">Active (available for assignment to classes)</span>
                    </label>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex items-center justify-between">
            <a href="{{ route('subjects.index') }}" class="btn btn-outline">
                <i class="fas fa-times mr-2"></i>Cancel
            </a>
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save mr-2"></i>Create Subject
            </button>
        </div>
    </form>
</div>
@endsection
