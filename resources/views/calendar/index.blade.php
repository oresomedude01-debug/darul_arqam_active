@extends('layouts.spa')

@section('title', 'Calendar & Events')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-slate-100 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
        <!-- Header Section -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-4xl font-bold text-gray-900">Calendar & Events</h1>
                <p class="text-gray-600 mt-2">Manage school events and academic calendar</p>
            </div>
            @hasPermission('create-event')
            <a href="{{ route('events.create') }}" class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-blue-600 to-blue-700 text-white font-semibold rounded-xl hover:from-blue-700 hover:to-blue-800 transition-all shadow-lg hover:shadow-xl hover:-translate-y-1">
                <i class="fas fa-plus mr-2"></i>Add Event
            </a>
            @endhasPermission
        </div>

        <!-- Main Content Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Left Column: Calendar -->
            <div class="lg:col-span-2">
                @if(auth()->user()->hasPermission('create-event') || auth()->user()->hasPermission('edit-event'))
                <div class="bg-white rounded-2xl shadow-lg overflow-hidden border border-blue-100 hover:shadow-xl transition-shadow">
                    <!-- Month Navigation Header -->
                    <div class="bg-gradient-to-r from-blue-600 via-blue-500 to-blue-600 px-6 py-5">
                        <div class="flex items-center justify-between">
                            <a href="{{ route('calendar.index', ['year' => $prevDate->year, 'month' => $prevDate->month]) }}" 
                               class="inline-flex items-center px-4 py-2 bg-white/20 backdrop-blur-sm text-white rounded-lg hover:bg-white/30 transition-all font-medium">
                                <i class="fas fa-chevron-left mr-2"></i>Previous
                            </a>
                            <h2 class="text-2xl font-bold text-white">{{ $prevDate->addMonths(1)->format('F Y') }}</h2>
                            <a href="{{ route('calendar.index', ['year' => $nextDate->year, 'month' => $nextDate->month]) }}" 
                               class="inline-flex items-center px-4 py-2 bg-white/20 backdrop-blur-sm text-white rounded-lg hover:bg-white/30 transition-all font-medium">
                                Next<i class="fas fa-chevron-right ml-2"></i>
                            </a>
                        </div>
                    </div>

                    <!-- Calendar Grid -->
                    <div class="p-6">
                        <!-- Day Headers -->
                        <div class="grid grid-cols-7 gap-2 mb-4">
                            @foreach(['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'] as $day)
                            <div class="text-center font-bold text-sm text-blue-700 py-2 bg-blue-50 rounded-lg">
                                {{ $day }}
                            </div>
                            @endforeach
                        </div>

                        <!-- Calendar Days Grid -->
                        <div class="space-y-2">
                            @foreach($calendar as $week)
                            <div class="grid grid-cols-7 gap-2">
                                @foreach($week as $day)
                                <div class="min-h-28 p-3 border-2 rounded-xl transition-all
                                    @if($day['date'] && $day['date']->isToday()) 
                                        bg-gradient-to-br from-blue-100 to-blue-50 border-blue-400 shadow-md ring-2 ring-blue-300/50
                                    @elseif(!$day['date']) 
                                        bg-gray-50 border-gray-200
                                    @else 
                                        bg-white border-gray-200 hover:border-blue-300 hover:shadow-md hover:bg-gradient-to-br hover:from-blue-50 hover:to-white
                                    @endif">
                                    @if($day['date'])
                                        <div class="text-sm font-bold 
                                            @if($day['date']->isToday()) text-blue-700 @else text-gray-700 @endif
                                            mb-2">
                                            {{ $day['date']->day }}
                                        </div>
                                        <div class="space-y-1">
                                            @foreach($day['events'] as $event)
                                            <a href="{{ route('events.edit', $event) }}" 
                                               class="block text-xs px-2 py-1 rounded-lg text-white truncate font-medium transition-all hover:shadow-md hover:scale-105 transform"
                                               style="background-color: {{ $event->type_color }};"
                                               title="{{ $event->title }}">
                                                {{ $event->title }}
                                            </a>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                                @endforeach
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                @else
                <!-- No Permission Message -->
                <div class="bg-gradient-to-br from-amber-50 to-orange-50 rounded-2xl shadow-lg border-2 border-amber-200 p-12 text-center">
                    <i class="fas fa-lock text-5xl text-amber-600 mb-4 block"></i>
                    <h3 class="text-xl font-bold text-amber-900 mb-2">Limited Access</h3>
                    <p class="text-amber-800">You don't have permission to view the calendar. Please contact an administrator if you need access.</p>
                </div>
                @endif
            </div>

            <!-- Right Column: Sidebar -->
            <div class="space-y-6">
                <!-- Upcoming Events Card -->
                <div class="bg-white rounded-2xl shadow-lg overflow-hidden border border-green-100 hover:shadow-xl transition-shadow">
                    <div class="bg-gradient-to-r from-green-600 to-emerald-600 px-6 py-4">
                        <h3 class="font-bold text-lg text-white flex items-center gap-2">
                            <i class="fas fa-calendar-check"></i>Upcoming Events
                        </h3>
                    </div>
                    <div class="p-6">
                        @if($upcomingEvents->count() > 0)
                            <div class="space-y-3">
                                @foreach($upcomingEvents as $event)
                                <div class="pb-3 border-b last:border-b-0 hover:bg-green-50/50 p-3 rounded-lg transition-all group">
                                    <div class="flex items-start gap-3">
                                        <div class="w-3 h-3 rounded-full mt-1.5 flex-shrink-0 shadow-md group-hover:scale-125 transition-transform"
                                             style="background-color: {{ $event->type_color }};"></div>
                                        <div class="flex-1 min-w-0">
                                            <p class="font-semibold text-sm text-gray-900">{{ $event->title }}</p>
                                            <p class="text-xs text-gray-600 mt-1">
                                                {{ $event->start_date->format('M d, Y') }}
                                            </p>
                                            <span class="inline-block mt-2 text-xs bg-gradient-to-r from-green-100 to-emerald-100 text-green-800 px-2.5 py-1 rounded-lg font-medium">
                                                {{ $event->type_name }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-sm text-gray-500 text-center py-8">
                                <i class="fas fa-inbox text-3xl text-gray-300 mb-2 block"></i>
                                No upcoming events
                            </p>
                        @endif
                    </div>
                </div>

                <!-- Event Types Card -->
                <div class="bg-white rounded-2xl shadow-lg overflow-hidden border border-purple-100 hover:shadow-xl transition-shadow">
                    <div class="bg-gradient-to-r from-purple-600 to-pink-600 px-6 py-4">
                        <h3 class="font-bold text-lg text-white flex items-center gap-2">
                            <i class="fas fa-palette"></i>Event Types
                        </h3>
                    </div>
                    <div class="p-6">
                        <div class="space-y-2.5">
                            @foreach(['holiday' => 'Holiday', 'exam' => 'Exam', 'break' => 'Break', 'meeting' => 'Meeting', 'celebration' => 'Celebration', 'other' => 'Other'] as $type => $label)
                            <div class="flex items-center gap-3 p-2.5 rounded-lg hover:bg-purple-50 transition-colors group cursor-pointer">
                                <div class="w-4 h-4 rounded-lg shadow-sm group-hover:scale-110 transition-transform" style="background-color: {{ match($type) {
                                    'holiday' => '#f59e0b',
                                    'exam' => '#8b5cf6',
                                    'break' => '#ef4444',
                                    'meeting' => '#3b82f6',
                                    'celebration' => '#06b6d4',
                                    'other' => '#6b7280',
                                    default => '#6b7280'
                                } }};"></div>
                                <span class="text-sm font-medium text-gray-700 group-hover:text-gray-900 transition-colors">{{ $label }}</span>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Events List Table -->
        @if($events->count() > 0 && auth()->user()->hasPermission('view-calendar'))
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden border border-indigo-100 hover:shadow-xl transition-shadow">
            <div class="bg-gradient-to-r from-indigo-600 to-purple-600 px-6 py-5">
                <h3 class="font-bold text-lg text-white flex items-center gap-2">
                    <i class="fas fa-list"></i>Events for {{ $prevDate->addMonths(1)->format('F Y') }}
                </h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gradient-to-r from-indigo-50 to-purple-50 border-b-2 border-indigo-200">
                        <tr>
                            <th class="px-6 py-4 text-left text-sm font-bold text-indigo-900">Event</th>
                            <th class="px-6 py-4 text-left text-sm font-bold text-indigo-900">Type</th>
                            <th class="px-6 py-4 text-left text-sm font-bold text-indigo-900">Date Range</th>
                            <th class="px-6 py-4 text-left text-sm font-bold text-indigo-900">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($events as $event)
                        <tr class="hover:bg-indigo-50/50 transition-colors">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-3 h-3 rounded-full shadow-sm" style="background-color: {{ $event->type_color }};"></div>
                                    <span class="font-semibold text-gray-900">{{ $event->title }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-block bg-gradient-to-r from-gray-100 to-gray-200 text-gray-800 text-xs px-3 py-1.5 rounded-lg font-semibold">
                                    {{ $event->type_name }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">
                                {{ $event->start_date->format('M d') }}
                                @if($event->is_multi_day)
                                    - {{ $event->end_date->format('M d, Y') }}
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2">
                                    @hasPermission('edit-event')
                                    <a href="{{ route('events.edit', $event) }}" class="inline-flex items-center justify-center w-8 h-8 text-blue-600 hover:text-blue-700 hover:bg-blue-50 rounded-lg transition-all group">
                                        <i class="fas fa-edit group-hover:scale-110 transition-transform"></i>
                                    </a>
                                    @endhasPermission
                                    @hasPermission('delete-event')
                                    <form method="POST" action="{{ route('events.destroy', $event) }}" class="inline" 
                                          onsubmit="return confirm('Are you sure you want to delete this event?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="inline-flex items-center justify-center w-8 h-8 text-red-600 hover:text-red-700 hover:bg-red-50 rounded-lg transition-all group">
                                            <i class="fas fa-trash group-hover:scale-110 transition-transform"></i>
                                        </button>
                                    </form>
                                    @endhasPermission
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
