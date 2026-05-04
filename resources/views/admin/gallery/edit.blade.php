@extends('layouts.spa')

@section('title', __('gallery.edit_gallery'))

@section('content')
<div class="max-w-6xl mx-auto p-6">

    <div class="mb-8 flex items-center justify-between gap-4">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.gallery.index') }}" class="p-2 text-gray-400 hover:text-gray-700 hover:bg-gray-100 rounded-lg transition">
                <i class="fas fa-arrow-left"></i>
            </a>
            <div>
                <h1 class="text-3xl font-bold text-gray-900">{{ __('gallery.edit_gallery') }}</h1>
                <p class="text-gray-500 mt-1">{{ __('gallery.edit_and_update') }}</p>
            </div>
        </div>
        <form action="{{ route('admin.gallery.toggle', $gallery) }}" method="POST" class="inline">
            @csrf
            @method('PATCH')
            <button type="submit" class="px-4 py-2 {{ $gallery->status === 'published' ? 'bg-green-500 hover:bg-green-600' : 'bg-amber-500 hover:bg-amber-600' }} text-white font-semibold rounded-lg transition">
                @if($gallery->status === 'published')
                    <i class="fas fa-eye-slash mr-2"></i>{{ __('gallery.unpublish') }}
                @else
                    <i class="fas fa-eye mr-2"></i>{{ __('gallery.publish') }}
                @endif
            </button>
        </form>
    </div>

    @if($message = session('success'))
        <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-4 rounded-lg">
            <i class="fas fa-check-circle mr-2"></i>{{ $message }}
        </div>
    @endif

    @if($errors->any())
    <div class="mb-6 bg-red-50 border border-red-200 text-red-700 px-4 py-4 rounded-xl">
        <ul class="list-disc list-inside text-sm space-y-1">
            @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
        </ul>
    </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Gallery Details Form -->
        <div class="lg:col-span-2">
            <form action="{{ route('admin.gallery.update', $gallery) }}" method="POST" class="bg-white rounded-xl shadow-md p-8">
                @csrf
                @method('PUT')

                <!-- Gallery Title -->
                <div class="mb-6">
                    <label for="title" class="block text-sm font-semibold text-gray-900 mb-2">
                        {{ __('gallery.title_label') }} <span class="text-red-600">*</span>
                    </label>
                    <input type="text" id="title" name="title" value="{{ old('title', $gallery->title) }}"
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
                              placeholder="Enter gallery description">{{ old('description', $gallery->description) }}</textarea>
                    @error('description')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <!-- Grid: Color & Icon -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 mb-6">
                    <!-- Cover Color -->
                    <div>
                        <label for="cover_color" class="block text-sm font-semibold text-gray-900 mb-2">
                            {{ __('gallery.cover_color') }} <span class="text-red-600">*</span>
                        </label>
                        <input type="color" id="cover_color" name="cover_color" value="{{ old('cover_color', $gallery->cover_color) }}"
                               class="w-full h-12 rounded-lg cursor-pointer border border-gray-300" required>
                        @error('cover_color')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <!-- Cover Icon -->
                    <div>
                        <label for="cover_icon" class="block text-sm font-semibold text-gray-900 mb-2">
                            {{ __('gallery.cover_icon') }} <span class="text-red-600">*</span>
                        </label>
                        <input type="text" id="cover_icon" name="cover_icon" value="{{ old('cover_icon', $gallery->cover_icon) }}"
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
                            <input type="radio" name="status" value="draft" {{ old('status', $gallery->status) === 'draft' ? 'checked' : '' }}
                                   class="w-4 h-4 text-primary-600">
                            <span class="ml-3 text-gray-700">{{ __('common.draft') }} - {{ __('common.not_visible') }}</span>
                        </label>
                        <label class="flex items-center">
                            <input type="radio" name="status" value="published" {{ old('status', $gallery->status) === 'published' ? 'checked' : '' }}
                                   class="w-4 h-4 text-primary-600">
                            <span class="ml-3 text-gray-700">{{ __('common.published') }} - {{ __('common.publicly_visible') }}</span>
                        </label>
                    </div>
                    @error('status')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <!-- Buttons -->
                <div class="flex gap-3">
                    <button type="submit" class="flex-1 px-6 py-3 bg-primary-600 hover:bg-primary-700 text-white font-semibold rounded-lg transition">
                        <i class="fas fa-save mr-2"></i>{{ __('common.save_changes') }}
                    </button>
                    <a href="{{ route('admin.gallery.index') }}" class="flex-1 px-6 py-3 bg-gray-200 hover:bg-gray-300 text-gray-800 font-semibold rounded-lg transition text-center">
                        {{ __('common.cancel') }}
                    </a>
                </div>
            </form>
        </div>

        <!-- Gallery Images Section -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-xl shadow-md p-6">
                <h2 class="text-lg font-bold text-gray-900 mb-4">
                    <i class="fas fa-images mr-2"></i>{{ __('gallery.images') }} ({{ $gallery->items->count() }})
                </h2>

                <!-- Upload New Image -->
                <form action="{{ route('admin.gallery.upload', $gallery) }}" method="POST" enctype="multipart/form-data" class="mb-6 p-4 border-2 border-dashed border-gray-300 rounded-lg hover:border-primary-600 transition cursor-pointer">
                    @csrf
                    <input type="file" name="image" accept="image/*" required class="hidden" id="imageUpload">
                    <label for="imageUpload" class="cursor-pointer text-center">
                        <i class="fas fa-cloud-upload-alt text-3xl text-gray-400 mb-2 block"></i>
                        <p class="text-sm font-medium text-gray-700">{{ __('gallery.upload_images') }}</p>
                        <p class="text-xs text-gray-500 mt-1">PNG, JPG, GIF up to 5MB</p>
                    </label>
                </form>

                <!-- Images List -->
                <div class="space-y-2">
                    @forelse($gallery->items()->orderBy('sort_order')->get() as $item)
                        <div class="p-3 bg-gray-50 rounded-lg flex items-center justify-between group hover:bg-gray-100 transition">
                            <div class="flex items-center gap-3 flex-1 min-w-0">
                                <img src="{{ $item->getImageUrl() }}" alt="{{ $item->title }}" class="w-10 h-10 rounded object-cover flex-shrink-0">
                                <div class="min-w-0 flex-1">
                                    <p class="text-sm font-medium text-gray-900 truncate">{{ $item->getDisplayTitle() }}</p>
                                    <p class="text-xs text-gray-500">{{ $item->uploaded_at?->format('d M Y') }}</p>
                                </div>
                            </div>
                            <div class="flex gap-1 ml-2">
                                <form action="{{ route('admin.gallery.toggle-image', $item) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="p-2 {{ $item->is_visible ? 'text-green-600 hover:bg-green-50' : 'text-gray-400 hover:bg-gray-200' }} rounded transition" title="{{ $item->is_visible ? 'Hide' : 'Show' }}">
                                        <i class="fas {{ $item->is_visible ? 'fa-eye' : 'fa-eye-slash' }}"></i>
                                    </button>
                                </form>
                                <form action="{{ route('admin.gallery.delete-image', $item) }}" method="POST" class="inline" onsubmit="return confirm('{{ __('gallery.confirm_delete_image') }}')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-2 text-red-600 hover:bg-red-50 rounded transition">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    @empty
                        <p class="text-center text-sm text-gray-500 py-4">{{ __('common.no_images') }}</p>
                    @endforelse
                </div>
            </div>
        </div>

    </div>

</div>
@endsection
