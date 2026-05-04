@extends('layouts.spa')

@section('title', __('gallery.title'))

@section('content')
<div class="max-w-7xl mx-auto p-4 sm:p-6">

    {{-- ── Header ── --}}
    <div class="flex items-center justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">{{ __('gallery.title') }}</h1>
            <p class="mt-0.5 text-gray-500 text-sm hidden sm:block">{{ __('gallery.subtitle') }}</p>
        </div>
        <a href="{{ route('admin.gallery.create') }}"
           class="inline-flex items-center gap-2 px-4 py-2.5 bg-primary-600 hover:bg-primary-700 text-white font-semibold rounded-xl shadow transition-all text-sm flex-shrink-0">
            <i class="fas fa-plus"></i>
            <span class="hidden xs:inline">{{ __('gallery.new_gallery') }}</span>
            <span class="xs:hidden">{{ __('common.new') }}</span>
        </a>
    </div>

    {{-- ── Alert ── --}}
    @if($message = session('success'))
        <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-4 rounded-lg">
            <i class="fas fa-check-circle mr-2"></i>{{ $message }}
        </div>
    @endif

    @if($message = session('error'))
        <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-4 rounded-lg">
            <i class="fas fa-exclamation-circle mr-2"></i>{{ $message }}
        </div>
    @endif

    {{-- ── Stats ── --}}
    <div class="grid grid-cols-1 xs:grid-cols-3 gap-4 mb-8">
        <div class="bg-white rounded-lg shadow-sm p-4 border-l-4 border-blue-500">
            <p class="text-sm text-gray-600 font-medium">{{ __('gallery.total_galleries') }}</p>
            <p class="text-3xl font-bold text-gray-900 mt-1">{{ $stats['total'] }}</p>
        </div>
        <div class="bg-white rounded-lg shadow-sm p-4 border-l-4 border-green-500">
            <p class="text-sm text-gray-600 font-medium">{{ __('common.published') }}</p>
            <p class="text-3xl font-bold text-gray-900 mt-1">{{ $stats['published'] }}</p>
        </div>
        <div class="bg-white rounded-lg shadow-sm p-4 border-l-4 border-amber-500">
            <p class="text-sm text-gray-600 font-medium">{{ __('common.draft') }}</p>
            <p class="text-3xl font-bold text-gray-900 mt-1">{{ $stats['draft'] }}</p>
        </div>
    </div>

    {{-- ── Filters & Search ── --}}
    <div class="mb-6 flex flex-col sm:flex-row gap-3 items-center justify-between bg-white rounded-lg shadow-sm p-4">
        <form method="GET" action="{{ route('admin.gallery.index') }}" class="flex-1 flex gap-2 w-full">
            <input type="text" name="search" value="{{ request()->query('search') }}"
                   placeholder="{{ __('common.search') }}..."
                   class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-600 focus:border-transparent text-sm">
            <button type="submit" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium rounded-lg transition text-sm">
                <i class="fas fa-search"></i>
            </button>
        </form>

        <div class="flex gap-2">
            @foreach(['all'=>__('common.all'),'published'=>'✅ '.__('common.published'),'draft'=>'📝 '.__('common.draft')] as $key=>$label)
                <a href="{{ route('admin.gallery.index', array_merge(request()->query(), ['status'=>$key, 'page'=>1])) }}"
                   class="px-3 py-1 rounded-full text-xs font-semibold transition
                          {{ $status===$key ? 'bg-primary-600 text-white shadow' : 'bg-gray-100 text-gray-600 hover:bg-primary-50 hover:text-primary-700' }}">
                    {{ $label }}
                </a>
            @endforeach
        </div>
    </div>

    {{-- ── Gallery Grid ── --}}
    @if($galleries->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($galleries as $gallery)
                <div class="group bg-white rounded-xl shadow-md hover:shadow-xl transition-all overflow-hidden">
                    {{-- Cover ── --}}
                    <div class="h-32 flex items-center justify-center text-white text-5xl font-bold transition-transform group-hover:scale-105"
                         style="background-color: {{ $gallery->cover_color }}">
                        <i class="{{ $gallery->cover_icon }}"></i>
                    </div>

                    {{-- Content ── --}}
                    <div class="p-4">
                        {{-- Title ── --}}
                        <h3 class="text-lg font-bold text-gray-900 truncate">{{ $gallery->title }}</h3>

                        {{-- Description ── --}}
                        @if($gallery->description)
                            <p class="text-sm text-gray-600 mt-1 line-clamp-2">{{ $gallery->description }}</p>
                        @endif

                        {{-- Meta ── --}}
                        <div class="mt-3 flex items-center justify-between text-xs text-gray-500 space-x-3">
                            <span class="inline-flex items-center gap-1.5">
                                <i class="fas fa-image"></i>
                                {{ $gallery->items_count }} {{ __('gallery.images') }}
                            </span>
                            <span class="inline-flex items-center gap-1.5">
                                <i class="fas fa-eye"></i>
                                {{ $gallery->view_count }}
                            </span>
                        </div>

                        {{-- Status ── --}}
                        <div class="mt-3">
                            @if($gallery->status === 'published')
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-green-50 text-green-700 rounded-full text-xs font-semibold">
                                    <span class="w-1.5 h-1.5 bg-green-500 rounded-full"></span> {{ __('common.published') }}
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-amber-50 text-amber-700 rounded-full text-xs font-semibold">
                                    <span class="w-1.5 h-1.5 bg-amber-400 rounded-full"></span> {{ __('common.draft') }}
                                </span>
                            @endif
                        </div>

                        {{-- Date ── --}}
                        <p class="text-xs text-gray-400 mt-2">
                            {{ $gallery->uploaded_at ? $gallery->uploaded_at->format('d M Y') : $gallery->created_at->format('d M Y') }}
                        </p>

                        {{-- Actions ── --}}
                        <div class="mt-4 pt-4 border-t border-gray-200 flex gap-2">
                            <a href="{{ route('admin.gallery.edit', $gallery) }}" class="flex-1 px-3 py-2 bg-blue-500 hover:bg-blue-600 text-white font-medium rounded-lg transition text-sm text-center">
                                <i class="fas fa-edit mr-1"></i>{{ __('common.edit') }}
                            </a>
                            <form action="{{ route('admin.gallery.destroy', $gallery) }}" method="POST" class="flex-1" onsubmit="return confirm('{{ __('gallery.confirm_delete') }}')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="w-full px-3 py-2 bg-red-500 hover:bg-red-600 text-white font-medium rounded-lg transition text-sm">
                                    <i class="fas fa-trash mr-1"></i>{{ __('common.delete') }}
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Pagination ── --}}
        <div class="mt-8">
            {{ $galleries->links() }}
        </div>
    @else
        <div class="text-center py-12 bg-white rounded-lg shadow-sm">
            <i class="fas fa-image text-5xl text-gray-300 mb-4"></i>
            <h3 class="text-lg font-semibold text-gray-500">{{ __('common.no_results') }}</h3>
            <p class="text-gray-400 mt-1">{{ __('common.no_results_found') }}</p>
        </div>
    @endif

</div>
@endsection
