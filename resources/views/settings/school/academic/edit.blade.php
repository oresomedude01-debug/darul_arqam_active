@extends('layouts.spa')

@section('title', 'Edit Academic Session')

@section('breadcrumb')
    <span class="text-gray-400">Settings</span>
    <span class="text-gray-400">/</span>
    <a href="{{ route('settings.school.index') }}" class="text-primary-600 hover:text-primary-700">School Settings</a>
    <span class="text-gray-400">/</span>
    <a href="{{ route('settings.school.academic.sessions') }}" class="text-primary-600 hover:text-primary-700">Academic Sessions</a>
    <span class="text-gray-400">/</span>
    <span class="font-semibold text-gray-900">Edit Session {{ $session }}</span>
@endsection

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Edit Academic Session</h1>
            <p class="text-sm text-gray-600 mt-1">Session: <strong>{{ $session }}</strong></p>
        </div>
        <a href="{{ route('settings.school.academic.sessions') }}" class="btn btn-outline">
            <i class="fas fa-arrow-left mr-2"></i>Back
        </a>
    </div>

    <!-- Form -->
    <form action="{{ route('settings.school.academic.update', $academicSession->id) }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')

        @php
            $termsByPosition = [
                'First Term' => $terms->firstWhere('term', 'First Term'),
                'Second Term' => $terms->firstWhere('term', 'Second Term'),
                'Third Term' => $terms->firstWhere('term', 'Third Term'),
            ];
        @endphp

        <!-- Term 1 Configuration -->
        <div class="card">
            <div class="card-header bg-blue-50">
                <h3 class="text-lg font-semibold text-gray-900">
                    <i class="fas fa-bookmark mr-2 text-blue-600"></i>First Term Configuration
                </h3>
            </div>
            <div class="card-body space-y-4">
                @php $term1 = $termsByPosition['First Term']; @endphp
                
                <div class="form-group">
                    <label class="form-label">Term Name <span class="text-red-500">*</span></label>
                    <input type="text" 
                           name="term1_name" 
                           value="{{ old('term1_name', $term1?->name ?? '') }}"
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
                               value="{{ old('term1_start_date', $term1?->start_date?->format('Y-m-d') ?? '') }}"
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
                               value="{{ old('term1_end_date', $term1?->end_date?->format('Y-m-d') ?? '') }}"
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
                @php $term2 = $termsByPosition['Second Term']; @endphp
                
                <div class="form-group">
                    <label class="form-label">Term Name <span class="text-red-500">*</span></label>
                    <input type="text" 
                           name="term2_name" 
                           value="{{ old('term2_name', $term2?->name ?? '') }}"
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
                               value="{{ old('term2_start_date', $term2?->start_date?->format('Y-m-d') ?? '') }}"
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
                               value="{{ old('term2_end_date', $term2?->end_date?->format('Y-m-d') ?? '') }}"
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
                @php $term3 = $termsByPosition['Third Term']; @endphp
                
                <div class="form-group">
                    <label class="form-label">Term Name <span class="text-red-500">*</span></label>
                    <input type="text" 
                           name="term3_name" 
                           value="{{ old('term3_name', $term3?->name ?? '') }}"
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
                               value="{{ old('term3_start_date', $term3?->start_date?->format('Y-m-d') ?? '') }}"
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
                               value="{{ old('term3_end_date', $term3?->end_date?->format('Y-m-d') ?? '') }}"
                               class="form-input @error('term3_end_date') border-red-500 @enderror" 
                               required>
                        @error('term3_end_date')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <!-- Select Current Term Section -->
        <div class="card border-amber-200 bg-amber-50">
            <div class="card-header bg-amber-100">
                <h3 class="text-lg font-semibold text-gray-900">
                    <i class="fas fa-fire mr-2 text-amber-600"></i>Current Term Selection
                </h3>
            </div>
            <div class="card-body space-y-4">
                <p class="text-sm text-gray-600">
                    Select which term is currently active within this session. Only one term can be active at a time.
                </p>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <label class="flex items-start gap-3 p-4 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-amber-300 transition" id="term1-label">
                        <input type="radio" 
                               name="current_term" 
                               value="First Term" 
                               class="form-radio mt-1"
                               @checked(old('current_term') === 'First Term' || ($terms->firstWhere('term', 'First Term')?->is_active))>
                        <div class="flex-1">
                            <h4 class="font-semibold text-gray-900">First Term</h4>
                            @if ($term1 = $terms->firstWhere('term', 'First Term'))
                                <p class="text-xs text-gray-600 mt-1">
                                    {{ $term1->start_date->format('M d') }} - {{ $term1->end_date->format('M d, Y') }}
                                </p>
                                <p class="text-xs font-medium mt-2">
                                    <i class="fas fa-calendar text-amber-600"></i> 
                                    {{ $term1->school_opening_days }} school days
                                </p>
                                @if ($term1->is_active)
                                    <span class="inline-block mt-2 badge badge-success">
                                        <i class="fas fa-check-circle"></i> Active
                                    </span>
                                @endif
                            @endif
                        </div>
                    </label>

                    <label class="flex items-start gap-3 p-4 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-green-300 transition" id="term2-label">
                        <input type="radio" 
                               name="current_term" 
                               value="Second Term" 
                               class="form-radio mt-1"
                               @checked(old('current_term') === 'Second Term' || ($terms->firstWhere('term', 'Second Term')?->is_active))>
                        <div class="flex-1">
                            <h4 class="font-semibold text-gray-900">Second Term</h4>
                            @if ($term2 = $terms->firstWhere('term', 'Second Term'))
                                <p class="text-xs text-gray-600 mt-1">
                                    {{ $term2->start_date->format('M d') }} - {{ $term2->end_date->format('M d, Y') }}
                                </p>
                                <p class="text-xs font-medium mt-2">
                                    <i class="fas fa-calendar text-green-600"></i> 
                                    {{ $term2->school_opening_days }} school days
                                </p>
                                @if ($term2->is_active)
                                    <span class="inline-block mt-2 badge badge-success">
                                        <i class="fas fa-check-circle"></i> Active
                                    </span>
                                @endif
                            @endif
                        </div>
                    </label>

                    <label class="flex items-start gap-3 p-4 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-orange-300 transition" id="term3-label">
                        <input type="radio" 
                               name="current_term" 
                               value="Third Term" 
                               class="form-radio mt-1"
                               @checked(old('current_term') === 'Third Term' || ($terms->firstWhere('term', 'Third Term')?->is_active))>
                        <div class="flex-1">
                            <h4 class="font-semibold text-gray-900">Third Term</h4>
                            @if ($term3 = $terms->firstWhere('term', 'Third Term'))
                                <p class="text-xs text-gray-600 mt-1">
                                    {{ $term3->start_date->format('M d') }} - {{ $term3->end_date->format('M d, Y') }}
                                </p>
                                <p class="text-xs font-medium mt-2">
                                    <i class="fas fa-calendar text-orange-600"></i> 
                                    {{ $term3->school_opening_days }} school days
                                </p>
                                @if ($term3->is_active)
                                    <span class="inline-block mt-2 badge badge-success">
                                        <i class="fas fa-check-circle"></i> Active
                                    </span>
                                @endif
                            @endif
                        </div>
                    </label>
                </div>

                <div class="bg-white p-4 rounded-lg border border-amber-200">
                    <p class="text-sm text-gray-700">
                        <i class="fas fa-info-circle text-amber-600 mr-2"></i>
                        <strong>Note:</strong> Selecting a term here will also set this session as the current session across the school system. The first term is selected by default when a new session is created.
                    </p>
                </div>
            </div>
        </div>

        <!-- Info Alert -->
        <div class="alert alert-info">
            <i class="fas fa-info-circle mr-2"></i>
            <strong>Important:</strong> Each term's start date must be <strong>strictly after</strong> the previous term's end date. Terms cannot overlap or share dates. The system will validate this when you save.
        </div>

        <!-- Action Buttons -->
        <div class="flex items-center justify-between">
            <a href="{{ route('settings.school.academic.sessions') }}" class="btn btn-outline">
                <i class="fas fa-times mr-2"></i>Cancel
            </a>
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save mr-2"></i>Save Changes
            </button>
        </div>
    </form>
</div>
@endsection
