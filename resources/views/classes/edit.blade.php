@extends('layouts.spa')

@section('title', 'Edit Class - ' . $class->full_name)

@section('breadcrumb')
    <span class="text-gray-400">Classes</span>
    <span class="text-gray-400">/</span>
    <a href="{{ route('classes.index') }}" class="text-primary-600 hover:text-primary-700">All Classes</a>
    <span class="text-gray-400">/</span>
    <a href="{{ route('classes.show', $class) }}" class="text-primary-600 hover:text-primary-700">{{ $class->full_name }}</a>
    <span class="text-gray-400">/</span>
    <span class="font-semibold text-gray-900">Edit</span>
@endsection

@section('content')
<div class="max-w-5xl mx-auto space-y-6">
    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Edit Class</h1>
            <p class="text-sm text-gray-600 mt-1">Update {{ $class->full_name }}'s information</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('classes.show', $class) }}" class="btn btn-outline">
                <i class="fas fa-eye mr-2"></i>View Profile
            </a>
            <a href="{{ route('classes.index') }}" class="btn btn-outline">
                <i class="fas fa-arrow-left mr-2"></i>Back to List
            </a>
        </div>
    </div>

    <form action="{{ route('classes.update', $class) }}" method="POST" class="space-y-6">
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
                        <label class="form-label">Class Name <span class="text-red-500">*</span></label>
                        <input
                            type="text"
                            name="name"
                            value="{{ old('name', $class->name) }}"
                            placeholder="e.g., Primary 1, JSS 2, SSS 3"
                            class="form-input @error('name') border-red-500 @enderror"
                            required
                        >
                        @error('name')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label">Section</label>
                        <input
                            type="text"
                            name="section"
                            value="{{ old('section', $class->section) }}"
                            placeholder="e.g., A, B, Gold, Diamond"
                            class="form-input @error('section') border-red-500 @enderror"
                        >
                        <p class="text-xs text-gray-500 mt-1">Optional - for dividing classes into sections</p>
                        @error('section')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label">Class Code <span class="text-red-500">*</span></label>
                        <input
                            type="text"
                            name="class_code"
                            value="{{ old('class_code', $class->class_code) }}"
                            placeholder="e.g., PRI1-A, JSS2-B, SSS3-GOLD"
                            class="form-input font-mono @error('class_code') border-red-500 @enderror"
                            required
                        >
                        <p class="text-xs text-gray-500 mt-1">Unique identifier for the class</p>
                        @error('class_code')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label">Status <span class="text-red-500">*</span></label>
                        <select name="status" class="form-select @error('status') border-red-500 @enderror" required>
                            <option value="active" {{ old('status', $class->status) === 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ old('status', $class->status) === 'inactive' ? 'selected' : '' }}>Inactive</option>
                            <option value="archived" {{ old('status', $class->status) === 'archived' ? 'selected' : '' }}>Archived</option>
                        </select>
                        @error('status')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label">Room Number</label>
                        <input
                            type="text"
                            name="room_number"
                            value="{{ old('room_number', $class->room_number) }}"
                            placeholder="e.g., Room 101, Block A-12"
                            class="form-input @error('room_number') border-red-500 @enderror"
                        >
                        @error('room_number')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <!-- Class Teacher & Capacity -->
        <div class="card">
            <div class="card-header">
                <h3 class="text-lg font-semibold text-gray-900">
                    <i class="fas fa-user-tie mr-2 text-primary-600"></i>Teacher & Capacity
                </h3>
            </div>
            <div class="card-body space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="form-group">
                        <label class="form-label">Class Teacher</label>
                        <select
                            name="teacher_id"
                            class="form-select @error('teacher_id') border-red-500 @enderror"
                        >
                            <option value="">-- Select Teacher --</option>
                            @if($teachers->count() > 0)
                                @foreach($teachers as $teacher)
                                <option 
                                    value="{{ $teacher->user_id }}"
                                    {{ old('teacher_id', $class->teacher_id) == $teacher->user_id ? 'selected' : '' }}
                                >
                                    {{ $teacher->first_name }} {{ $teacher->last_name }}
                                    @php $subjects = $teacher->getAssignedSubjects(); @endphp
                                    @if(!empty($subjects)) - {{ implode(', ', $subjects) }} @endif
                                </option>
                                @endforeach
                            @endif
                        </select>
                        <p class="text-xs text-gray-500 mt-2">Select the primary teacher responsible for managing this class</p>
                        @error('teacher_id')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label">Capacity <span class="text-red-500">*</span></label>
                        <input
                            type="number"
                            name="capacity"
                            value="{{ old('capacity', $class->capacity) }}"
                            min="1"
                            max="100"
                            class="form-input @error('capacity') border-red-500 @enderror"
                            required
                        >
                        <p class="text-xs text-gray-500 mt-1">Maximum number of students</p>
                        @error('capacity')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <!-- Additional Information -->
        <div class="card">
            <div class="card-header">
                <h3 class="text-lg font-semibold text-gray-900">
                    <i class="fas fa-file-alt mr-2 text-primary-600"></i>Additional Information
                </h3>
            </div>
            <div class="card-body">
                <div class="form-group">
                    <label class="form-label">Description / Notes</label>
                    <textarea
                        name="description"
                        rows="4"
                        class="form-textarea @error('description') border-red-500 @enderror"
                        placeholder="Any additional notes about this class..."
                    >{{ old('description', $class->description) }}</textarea>
                    @error('description')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex items-center justify-between">
            <a href="{{ route('classes.show', $class) }}" class="btn btn-outline">
                <i class="fas fa-times mr-2"></i>Cancel
            </a>
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save mr-2"></i>Update Class
            </button>
        </div>
    </form>
</div>
@endsection
