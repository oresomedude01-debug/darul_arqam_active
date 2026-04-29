@extends('layouts.spa')

@section('title', 'Blog Management')

@section('content')
<div class="max-w-7xl mx-auto p-6">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Blog Management</h1>
            <p class="mt-1 text-gray-500">Create and manage public blog posts</p>
        </div>
        <a href="{{ route('admin.blog.create') }}"
           class="inline-flex items-center gap-2 px-5 py-2.5 bg-primary-600 hover:bg-primary-700 text-white font-semibold rounded-xl shadow transition-all">
            <i class="fas fa-plus"></i> New Post
        </a>
    </div>

    {{-- Alerts --}}
    @if(session('success'))
    <div class="mb-6 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl flex items-center gap-2">
        <i class="fas fa-check-circle"></i> {{ session('success') }}
    </div>
    @endif

    {{-- Stats --}}
    <div class="grid grid-cols-3 gap-4 mb-8">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 text-center">
            <p class="text-3xl font-extrabold text-gray-800">{{ $stats['total'] }}</p>
            <p class="text-sm text-gray-500 mt-1">Total Posts</p>
        </div>
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 text-center">
            <p class="text-3xl font-extrabold text-green-600">{{ $stats['published'] }}</p>
            <p class="text-sm text-gray-500 mt-1">Published</p>
        </div>
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 text-center">
            <p class="text-3xl font-extrabold text-amber-500">{{ $stats['draft'] }}</p>
            <p class="text-sm text-gray-500 mt-1">Drafts</p>
        </div>
    </div>

    {{-- Filters --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 mb-6 flex flex-wrap gap-3 items-center">
        <span class="text-sm font-semibold text-gray-600">Filter:</span>
        @foreach(['all'=>'All','published'=>'Published','draft'=>'Draft'] as $key=>$label)
            <a href="{{ route('admin.blog.index', array_merge(request()->query(), ['status'=>$key])) }}"
               class="px-4 py-1.5 rounded-full text-sm font-medium transition {{ $status===$key ? 'bg-primary-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-primary-50 hover:text-primary-700' }}">
                {{ $label }}
            </a>
        @endforeach
        <span class="mx-2 text-gray-300">|</span>
        @foreach(['all'=>'All Categories','news'=>'News','islamic'=>'Islamic Studies','events'=>'Events','tips'=>'Study Tips'] as $key=>$label)
            <a href="{{ route('admin.blog.index', array_merge(request()->query(), ['category'=>$key])) }}"
               class="px-4 py-1.5 rounded-full text-sm font-medium transition {{ $category===$key ? 'bg-primary-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-primary-50 hover:text-primary-700' }}">
                {{ $label }}
            </a>
        @endforeach
    </div>

    {{-- Posts Table --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        @if($posts->isEmpty())
            <div class="text-center py-20">
                <i class="fas fa-newspaper text-gray-300 text-5xl mb-4"></i>
                <p class="text-gray-500 font-medium">No posts found.</p>
                <a href="{{ route('admin.blog.create') }}" class="mt-4 inline-flex items-center gap-2 text-primary-600 font-semibold hover:underline">
                    <i class="fas fa-plus"></i> Create your first post
                </a>
            </div>
        @else
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="text-left px-6 py-4 font-semibold text-gray-700">Post</th>
                        <th class="text-left px-4 py-4 font-semibold text-gray-700 hidden md:table-cell">Category</th>
                        <th class="text-left px-4 py-4 font-semibold text-gray-700 hidden lg:table-cell">Author</th>
                        <th class="text-left px-4 py-4 font-semibold text-gray-700">Status</th>
                        <th class="text-left px-4 py-4 font-semibold text-gray-700 hidden sm:table-cell">Date</th>
                        <th class="text-right px-6 py-4 font-semibold text-gray-700">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($posts as $post)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-lg bg-gradient-to-br {{ $post->cover_color }} flex items-center justify-center flex-shrink-0">
                                    <i class="{{ $post->cover_icon }} text-white/70 text-sm"></i>
                                </div>
                                <div class="min-w-0">
                                    <p class="font-semibold text-gray-800 truncate max-w-xs">{{ $post->title }}</p>
                                    <p class="text-xs text-gray-400 truncate max-w-xs">{{ $post->excerpt }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-4 hidden md:table-cell">
                            <span class="px-2 py-1 rounded-full text-xs font-semibold {{ $post->category_color }}">{{ $post->category_label }}</span>
                        </td>
                        <td class="px-4 py-4 text-gray-600 hidden lg:table-cell">{{ $post->author->name ?? '—' }}</td>
                        <td class="px-4 py-4">
                            @if($post->status === 'published')
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 bg-green-50 text-green-700 rounded-full text-xs font-semibold">
                                    <span class="w-1.5 h-1.5 bg-green-500 rounded-full"></span> Published
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 bg-amber-50 text-amber-700 rounded-full text-xs font-semibold">
                                    <span class="w-1.5 h-1.5 bg-amber-400 rounded-full"></span> Draft
                                </span>
                            @endif
                        </td>
                        <td class="px-4 py-4 text-gray-500 text-xs hidden sm:table-cell">
                            {{ $post->published_at ? $post->published_at->format('d M Y') : $post->created_at->format('d M Y') }}
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center justify-end gap-2">
                                @if($post->status === 'published')
                                    <a href="{{ route('blog.show', $post->slug) }}" target="_blank"
                                       class="p-2 text-gray-400 hover:text-primary-600 hover:bg-primary-50 rounded-lg transition" title="View">
                                        <i class="fas fa-eye text-sm"></i>
                                    </a>
                                @endif
                                <form action="{{ route('admin.blog.toggle', $post) }}" method="POST">
                                    @csrf @method('PATCH')
                                    <button type="submit"
                                            class="p-2 rounded-lg transition text-sm {{ $post->status==='published' ? 'text-amber-500 hover:bg-amber-50' : 'text-green-600 hover:bg-green-50' }}"
                                            title="{{ $post->status==='published' ? 'Move to Draft' : 'Publish' }}">
                                        <i class="fas {{ $post->status==='published' ? 'fa-eye-slash' : 'fa-check-circle' }}"></i>
                                    </button>
                                </form>
                                <a href="{{ route('admin.blog.edit', $post) }}"
                                   class="p-2 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition" title="Edit">
                                    <i class="fas fa-pen text-sm"></i>
                                </a>
                                <form action="{{ route('admin.blog.destroy', $post) }}" method="POST"
                                      onsubmit="return confirm('Delete this post permanently?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="p-2 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition" title="Delete">
                                        <i class="fas fa-trash text-sm"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="px-6 py-4 border-t border-gray-100">
                {{ $posts->withQueryString()->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
