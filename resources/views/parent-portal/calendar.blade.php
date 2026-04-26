@extends('layouts.spa')

@section('title', 'School Calendar')

@section('content')
<div class="min-h-screen bg-gray-50">
    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-6">
            <h1 class="text-3xl font-bold text-gray-900">School Calendar & Events</h1>
            <p class="mt-2 text-sm text-gray-600">View important school dates and events</p>
        </div>

        <!-- Calendar Navigation -->
        <div class="bg-white shadow-md rounded-lg p-6 mb-6">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-xl font-semibold text-gray-900">{{ \Carbon\Carbon::now()->format('F Y') }}</h2>
                <div class="flex gap-2">
                    <a href="?month={{ \Carbon\Carbon::now()->subMonth()->format('m') }}&year={{ \Carbon\Carbon::now()->subMonth()->format('Y') }}" class="px-4 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300">← Previous</a>
                    <a href="?month={{ \Carbon\Carbon::now()->format('m') }}&year={{ \Carbon\Carbon::now()->format('Y') }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Today</a>
                    <a href="?month={{ \Carbon\Carbon::now()->addMonth()->format('m') }}&year={{ \Carbon\Carbon::now()->addMonth()->format('Y') }}" class="px-4 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300">Next →</a>
                </div>
            </div>

            <!-- Calendar Grid -->
            <div class="grid grid-cols-7 gap-2">
                @php
                    $days = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
                    $firstDay = \Carbon\Carbon::now()->firstOfMonth();
                    $lastDay = \Carbon\Carbon::now()->lastOfMonth();
                @endphp

                @foreach($days as $day)
                    <div class="text-center font-bold text-gray-700 py-2">{{ $day }}</div>
                @endforeach

                @for($i = 0; $i < $firstDay->dayOfWeek; $i++)
                    <div class="bg-gray-100 rounded-lg p-4 text-center text-gray-400 min-h-24"></div>
                @endfor

                @for($day = 1; $day <= $lastDay->day; $day++)
                    @php
                        $date = \Carbon\Carbon::now()->setDay($day);
                        $dayEvents = $events->filter(fn($e) => \Carbon\Carbon::parse($e->start_date)->format('Y-m-d') === $date->format('Y-m-d'));
                    @endphp
                    <div class="bg-white border-2 border-gray-200 rounded-lg p-3 min-h-24 hover:border-blue-400 transition cursor-pointer">
                        <div class="text-sm font-semibold text-gray-900">{{ $day }}</div>
                        <div class="mt-1 space-y-1">
                            @foreach($dayEvents->take(2) as $event)
                                <div class="text-xs bg-blue-100 text-blue-800 rounded px-2 py-1 truncate" title="{{ $event->title }}">
                                    {{ $event->title }}
                                </div>
                            @endforeach
                            @if($dayEvents->count() > 2)
                                <div class="text-xs text-gray-500">+{{ $dayEvents->count() - 2 }} more</div>
                            @endif
                        </div>
                    </div>
                @endfor
            </div>
        </div>

        <!-- Upcoming Events -->
        <div class="bg-white shadow-md rounded-lg overflow-hidden">
            <div class="px-6 py-4 bg-gradient-to-r from-blue-600 to-blue-700">
                <h2 class="text-lg font-semibold text-white">Upcoming Events</h2>
            </div>

            <div class="divide-y">
                @if($upcomingEvents && $upcomingEvents->count() > 0)
                    @foreach($upcomingEvents as $event)
                        <div class="px-6 py-4 hover:bg-gray-50 transition">
                            <div class="flex justify-between items-start">
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900">{{ $event->title }}</h3>
                                    <p class="mt-1 text-sm text-gray-600">{{ $event->description }}</p>
                                    <div class="mt-2 flex items-center gap-4 text-sm text-gray-500">
                                        <span>📅 {{ \Carbon\Carbon::parse($event->start_date)->format('M d, Y') }}</span>
                                        <span>🕐 {{ \Carbon\Carbon::parse($event->start_time)->format('h:i A') }} - {{ \Carbon\Carbon::parse($event->end_time)->format('h:i A') }}</span>
                                    </div>
                                </div>
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium 
                                    @if($event->type === 'holiday') bg-red-100 text-red-800
                                    @elseif($event->type === 'exam') bg-yellow-100 text-yellow-800
                                    @elseif($event->type === 'activity') bg-green-100 text-green-800
                                    @else bg-blue-100 text-blue-800 @endif">
                                    {{ ucfirst($event->type) }}
                                </span>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="px-6 py-12 text-center">
                        <p class="text-gray-500">No upcoming events scheduled</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
