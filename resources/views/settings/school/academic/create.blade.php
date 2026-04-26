@extends('layouts.spa')

@section('title', 'Create Academic Session')

@section('breadcrumb')
    <span class="text-gray-400">Settings</span>
    <span class="text-gray-400">/</span>
    <a href="{{ route('settings.school.index') }}" class="text-primary-600 hover:text-primary-700">School Settings</a>
    <span class="text-gray-400">/</span>
    <a href="{{ route('settings.school.academic.sessions') }}" class="text-primary-600 hover:text-primary-700">Academic Sessions</a>
    <span class="text-gray-400">/</span>
    <span class="font-semibold text-gray-900">Create New Session</span>
@endsection

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Create Academic Session</h1>
            <p class="text-sm text-gray-600 mt-1">Set up a new academic session with three terms</p>
        </div>
        <a href="{{ route('settings.school.academic.sessions') }}" class="btn btn-outline">
            <i class="fas fa-arrow-left mr-2"></i>Back
        </a>
    </div>

    <!-- Form -->
    <form action="{{ route('settings.school.academic.store') }}" method="POST" class="space-y-6">
        @csrf

        <!-- Session Information -->
        <div class="card">
            <div class="card-header">
                <h3 class="text-lg font-semibold text-gray-900">
                    <i class="fas fa-calendar-alt mr-2 text-primary-600"></i>Session Information
                </h3>
            </div>
            <div class="card-body space-y-4">
                <div class="form-group">
                    <label class="form-label">Academic Session <span class="text-red-500">*</span></label>
                    <input type="text" 
                           name="session" 
                           placeholder="e.g., 2024/2025" 
                           value="{{ old('session') }}"
                           class="form-input @error('session') border-red-500 @enderror" 
                           required>
                    <p class="text-xs text-gray-500 mt-1">Format: YYYY/YYYY (e.g., 2024/2025)</p>
                    @error('session')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Term 1 Configuration -->
        <div class="card">
            <div class="card-header bg-blue-50">
                <h3 class="text-lg font-semibold text-gray-900">
                    <i class="fas fa-bookmark mr-2 text-blue-600"></i>First Term Configuration
                </h3>
            </div>
            <div class="card-body space-y-4">
                <div class="form-group">
                    <label class="form-label">Term Name <span class="text-red-500">*</span></label>
                    <input type="text" 
                           name="term1_name" 
                           placeholder="e.g., First Term 2024/2025" 
                           value="{{ old('term1_name') }}"
                           class="form-input @error('term1_name') border-red-500 @enderror" 
                           required>
                    @error('term1_name')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="form-group">
                        <label class="form-label">Start Date <span class="text-red-500">*</span></label>
                        <input type="date" 
                               name="term1_start_date" 
                               value="{{ old('term1_start_date') }}"
                               class="form-input @error('term1_start_date') border-red-500 @enderror" 
                               required>
                        @error('term1_start_date')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label">End Date <span class="text-red-500">*</span></label>
                        <input type="date" 
                               name="term1_end_date" 
                               value="{{ old('term1_end_date') }}"
                               class="form-input @error('term1_end_date') border-red-500 @enderror" 
                               required>
                        @error('term1_end_date')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <!-- Term 2 Configuration -->
        <div class="card">
            <div class="card-header bg-green-50">
                <h3 class="text-lg font-semibold text-gray-900">
                    <i class="fas fa-bookmark mr-2 text-green-600"></i>Second Term Configuration
                </h3>
            </div>
            <div class="card-body space-y-4">
                <div class="form-group">
                    <label class="form-label">Term Name <span class="text-red-500">*</span></label>
                    <input type="text" 
                           name="term2_name" 
                           placeholder="e.g., Second Term 2024/2025" 
                           value="{{ old('term2_name') }}"
                           class="form-input @error('term2_name') border-red-500 @enderror" 
                           required>
                    @error('term2_name')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="form-group">
                        <label class="form-label">Start Date <span class="text-red-500">*</span></label>
                        <input type="date" 
                               name="term2_start_date" 
                               value="{{ old('term2_start_date') }}"
                               class="form-input @error('term2_start_date') border-red-500 @enderror" 
                               required>
                        @error('term2_start_date')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label">End Date <span class="text-red-500">*</span></label>
                        <input type="date" 
                               name="term2_end_date" 
                               value="{{ old('term2_end_date') }}"
                               class="form-input @error('term2_end_date') border-red-500 @enderror" 
                               required>
                        @error('term2_end_date')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <!-- Term 3 Configuration -->
        <div class="card">
            <div class="card-header bg-orange-50">
                <h3 class="text-lg font-semibold text-gray-900">
                    <i class="fas fa-bookmark mr-2 text-orange-600"></i>Third Term Configuration
                </h3>
            </div>
            <div class="card-body space-y-4">
                <div class="form-group">
                    <label class="form-label">Term Name <span class="text-red-500">*</span></label>
                    <input type="text" 
                           name="term3_name" 
                           placeholder="e.g., Third Term 2024/2025" 
                           value="{{ old('term3_name') }}"
                           class="form-input @error('term3_name') border-red-500 @enderror" 
                           required>
                    @error('term3_name')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="form-group">
                        <label class="form-label">Start Date <span class="text-red-500">*</span></label>
                        <input type="date" 
                               name="term3_start_date" 
                               value="{{ old('term3_start_date') }}"
                               class="form-input @error('term3_start_date') border-red-500 @enderror" 
                               required>
                        @error('term3_start_date')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label">End Date <span class="text-red-500">*</span></label>
                        <input type="date" 
                               name="term3_end_date" 
                               value="{{ old('term3_end_date') }}"
                               class="form-input @error('term3_end_date') border-red-500 @enderror" 
                               required>
                        @error('term3_end_date')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <!-- Info Alert -->
        <div class="alert alert-info">
            <i class="fas fa-info-circle mr-2"></i>
            <strong>Note:</strong> Each term's start date must be after the previous term's end date. Make sure there are no overlapping dates.
        </div>

        <!-- Action Buttons -->
        <div class="flex items-center justify-between">
            <a href="{{ route('settings.school.academic.sessions') }}" class="btn btn-outline">
                <i class="fas fa-times mr-2"></i>Cancel
            </a>
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save mr-2"></i>Create Session
            </button>
        </div>
    </form>
</div>
@endsection
