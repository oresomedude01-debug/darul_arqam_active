@extends('layouts.spa')

@section('title', 'Blog Management')

@section('content')
<div class="max-w-7xl mx-auto p-4 sm:p-6">

    {{-- ── Header ── --}}
    <div class="flex items-center justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">Blog</h1>
            <p class="mt-0.5 text-gray-500 text-sm hidden sm:block">Create and manage public blog posts</p>
        </div>
        <a href="{{ route('admin.blog.create') }}"
           class="inline-flex items-center gap-2 px-4 py-2.5 bg-primary-600 hover:bg-primary-700 text-white font-semibold rounded-xl shadow transition-all text-sm flex-shrink-0">
            <i class="fas fa-plus"></i>
            <span class="hidden xs:inline">New Post</span>
            <span class="xs:hidden">New</span>
        </a>
    </div>

    {{-- ── Alert ── --}}
    @if(session('success'))
    <div class="mb-5 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl flex items-center gap-2 text-sm">
        <i class="fas fa-check-circle"></i> {{ session('success') }}
    </div>
    @endif

    {{-- ── Stats (compact on mobile) ── --}}
    <div class="grid grid-cols-3 gap-3 mb-6">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-3 sm:p-5 text-center">
            <p class="text-2xl sm:text-3xl font-extrabold text-gray-800">{{ $stats['total'] }}</p>
            <p class="text-xs sm:text-sm text-gray-500 mt-0.5">Total</p>
        </div>
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-3 sm:p-5 text-center">
            <p class="text-2xl sm:text-3xl font-extrabold text-green-600">{{ $stats['published'] }}</p>
            <p class="text-xs sm:text-sm text-gray-500 mt-0.5">Published</p>
        </div>
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-3 sm:p-5 text-center">
            <p class="text-2xl sm:text-3xl font-extrabold text-amber-500">{{ $stats['draft'] }}</p>
            <p class="text-xs sm:text-sm text-gray-500 mt-0.5">Drafts</p>
        </div>
    </div>

    {{-- ── Search + Filters ── --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 mb-5">
        {{-- Search bar --}}
        <form method="GET" action="{{ route('admin.blog.index') }}" class="mb-3">
            <input type="hidden" name="status"   value="{{ $status }}">
            <input type="hidden" name="category" value="{{ $category }}">
            <div class="relative">
                <i class="fas fa-search absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                <input type="text" name="search" value="{{ $search }}"
                       placeholder="Search by title or excerpt…"
                       class="w-full pl-10 pr-10 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition bg-gray-50">
                @if($search)
                    <a href="{{ route('admin.blog.index', ['status'=>$status,'category'=>$category]) }}"
                       class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times text-sm"></i>
                    </a>
                @endif
            </div>
        </form>

        {{-- Filter chips --}}
        <div class="flex flex-wrap gap-2">
            {{-- Status --}}
            @foreach(['all'=>'All','published'=>'✅ Published','draft'=>'📝 Draft'] as $key=>$label)
                <a href="{{ route('admin.blog.index', array_merge(request()->query(), ['status'=>$key, 'page'=>1])) }}"
                   class="px-3 py-1 rounded-full text-xs font-semibold transition
                          {{ $status===$key ? 'bg-primary-600 text-white shadow' : 'bg-gray-100 text-gray-600 hover:bg-primary-50 hover:text-primary-700' }}">
                    {{ $label }}
                </a>
            @endforeach

            <span class="w-px h-5 bg-gray-200 self-center mx-1"></span>

            {{-- Category --}}
            @foreach(['all'=>'All Categories','news'=>'📰 News','islamic'=>'📖 Islamic','events'=>'🎉 Events','tips'=>'💡 Tips'] as $key=>$label)
                <a href="{{ route('admin.blog.index', array_merge(request()->query(), ['category'=>$key, 'page'=>1])) }}"
                   class="px-3 py-1 rounded-full text-xs font-semibold transition
                          {{ $category===$key ? 'bg-primary-600 text-white shadow' : 'bg-gray-100 text-gray-600 hover:bg-primary-50 hover:text-primary-700' }}">
                    {{ $label }}
                </a>
            @endforeach
        </div>
    </div>

    {{-- ── Posts ── --}}
    @if($posts->isEmpty())
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 text-center py-20 px-6">
            <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-5">
                <i class="fas fa-newspaper text-gray-300 text-3xl"></i>
            </div>
            <p class="text-gray-500 font-semibold text-lg">No posts found</p>
            <p class="text-gray-400 text-sm mt-1">
                @if($search)
                    No results for "<strong>{{ $search }}</strong>"
                @else
                    Try adjusting your filters or create a new post.
                @endif
            </p>
            <a href="{{ route('admin.blog.create') }}" class="mt-5 inline-flex items-center gap-2 text-primary-600 font-semibold hover:underline text-sm">
                <i class="fas fa-plus"></i> Create your first post
            </a>
        </div>

    @else

        {{-- ────────────────── MOBILE GRID (< md) ────────────────── --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 md:hidden">
            @foreach($posts as $post)
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden flex flex-col">

                {{-- Thumbnail --}}
                <div class="relative h-36 flex-shrink-0">
                    @if($post->featured_image)
                        <img src="{{ asset('storage/'.$post->featured_image) }}"
                             alt="{{ $post->title }}"
                             class="w-full h-full object-cover">
                    @else
                        <div class="w-full h-full bg-gradient-to-br {{ $post->cover_color }} flex items-center justify-center">
                            <i class="{{ $post->cover_icon }} text-white/30 text-5xl"></i>
                        </div>
                    @endif

                    {{-- Status badge --}}
                    <div class="absolute top-2 left-2">
                        @if($post->status === 'published')
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 bg-green-500 text-white rounded-full text-xs font-bold">
                                <span class="w-1.5 h-1.5 bg-white rounded-full"></span> Live
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 bg-amber-400 text-white rounded-full text-xs font-bold">
                                <span class="w-1.5 h-1.5 bg-white rounded-full"></span> Draft
                            </span>
                        @endif
                    </div>

                    {{-- Category badge --}}
                    <div class="absolute top-2 right-2">
                        <span class="px-2 py-0.5 rounded-full text-xs font-semibold {{ $post->category_color }}">
                            {{ $post->category_label }}
                        </span>
                    </div>
                </div>

                {{-- Content --}}
                <div class="p-4 flex flex-col flex-1">
                    <p class="font-bold text-gray-800 text-sm leading-snug line-clamp-2 mb-1">{{ $post->title }}</p>
                    <p class="text-xs text-gray-400 mb-3">
                        {{ $post->published_at ? $post->published_at->format('d M Y') : $post->created_at->format('d M Y') }}
                        &bull; {{ $post->author->name ?? '—' }}
                    </p>
                    <p class="text-xs text-blue-600 font-semibold mb-3">
                        <i class="fas fa-eye"></i> {{ number_format($post->view_count ?? 0) }} views
                    </p>

                    {{-- Actions row --}}
                    <div class="flex items-center gap-1 mt-auto pt-3 border-t border-gray-100">
                        @if($post->status === 'published')
                            <a href="{{ route('blog.show', $post->slug) }}" target="_blank"
                               class="flex-1 flex items-center justify-center gap-1.5 py-2 text-xs font-semibold text-gray-600 bg-gray-50 hover:bg-gray-100 rounded-lg transition">
                                <i class="fas fa-eye"></i> View
                            </a>
                        @endif
                        <form action="{{ route('admin.blog.toggle', $post) }}" method="POST" class="flex-1">
                            @csrf @method('PATCH')
                            <button type="submit"
                                    class="w-full flex items-center justify-center gap-1.5 py-2 text-xs font-semibold rounded-lg transition
                                           {{ $post->status==='published' ? 'text-amber-600 bg-amber-50 hover:bg-amber-100' : 'text-green-600 bg-green-50 hover:bg-green-100' }}">
                                <i class="fas {{ $post->status==='published' ? 'fa-eye-slash' : 'fa-check-circle' }}"></i>
                                {{ $post->status==='published' ? 'Unpublish' : 'Publish' }}
                            </button>
                        </form>
                        <a href="{{ route('admin.blog.edit', $post) }}"
                           class="flex-1 flex items-center justify-center gap-1.5 py-2 text-xs font-semibold text-blue-600 bg-blue-50 hover:bg-blue-100 rounded-lg transition">
                            <i class="fas fa-pen"></i> Edit
                        </a>
                        <form action="{{ route('admin.blog.destroy', $post) }}" method="POST"
                              onsubmit="return confirm('Delete this post permanently?')">
                            @csrf @method('DELETE')
                            <button type="submit"
                                    class="flex items-center justify-center gap-1.5 px-3 py-2 text-xs font-semibold text-red-500 bg-red-50 hover:bg-red-100 rounded-lg transition">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        {{-- ────────────────── DESKTOP TABLE (≥ md) ────────────────── --}}
        <div class="hidden md:block bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="text-left px-6 py-4 font-semibold text-gray-600">Post</th>
                        <th class="text-left px-4 py-4 font-semibold text-gray-600">Category</th>
                        <th class="text-left px-4 py-4 font-semibold text-gray-600 hidden lg:table-cell">Author</th>
                        <th class="text-left px-4 py-4 font-semibold text-gray-600">Status</th>
                        <th class="text-left px-4 py-4 font-semibold text-gray-600">Date</th>
                        <th class="text-center px-4 py-4 font-semibold text-gray-600">Views</th>
                        <th class="text-center px-4 py-4 font-semibold text-gray-600 w-40">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($posts as $post)
                    <tr class="hover:bg-gray-50/70 transition">
                        {{-- Post --}}
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                {{-- Thumbnail --}}
                                <div class="w-12 h-12 rounded-xl overflow-hidden flex-shrink-0">
                                    @if($post->featured_image)
                                        <img src="{{ asset('storage/'.$post->featured_image) }}"
                                             alt="{{ $post->title }}"
                                             class="w-full h-full object-cover">
                                    @else
                                        <div class="w-full h-full bg-gradient-to-br {{ $post->cover_color }} flex items-center justify-center">
                                            <i class="{{ $post->cover_icon }} text-white/70 text-sm"></i>
                                        </div>
                                    @endif
                                </div>
                                <div class="min-w-0">
                                    <p class="font-semibold text-gray-800 truncate max-w-[220px]">{{ $post->title }}</p>
                                    <p class="text-xs text-gray-400 truncate max-w-[220px] mt-0.5">{{ $post->excerpt }}</p>
                                </div>
                            </div>
                        </td>
                        {{-- Category --}}
                        <td class="px-4 py-4">
                            <span class="px-2.5 py-1 rounded-full text-xs font-semibold {{ $post->category_color }}">
                                {{ $post->category_label }}
                            </span>
                        </td>
                        {{-- Author --}}
                        <td class="px-4 py-4 text-gray-600 text-sm hidden lg:table-cell">
                            {{ $post->author->name ?? '—' }}
                        </td>
                        {{-- Status --}}
                        <td class="px-4 py-4">
                            @if($post->status === 'published')
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-green-50 text-green-700 rounded-full text-xs font-semibold">
                                    <span class="w-1.5 h-1.5 bg-green-500 rounded-full"></span> Published
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-amber-50 text-amber-700 rounded-full text-xs font-semibold">
                                    <span class="w-1.5 h-1.5 bg-amber-400 rounded-full"></span> Draft
                                </span>
                            @endif
                        </td>
                        {{-- Date --}}
                        <td class="px-4 py-4 text-gray-500 text-xs whitespace-nowrap">
                            {{ $post->published_at ? $post->published_at->format('d M Y') : $post->created_at->format('d M Y') }}
                        </td>
                        {{-- Views --}}
                        <td class="px-4 py-4 text-center">
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-blue-50 text-blue-700 rounded-full text-xs font-semibold">
                                <i class="fas fa-eye text-blue-500"></i>
                                {{ number_format($post->view_count ?? 0) }}
                            </span>
                        </td>
                        {{-- Actions — dropdown --}}
                        <td class="px-4 py-4">
                            <div class="flex items-center justify-center gap-1">
                                @if($post->status === 'published')
                                    <a href="{{ route('blog.show', $post->slug) }}" target="_blank"
                                       class="p-2 text-gray-400 hover:text-primary-600 hover:bg-primary-50 rounded-lg transition" title="View live">
                                        <i class="fas fa-eye text-sm"></i>
                                    </a>
                                @endif
                                <form action="{{ route('admin.blog.toggle', $post) }}" method="POST">
                                    @csrf @method('PATCH')
                                    <button type="submit"
                                            title="{{ $post->status==='published' ? 'Move to Draft' : 'Publish' }}"
                                            class="p-2 rounded-lg transition
                                                   {{ $post->status==='published' ? 'text-amber-500 hover:bg-amber-50' : 'text-green-600 hover:bg-green-50' }}">
                                        <i class="fas {{ $post->status==='published' ? 'fa-eye-slash' : 'fa-check-circle' }} text-sm"></i>
                                    </button>
                                </form>
                                <a href="{{ route('admin.blog.edit', $post) }}"
                                   class="p-2 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition" title="Edit">
                                    <i class="fas fa-pen text-sm"></i>
                                </a>
                                <form action="{{ route('admin.blog.destroy', $post) }}" method="POST"
                                      onsubmit="return confirm('Delete this post permanently?')">
                                    @csrf @method('DELETE')
                                    <button type="submit"
                                            class="p-2 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition" title="Delete">
                                        <i class="fas fa-trash text-sm"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            {{-- Pagination --}}
            <div class="px-6 py-4 border-t border-gray-100 flex items-center justify-between gap-4">
                <p class="text-sm text-gray-500">
                    Showing {{ $posts->firstItem() }}–{{ $posts->lastItem() }} of {{ $posts->total() }} posts
                </p>
                {{ $posts->withQueryString()->links() }}
            </div>
        </div>

        {{-- Mobile pagination --}}
        <div class="mt-4 md:hidden">
            {{ $posts->withQueryString()->links() }}
        </div>

    @endif

</div>
@endsection
