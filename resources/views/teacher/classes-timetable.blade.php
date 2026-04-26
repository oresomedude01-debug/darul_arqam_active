@extends('layouts.spa')

@section('title', 'My Classes Timetable')

@section('breadcrumb')
    <span class="text-gray-400">Teaching</span>
    <span class="text-gray-400">/</span>
    <span class="text-gray-400">
        <a href="{{ route('teacher.my-classes') }}" class="hover:text-gray-600">My Classes</a>
    </span>
    <span class="text-gray-400">/</span>
    @if(request('class'))
        <span class="font-semibold text-gray-900">Class Timetable</span>
    @else
        <span class="font-semibold text-gray-900">All Classes Timetable</span>
    @endif
@endsection

@section('content')
<div class="space-y-6">
    <!-- Hero Header Section -->
    <div class="bg-gradient-to-r from-indigo-50 to-blue-50 rounded-xl shadow-sm border border-indigo-100 p-8">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-6">
            <div>
                @if(request('class') && $classes->count() === 1)
                    <h1 class="text-4xl font-bold text-gray-900 mb-2">{{ $classes->first()->name }} - Timetable</h1>
                    <p class="text-gray-600 text-lg flex items-center gap-2">
                        <i class="fas fa-calendar-week text-indigo-600"></i>
                        View and print timetable for this class
                    </p>
                @else
                    <h1 class="text-4xl font-bold text-gray-900 mb-2">My Classes Timetable</h1>
                    <p class="text-gray-600 text-lg flex items-center gap-2">
                        <i class="fas fa-calendar-week text-indigo-600"></i>
                        View and print timetables for your assigned classes
                    </p>
                @endif
            </div>
            <div class="flex items-center gap-3">
                <button onclick="window.print()" class="inline-flex items-center gap-2 px-6 py-3 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition font-semibold">
                    <i class="fas fa-print"></i>
                    Print Timetable
                </button>
                @if(request('class'))
                    <a href="{{ route('teacher.my-classes') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition font-semibold">
                        <i class="fas fa-arrow-left"></i>
                        Back
                    </a>
                @endif
            </div>
        </div>
    </div>

    @if($classes->isEmpty())
        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-6 text-center">
            <i class="fas fa-info-circle text-yellow-600 text-3xl mb-3"></i>
            <p class="text-gray-700 font-semibold">No classes assigned</p>
            <p class="text-gray-500 mt-2">You don't have any classes assigned yet. Contact your administrator.</p>
        </div>
    @else
        <!-- Classes Timetables -->
        @foreach($classes as $class)
        <div class="card shadow-sm border border-gray-200">
            <div class="card-header bg-gradient-to-r from-indigo-500 to-blue-500 text-white">
                <h3 class="text-xl font-bold">
                    <i class="fas fa-door-open mr-2"></i>{{ $class->name }}
                </h3>
                <p class="text-indigo-100 text-sm mt-1">{{ $class->full_name }}</p>
            </div>

            <div class="card-body">
                <!-- Subjects Section -->
                @if($class->subjects->isNotEmpty())
                <div class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                    <h4 class="text-sm font-semibold text-gray-900 mb-3 flex items-center gap-2">
                        <i class="fas fa-book text-blue-600"></i>
                        Subjects Assigned to this Class
                    </h4>
                    <div class="flex flex-wrap gap-2">
                        @foreach($class->subjects as $subject)
                        <span class="inline-flex items-center gap-2 px-3 py-1 bg-white border border-blue-300 text-blue-700 rounded-full text-sm font-medium">
                            <i class="fas fa-bookmark text-blue-600"></i>
                            {{ $subject->name }}
                        </span>
                        @endforeach
                    </div>
                </div>
                @endif

                @if($class->timetables->isEmpty())
                    <div class="bg-gray-50 border border-gray-200 rounded-lg p-6 text-center">
                        <p class="text-gray-600">No timetable entries for this class</p>
                    </div>
                @else
                    @php
                        $dayLabels = [
                            'monday' => 'Monday',
                            'tuesday' => 'Tuesday',
                            'wednesday' => 'Wednesday',
                            'thursday' => 'Thursday',
                            'friday' => 'Friday',
                            'saturday' => 'Saturday',
                            'sunday' => 'Sunday'
                        ];
                    @endphp
                    <!-- Days Grid Layout (Table Format) -->
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 bg-white">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase border-r border-gray-200" style="width: 140px;">Period / Time</th>
                                    @foreach($schoolDays as $day)
                                        @php $dayKey = strtolower($day); @endphp
                                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase border-r border-gray-200 last:border-r-0">
                                            {{ $dayLabels[$dayKey] ?? ucfirst($dayKey) }}
                                        </th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @php
                                    // Group timetables by period number and time
                                    $timetableGrid = [];
                                    foreach($class->timetables as $entry) {
                                        $timeSlot = substr($entry->start_time, 11, 5) . ' - ' . substr($entry->end_time, 11, 5);
                                        $key = $entry->period_number . '_' . $timeSlot;

                                        if (!isset($timetableGrid[$key])) {
                                            $timetableGrid[$key] = [
                                                'period_number' => $entry->period_number,
                                                'time_slot' => $timeSlot,
                                                'entries' => []
                                            ];
                                        }

                                        $timetableGrid[$key]['entries'][strtolower($entry->day_of_week)] = $entry;
                                    }

                                    // Sort by period number
                                    uasort($timetableGrid, function($a, $b) {
                                        return $a['period_number'] <=> $b['period_number'];
                                    });
                                @endphp

                                @if(count($timetableGrid) > 0)
                                    @foreach($timetableGrid as $key => $periodData)
                                    <tr class="hover:bg-gray-50 transition-colors">
                                        <td class="px-4 py-3 border-r border-gray-200 whitespace-nowrap bg-gray-50">
                                            <div class="text-sm font-medium text-gray-900">Period {{ $periodData['period_number'] }}</div>
                                            <div class="text-xs text-gray-500">{{ $periodData['time_slot'] }}</div>
                                        </td>
                                        @foreach($schoolDays as $day)
                                            @php
                                                $dayKey = strtolower($day);
                                                $entry = $periodData['entries'][$dayKey] ?? null;
                                            @endphp
                                            <td class="px-3 py-3 border-r border-gray-200 last:border-r-0 align-top">
                                                @if($entry)
                                                    @if($entry->type === 'break')
                                                        <div class="bg-green-50 border border-green-200 rounded p-2 text-center">
                                                            <p class="text-xs font-semibold text-green-700 uppercase">Break</p>
                                                        </div>
                                                    @elseif($entry->type === 'lunch')
                                                        <div class="bg-orange-50 border border-orange-200 rounded p-2 text-center">
                                                            <p class="text-xs font-semibold text-orange-700 uppercase">Lunch</p>
                                                        </div>
                                                    @elseif($entry->type === 'assembly')
                                                        <div class="bg-purple-50 border border-purple-200 rounded p-2 text-center">
                                                            <p class="text-xs font-semibold text-purple-700 uppercase">Assembly</p>
                                                        </div>
                                                    @else
                                                        <div class="bg-blue-50 border border-blue-200 rounded p-2">
                                                            <p class="text-sm font-semibold text-blue-900">
                                                                {{ $entry->subject ? $entry->subject->name : 'No Subject' }}
                                                            </p>
                                                            <p class="text-xs text-blue-600 mt-1">
                                                                <i class="fas fa-clock text-xs mr-1"></i>{{ substr($entry->start_time, 11, 5) }} - {{ substr($entry->end_time, 11, 5) }}
                                                            </p>
                                                            @if($entry->teacher)
                                                                <p class="text-xs text-blue-600 mt-1">
                                                                    <i class="fas fa-user-tie text-xs mr-1"></i>{{ $entry->teacher->full_name }}
                                                                </p>
                                                            @endif
                                                            @if($entry->room_number)
                                                                <p class="text-xs text-blue-600 mt-1">
                                                                    <i class="fas fa-door-open text-xs mr-1"></i>Room: {{ $entry->room_number }}
                                                                </p>
                                                            @endif
                                                            @if($entry->notes)
                                                                <p class="text-xs text-blue-500 mt-1">
                                                                    <i class="fas fa-sticky-note text-xs mr-1"></i>{{ $entry->notes }}
                                                                </p>
                                                            @endif
                                                        </div>
                                                    @endif
                                                @else
                                                    <div class="bg-gray-50 border border-gray-200 rounded p-2 text-center">
                                                        <p class="text-xs text-gray-400">-</p>
                                                    </div>
                                                @endif
                                            </td>
                                        @endforeach
                                    </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="{{ count($schoolDays) + 1 }}" class="px-4 py-6 text-center">
                                            <p class="text-gray-500">No timetable entries for this class</p>
                                        </td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
        @endforeach
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
