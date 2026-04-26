@extends('layouts.spa')

@section('title', $student->full_name . ' - Attendance Profile')

@section('breadcrumb')
    <span class="text-gray-400">Attendance</span>
    <span class="text-gray-400">/</span>
    <a href="{{ route('attendance.records') }}" class="text-gray-400 hover:text-gray-600">Records</a>
    <span class="text-gray-400">/</span>
    <span class="font-semibold text-gray-900">{{ $student->full_name }}</span>
@endsection

@section('content')
<div class="space-y-6">
    <!-- Student Header -->
    <div class="card">
        <div class="card-body">
            <div class="flex flex-col md:flex-row items-start md:items-center gap-4">
                <div class="avatar avatar-lg {{ $student->gender === 'male' ? 'bg-blue-500' : 'bg-pink-500' }}">
                    @if($student->photo_path)
                        <img src="{{ Storage::url($student->photo_path) }}" alt="{{ $student->full_name }}" class="w-full h-full rounded-full object-cover">
                    @else
                        <span class="text-3xl">{{ substr($student->first_name, 0, 1) }}{{ substr($student->last_name, 0, 1) }}</span>
                    @endif
                </div>
                <div class="flex-1">
                    <h1 class="text-2xl font-bold text-gray-900">{{ $student->full_name }}</h1>
                    <div class="flex flex-wrap gap-3 mt-2 text-sm text-gray-600">
                        <span><i class="fas fa-id-card mr-1 text-gray-400"></i> {{ $student->admission_number }}</span>
                        <span><i class="fas fa-school mr-1 text-gray-400"></i> {{ $student->class_level }} - Section {{ $student->section }}</span>
                        <span><i class="fas fa-{{ $student->gender === 'male' ? 'male' : 'female' }} mr-1 text-gray-400"></i> {{ ucfirst($student->gender) }}</span>
                    </div>
                </div>
                <a href="{{ route('attendance.records', ['student_id' => $student->id]) }}" class="btn btn-outline">
                    <i class="fas fa-list mr-2"></i>
                    View All Records
                </a>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
        <div class="card">
            <div class="card-body text-center">
                <div class="text-3xl font-bold text-gray-900">{{ $totalRecords }}</div>
                <div class="text-sm text-gray-600 mt-1">Total Days</div>
            </div>
        </div>

        <div class="card">
            <div class="card-body text-center">
                <div class="text-3xl font-bold text-green-600">{{ $presentCount }}</div>
                <div class="text-sm text-gray-600 mt-1">Present</div>
            </div>
        </div>

        <div class="card">
            <div class="card-body text-center">
                <div class="text-3xl font-bold text-red-600">{{ $absentCount }}</div>
                <div class="text-sm text-gray-600 mt-1">Absent</div>
            </div>
        </div>

        <div class="card">
            <div class="card-body text-center">
                <div class="text-3xl font-bold text-orange-600">{{ $lateCount }}</div>
                <div class="text-sm text-gray-600 mt-1">Late</div>
            </div>
        </div>

        <div class="card">
            <div class="card-body text-center">
                <div class="text-3xl font-bold text-blue-600">{{ $excusedCount }}</div>
                <div class="text-sm text-gray-600 mt-1">Excused</div>
            </div>
        </div>
    </div>

    <!-- Attendance Rate -->
    <div class="card">
        <div class="card-header">
            <h2 class="text-lg font-semibold text-gray-900">Attendance Rate</h2>
        </div>
        <div class="card-body">
            <div class="flex items-center gap-6">
                <!-- Circular Progress -->
                <div class="relative w-32 h-32 flex-shrink-0">
                    <svg class="w-32 h-32 transform -rotate-90">
                        <circle cx="64" cy="64" r="56" stroke="#e5e7eb" stroke-width="8" fill="none" />
                        <circle cx="64" cy="64" r="56"
                                stroke="{{ $attendanceRate >= 90 ? '#10b981' : ($attendanceRate >= 75 ? '#f59e0b' : '#ef4444') }}"
                                stroke-width="8"
                                fill="none"
                                stroke-dasharray="{{ 2 * 3.14159 * 56 }}"
                                stroke-dashoffset="{{ 2 * 3.14159 * 56 * (1 - $attendanceRate / 100) }}"
                                stroke-linecap="round"
                                class="transition-all duration-1000" />
                    </svg>
                    <div class="absolute inset-0 flex items-center justify-center">
                        <div class="text-center">
                            <div class="text-2xl font-bold {{ $attendanceRate >= 90 ? 'text-green-600' : ($attendanceRate >= 75 ? 'text-orange-600' : 'text-red-600') }}">
                                {{ $attendanceRate }}%
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Legend -->
                <div class="flex-1">
                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <div class="w-4 h-4 bg-green-500 rounded mr-3"></div>
                                <span class="text-sm text-gray-700">Present</span>
                            </div>
                            <div class="text-right">
                                <span class="font-semibold text-gray-900">{{ $presentCount }}</span>
                                <span class="text-xs text-gray-500 ml-1">
                                    ({{ $totalRecords > 0 ? round(($presentCount / $totalRecords) * 100, 1) : 0 }}%)
                                </span>
                            </div>
                        </div>
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <div class="w-4 h-4 bg-red-500 rounded mr-3"></div>
                                <span class="text-sm text-gray-700">Absent</span>
                            </div>
                            <div class="text-right">
                                <span class="font-semibold text-gray-900">{{ $absentCount }}</span>
                                <span class="text-xs text-gray-500 ml-1">
                                    ({{ $totalRecords > 0 ? round(($absentCount / $totalRecords) * 100, 1) : 0 }}%)
                                </span>
                            </div>
                        </div>
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <div class="w-4 h-4 bg-orange-500 rounded mr-3"></div>
                                <span class="text-sm text-gray-700">Late</span>
                            </div>
                            <div class="text-right">
                                <span class="font-semibold text-gray-900">{{ $lateCount }}</span>
                                <span class="text-xs text-gray-500 ml-1">
                                    ({{ $totalRecords > 0 ? round(($lateCount / $totalRecords) * 100, 1) : 0 }}%)
                                </span>
                            </div>
                        </div>
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <div class="w-4 h-4 bg-blue-500 rounded mr-3"></div>
                                <span class="text-sm text-gray-700">Excused</span>
                            </div>
                            <div class="text-right">
                                <span class="font-semibold text-gray-900">{{ $excusedCount }}</span>
                                <span class="text-xs text-gray-500 ml-1">
                                    ({{ $totalRecords > 0 ? round(($excusedCount / $totalRecords) * 100, 1) : 0 }}%)
                                </span>
                            </div>
                        </div>
                    </div>
                    @if($attendanceRate < 75)
                        <div class="mt-4 p-3 bg-red-50 border-l-4 border-red-500 rounded">
                            <p class="text-sm text-red-700">
                                <i class="fas fa-exclamation-triangle mr-1"></i>
                                Low attendance rate. Please review and follow up.
                            </p>
                        </div>
                    @elseif($attendanceRate < 90)
                        <div class="mt-4 p-3 bg-yellow-50 border-l-4 border-yellow-500 rounded">
                            <p class="text-sm text-yellow-700">
                                <i class="fas fa-info-circle mr-1"></i>
                                Attendance could be improved.
                            </p>
                        </div>
                    @else
                        <div class="mt-4 p-3 bg-green-50 border-l-4 border-green-500 rounded">
                            <p class="text-sm text-green-700">
                                <i class="fas fa-check-circle mr-1"></i>
                                Excellent attendance record!
                            </p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Monthly Breakdown Chart -->
    <div class="card">
        <div class="card-header">
            <h2 class="text-lg font-semibold text-gray-900">Monthly Breakdown (Last 6 Months)</h2>
        </div>
        <div class="card-body">
            <div class="space-y-4">
                @foreach($monthlyData as $month => $data)
                    @php
                        $monthTotal = $data['present'] + $data['absent'];
                        $presentPercentage = $monthTotal > 0 ? ($data['present'] / $monthTotal) * 100 : 0;
                        $absentPercentage = $monthTotal > 0 ? ($data['absent'] / $monthTotal) * 100 : 0;
                    @endphp
                    <div>
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-sm font-medium text-gray-700">{{ $month }}</span>
                            <span class="text-xs text-gray-500">
                                {{ $data['present'] }} Present / {{ $data['absent'] }} Absent
                            </span>
                        </div>
                        <div class="flex h-8 rounded-lg overflow-hidden bg-gray-100">
                            @if($monthTotal > 0)
                                @if($data['present'] > 0)
                                    <div class="bg-green-500 flex items-center justify-center text-white text-xs font-medium"
                                         style="width: {{ $presentPercentage }}%"
                                         title="Present: {{ $data['present'] }}">
                                        @if($presentPercentage > 15){{ $data['present'] }}@endif
                                    </div>
                                @endif
                                @if($data['absent'] > 0)
                                    <div class="bg-red-500 flex items-center justify-center text-white text-xs font-medium"
                                         style="width: {{ $absentPercentage }}%"
                                         title="Absent: {{ $data['absent'] }}">
                                        @if($absentPercentage > 15){{ $data['absent'] }}@endif
                                    </div>
                                @endif
                            @else
                                <div class="flex-1 flex items-center justify-center text-gray-400 text-xs">
                                    No data
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Recent Attendance (Last 30 Days) -->
    <div class="card">
        <div class="card-header">
            <h2 class="text-lg font-semibold text-gray-900">Recent Attendance (Last 30 Days)</h2>
        </div>
        <div class="card-body p-0">
            <!-- Desktop Table -->
            <div class="hidden md:block overflow-x-auto">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Day</th>
                            <th>Class</th>
                            <th>Subject</th>
                            <th>Status</th>
                            <th>Notes</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentAttendance as $record)
                        <tr>
                            <td>{{ $record->date->format('M d, Y') }}</td>
                            <td>{{ $record->date->format('l') }}</td>
                            <td>
                                @if($record->schoolClass)
                                    <span class="badge badge-primary text-xs">
                                        {{ $record->schoolClass->name }} - {{ $record->schoolClass->section }}
                                    </span>
                                @else
                                    <span class="text-gray-400">N/A</span>
                                @endif
                            </td>
                            <td>
                                @if($record->subject)
                                    <span class="text-sm text-gray-700">{{ $record->subject->name }}</span>
                                @else
                                    <span class="text-gray-400 text-sm">General</span>
                                @endif
                            </td>
                            <td>
                                @switch($record->status)
                                    @case('present')
                                        <span class="badge badge-success">
                                            <i class="fas fa-check-circle mr-1"></i> Present
                                        </span>
                                        @break
                                    @case('absent')
                                        <span class="badge badge-danger">
                                            <i class="fas fa-times-circle mr-1"></i> Absent
                                        </span>
                                        @break
                                    @case('late')
                                        <span class="badge badge-warning">
                                            <i class="fas fa-clock mr-1"></i> Late
                                        </span>
                                        @break
                                    @case('excused')
                                        <span class="badge badge-info">
                                            <i class="fas fa-user-shield mr-1"></i> Excused
                                        </span>
                                        @break
                                @endswitch
                            </td>
                            <td>
                                @if($record->notes)
                                    <span class="text-sm text-gray-600">{{ Str::limit($record->notes, 30) }}</span>
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-8">
                                <div class="text-gray-400">
                                    <i class="fas fa-calendar-times text-4xl mb-3"></i>
                                    <p>No attendance records in the last 30 days</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Mobile Cards -->
            <div class="md:hidden space-y-3 p-4">
                @forelse($recentAttendance as $record)
                <div class="bg-white border border-gray-200 rounded-lg p-4">
                    <div class="flex items-center justify-between mb-2">
                        <div>
                            <div class="font-medium text-gray-900">{{ $record->date->format('M d, Y') }}</div>
                            <div class="text-sm text-gray-500">{{ $record->date->format('l') }}</div>
                        </div>
                        @switch($record->status)
                            @case('present')
                                <span class="badge badge-success">
                                    <i class="fas fa-check-circle mr-1"></i> Present
                                </span>
                                @break
                            @case('absent')
                                <span class="badge badge-danger">
                                    <i class="fas fa-times-circle mr-1"></i> Absent
                                </span>
                                @break
                            @case('late')
                                <span class="badge badge-warning">
                                    <i class="fas fa-clock mr-1"></i> Late
                                </span>
                                @break
                            @case('excused')
                                <span class="badge badge-info">
                                    <i class="fas fa-user-shield mr-1"></i> Excused
                                </span>
                                @break
                        @endswitch
                    </div>
                    @if($record->schoolClass)
                        <p class="text-sm text-gray-600">
                            <i class="fas fa-school text-gray-400 mr-1"></i>
                            {{ $record->schoolClass->name }} - {{ $record->schoolClass->section }}
                        </p>
                    @endif
                    @if($record->subject)
                        <p class="text-sm text-gray-600">
                            <i class="fas fa-book text-gray-400 mr-1"></i>
                            {{ $record->subject->name }}
                        </p>
                    @endif
                    @if($record->notes)
                        <p class="text-sm text-gray-500 italic mt-2">{{ $record->notes }}</p>
                    @endif
                </div>
                @empty
                <div class="text-center py-8 text-gray-400">
                    <i class="fas fa-calendar-times text-4xl mb-3"></i>
                    <p>No attendance records in the last 30 days</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
