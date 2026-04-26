@extends('layouts.spa')

@section('title', 'My Timetable')

@section('breadcrumb')
    <span class="text-gray-400">Teaching</span>
    <span class="text-gray-400">/</span>
    <span class="font-semibold text-gray-900">My Timetable</span>
@endsection

@section('content')
<div class="space-y-6">
    <!-- Hero Header Section -->
    <div class="bg-gradient-to-r from-purple-50 to-pink-50 rounded-xl shadow-sm border border-purple-100 p-8">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-6">
            <div>
                <h1 class="text-4xl font-bold text-gray-900 mb-2">My Timetable</h1>
                <p class="text-gray-600 text-lg flex items-center gap-2">
                    <i class="fas fa-calendar-check text-purple-600"></i>
                    Your teaching schedule across all classes
                </p>
            </div>
            <button onclick="window.print()" class="inline-flex items-center gap-2 px-6 py-3 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition font-semibold">
                <i class="fas fa-print"></i>
                Print Schedule
            </button>
        </div>
    </div>

    @if($timetables->isEmpty())
        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-6 text-center">
            <i class="fas fa-info-circle text-yellow-600 text-3xl mb-3"></i>
            <p class="text-gray-700 font-semibold">No timetable entries</p>
            <p class="text-gray-500 mt-2">You haven't been assigned any classes yet.</p>
        </div>
    @else
        <!-- Weekly Schedule -->
        <div x-data="{ activeDay: 'monday' }" class="space-y-4">
            <!-- Day Selector -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 flex gap-2 flex-wrap">
                @php
                    $days = ['monday' => 'Monday', 'tuesday' => 'Tuesday', 'wednesday' => 'Wednesday', 'thursday' => 'Thursday', 'friday' => 'Friday'];
                @endphp
                @foreach($days as $dayKey => $dayName)
                    <button @click="activeDay = '{{ $dayKey }}'"
                            :class="activeDay === '{{ $dayKey }}' ? 'bg-purple-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'"
                            class="px-4 py-2 rounded-lg font-medium text-sm transition">
                        {{ $dayName }}
                    </button>
                @endforeach
            </div>

            <!-- Timetable for each day -->
            @foreach($days as $dayKey => $dayName)
                @php
                    $dayTimetables = $timetables->filter(fn($t) => strtolower($t->day_of_week) === $dayKey)->sortBy('start_time');
                @endphp
                <div x-show="activeDay === '{{ $dayKey }}'" class="space-y-3">
                    @if($dayTimetables->isEmpty())
                        <div class="bg-gray-50 border border-gray-200 rounded-lg p-8 text-center">
                            <i class="fas fa-calendar-times text-gray-400 text-3xl mb-3 block"></i>
                            <p class="text-gray-600 font-medium">No classes scheduled for {{ $dayName }}</p>
                        </div>
                    @else
                        @foreach($dayTimetables as $timetable)
                            <div class="card shadow-sm border border-gray-200 hover:shadow-md transition">
                                <div class="card-body">
                                    <div class="flex items-start justify-between gap-4">
                                        <!-- Time Box -->
                                        <div class="bg-gradient-to-br from-purple-500 to-pink-500 text-white rounded-lg p-4 text-center min-w-max">
                                            <p class="text-2xl font-bold">{{ substr($timetable->start_time, 0, 5) }}</p>
                                            <p class="text-sm text-purple-100">to</p>
                                            <p class="text-lg font-semibold">{{ substr($timetable->end_time, 0, 5) }}</p>
                                        </div>

                                        <!-- Class Details -->
                                        <div class="flex-1">
                                            <div class="flex items-center gap-3 mb-2">
                                                <i class="fas fa-book text-purple-600 text-lg"></i>
                                                <h3 class="text-xl font-bold text-gray-900">{{ $timetable->subject?->name ?? 'No Subject' }}</h3>
                                            </div>
                                            
                                            <div class="space-y-2 text-sm">
                                                <div class="flex items-center gap-2 text-gray-600">
                                                    <i class="fas fa-door-open text-purple-600"></i>
                                                    <span class="font-semibold">Class:</span>
                                                    <span>{{ $timetable->schoolClass->name }}</span>
                                                </div>

                                                @if($timetable->room_number)
                                                    <div class="flex items-center gap-2 text-gray-600">
                                                        <i class="fas fa-door-open text-pink-600"></i>
                                                        <span class="font-semibold">Room:</span>
                                                        <span>{{ $timetable->room_number }}</span>
                                                    </div>
                                                @endif

                                                @if($timetable->notes)
                                                    <div class="flex items-start gap-2 text-gray-600">
                                                        <i class="fas fa-sticky-note text-purple-600 mt-0.5"></i>
                                                        <span>{{ $timetable->notes }}</span>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @endif
                </div>
            @endforeach
        </div>

        <!-- Summary Stats -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-8">
            <div class="card shadow-sm border border-gray-200">
                <div class="card-body">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Total Classes</p>
                            <p class="text-2xl font-bold text-purple-600 mt-1">{{ $timetables->unique('school_class_id')->count() }}</p>
                        </div>
                        <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-door-open text-purple-600 text-lg"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm border border-gray-200">
                <div class="card-body">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Total Periods</p>
                            <p class="text-2xl font-bold text-pink-600 mt-1">{{ $timetables->count() }}</p>
                        </div>
                        <div class="w-12 h-12 bg-pink-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-calendar-check text-pink-600 text-lg"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm border border-gray-200">
                <div class="card-body">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Teaching Days</p>
                            <p class="text-2xl font-bold text-indigo-600 mt-1">{{ $timetables->unique('day_of_week')->count() }}</p>
                        </div>
                        <div class="w-12 h-12 bg-indigo-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-calendar-week text-indigo-600 text-lg"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

<!-- Print Styles -->
<style media="print">
    @page {
        size: A4;
        margin: 1cm;
    }
    
    body {
        background: white;
    }
    
    .no-print {
        display: none !important;
    }
    
    .card {
        page-break-inside: avoid;
    }
</style>
@endsection
