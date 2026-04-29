@extends('layouts.spa')

@section('title', 'New Blog Post')

@section('content')
<div class="max-w-4xl mx-auto p-6">

    <div class="mb-8 flex items-center gap-4">
        <a href="{{ route('admin.blog.index') }}" class="p-2 text-gray-400 hover:text-gray-700 hover:bg-gray-100 rounded-lg transition">
            <i class="fas fa-arrow-left"></i>
        </a>
        <div>
            <h1 class="text-3xl font-bold text-gray-900">New Blog Post</h1>
            <p class="text-gray-500 mt-1">Create and publish a new article</p>
        </div>
    </div>

    @if($errors->any())
    <div class="mb-6 bg-red-50 border border-red-200 text-red-700 px-4 py-4 rounded-xl">
        <ul class="list-disc list-inside text-sm space-y-1">
            @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
        </ul>
    </div>
    @endif

    <form action="{{ route('admin.blog.store') }}" method="POST" x-data="blogForm()" class="space-y-6">
        @csrf

        {{-- Title --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <label class="block text-sm font-semibold text-gray-700 mb-2">Post Title <span class="text-red-500">*</span></label>
            <input type="text" name="title" value="{{ old('title') }}" required
                   placeholder="Enter a compelling title..."
                   class="w-full px-4 py-3 border border-gray-200 rounded-xl text-lg font-semibold focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition">
        </div>

        {{-- Meta --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 grid sm:grid-cols-2 gap-6" x-data="{ type: '{{ old('type', 'article') }}' }">
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Post Type <span class="text-red-500">*</span></label>
                <select name="type" x-model="type" required class="w-full px-4 py-2.5 border border-gray-200 rounded-xl bg-white focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition">
                    <option value="article">Standard Article</option>
                    <option value="video">Video Blog (YouTube)</option>
                </select>
            </div>
            <div x-show="type === 'video'" x-cloak>
                <label class="block text-sm font-semibold text-gray-700 mb-2">YouTube Video ID</label>
                <input type="text" name="youtube_video_id" value="{{ old('youtube_video_id') }}"
                       placeholder="e.g. dQw4w9WgXcQ"
                       class="w-full px-4 py-2.5 border border-gray-200 rounded-xl bg-white focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition">
                <p class="text-xs text-gray-500 mt-1">The 11-character ID from the YouTube URL.</p>
            </div>
            
            <div class="sm:col-span-2 grid sm:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Category <span class="text-red-500">*</span></label>
                    <select name="category" required class="w-full px-4 py-2.5 border border-gray-200 rounded-xl bg-white focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition">
                        <option value="news" {{ old('category')==='news' ? 'selected':'' }}>News</option>
                        <option value="islamic" {{ old('category')==='islamic' ? 'selected':'' }}>Islamic Studies</option>
                        <option value="events" {{ old('category')==='events' ? 'selected':'' }}>Events</option>
                        <option value="tips" {{ old('category')==='tips' ? 'selected':'' }}>Study Tips</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Status <span class="text-red-500">*</span></label>
                    <select name="status" required class="w-full px-4 py-2.5 border border-gray-200 rounded-xl bg-white focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition">
                        <option value="draft" {{ old('status','draft')==='draft' ? 'selected':'' }}>Save as Draft</option>
                        <option value="published" {{ old('status')==='published' ? 'selected':'' }}>Publish Now</option>
                    </select>
                </div>
            </div>
        </div>

        {{-- Cover Style --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-sm font-semibold text-gray-700 mb-4">Cover Style</h3>
            <div class="grid grid-cols-2 gap-6">
                <div>
                    <label class="block text-xs text-gray-500 mb-2">Gradient Color</label>
                    <select name="cover_color" class="w-full px-4 py-2.5 border border-gray-200 rounded-xl bg-white text-sm focus:ring-2 focus:ring-primary-500 transition">
                        <option value="from-blue-500 to-blue-700" {{ old('cover_color')==='from-blue-500 to-blue-700'?'selected':'' }}>Blue</option>
                        <option value="from-green-500 to-green-700" {{ old('cover_color')==='from-green-500 to-green-700'?'selected':'' }}>Green</option>
                        <option value="from-purple-500 to-purple-700" {{ old('cover_color')==='from-purple-500 to-purple-700'?'selected':'' }}>Purple</option>
                        <option value="from-amber-400 to-amber-600" {{ old('cover_color')==='from-amber-400 to-amber-600'?'selected':'' }}>Amber</option>
                        <option value="from-teal-500 to-teal-700" {{ old('cover_color')==='from-teal-500 to-teal-700'?'selected':'' }}>Teal</option>
                        <option value="from-rose-500 to-rose-700" {{ old('cover_color')==='from-rose-500 to-rose-700'?'selected':'' }}>Rose</option>
                        <option value="from-indigo-500 to-indigo-700" {{ old('cover_color')==='from-indigo-500 to-indigo-700'?'selected':'' }}>Indigo</option>
                        <option value="from-sky-500 to-sky-700" {{ old('cover_color')==='from-sky-500 to-sky-700'?'selected':'' }}>Sky</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs text-gray-500 mb-2">Icon (Font Awesome class)</label>
                    <select name="cover_icon" class="w-full px-4 py-2.5 border border-gray-200 rounded-xl bg-white text-sm focus:ring-2 focus:ring-primary-500 transition">
                        <option value="fas fa-newspaper">Newspaper</option>
                        <option value="fas fa-quran">Quran</option>
                        <option value="fas fa-graduation-cap">Graduation Cap</option>
                        <option value="fas fa-mosque">Mosque</option>
                        <option value="fas fa-book-open">Book Open</option>
                        <option value="fas fa-star">Star</option>
                        <option value="fas fa-pen-fancy">Pen</option>
                        <option value="fas fa-lightbulb">Lightbulb</option>
                        <option value="fas fa-bullhorn">Bullhorn</option>
                        <option value="fas fa-award">Award</option>
                    </select>
                </div>
            </div>
        </div>

        {{-- Excerpt --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <label class="block text-sm font-semibold text-gray-700 mb-2">
                Excerpt <span class="text-red-500">*</span>
                <span class="text-xs font-normal text-gray-400 ml-2">Short summary shown on blog listing (max 500 chars)</span>
            </label>
            <textarea name="excerpt" rows="3" required maxlength="500"
                      placeholder="Write a brief, engaging summary of this post..."
                      class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition resize-none">{{ old('excerpt') }}</textarea>
        </div>

        {{-- Body --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <label class="block text-sm font-semibold text-gray-700 mb-2">
                Full Article <span class="text-red-500">*</span>
                <span class="text-xs font-normal text-gray-400 ml-2">Use blank lines to separate paragraphs</span>
            </label>
            <textarea name="body" rows="18" required
                      placeholder="Write the full article content here..."
                      class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition resize-y font-mono">{{ old('body') }}</textarea>
        </div>

        {{-- Actions --}}
        <div class="flex items-center justify-end gap-3 pb-8">
            <a href="{{ route('admin.blog.index') }}" class="px-6 py-3 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-xl transition">Cancel</a>
            <button type="submit" class="px-8 py-3 text-sm font-semibold text-white bg-primary-600 hover:bg-primary-700 rounded-xl shadow transition flex items-center gap-2">
                <i class="fas fa-save"></i> Save Post
            </button>
        </div>
    </form>
</div>
@endsection
