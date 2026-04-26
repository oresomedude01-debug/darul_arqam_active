@extends('layouts.app')

@section('title', 'Documents')

@section('content')
<div class="min-h-screen bg-gray-50">
    <div class="max-w-6xl mx-auto py-6 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-6">
            <h1 class="text-3xl font-bold text-gray-900">School Documents & Forms</h1>
            <p class="mt-2 text-sm text-gray-600">Access important documents, forms, and school policies</p>
        </div>

        <!-- Document Categories -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            <a href="?category=forms" class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition border-l-4 border-blue-500">
                <div class="text-3xl mb-2">📋</div>
                <h3 class="font-semibold text-gray-900">Forms</h3>
                <p class="text-sm text-gray-600">{{ $categoryCounts['forms'] ?? 0 }} documents</p>
            </a>

            <a href="?category=policies" class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition border-l-4 border-green-500">
                <div class="text-3xl mb-2">📑</div>
                <h3 class="font-semibold text-gray-900">Policies</h3>
                <p class="text-sm text-gray-600">{{ $categoryCounts['policies'] ?? 0 }} documents</p>
            </a>

            <a href="?category=reports" class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition border-l-4 border-yellow-500">
                <div class="text-3xl mb-2">📊</div>
                <h3 class="font-semibold text-gray-900">Reports</h3>
                <p class="text-sm text-gray-600">{{ $categoryCounts['reports'] ?? 0 }} documents</p>
            </a>

            <a href="?category=circulars" class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition border-l-4 border-purple-500">
                <div class="text-3xl mb-2">📢</div>
                <h3 class="font-semibold text-gray-900">Circulars</h3>
                <p class="text-sm text-gray-600">{{ $categoryCounts['circulars'] ?? 0 }} documents</p>
            </a>
        </div>

        <!-- Search & Filter -->
        <div class="bg-white shadow-md rounded-lg p-6 mb-6">
            <form method="GET" class="flex gap-4">
                <div class="flex-1">
                    <input type="text" name="search" placeholder="Search documents..." value="{{ request('search') }}" 
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Search</button>
                <a href="{{ route('parent-portal.documents') }}" class="px-6 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300">Clear</a>
            </form>
        </div>

        <!-- Documents List -->
        <div class="space-y-4">
            @if($documents && $documents->count() > 0)
                @foreach($documents as $document)
                    <div class="bg-white rounded-lg shadow-md hover:shadow-lg transition overflow-hidden">
                        <div class="px-6 py-4 flex items-center justify-between">
                            <div class="flex items-center flex-1">
                                <div class="text-4xl mr-4">
                                    @if(str_ends_with($document->file_path, '.pdf'))
                                        📄
                                    @elseif(str_ends_with($document->file_path, ['.doc', '.docx']))
                                        📝
                                    @elseif(str_ends_with($document->file_path, ['.xls', '.xlsx']))
                                        📊
                                    @else
                                        📁
                                    @endif
                                </div>
                                <div class="flex-1">
                                    <h3 class="text-lg font-semibold text-gray-900">{{ $document->title }}</h3>
                                    <p class="text-sm text-gray-600 mt-1">{{ $document->description }}</p>
                                    <div class="flex items-center gap-4 mt-2 text-xs text-gray-500">
                                        <span>📅 {{ \Carbon\Carbon::parse($document->created_at)->format('M d, Y') }}</span>
                                        <span>💾 {{ $document->file_size ? round($document->file_size / 1024, 2) . ' KB' : 'N/A' }}</span>
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            {{ ucfirst($document->category) }}
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <div class="ml-4 flex items-center gap-2">
                                @if($document->file_path)
                                    <a href="{{ Storage::url($document->file_path) }}" target="_blank" 
                                       class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                                        </svg>
                                        Download
                                    </a>
                                    <a href="{{ Storage::url($document->file_path) }}" target="_blank" 
                                       class="inline-flex items-center px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 text-sm">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                        View
                                    </a>
                                @else
                                    <span class="text-gray-400 text-sm">No file</span>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach

                <!-- Pagination -->
                <div class="mt-6">
                    {{ $documents->links() }}
                </div>
            @else
                <div class="bg-white rounded-lg shadow-md p-12 text-center">
                    <div class="text-gray-400 mb-4">
                        <svg class="w-16 h-16 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                    <p class="text-gray-500 text-lg">No documents found</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
