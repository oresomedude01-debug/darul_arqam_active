@extends('layouts.spa')

@section('title', 'PWA Settings')

@section('breadcrumb')
    <span class="text-gray-400">Settings</span>
    <span class="text-gray-400">/</span>
    <a href="{{ route('settings.school.index') }}" class="text-primary-600 hover:text-primary-700">School Settings</a>
    <span class="text-gray-400">/</span>
    <span class="font-semibold text-gray-900">PWA Settings</span>
@endsection

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">PWA Settings</h1>
            <p class="text-sm text-gray-600 mt-1">Configure Progressive Web App appearance and branding</p>
        </div>
        <a href="{{ route('settings.school.index') }}" class="btn btn-outline">
            <i class="fas fa-arrow-left mr-2"></i>Back to Settings
        </a>
    </div>

    <!-- Form -->
    <form action="{{ route('settings.school.update-pwa') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf
        @method('PUT')

        <!-- PWA App Icon -->
        <div class="card">
            <div class="card-header">
                <h3 class="text-lg font-semibold text-gray-900">
                    <i class="fas fa-mobile-alt mr-2 text-primary-600"></i>App Icon
                </h3>
                <p class="text-sm text-gray-600 mt-1">This icon appears on home screens and app launchers</p>
            </div>
            <div class="card-body">
                <div class="flex items-center gap-6">
                    <div>
                        @if($settings->pwa_icon)
                            <img src="{{ asset('storage/' . $settings->pwa_icon) }}" alt="PWA Icon" class="w-32 h-32 object-cover rounded-lg border border-gray-200">
                        @elseif($settings->school_logo)
                            <img src="{{ asset('storage/' . $settings->school_logo) }}" alt="School Logo" class="w-32 h-32 object-cover rounded-lg border border-gray-200">
                        @else
                            <div class="w-32 h-32 bg-gray-100 rounded-lg border border-gray-300 flex items-center justify-center">
                                <i class="fas fa-image text-4xl text-gray-300"></i>
                            </div>
                        @endif
                    </div>
                    <div class="flex-1">
                        <label class="form-label">Upload App Icon</label>
                        <input type="file" name="pwa_icon" accept="image/*" class="form-input @error('pwa_icon') border-red-500 @enderror">
                        <p class="text-xs text-gray-500 mt-2">
                            Recommended: 512x512 px square image in PNG format. Max 5MB.<br>
                            If not provided, school logo will be used.
                        </p>
                        @error('pwa_icon')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <!-- App Names -->
        <div class="card">
            <div class="card-header">
                <h3 class="text-lg font-semibold text-gray-900">
                    <i class="fas fa-heading mr-2 text-primary-600"></i>App Names
                </h3>
            </div>
            <div class="card-body space-y-4">
                <div class="form-group">
                    <label class="form-label">Full App Name <span class="text-red-500">*</span></label>
                    <input 
                        type="text" 
                        name="pwa_app_name" 
                        value="{{ old('pwa_app_name', $settings->pwa_app_name ?? $settings->school_name . ' School Management') }}" 
                        class="form-input @error('pwa_app_name') border-red-500 @enderror" 
                        placeholder="e.g., Darul Arqam School Management System"
                        required>
                    <p class="text-xs text-gray-500 mt-1">The full name shown during app installation</p>
                    @error('pwa_app_name')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Short App Name</label>
                    <input 
                        type="text" 
                        name="pwa_short_name" 
                        value="{{ old('pwa_short_name', $settings->pwa_short_name ?? 'Darul Arqam') }}" 
                        class="form-input @error('pwa_short_name') border-red-500 @enderror"
                        placeholder="e.g., Darul Arqam"
                        maxlength="12">
                    <p class="text-xs text-gray-500 mt-1">Short name for home screen (max 12 characters)</p>
                    @error('pwa_short_name')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- App Colors -->
        <div class="card">
            <div class="card-header">
                <h3 class="text-lg font-semibold text-gray-900">
                    <i class="fas fa-palette mr-2 text-primary-600"></i>App Colors
                </h3>
            </div>
            <div class="card-body space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="form-group">
                        <label class="form-label">Theme Color</label>
                        <div class="flex items-center gap-2">
                            <input 
                                type="color" 
                                name="pwa_theme_color" 
                                value="{{ old('pwa_theme_color', $settings->pwa_theme_color ?? '#0284c7') }}" 
                                class="h-10 w-20 border border-gray-300 rounded cursor-pointer">
                            <input 
                                type="text" 
                                name="pwa_theme_color_text" 
                                value="{{ old('pwa_theme_color', $settings->pwa_theme_color ?? '#0284c7') }}" 
                                class="form-input flex-1 @error('pwa_theme_color') border-red-500 @enderror"
                                placeholder="#0284c7">
                        </div>
                        <p class="text-xs text-gray-500 mt-1">Color of browser chrome (top bar)</p>
                        @error('pwa_theme_color')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label">Background Color</label>
                        <div class="flex items-center gap-2">
                            <input 
                                type="color" 
                                name="pwa_background_color" 
                                value="{{ old('pwa_background_color', $settings->pwa_background_color ?? '#ffffff') }}" 
                                class="h-10 w-20 border border-gray-300 rounded cursor-pointer">
                            <input 
                                type="text" 
                                name="pwa_background_color_text" 
                                value="{{ old('pwa_background_color', $settings->pwa_background_color ?? '#ffffff') }}" 
                                class="form-input flex-1 @error('pwa_background_color') border-red-500 @enderror"
                                placeholder="#ffffff">
                        </div>
                        <p class="text-xs text-gray-500 mt-1">Background color during app launch</p>
                        @error('pwa_background_color')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <!-- Info Box -->
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
            <div class="flex gap-3">
                <i class="fas fa-info-circle text-blue-600 mt-0.5 flex-shrink-0"></i>
                <div class="text-sm text-blue-700">
                    <p class="font-semibold mb-1">PWA Preview</p>
                    <p>These settings will appear when users install the app on their device. Changes take effect after clearing browser cache or reinstalling the app.</p>
                </div>
            </div>
        </div>

        <!-- Submit Button -->
        <div class="flex items-center justify-end gap-3">
            <a href="{{ route('settings.school.index') }}" class="btn btn-outline">Cancel</a>
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save mr-2"></i>Save PWA Settings
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
    // Sync color text input with color picker
    document.querySelector('input[name="pwa_theme_color"]').addEventListener('change', function() {
        document.querySelector('input[name="pwa_theme_color_text"]').value = this.value;
    });
    document.querySelector('input[name="pwa_theme_color_text"]').addEventListener('change', function() {
        if (/^#[0-9A-F]{6}$/i.test(this.value)) {
            document.querySelector('input[name="pwa_theme_color"]').value = this.value;
        }
    });

    // Sync background color text input with color picker
    document.querySelector('input[name="pwa_background_color"]').addEventListener('change', function() {
        document.querySelector('input[name="pwa_background_color_text"]').value = this.value;
    });
    document.querySelector('input[name="pwa_background_color_text"]').addEventListener('change', function() {
        if (/^#[0-9A-F]{6}$/i.test(this.value)) {
            document.querySelector('input[name="pwa_background_color"]').value = this.value;
        }
    });
</script>
@endpush
