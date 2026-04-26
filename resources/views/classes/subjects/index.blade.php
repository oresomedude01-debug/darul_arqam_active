@extends('layouts.spa')

@section('title', 'Manage Subjects - ' . $class->full_name)

@section('breadcrumb')
    <span class="text-gray-400">Classes</span>
    <span class="text-gray-400">/</span>
    <a href="{{ route('classes.index') }}" class="text-primary-600 hover:text-primary-700">All Classes</a>
    <span class="text-gray-400">/</span>
    <a href="{{ route('classes.show', $class) }}" class="text-primary-600 hover:text-primary-700">{{ $class->full_name }}</a>
    <span class="text-gray-400">/</span>
    <span class="font-semibold text-gray-900">Manage Subjects</span>
@endsection

@section('content')
<div class="max-w-7xl mx-auto space-y-6" x-data="{ editingSubject: null }">
    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Manage Subjects</h1>
            <p class="text-sm text-gray-600 mt-1">{{ $class->full_name }}</p>
        </div>
        <a href="{{ route('classes.show', $class) }}" class="btn btn-outline">
            <i class="fas fa-arrow-left mr-2"></i>Back to Class
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Assigned Subjects List -->
        <div class="lg:col-span-2 space-y-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="text-lg font-semibold text-gray-900">
                        <i class="fas fa-book mr-2 text-primary-600"></i>Assigned Subjects ({{ $class->subjects->count() }})
                    </h3>
                </div>
                <div class="card-body">
                    @if($class->subjects->count() > 0)
                    <div class="space-y-4">
                        @foreach($class->subjects as $subject)
                        <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                            <!-- View Mode -->
                            <div x-show="editingSubject !== {{ $subject->id }}">
                                <div class="flex items-start justify-between mb-3">
                                    <div class="flex items-center flex-1">
                                        <div class="w-12 h-12 rounded-lg bg-{{ $subject->color }}-100 flex items-center justify-center mr-3">
                                            <i class="fas fa-book text-{{ $subject->color }}-600 text-lg"></i>
                                        </div>
                                        <div>
                                            <h4 class="font-semibold text-gray-900">{{ $subject->name }}</h4>
                                            <p class="text-sm text-gray-600 font-mono">{{ $subject->code }}</p>
                                        </div>
                                    </div>
                                    <span class="badge badge-{{ $subject->color }}">{{ $subject->pivot->periods_per_week }} periods/week</span>
                                </div>

                                <div class="flex items-center justify-between">
                                    <div class="text-sm text-gray-600">
                                        <i class="fas fa-chalkboard-teacher mr-1"></i>
                                        @php
                                            $teacher = null;
                                            if ($subject->pivot->teacher_id) {
                                                $teacher = \App\Models\UserProfile::find($subject->pivot->teacher_id);
                                            }
                                        @endphp
                                        {{ $teacher ? $teacher->first_name . ' ' . $teacher->last_name : 'No teacher assigned' }}
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <button type="button" @click="editingSubject = {{ $subject->id }}" class="btn btn-xs btn-primary">
                                            <i class="fas fa-edit mr-1"></i>Edit
                                        </button>
                                        <form action="{{ route('classes.subjects.destroy', [$class, $subject]) }}" method="POST" class="inline" onsubmit="return confirm('Remove {{ $subject->name }} from this class?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-xs btn-danger">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>

                            <!-- Edit Mode -->
                            <form x-show="editingSubject === {{ $subject->id }}"
                                  x-cloak
                                  action="{{ route('classes.subjects.update', [$class, $subject]) }}"
                                  method="POST"
                                  class="space-y-4">
                                @csrf
                                @method('PUT')

                                <div class="flex items-center mb-3">
                                    <div class="w-12 h-12 rounded-lg bg-{{ $subject->color }}-100 flex items-center justify-center mr-3">
                                        <i class="fas fa-book text-{{ $subject->color }}-600 text-lg"></i>
                                    </div>
                                    <div>
                                        <h4 class="font-semibold text-gray-900">{{ $subject->name }}</h4>
                                        <p class="text-sm text-gray-600 font-mono">{{ $subject->code }}</p>
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div class="form-group">
                                        <label class="form-label">Teacher</label>
                                        <select name="teacher_id" class="form-select">
                                            <option value="">No teacher</option>
                                            @foreach($teachers as $teacher)
                                                <option value="{{ $teacher->id }}" {{ old('teacher_id', $subject->pivot->teacher_id) == $teacher->id ? 'selected' : '' }}>
                                                    {{ $teacher->full_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label class="form-label">Periods per Week <span class="text-red-500">*</span></label>
                                        <input
                                            type="number"
                                            name="periods_per_week"
                                            value="{{ old('periods_per_week', $subject->pivot->periods_per_week) }}"
                                            min="1"
                                            max="20"
                                            class="form-input"
                                            required
                                        >
                                    </div>
                                </div>

                                <div class="flex items-center gap-2">
                                    <button type="submit" class="btn btn-sm btn-primary">
                                        <i class="fas fa-save mr-2"></i>Save Changes
                                    </button>
                                    <button type="button" @click="editingSubject = null" class="btn btn-sm btn-outline">
                                        Cancel
                                    </button>
                                </div>
                            </form>
                        </div>
                        @endforeach
                    </div>
                    @else
                    <div class="text-center py-12 text-gray-500">
                        <i class="fas fa-book text-6xl text-gray-300 mb-4"></i>
                        <p class="text-lg font-medium mb-2">No subjects assigned yet</p>
                        <p class="text-sm">Use the form on the right to add subjects to this class</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Add Subject Form -->
        <div class="space-y-6">
            <div class="card sticky top-6">
                <div class="card-header">
                    <h3 class="text-lg font-semibold text-gray-900">
                        <i class="fas fa-plus mr-2 text-primary-600"></i>Add Subject
                    </h3>
                </div>
                <div class="card-body">
                    @if($availableSubjects->count() > 0)
                    <form action="{{ route('classes.subjects.store', $class) }}" method="POST" class="space-y-4">
                        @csrf

                        <div class="form-group">
                            <label class="form-label">Subject <span class="text-red-500">*</span></label>
                            <select name="subject_id" class="form-select @error('subject_id') border-red-500 @enderror" required>
                                <option value="">Select a subject</option>
                                @foreach($availableSubjects as $subject)
                                    <option value="{{ $subject->id }}" {{ old('subject_id') == $subject->id ? 'selected' : '' }}>
                                        {{ $subject->name }} ({{ $subject->code }})
                                    </option>
                                @endforeach
                            </select>
                            @error('subject_id')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label class="form-label">Teacher</label>
                            <select name="teacher_id" class="form-select @error('teacher_id') border-red-500 @enderror">
                                <option value="">No teacher</option>
                                @foreach($teachers as $teacher)
                                    <option value="{{ $teacher->id }}" {{ old('teacher_id') == $teacher->id ? 'selected' : '' }}>
                                        {{ $teacher->full_name }}
                                    </option>
                                @endforeach
                            </select>
                            <p class="text-xs text-gray-500 mt-1">Teacher for this subject in this class</p>
                            @error('teacher_id')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label class="form-label">Periods per Week <span class="text-red-500">*</span></label>
                            <input
                                type="number"
                                name="periods_per_week"
                                value="{{ old('periods_per_week', 3) }}"
                                min="1"
                                max="20"
                                class="form-input @error('periods_per_week') border-red-500 @enderror"
                                required
                            >
                            <p class="text-xs text-gray-500 mt-1">Number of periods per week</p>
                            @error('periods_per_week')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <button type="submit" class="btn btn-primary w-full">
                            <i class="fas fa-plus mr-2"></i>Add Subject
                        </button>
                    </form>
                    @else
                    <div class="text-center py-8 text-gray-500">
                        <i class="fas fa-check-circle text-4xl text-green-300 mb-3"></i>
                        <p class="text-sm mb-2">All active subjects have been assigned!</p>
                        <a href="{{ route('subjects.create') }}" class="text-primary-600 hover:text-primary-700 text-sm">
                            <i class="fas fa-plus mr-1"></i>Create New Subject
                        </a>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Quick Stats -->
            <div class="card">
                <div class="card-header">
                    <h3 class="text-lg font-semibold text-gray-900">
                        <i class="fas fa-chart-bar mr-2 text-primary-600"></i>Quick Stats
                    </h3>
                </div>
                <div class="card-body space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-gray-600">Assigned Subjects</span>
                        <span class="text-2xl font-bold text-primary-600">{{ $class->subjects->count() }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-gray-600">Total Periods/Week</span>
                        <span class="text-2xl font-bold text-primary-600">{{ $class->subjects->sum('pivot.periods_per_week') }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-gray-600">Available Subjects</span>
                        <span class="text-2xl font-bold text-gray-600">{{ $availableSubjects->count() }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    [x-cloak] { display: none !important; }
</style>
@endsection
