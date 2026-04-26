@extends('layouts.spa')

@section('title', 'Assign Classes & Subjects - ' . $userProfile->first_name . ' ' . $userProfile->last_name)

@section('breadcrumb')
    <span class="text-gray-400">Teachers</span>
    <span class="text-gray-400">/</span>
    <a href="{{ route('teachers.index') }}" class="text-primary-600 hover:text-primary-700">All Teachers</a>
    <span class="text-gray-400">/</span>
    <a href="{{ route('teachers.show', $userProfile) }}" class="text-primary-600 hover:text-primary-700">{{ $userProfile->first_name }} {{ $userProfile->last_name }}</a>
    <span class="text-gray-400">/</span>
    <span class="font-semibold text-gray-900">Assign</span>
@endsection

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Page Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Assign Classes & Subjects</h1>
            <p class="text-sm text-gray-600 mt-1">Update teaching assignments for {{ $userProfile->first_name }} {{ $userProfile->last_name }}</p>
        </div>
        <a href="{{ route('teachers.show', $userProfile) }}" class="btn btn-outline">
            <i class="fas fa-arrow-left mr-2"></i>Back to Profile
        </a>
    </div>

    <!-- Teacher Info Card -->
    <div class="card bg-gradient-to-r from-primary-50 to-blue-50 border-l-4 border-primary-500">
        <div class="card-body">
            <div class="flex items-center space-x-4">
                <div class="flex-shrink-0">
                    @if($userProfile->profile_picture)
                        <img src="{{ asset('storage/' . $userProfile->profile_picture) }}" alt="{{ $userProfile->first_name }} {{ $userProfile->last_name }}" class="w-16 h-16 rounded-full object-cover">
                    @else
                        <div class="w-16 h-16 rounded-full bg-gradient-to-br from-primary-500 to-primary-600 flex items-center justify-center text-white font-bold text-xl">
                            {{ substr($userProfile->first_name, 0, 1) }}{{ substr($userProfile->last_name, 0, 1) }}
                        </div>
                    @endif
                </div>
                <div class="flex-1">
                    <h3 class="text-lg font-semibold text-gray-900">{{ $userProfile->first_name }} {{ $userProfile->last_name }}</h3>
                    <p class="text-gray-700 text-sm">{{ $userProfile->user->email }}</p>
                </div>
            </div>
        </div>
    </div>

        <form action="{{ route('teachers.update-assignments', $userProfile) }}" method="POST" class="space-y-6">
        @csrf

        <!-- Subjects Assignment -->
        <div class="card">
            <div class="card-header">
                <h3 class="text-lg font-semibold text-gray-900">
                    <i class="fas fa-book mr-2 text-primary-600"></i>Subjects
                </h3>
                <p class="text-sm text-gray-600 mt-1">Select the subjects this teacher will teach</p>
            </div>
            <div class="card-body">
                <div x-data="{
                    showAllSubjects: {{ count($allSubjects) <= 12 ? 'true' : 'false' }},
                    showInput: false,
                    newSubject: '',
                    selectedCount: {{ count($userProfile->subjects ?? []) }}
                }">
                    <div class="space-y-4">
                        <!-- Selected Count -->
                        <div class="flex items-center justify-between p-3 bg-primary-50 rounded-lg">
                            <span class="text-sm font-semibold text-gray-700">Selected Subjects:</span>
                            <span class="text-lg font-bold text-primary-600" x-text="selectedCount"></span>
                        </div>

                        <!-- Subjects Grid -->
                        <div class="flex flex-wrap gap-2" id="subjectsContainer">
                            @foreach($allSubjects as $subject)
                                <label class="inline-flex items-center px-4 py-2 bg-white hover:bg-primary-50 border-2 rounded-lg cursor-pointer transition-all duration-200"
                                       :class="$el.querySelector('input').checked ? 'border-primary-500 bg-primary-50' : 'border-gray-200'">
                                    <input
                                        type="checkbox"
                                        name="subjects[]"
                                        value="{{ $subject }}"
                                        class="form-checkbox text-primary-600 mr-2"
                                        {{ in_array($subject, old('subjects', $userProfile->subjects ?? [])) ? 'checked' : '' }}
                                        @change="selectedCount = document.querySelectorAll('input[name=\'subjects[]\']:checked').length"
                                    >
                                    <span class="text-sm font-medium">{{ $subject }}</span>
                                </label>
                            @endforeach
                        </div>

                        @error('subjects')
                            <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <!-- Classes Assignment -->
        <div class="card">
            <div class="card-header">
                <h3 class="text-lg font-semibold text-gray-900">
                    <i class="fas fa-users-class mr-2 text-primary-600"></i>Classes
                </h3>
                <p class="text-sm text-gray-600 mt-1">Select the classes this teacher will handle</p>
            </div>
            <div class="card-body">
                <div x-data="{
                    showInput: false,
                    newClass: '',
                    selectedCount: {{ count($userProfile->classes ?? []) }}
                }">
                    <div class="space-y-4">
                        <!-- Selected Count -->
                        <div class="flex items-center justify-between p-3 bg-primary-50 rounded-lg">
                            <span class="text-sm font-semibold text-gray-700">Selected Classes:</span>
                            <span class="text-lg font-bold text-primary-600" x-text="selectedCount"></span>
                        </div>

                        <!-- Classes Grid -->
                        <div class="flex flex-wrap gap-2" id="classesContainer">
                            @foreach($allClasses as $class)
                                <label class="inline-flex items-center px-4 py-2 bg-white hover:bg-primary-50 border-2 rounded-lg cursor-pointer transition-all duration-200"
                                       :class="$el.querySelector('input').checked ? 'border-primary-500 bg-primary-50' : 'border-gray-200'">
                                    <input
                                        type="checkbox"
                                        name="classes[]"
                                        value="{{ $class }}"
                                        class="form-checkbox text-primary-600 mr-2"
                                        {{ in_array($class, old('classes', $userProfile->classes ?? [])) ? 'checked' : '' }}
                                        @change="selectedCount = document.querySelectorAll('input[name=\'classes[]\']:checked').length"
                                    >
                                    <span class="text-sm font-medium">{{ $class }}</span>
                                </label>
                            @endforeach
                        </div>

                        @error('classes')
                            <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex items-center justify-between">
            <a href="{{ route('teachers.show', $userProfile->id) }}" class="btn btn-outline">
                <i class="fas fa-times mr-2"></i>Cancel
            </a>
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save mr-2"></i>Save Assignments
            </button>
        </div>
    </form>
</div>
@endsection
