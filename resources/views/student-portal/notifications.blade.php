@extends('layouts.spa')

@section('title', 'Notifications')

@section('content')
<div class="px-6 py-8">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Notifications</h1>
        <p class="text-gray-600 mt-2">Stay updated with school announcements and important messages</p>
    </div>

    <!-- Notifications List -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        @forelse($notifications as $notification)
            <div class="border-b border-gray-200 last:border-b-0 p-6 hover:bg-gray-50 transition">
                <div class="flex items-start gap-4">
                    <div class="flex-shrink-0 mt-1">
                        @switch($notification['type'] ?? 'default')
                            @case('attendance')
                                <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center">
                                    <i class="fas fa-clipboard-list text-blue-600"></i>
                                </div>
                                @break
                            @case('result')
                                <div class="w-10 h-10 rounded-full bg-green-100 flex items-center justify-center">
                                    <i class="fas fa-chart-bar text-green-600"></i>
                                </div>
                                @break
                            @case('announcement')
                                <div class="w-10 h-10 rounded-full bg-yellow-100 flex items-center justify-center">
                                    <i class="fas fa-bell text-yellow-600"></i>
                                </div>
                                @break
                            @case('event')
                                <div class="w-10 h-10 rounded-full bg-purple-100 flex items-center justify-center">
                                    <i class="fas fa-calendar-alt text-purple-600"></i>
                                </div>
                                @break
                            @default
                                <div class="w-10 h-10 rounded-full bg-gray-100 flex items-center justify-center">
                                    <i class="fas fa-info-circle text-gray-600"></i>
                                </div>
                        @endswitch
                    </div>

                    <div class="flex-1 min-w-0">
                        <p class="text-gray-900 font-medium">{{ $notification['message'] ?? 'No message' }}</p>
                        <p class="text-gray-500 text-sm mt-1">
                            @if(isset($notification['date']))
                                {{ $notification['date']->diffForHumans() }}
                            @else
                                Just now
                            @endif
                        </p>
                    </div>

                    @if(!($notification['read'] ?? true))
                        <div class="flex-shrink-0">
                            <span class="inline-block w-3 h-3 bg-blue-500 rounded-full"></span>
                        </div>
                    @endif
                </div>
            </div>
        @empty
            <div class="p-6 text-center">
                <div class="w-12 h-12 rounded-full bg-gray-100 flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-bell-slash text-gray-400 text-lg"></i>
                </div>
                <p class="text-gray-500 font-medium">No notifications yet</p>
                <p class="text-gray-400 text-sm mt-1">You'll receive notifications about important school events and updates here</p>
            </div>
        @endforelse
    </div>

    <!-- Info Card -->
    <div class="mt-8 bg-blue-50 border border-blue-200 rounded-lg p-6">
        <div class="flex gap-4">
            <div class="flex-shrink-0 mt-0.5">
                <i class="fas fa-info-circle text-blue-600 text-xl"></i>
            </div>
            <div>
                <h3 class="font-medium text-blue-900">About Notifications</h3>
                <p class="text-blue-800 text-sm mt-1">
                    You'll receive notifications about:
                </p>
                <ul class="text-blue-800 text-sm mt-2 space-y-1 ml-4">
                    <li>✓ Attendance-related updates and complaint reviews</li>
                    <li>✓ New results and grade postings</li>
                    <li>✓ Important school announcements</li>
                    <li>✓ Upcoming events and holidays</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection
