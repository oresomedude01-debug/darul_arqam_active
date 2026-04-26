@extends('layouts.spa')

@section('title', 'Attendance Dashboard')

@section('breadcrumb')
    <span class="text-gray-400">Attendance</span>
    <span class="text-gray-400">/</span>
    <span class="font-semibold text-gray-900">Dashboard</span>
@endsection

@section('content')
<div class="space-y-6">
    <!-- Success/Error Messages -->
    @if(session('success'))
    <div class="alert alert-success fade-in">
        <i class="fas fa-check-circle text-xl"></i>
        <p>{{ session('success') }}</p>
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger fade-in">
        <i class="fas fa-exclamation-circle text-xl"></i>
        <p>{{ session('error') }}</p>
    </div>
    @endif

    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Attendance Dashboard</h1>
            <p class="text-gray-600 mt-1">Track and manage student attendance</p>
        </div>
        <div class="flex items-center space-x-3">
            <a href="{{ route('attendance.records') }}" class="btn btn-outline">
                <i class="fas fa-history mr-2"></i>
                View Records
            </a>
            <a href="{{ route('attendance.create') }}" class="btn btn-primary">
                <i class="fas fa-clipboard-check mr-2"></i>
                Take Attendance
            </a>
        </div>
    </div>

    <!-- Filters -->
    <div class="card">
        <div class="card-body">
            <form method="GET" action="{{ route('attendance.index') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <!-- Date Filter -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Date</label>
                    <input type="date"
                           name="date"
                           value="{{ $date }}"
                           class="form-input"
                           onchange="this.form.submit()">
                </div>

                <!-- Class Filter -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Class</label>
                    <select name="class_id" class="form-select" onchange="this.form.submit()">
                        <option value="">All Classes</option>
                        @foreach($classes as $class)
                            <option value="{{ $class->id }}" {{ $classId == $class->id ? 'selected' : '' }}>
                                {{ $class->name }} - Section {{ $class->section }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Actions -->
                <div class="flex items-end">
                    <a href="{{ route('attendance.index') }}" class="btn btn-outline w-full">
                        <i class="fas fa-redo mr-2"></i>
                        Clear Filters
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Stats Overview -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
        <div class="card">
            <div class="card-body">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600">Total Students</p>
                        <p class="text-2xl font-bold text-gray-900 mt-1">{{ number_format($stats['total']) }}</p>
                    </div>
                    <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-users text-blue-600 text-xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600">Present</p>
                        <p class="text-2xl font-bold text-green-600 mt-1">{{ number_format($stats['present']) }}</p>
                    </div>
                    <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-check-circle text-green-600 text-xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600">Absent</p>
                        <p class="text-2xl font-bold text-red-600 mt-1">{{ number_format($stats['absent']) }}</p>
                    </div>
                    <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-times-circle text-red-600 text-xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600">Late</p>
                        <p class="text-2xl font-bold text-orange-600 mt-1">{{ number_format($stats['late']) }}</p>
                    </div>
                    <div class="w-12 h-12 bg-orange-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-clock text-orange-600 text-xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600">Excused</p>
                        <p class="text-2xl font-bold text-blue-600 mt-1">{{ number_format($stats['excused']) }}</p>
                    </div>
                    <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-user-shield text-blue-600 text-xl"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Missing Attendance Alert -->
    @if($classesWithoutAttendance > 0)
    <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 rounded-md">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <i class="fas fa-exclamation-triangle text-yellow-400 text-xl"></i>
            </div>
            <div class="ml-3">
                <p class="text-sm text-yellow-700">
                    <span class="font-medium">{{ $classesWithoutAttendance }} {{ Str::plural('class', $classesWithoutAttendance) }}</span>
                    {{ $classesWithoutAttendance === 1 ? 'has' : 'have' }} missing attendance for {{ \Carbon\Carbon::parse($date)->format('M d, Y') }}
                </p>
            </div>
            <div class="ml-auto">
                <a href="{{ route('attendance.create', ['date' => $date]) }}" class="btn btn-sm btn-warning">
                    Take Attendance
                </a>
            </div>
        </div>
    </div>
    @endif

    <!-- Recent Attendance Records -->
    <div class="card">
        <div class="card-header">
            <h2 class="text-lg font-semibold text-gray-900">Recent Attendance Records</h2>
            <a href="{{ route('attendance.records') }}" class="text-primary-600 hover:text-primary-700 text-sm font-medium">
                View All <i class="fas fa-arrow-right ml-1"></i>
            </a>
        </div>
        <div class="card-body p-0">
            <!-- Desktop Table -->
            <div class="hidden md:block overflow-x-auto">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Student</th>
                            <th>Class</th>
                            <th>Status</th>
                            <th>Notes</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentRecords as $record)
                        <tr>
                            <td>
                                <div class="text-sm">
                                    <div class="text-gray-900">{{ $record->date->format('M d, Y') }}</div>
                                    <div class="text-gray-500 text-xs">{{ $record->date->diffForHumans() }}</div>
                                </div>
                            </td>
                            <td>
                                <a href="{{ route('attendance.student-profile', $record->student) }}"
                                   class="font-medium text-primary-600 hover:text-primary-700 hover:underline">
                                    {{ $record->student->full_name }}
                                </a>
                            </td>
                            <td>
                                @if($record->schoolClass)
                                    <span class="badge badge-primary">
                                        {{ $record->schoolClass->name }} - {{ $record->schoolClass->section }}
                                    </span>
                                @else
                                    <span class="text-gray-400">N/A</span>
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
                            <td colspan="5" class="text-center py-8">
                                <div class="text-gray-400">
                                    <i class="fas fa-clipboard-list text-4xl mb-3"></i>
                                    <p class="text-lg">No attendance records found</p>
                                    <a href="{{ route('attendance.create') }}" class="text-primary-600 hover:text-primary-700 mt-2 inline-block">
                                        Take attendance now
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Mobile Cards -->
            <div class="md:hidden space-y-3 p-4">
                @forelse($recentRecords as $record)
                <div class="bg-white border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                    <div class="flex items-start justify-between mb-2">
                        <div class="flex-1">
                            <a href="{{ route('attendance.student-profile', $record->student) }}"
                               class="font-medium text-gray-900 hover:text-primary-600">
                                {{ $record->student->full_name }}
                            </a>
                            <p class="text-sm text-gray-500 mt-1">{{ $record->date->format('M d, Y') }}</p>
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
                    @if($record->notes)
                        <p class="text-sm text-gray-500 mt-2 italic">{{ Str::limit($record->notes, 50) }}</p>
                    @endif
                </div>
                @empty
                <div class="text-center py-8 text-gray-400">
                    <i class="fas fa-clipboard-list text-4xl mb-3"></i>
                    <p class="text-lg">No attendance records found</p>
                    <a href="{{ route('attendance.create') }}" class="text-primary-600 hover:text-primary-700 mt-2 inline-block">
                        Take attendance now
                    </a>
                </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

<!-- Mobile Footer Navigation -->
<div class="md:hidden fixed bottom-0 left-0 right-0 bg-white border-t border-gray-200 shadow-lg z-40">
    <div class="grid grid-cols-3 gap-1 p-2">
        <a href="{{ route('attendance.index') }}" class="flex flex-col items-center py-2 px-3 text-primary-600 bg-primary-50 rounded-lg">
            <i class="fas fa-chart-line text-xl mb-1"></i>
            <span class="text-xs font-medium">Dashboard</span>
        </a>
        <a href="{{ route('attendance.create') }}" class="flex flex-col items-center py-2 px-3 text-gray-600 hover:bg-gray-50 rounded-lg transition-colors">
            <i class="fas fa-clipboard-check text-xl mb-1"></i>
            <span class="text-xs font-medium">Take</span>
        </a>
        <a href="{{ route('attendance.records') }}" class="flex flex-col items-center py-2 px-3 text-gray-600 hover:bg-gray-50 rounded-lg transition-colors">
            <i class="fas fa-history text-xl mb-1"></i>
            <span class="text-xs font-medium">Records</span>
        </a>
    </div>
</div>
<div class="md:hidden h-20"></div> <!-- Spacer for fixed footer -->
@endsection
