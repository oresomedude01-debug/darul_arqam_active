@extends('student-portal.layout')

@section('student-content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="bg-gradient-to-r from-purple-600 to-pink-600 rounded-2xl p-8 text-white shadow-lg">
        <h1 class="text-3xl font-bold flex items-center gap-3">
            <i class="fas fa-calendar-alt"></i>Academic Calendar
        </h1>
        <p class="text-purple-100 mt-2">Important dates and events for {{ $currentSession?->name ?? 'this session' }}</p>
    </div>

    <!-- Calendar Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Calendar -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-2xl shadow-md border border-gray-200 overflow-hidden">
                <!-- Month Header -->
                <div class="bg-gradient-to-r from-purple-600 to-pink-600 px-6 py-4 text-white flex items-center justify-between">
                    <button class="hover:bg-white/20 p-2 rounded-lg transition">
                        <i class="fas fa-chevron-left"></i>
                    </button>
                    <h2 class="text-xl font-bold">{{ now()->format('F Y') }}</h2>
                    <button class="hover:bg-white/20 p-2 rounded-lg transition">
                        <i class="fas fa-chevron-right"></i>
                    </button>
                </div>

                <!-- Calendar Days -->
                <div class="p-6">
                    <!-- Day Headers -->
                    <div class="grid grid-cols-7 gap-2 mb-4">
                        @foreach(['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'] as $day)
                            <div class="text-center font-bold text-gray-600 text-sm py-2">{{ $day }}</div>
                        @endforeach
                    </div>

                    <!-- Calendar Days -->
                    <div class="grid grid-cols-7 gap-2">
                        @php
                            $firstDay = now()->startOfMonth();
                            $daysInMonth = now()->daysInMonth;
                            $startingDayOfWeek = $firstDay->dayOfWeek;
                        @endphp

                        @for($i = 0; $i < $startingDayOfWeek; $i++)
                            <div class="aspect-square"></div>
                        @endfor

                        @for($day = 1; $day <= $daysInMonth; $day++)
                            <div class="aspect-square flex items-center justify-center rounded-lg border border-gray-200 hover:bg-purple-50 hover:border-purple-300 cursor-pointer transition"
                                 :class="{ 'bg-purple-100 border-purple-600 font-bold': {{ $day }} === {{ now()->day }}, 'text-gray-600': {{ $day }} !== {{ now()->day }} }">
                                {{ $day }}
                            </div>
                        @endfor
                    </div>
                </div>
            </div>
        </div>

        <!-- Upcoming Events Sidebar -->
        <div class="space-y-4">
            <!-- Events -->
            <div class="bg-white rounded-2xl shadow-md border border-gray-200 p-6">
                <h3 class="font-bold text-gray-900 mb-4 flex items-center gap-2">
                    <i class="fas fa-bell text-purple-600"></i>Upcoming Events
                </h3>
                <div class="space-y-3">
                    <!-- Sample Events -->
                    <div class="border-l-4 border-blue-500 pl-4 py-2">
                        <p class="font-semibold text-gray-900 text-sm">Mid-Term Exams</p>
                        <p class="text-xs text-gray-600 mt-1">
                            <i class="fas fa-calendar-day"></i> March 10-20, 2025
                        </p>
                    </div>

                    <div class="border-l-4 border-green-500 pl-4 py-2">
                        <p class="font-semibold text-gray-900 text-sm">Term Break</p>
                        <p class="text-xs text-gray-600 mt-1">
                            <i class="fas fa-calendar-day"></i> March 21-31, 2025
                        </p>
                    </div>

                    <div class="border-l-4 border-purple-500 pl-4 py-2">
                        <p class="font-semibold text-gray-900 text-sm">Sports Day</p>
                        <p class="text-xs text-gray-600 mt-1">
                            <i class="fas fa-calendar-day"></i> April 15, 2025
                        </p>
                    </div>

                    <div class="border-l-4 border-pink-500 pl-4 py-2">
                        <p class="font-semibold text-gray-900 text-sm">End of Term 2</p>
                        <p class="text-xs text-gray-600 mt-1">
                            <i class="fas fa-calendar-day"></i> May 30, 2025
                        </p>
                    </div>

                    <div class="border-l-4 border-amber-500 pl-4 py-2">
                        <p class="font-semibold text-gray-900 text-sm">Long Vacation</p>
                        <p class="text-xs text-gray-600 mt-1">
                            <i class="fas fa-calendar-day"></i> June 1 - August 31, 2025
                        </p>
                    </div>
                </div>
            </div>

            <!-- Legend -->
            <div class="bg-gradient-to-br from-purple-50 to-pink-50 rounded-2xl border border-purple-200 p-4">
                <h4 class="font-bold text-purple-900 mb-3 text-sm">Event Types</h4>
                <div class="space-y-2 text-xs">
                    <div class="flex items-center gap-2">
                        <div class="w-3 h-3 rounded-full bg-blue-500"></div>
                        <span class="text-gray-700">Exams</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="w-3 h-3 rounded-full bg-green-500"></div>
                        <span class="text-gray-700">Holidays</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="w-3 h-3 rounded-full bg-purple-500"></div>
                        <span class="text-gray-700">Sports Events</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="w-3 h-3 rounded-full bg-pink-500"></div>
                        <span class="text-gray-700">Academic Events</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
