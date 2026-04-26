@extends('layouts.spa')

@section('title', 'School Announcements')

@section('content')
<div class="min-h-screen bg-gray-50">
    <div class="max-w-4xl mx-auto py-6 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-6">
            <h1 class="text-3xl font-bold text-gray-900">School Announcements</h1>
            <p class="mt-2 text-sm text-gray-600">Stay updated with important school announcements and news</p>
        </div>

        <!-- Announcements List -->
        <div class="space-y-4">
            @if($announcements && $announcements->count() > 0)
                @foreach($announcements as $announcement)
                    <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition">
                        <div class="px-6 py-4 border-l-4 border-blue-600">
                            <div class="flex justify-between items-start">
                                <div class="flex-1">
                                    <h3 class="text-lg font-semibold text-gray-900">{{ $announcement->title }}</h3>
                                    <p class="mt-2 text-gray-700 text-sm leading-relaxed">{{ $announcement->description }}</p>
                                    
                                    @if($announcement->content)
                                        <div class="mt-3 text-sm text-gray-600 bg-gray-50 p-3 rounded">
                                            {{ Str::limit($announcement->content, 150) }}
                                        </div>
                                    @endif
                                </div>
                                <div class="ml-4 text-right flex-shrink-0">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                                        {{ $announcement->category ?? 'General' }}
                                    </span>
                                </div>
                            </div>
                            
                            <div class="mt-4 flex items-center justify-between text-xs text-gray-500">
                                <span>📅 {{ \Carbon\Carbon::parse($announcement->created_at)->format('M d, Y') }}</span>
                                <span>👤 {{ $announcement->author->first_name ?? 'Admin' }} {{ $announcement->author->last_name ?? '' }}</span>
                            </div>
                        </div>
                    </div>
                @endforeach

                <!-- Pagination -->
                <div class="mt-6">
                    {{ $announcements->links() }}
                </div>
            @else
                <div class="bg-white rounded-lg shadow-md p-12 text-center">
                    <div class="text-gray-400 mb-4">
                        <svg class="w-16 h-16 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                        </svg>
                    </div>
                    <p class="text-gray-500 text-lg">No announcements at this time</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
