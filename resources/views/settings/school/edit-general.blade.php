@extends('layouts.spa')

@section('title', 'Edit School Information')

@section('breadcrumb')
    <span class="text-gray-400">Settings</span>
    <span class="text-gray-400">/</span>
    <a href="{{ route('settings.school.index') }}" class="text-primary-600 hover:text-primary-700">School Settings</a>
    <span class="text-gray-400">/</span>
    <span class="font-semibold text-gray-900">School Information</span>
@endsection

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">School Information</h1>
            <p class="text-sm text-gray-600 mt-1">Update your school's basic information</p>
        </div>
        <a href="{{ route('settings.school.index') }}" class="btn btn-outline">
            <i class="fas fa-arrow-left mr-2"></i>Back to Settings
        </a>
    </div>

    <!-- Form -->
    <form action="{{ route('settings.school.update-general') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf
        @method('PUT')

        <!-- School Logo -->
        <div class="card">
            <div class="card-header">
                <h3 class="text-lg font-semibold text-gray-900">
                    <i class="fas fa-image mr-2 text-primary-600"></i>School Logo
                </h3>
            </div>
            <div class="card-body">
                <div class="flex items-center gap-6">
                    <div>
                        @if($settings->school_logo)
                            <img src="{{ asset('storage/' . $settings->school_logo) }}" alt="School Logo" class="w-32 h-32 object-cover rounded-lg border border-gray-200">
                        @else
                            <div class="w-32 h-32 bg-gray-100 rounded-lg border border-gray-300 flex items-center justify-center">
                                <i class="fas fa-image text-4xl text-gray-300"></i>
                            </div>
                        @endif
                    </div>
                    <div class="flex-1">
                        <label class="form-label">Upload New Logo</label>
                        <input type="file" name="school_logo" accept="image/*" class="form-input @error('school_logo') border-red-500 @enderror">
                        <p class="text-xs text-gray-500 mt-2">Max 5MB. Formats: JPEG, PNG, JPG</p>
                        @error('school_logo')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <!-- Basic Information -->
        <div class="card">
            <div class="card-header">
                <h3 class="text-lg font-semibold text-gray-900">
                    <i class="fas fa-info-circle mr-2 text-primary-600"></i>Basic Information
                </h3>
            </div>
            <div class="card-body space-y-4">
                <div class="form-group">
                    <label class="form-label">School Name <span class="text-red-500">*</span></label>
                    <input type="text" name="school_name" value="{{ old('school_name', $settings->school_name) }}" class="form-input @error('school_name') border-red-500 @enderror" required>
                    @error('school_name')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Motto / Slogan</label>
                    <input type="text" name="school_motto" value="{{ old('school_motto', $settings->school_motto) }}" class="form-input @error('school_motto') border-red-500 @enderror" placeholder="e.g., Excellence in Education">
                    @error('school_motto')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Contact Information -->
        <div class="card">
            <div class="card-header">
                <h3 class="text-lg font-semibold text-gray-900">
                    <i class="fas fa-phone mr-2 text-primary-600"></i>Contact Information
                </h3>
            </div>
            <div class="card-body space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="form-group">
                        <label class="form-label">Email Address</label>
                        <input type="email" name="school_email" value="{{ old('school_email', $settings->school_email) }}" class="form-input @error('school_email') border-red-500 @enderror">
                        @error('school_email')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label">Phone Number</label>
                        <input type="tel" name="school_phone" value="{{ old('school_phone', $settings->school_phone) }}" class="form-input @error('school_phone') border-red-500 @enderror">
                        @error('school_phone')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">School Address</label>
                    <textarea name="school_address" rows="3" class="form-textarea @error('school_address') border-red-500 @enderror">{{ old('school_address', $settings->school_address) }}</textarea>
                    @error('school_address')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
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
