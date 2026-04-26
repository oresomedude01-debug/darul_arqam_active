@extends('student-portal.layout')

@section('student-content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="bg-gradient-to-r from-indigo-600 to-purple-600 rounded-2xl p-8 text-white shadow-lg">
        <h1 class="text-3xl font-bold flex items-center gap-3">
            <i class="fas fa-clock"></i>Class Timetable
        </h1>
        <p class="text-indigo-100 mt-2">View your weekly class schedule</p>
    </div>

    @php
        $hasTimetable = collect($timetable)->flatten()->count() > 0;
    @endphp

    @if(!$hasTimetable)
        <!-- Empty State -->
        <div class="bg-white rounded-2xl shadow-md border border-gray-200 p-12 text-center">
            <i class="fas fa-calendar-times text-6xl text-gray-300 mb-4"></i>
            <p class="text-xl font-semibold text-gray-900 mb-2">No Timetable Available</p>
            <p class="text-gray-600">Your class timetable will be displayed here once it's scheduled.</p>
        </div>
    @else
        <!-- Timetable Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach(['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'] as $day)
                <div class="bg-white rounded-2xl shadow-md border border-gray-200 overflow-hidden hover:shadow-lg transition-all">
                    <!-- Day Header -->
                    <div class="bg-gradient-to-r from-indigo-600 to-purple-600 px-6 py-4 text-white">
                        <h3 class="font-bold text-lg">{{ $day }}</h3>
                    </div>

                    <!-- Schedule Items -->
                    <div class="divide-y divide-gray-200">
                        @forelse($timetable[$day] ?? [] as $schedule)
                            <div class="px-6 py-4 hover:bg-gray-50 transition-colors">
                                <!-- Time -->
                                <p class="text-xs font-bold text-indigo-600 uppercase tracking-widest mb-2">
                                    {{ \Carbon\Carbon::parse($schedule->start_time)->format('h:i A') }} - {{ \Carbon\Carbon::parse($schedule->end_time)->format('h:i A') }}
                                </p>

                                <!-- Subject -->
                                <p class="font-bold text-gray-900 text-base mb-2">{{ $schedule->subject?->name ?? 'N/A' }}</p>

                                <!-- Teacher -->
                                @if($schedule->teacher)
                                    <p class="text-sm text-gray-600 flex items-center gap-2 mb-2">
                                        <i class="fas fa-user-tie text-purple-600"></i>{{ $schedule->teacher->name }}
                                    </p>
                                @endif

                                <!-- Room -->
                                @if($schedule->room)
                                    <p class="text-sm text-gray-600 flex items-center gap-2">
                                        <i class="fas fa-door-open text-indigo-600"></i>{{ $schedule->room }}
                                    </p>
                                @endif
                            </div>
                        @empty
                            <div class="px-6 py-8 text-center text-gray-500 text-sm">
                                No classes scheduled
                            </div>
                        @endforelse
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Legend -->
        <div class="bg-blue-50 border border-blue-200 rounded-2xl p-6">
            <h3 class="font-bold text-blue-900 mb-4 flex items-center gap-2">
                <i class="fas fa-info-circle text-blue-600"></i>Timetable Information
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                <div>
                    <p class="font-semibold text-blue-900">Class</p>
                    <p class="text-blue-700">{{ $student->schoolClass->name ?? 'N/A' }}</p>
                </div>
                <div>
                    <p class="font-semibold text-blue-900">Academic Year</p>
                    <p class="text-blue-700">{{ now()->year }} - {{ now()->year + 1 }}</p>
                </div>
                <div>
                    <p class="font-semibold text-blue-900">Last Updated</p>
                    <p class="text-blue-700">{{ now()->format('M d, Y') }}</p>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection
