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

    <form action="{{ route('admin.blog.store') }}" method="POST" enctype="multipart/form-data"
          x-data="blogForm()" @submit="submitForm" class="space-y-6">
        @csrf

        {{-- Title --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <label class="block text-sm font-semibold text-gray-700 mb-2">Post Title <span class="text-red-500">*</span></label>
            <input type="text" name="title" value="{{ old('title') }}" required
                   placeholder="Enter a compelling title..."
                   class="w-full px-4 py-3 border border-gray-200 rounded-xl text-lg font-semibold focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition">
        </div>

        {{-- Meta --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 grid sm:grid-cols-2 gap-6"
             x-data="{ type: '{{ old('type', 'article') }}' }">
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
                        <option value="news"    {{ old('category')==='news'    ? 'selected':'' }}>News</option>
                        <option value="islamic" {{ old('category')==='islamic' ? 'selected':'' }}>Islamic Studies</option>
                        <option value="events"  {{ old('category')==='events'  ? 'selected':'' }}>Events</option>
                        <option value="tips"    {{ old('category')==='tips'    ? 'selected':'' }}>Study Tips</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Status <span class="text-red-500">*</span></label>
                    <select name="status" required class="w-full px-4 py-2.5 border border-gray-200 rounded-xl bg-white focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition">
                        <option value="draft"     {{ old('status','draft')==='draft'     ? 'selected':'' }}>Save as Draft</option>
                        <option value="published" {{ old('status')==='published'         ? 'selected':'' }}>Publish Now</option>
                    </select>
                </div>
            </div>
        </div>

        {{-- Featured Image --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6"
             x-data="{ preview: null, dragging: false }">
            <label class="block text-sm font-semibold text-gray-700 mb-1">Featured Image</label>
            <p class="text-xs text-gray-400 mb-4">Recommended: 1200×630px · JPG, PNG or WebP · Max 3MB. This is displayed as the card thumbnail and article hero.</p>

            {{-- Drop zone --}}
            <div
                class="relative border-2 border-dashed rounded-xl transition-all cursor-pointer overflow-hidden"
                :class="dragging ? 'border-primary-500 bg-primary-50' : 'border-gray-200 hover:border-primary-400 bg-gray-50'"
                @dragover.prevent="dragging=true"
                @dragleave.prevent="dragging=false"
                @drop.prevent="dragging=false; preview=URL.createObjectURL($event.dataTransfer.files[0]); $refs.imgInput.files=$event.dataTransfer.files"
                @click="$refs.imgInput.click()">

                <input type="file" name="featured_image" accept="image/*" x-ref="imgInput" class="hidden"
                       @change="preview=$event.target.files[0] ? URL.createObjectURL($event.target.files[0]) : null">

                {{-- Preview --}}
                <template x-if="preview">
                    <div class="relative">
                        <img :src="preview" class="w-full h-56 object-cover">
                        <div class="absolute inset-0 bg-black/30 flex items-center justify-center opacity-0 hover:opacity-100 transition">
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

            {{-- Clear button --}}
            <template x-if="preview">
                <button type="button" @click.prevent="preview=null; $refs.imgInput.value=''"
                        class="mt-2 text-xs text-red-500 hover:text-red-700 flex items-center gap-1">
                    <i class="fas fa-times"></i> Remove image
                </button>
            </template>
        </div>

        {{-- Cover Style (fallback when no image) --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-sm font-semibold text-gray-700 mb-1">Cover Style <span class="text-gray-400 font-normal">(fallback if no featured image)</span></h3>
            <div class="grid grid-cols-2 gap-6 mt-4">
                <div>
                    <label class="block text-xs text-gray-500 mb-2">Gradient Color</label>
                    <select name="cover_color" class="w-full px-4 py-2.5 border border-gray-200 rounded-xl bg-white text-sm focus:ring-2 focus:ring-primary-500 transition">
                        <option value="from-blue-500 to-blue-700"     {{ old('cover_color')==='from-blue-500 to-blue-700'   ?'selected':'' }}>Blue</option>
                        <option value="from-green-500 to-green-700"   {{ old('cover_color')==='from-green-500 to-green-700' ?'selected':'' }}>Green</option>
                        <option value="from-purple-500 to-purple-700" {{ old('cover_color')==='from-purple-500 to-purple-700'?'selected':'' }}>Purple</option>
                        <option value="from-amber-400 to-amber-600"   {{ old('cover_color')==='from-amber-400 to-amber-600' ?'selected':'' }}>Amber</option>
                        <option value="from-teal-500 to-teal-700"     {{ old('cover_color')==='from-teal-500 to-teal-700'   ?'selected':'' }}>Teal</option>
                        <option value="from-rose-500 to-rose-700"     {{ old('cover_color')==='from-rose-500 to-rose-700'   ?'selected':'' }}>Rose</option>
                        <option value="from-indigo-500 to-indigo-700" {{ old('cover_color')==='from-indigo-500 to-indigo-700'?'selected':'' }}>Indigo</option>
                        <option value="from-sky-500 to-sky-700"       {{ old('cover_color')==='from-sky-500 to-sky-700'     ?'selected':'' }}>Sky</option>
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
                <span class="text-xs font-normal text-gray-400 ml-2">Write and format your article content</span>
            </label>
            
            <!-- Quill Editor Container -->
            <div id="blog-editor-container" class="bg-white rounded-lg border border-gray-200" style="height: 400px;"></div>
            
            <!-- Hidden textarea to store the content for form submission -->
            <textarea name="body" id="blog-body-content" required style="display: none;">{{ old('body') }}</textarea>
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

@push('scripts')
<script>
function blogForm() {
    return {
        isSubmitting: false,
        
        init() {
            console.log('Blog form initialized');
            // Give Quill time to initialize before allowing submission
            setTimeout(() => {
                console.log('Blog form ready for submission');
            }, 500);
        },
        
        async submitForm(e) {
            if (this.isSubmitting) {
                e.preventDefault();
                return;
            }
            
            // Validate that we have content in the Quill editor
            const hiddenTextarea = document.getElementById('blog-body-content');
            const editorContainer = document.getElementById('blog-editor-container');
            const form = e.target;
            
            // Check for required fields
            const title = form.querySelector('input[name="title"]').value.trim();
            const excerpt = form.querySelector('textarea[name="excerpt"]').value.trim();
            
            if (!title) {
                alert('Please enter a post title');
                e.preventDefault();
                return;
            }
            
            if (!excerpt) {
                alert('Please enter an excerpt');
                e.preventDefault();
                return;
            }
            
            // Ensure Quill content is synced
            if (editorContainer && hiddenTextarea) {
                // Wait for Quill to be available
                if (typeof window.quillInstance !== 'undefined' && window.quillInstance) {
                    hiddenTextarea.value = window.quillInstance.root.innerHTML;
                    console.log('Quill content synced:', hiddenTextarea.value);
                }
            }
            
            // Check if body is empty
            if (!hiddenTextarea.value.trim() || hiddenTextarea.value === '<p><br></p>') {
                alert('Please write some content in the article body');
                e.preventDefault();
                return;
            }
            
            this.isSubmitting = true;
            console.log('Form is being submitted');
        }
    };
}
</script>
@endpush
