@extends('layouts.spa')

@section('title', 'Edit Promotion Settings')

@section('breadcrumb')
    <span class="text-gray-400">Settings</span>
    <span class="text-gray-400">/</span>
    <a href="{{ route('settings.school.index') }}" class="text-primary-600 hover:text-primary-700">School Settings</a>
    <span class="text-gray-400">/</span>
    <span class="font-semibold text-gray-900">Promotion Settings</span>
@endsection

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Student Promotion Settings</h1>
            <p class="text-sm text-gray-600 mt-1">Configure how students are promoted to the next class</p>
        </div>
        <a href="{{ route('settings.school.index') }}" class="btn btn-outline">
            <i class="fas fa-arrow-left mr-2"></i>Back to Settings
        </a>
    </div>

    <!-- Form -->
    <form action="{{ route('settings.school.update-promotion') }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')

        <!-- Promotion Mode -->
        <div class="card">
            <div class="card-header">
                <h3 class="text-lg font-semibold text-gray-900">
                    <i class="fas fa-graduation-cap mr-2 text-primary-600"></i>Promotion Mode
                </h3>
            </div>
            <div class="card-body space-y-4">
                @php
                    $promotionSettings = $settings->promotion_settings ?? [];
                    $autoPromotion = $promotionSettings['auto_promotion'] ?? false;
                @endphp

                <fieldset class="space-y-3">
                    <label class="flex items-start p-4 border border-gray-200 rounded-lg hover:bg-primary-50 cursor-pointer transition-colors">
                        <input type="radio" name="auto_promotion" value="1" @checked($autoPromotion) class="mt-1 w-5 h-5 text-primary-600 border-gray-300 focus:ring-primary-500">
                        <div class="ml-3 flex-1">
                            <span class="font-medium text-gray-900 block">Automatic Promotion</span>
                            <p class="text-sm text-gray-600 mt-1">Students meeting the pass mark are automatically promoted to the next class.</p>
                        </div>
                    </label>

                    <label class="flex items-start p-4 border border-gray-200 rounded-lg hover:bg-primary-50 cursor-pointer transition-colors">
                        <input type="radio" name="auto_promotion" value="0" @checked(!$autoPromotion) class="mt-1 w-5 h-5 text-primary-600 border-gray-300 focus:ring-primary-500">
                        <div class="ml-3 flex-1">
                            <span class="font-medium text-gray-900 block">Manual Promotion</span>
                            <p class="text-sm text-gray-600 mt-1">Administrators manually review and decide student promotions.</p>
                        </div>
                    </label>
                </fieldset>

                @error('auto_promotion')
                    <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Promotion Criteria -->
        <div class="card">
            <div class="card-header">
                <h3 class="text-lg font-semibold text-gray-900">
                    <i class="fas fa-chart-line mr-2 text-primary-600"></i>Promotion Criteria
                </h3>
            </div>
            <div class="card-body space-y-4">
                <div class="form-group">
                    <label class="form-label">Pass Mark for Promotion <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <input type="number" name="pass_mark" value="{{ old('pass_mark', $promotionSettings['pass_mark'] ?? 40) }}" class="form-input @error('pass_mark') border-red-500 @enderror" min="0" max="100" required>
                        <span class="absolute right-3 top-3 text-gray-500">%</span>
                    </div>
                    <p class="text-xs text-gray-500 mt-2">Students must achieve this score or higher to be promoted.</p>
                    @error('pass_mark')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Minimum Average Score for Subjects <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <input type="number" name="min_average" value="{{ old('min_average', $promotionSettings['min_average'] ?? 40) }}" class="form-input @error('min_average') border-red-500 @enderror" min="0" max="100" step="0.1" required>
                        <span class="absolute right-3 top-3 text-gray-500">%</span>
                    </div>
                    <p class="text-xs text-gray-500 mt-2">Student's average score across all subjects must meet this threshold.</p>
                    @error('min_average')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Maximum Failed Subjects Allowed <span class="text-red-500">*</span></label>
                    <input type="number" name="max_failed_subjects" value="{{ old('max_failed_subjects', $promotionSettings['max_failed_subjects'] ?? 0) }}" class="form-input @error('max_failed_subjects') border-red-500 @enderror" min="0" max="20" required>
                    <p class="text-xs text-gray-500 mt-2">Students with more failures than this will not be promoted (0 = zero tolerance).</p>
                    @error('max_failed_subjects')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Additional Options -->
        <div class="card">
            <div class="card-header">
                <h3 class="text-lg font-semibold text-gray-900">
                    <i class="fas fa-cog mr-2 text-primary-600"></i>Additional Options
                </h3>
            </div>
            <div class="card-body space-y-4">
                <label class="flex items-center p-4 border border-gray-200 rounded-lg hover:bg-primary-50 cursor-pointer transition-colors">
                    <input type="checkbox" name="allow_retention" value="1" @checked($promotionSettings['allow_retention'] ?? false) class="w-5 h-5 text-primary-600 border-gray-300 rounded focus:ring-primary-500">
                    <span class="ml-3 font-medium text-gray-900">Allow Student Retention</span>
                </label>

                <label class="flex items-center p-4 border border-gray-200 rounded-lg hover:bg-primary-50 cursor-pointer transition-colors">
                    <input type="checkbox" name="allow_skipping" value="1" @checked($promotionSettings['allow_skipping'] ?? false) class="w-5 h-5 text-primary-600 border-gray-300 rounded focus:ring-primary-500">
                    <span class="ml-3 font-medium text-gray-900">Allow Class Skipping (for exceptional students)</span>
                </label>
            </div>
        </div>

        <!-- Information Alert -->
        <div class="alert alert-info">
            <i class="fas fa-lightbulb mr-2"></i>
            <strong>Example:</strong> If Pass Mark is 40% and Max Failed Subjects is 1:
            <ul class="list-disc list-inside mt-2 text-sm space-y-1">
                <li>Student with 50% average and 1 failed subject → Promoted ✓</li>
                <li>Student with 35% average → Not promoted ✗</li>
                <li>Student with 50% average and 2 failed subjects → Not promoted ✗</li>
            </ul>
        </div>

        <!-- Action Buttons -->
        <div class="flex items-center justify-between">
            <a href="{{ route('settings.school.index') }}" class="btn btn-outline">
                <i class="fas fa-times mr-2"></i>Cancel
            </a>
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save mr-2"></i>Save Changes
            </button>
        </div>
    </form>
</div>
@endsection
