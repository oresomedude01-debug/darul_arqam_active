@extends('layouts.spa')

@section('title', __('gallery.new_gallery_title'))

@section('content')
<div class="max-w-4xl mx-auto p-6">

    <div class="mb-8 flex items-center gap-4">
        <a href="{{ route('admin.gallery.index') }}" class="p-2 text-gray-400 hover:text-gray-700 hover:bg-gray-100 rounded-lg transition">
            <i class="fas fa-arrow-left"></i>
        </a>
        <div>
            <h1 class="text-3xl font-bold text-gray-900">{{ __('gallery.new_gallery_title') }}</h1>
            <p class="text-gray-500 mt-1">{{ __('gallery.create_and_publish') }}</p>
        </div>
    </div>

    @if($errors->any())
    <div class="mb-6 bg-red-50 border border-red-200 text-red-700 px-4 py-4 rounded-xl">
        <ul class="list-disc list-inside text-sm space-y-1">
            @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
        </ul>
    </div>
    @endif

    <form action="{{ route('admin.gallery.store') }}" method="POST" class="bg-white rounded-xl shadow-md p-8">
        @csrf

        <!-- Gallery Title -->
        <div class="mb-6">
            <label for="title" class="block text-sm font-semibold text-gray-900 mb-2">
                {{ __('gallery.title_label') }} <span class="text-red-600">*</span>
            </label>
            <input type="text" id="title" name="title" value="{{ old('title') }}"
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-600 focus:border-transparent"
                   placeholder="Enter gallery title" required>
            @error('title')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
        </div>

        <!-- Description -->
        <div class="mb-6">
            <label for="description" class="block text-sm font-semibold text-gray-900 mb-2">
                {{ __('gallery.description_label') }}
            </label>
            <textarea id="description" name="description" rows="4"
                      class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-600 focus:border-transparent"
                      placeholder="Enter gallery description">{{ old('description') }}</textarea>
            @error('description')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
        </div>

        <!-- Grid: Color & Icon -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 mb-6">
            <!-- Cover Color -->
            <div>
                <label for="cover_color" class="block text-sm font-semibold text-gray-900 mb-2">
                    {{ __('gallery.cover_color') }} <span class="text-red-600">*</span>
                </label>
                <input type="color" id="cover_color" name="cover_color" value="{{ old('cover_color', '#3b82f6') }}"
                       class="w-full h-12 rounded-lg cursor-pointer border border-gray-300" required>
                @error('cover_color')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <!-- Cover Icon -->
            <div>
                <label for="cover_icon" class="block text-sm font-semibold text-gray-900 mb-2">
                    {{ __('gallery.cover_icon') }} <span class="text-red-600">*</span>
                </label>
                <input type="text" id="cover_icon" name="cover_icon" value="{{ old('cover_icon', 'fas fa-images') }}"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-600 focus:border-transparent"
                       placeholder="e.g., fas fa-images" required>
                @error('cover_icon')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
        </div>

        <!-- Status -->
        <div class="mb-8">
            <label class="block text-sm font-semibold text-gray-900 mb-3">
                {{ __('gallery.status_label') }} <span class="text-red-600">*</span>
            </label>
            <div class="space-y-2">
                <label class="flex items-center">
                    <input type="radio" name="status" value="draft" {{ old('status', 'draft') === 'draft' ? 'checked' : '' }}
                           class="w-4 h-4 text-primary-600">
                    <span class="ml-3 text-gray-700">{{ __('common.draft') }} - {{ __('common.not_visible') }}</span>
                </label>
                <label class="flex items-center">
                    <input type="radio" name="status" value="published" {{ old('status') === 'published' ? 'checked' : '' }}
                           class="w-4 h-4 text-primary-600">
                    <span class="ml-3 text-gray-700">{{ __('common.published') }} - {{ __('common.publicly_visible') }}</span>
                </label>
            </div>
            @error('status')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
        </div>

        <!-- Buttons -->
        <div class="flex gap-3">
            <button type="submit" class="flex-1 px-6 py-3 bg-primary-600 hover:bg-primary-700 text-white font-semibold rounded-lg transition">
                <i class="fas fa-save mr-2"></i>{{ __('common.create') }}
            </button>
            <a href="{{ route('admin.gallery.index') }}" class="flex-1 px-6 py-3 bg-gray-200 hover:bg-gray-300 text-gray-800 font-semibold rounded-lg transition text-center">
                {{ __('common.cancel') }}
            </a>
        </div>
    </form>

</div>
@endsection
