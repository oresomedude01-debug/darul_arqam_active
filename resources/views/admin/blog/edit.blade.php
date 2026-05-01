@extends('layouts.spa')

@section('title', 'Edit Blog Post')

@section('content')
<div class="max-w-4xl mx-auto p-6">

    <div class="mb-8 flex items-center justify-between gap-4">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.blog.index') }}" class="p-2 text-gray-400 hover:text-gray-700 hover:bg-gray-100 rounded-lg transition">
                <i class="fas fa-arrow-left"></i>
            </a>
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Edit Blog Post</h1>
                <p class="text-gray-500 mt-1">Make changes to your article</p>
            </div>
        </div>
        @if($blog->status === 'published')
            <a href="{{ route('blog.show', $blog->slug) }}" target="_blank"
               class="px-4 py-2 bg-gray-100 text-gray-700 hover:bg-gray-200 font-medium rounded-lg text-sm transition flex items-center gap-2">
                <i class="fas fa-external-link-alt"></i> View Public Post
            </a>
        @endif
    </div>

    @if($errors->any())
    <div class="mb-6 bg-red-50 border border-red-200 text-red-700 px-4 py-4 rounded-xl">
        <ul class="list-disc list-inside text-sm space-y-1">
            @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
        </ul>
    </div>
    @endif

    <form action="{{ route('admin.blog.update', $blog) }}" method="POST"
          enctype="multipart/form-data" class="space-y-6">
        @csrf @method('PUT')

        {{-- Title --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <label class="block text-sm font-semibold text-gray-700 mb-2">Post Title <span class="text-red-500">*</span></label>
            <input type="text" name="title" value="{{ old('title', $blog->title) }}" required
                   class="w-full px-4 py-3 border border-gray-200 rounded-xl text-lg font-semibold focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition">
        </div>

        {{-- Meta --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 grid sm:grid-cols-2 gap-6"
             x-data="{ type: '{{ old('type', $blog->type) }}' }">
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Post Type <span class="text-red-500">*</span></label>
                <select name="type" x-model="type" required class="w-full px-4 py-2.5 border border-gray-200 rounded-xl bg-white focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition">
                    <option value="article">Standard Article</option>
                    <option value="video">Video Blog (YouTube)</option>
                </select>
            </div>
            <div x-show="type === 'video'" x-cloak>
                <label class="block text-sm font-semibold text-gray-700 mb-2">YouTube Video ID</label>
                <input type="text" name="youtube_video_id" value="{{ old('youtube_video_id', $blog->youtube_video_id) }}"
                       placeholder="e.g. dQw4w9WgXcQ"
                       class="w-full px-4 py-2.5 border border-gray-200 rounded-xl bg-white focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition">
                <p class="text-xs text-gray-500 mt-1">The 11-character ID from the YouTube URL.</p>
            </div>

            <div class="sm:col-span-2 grid sm:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Category <span class="text-red-500">*</span></label>
                    <select name="category" required class="w-full px-4 py-2.5 border border-gray-200 rounded-xl bg-white focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition">
                        <option value="news"    {{ old('category', $blog->category)==='news'    ? 'selected':'' }}>News</option>
                        <option value="islamic" {{ old('category', $blog->category)==='islamic' ? 'selected':'' }}>Islamic Studies</option>
                        <option value="events"  {{ old('category', $blog->category)==='events'  ? 'selected':'' }}>Events</option>
                        <option value="tips"    {{ old('category', $blog->category)==='tips'    ? 'selected':'' }}>Study Tips</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Status <span class="text-red-500">*</span></label>
                    <select name="status" required class="w-full px-4 py-2.5 border border-gray-200 rounded-xl bg-white focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition">
                        <option value="draft"     {{ old('status', $blog->status)==='draft'     ? 'selected':'' }}>Draft</option>
                        <option value="published" {{ old('status', $blog->status)==='published' ? 'selected':'' }}>Published</option>
                    </select>
                    @if($blog->published_at)
                        <p class="text-xs text-gray-500 mt-2"><i class="fas fa-info-circle mr-1"></i> Originally published: {{ $blog->published_at->format('d M Y, h:i A') }}</p>
                    @endif
                </div>
            </div>
        </div>

        {{-- Featured Image --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6"
             x-data="{
                preview: '{{ $blog->featured_image ? asset('storage/'.$blog->featured_image) : '' }}',
                existing: '{{ $blog->featured_image ? 'yes' : '' }}',
                removeFlag: false
             }">
            <label class="block text-sm font-semibold text-gray-700 mb-1">Featured Image</label>
            <p class="text-xs text-gray-400 mb-4">Recommended: 1200×630px · JPG, PNG or WebP · Max 3MB. Upload a new image to replace the current one.</p>

            <input type="hidden" name="remove_image" :value="removeFlag ? '1' : '0'">

            {{-- Drop zone --}}
            <div class="relative border-2 border-dashed rounded-xl transition-all cursor-pointer overflow-hidden"
                 :class="preview ? 'border-transparent' : 'border-gray-200 hover:border-primary-400 bg-gray-50'"
                 @click="$refs.imgInput.click()">

                <input type="file" name="featured_image" accept="image/*" x-ref="imgInput" class="hidden"
                       @change="if($event.target.files[0]){ preview=URL.createObjectURL($event.target.files[0]); removeFlag=false; }">

                {{-- Preview --}}
                <template x-if="preview">
                    <div class="relative group">
                        <img :src="preview" class="w-full h-64 object-cover rounded-xl">
                        <div class="absolute inset-0 bg-black/30 flex items-center justify-center opacity-0 group-hover:opacity-100 transition rounded-xl">
                            <span class="text-white text-sm font-semibold bg-black/50 px-3 py-1.5 rounded-lg">
                                <i class="fas fa-exchange-alt mr-1"></i> Change Image
                            </span>
                        </div>
                    </div>
                </template>

                {{-- Placeholder --}}
                <template x-if="!preview">
                    <div class="flex flex-col items-center justify-center py-12 text-gray-400">
                        <div class="w-14 h-14 bg-gray-100 rounded-full flex items-center justify-center mb-3">
                            <i class="fas fa-image text-2xl text-gray-300"></i>
                        </div>
                        <p class="font-medium text-gray-500">Drop image here or <span class="text-primary-500 underline">browse</span></p>
                        <p class="text-xs mt-1">JPG, PNG, WebP up to 3MB</p>
                    </div>
                </template>
            </div>

            {{-- Remove / action buttons --}}
            <div class="flex items-center gap-4 mt-3">
                <template x-if="preview">
                    <button type="button"
                            @click.prevent="preview=null; removeFlag=true; $refs.imgInput.value=''"
                            class="text-xs text-red-500 hover:text-red-700 flex items-center gap-1 font-medium">
                        <i class="fas fa-trash-alt"></i> Remove image
                    </button>
                </template>
                <template x-if="!preview && existing">
                    <span class="text-xs text-amber-600 flex items-center gap-1">
                        <i class="fas fa-exclamation-triangle"></i> Current image will be removed on save
                    </span>
                </template>
            </div>
        </div>

        {{-- Cover Style (fallback) --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-sm font-semibold text-gray-700 mb-1">Cover Style <span class="text-gray-400 font-normal">(fallback if no featured image)</span></h3>
            <div class="grid grid-cols-2 gap-6 mt-4">
                <div>
                    <label class="block text-xs text-gray-500 mb-2">Gradient Color</label>
                    <select name="cover_color" class="w-full px-4 py-2.5 border border-gray-200 rounded-xl bg-white text-sm focus:ring-2 focus:ring-primary-500 transition">
                        <option value="from-brand-500 to-brand-700" {{ old('cover_color', $blog->cover_color)==='from-brand-500 to-brand-700'?'selected':'' }}>Brand (Blue)</option>
                        <option value="from-blue-500 to-blue-700"   {{ old('cover_color', $blog->cover_color)==='from-blue-500 to-blue-700'  ?'selected':'' }}>Blue</option>
                        <option value="from-green-500 to-green-700" {{ old('cover_color', $blog->cover_color)==='from-green-500 to-green-700' ?'selected':'' }}>Green</option>
                        <option value="from-purple-500 to-purple-700" {{ old('cover_color', $blog->cover_color)==='from-purple-500 to-purple-700'?'selected':'' }}>Purple</option>
                        <option value="from-amber-400 to-amber-600" {{ old('cover_color', $blog->cover_color)==='from-amber-400 to-amber-600' ?'selected':'' }}>Amber</option>
                        <option value="from-teal-500 to-teal-700"   {{ old('cover_color', $blog->cover_color)==='from-teal-500 to-teal-700'   ?'selected':'' }}>Teal</option>
                        <option value="from-rose-500 to-rose-700"   {{ old('cover_color', $blog->cover_color)==='from-rose-500 to-rose-700'   ?'selected':'' }}>Rose</option>
                        <option value="from-indigo-500 to-indigo-700" {{ old('cover_color', $blog->cover_color)==='from-indigo-500 to-indigo-700'?'selected':'' }}>Indigo</option>
                        <option value="from-sky-500 to-sky-700"     {{ old('cover_color', $blog->cover_color)==='from-sky-500 to-sky-700'     ?'selected':'' }}>Sky</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs text-gray-500 mb-2">Icon (Font Awesome class)</label>
                    <select name="cover_icon" class="w-full px-4 py-2.5 border border-gray-200 rounded-xl bg-white text-sm focus:ring-2 focus:ring-primary-500 transition">
                        <option value="fas fa-newspaper"      {{ old('cover_icon', $blog->cover_icon)==='fas fa-newspaper'      ?'selected':'' }}>Newspaper</option>
                        <option value="fas fa-quran"          {{ old('cover_icon', $blog->cover_icon)==='fas fa-quran'          ?'selected':'' }}>Quran</option>
                        <option value="fas fa-graduation-cap" {{ old('cover_icon', $blog->cover_icon)==='fas fa-graduation-cap' ?'selected':'' }}>Graduation Cap</option>
                        <option value="fas fa-mosque"         {{ old('cover_icon', $blog->cover_icon)==='fas fa-mosque'         ?'selected':'' }}>Mosque</option>
                        <option value="fas fa-book-open"      {{ old('cover_icon', $blog->cover_icon)==='fas fa-book-open'      ?'selected':'' }}>Book Open</option>
                        <option value="fas fa-star"           {{ old('cover_icon', $blog->cover_icon)==='fas fa-star'           ?'selected':'' }}>Star</option>
                        <option value="fas fa-pen-fancy"      {{ old('cover_icon', $blog->cover_icon)==='fas fa-pen-fancy'      ?'selected':'' }}>Pen</option>
                        <option value="fas fa-lightbulb"      {{ old('cover_icon', $blog->cover_icon)==='fas fa-lightbulb'      ?'selected':'' }}>Lightbulb</option>
                        <option value="fas fa-bullhorn"       {{ old('cover_icon', $blog->cover_icon)==='fas fa-bullhorn'       ?'selected':'' }}>Bullhorn</option>
                        <option value="fas fa-award"          {{ old('cover_icon', $blog->cover_icon)==='fas fa-award'          ?'selected':'' }}>Award</option>
                    </select>
                </div>
            </div>
        </div>

        {{-- Excerpt --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <label class="block text-sm font-semibold text-gray-700 mb-2">Excerpt <span class="text-red-500">*</span></label>
            <textarea name="excerpt" rows="3" required maxlength="500"
                      class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition resize-none">{{ old('excerpt', $blog->excerpt) }}</textarea>
        </div>

        {{-- Body --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <label class="block text-sm font-semibold text-gray-700 mb-2">Full Article <span class="text-red-500">*</span></label>
            
            <!-- Quill Editor Container -->
            <div id="blog-editor-container" class="bg-white rounded-lg border border-gray-200" style="height: 400px;"></div>
            
            <!-- Hidden textarea to store the content for form submission -->
            <textarea name="body" id="blog-body-content" required style="display: none;">{{ old('body', $blog->body) }}</textarea>
        </div>

        {{-- Actions --}}
        <div class="flex items-center justify-end gap-3 pb-8">
            <a href="{{ route('admin.blog.index') }}" class="px-6 py-3 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-xl transition">Cancel</a>
            <button type="submit" class="px-8 py-3 text-sm font-semibold text-white bg-primary-600 hover:bg-primary-700 rounded-xl shadow transition flex items-center gap-2">
                <i class="fas fa-save"></i> Save Changes
            </button>
        </div>
    </form>
</div>
@endsection
