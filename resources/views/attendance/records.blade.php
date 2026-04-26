@extends('layouts.spa')

@section('title', 'Attendance Records')

@section('breadcrumb')
    <span class="text-gray-400">Attendance</span>
    <span class="text-gray-400">/</span>
    <span class="font-semibold text-gray-900">Records</span>
@endsection

@section('content')
<div class="space-y-6 pb-20 md:pb-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Attendance Records</h1>
            <p class="text-gray-600 mt-1">View and filter historical attendance data</p>
        </div>
        <div class="flex items-center space-x-3">
            <a href="{{ route('attendance.create') }}" class="btn btn-primary">
                <i class="fas fa-clipboard-check mr-2"></i>
                Take Attendance
            </a>
        </div>
    </div>

    <!-- Filters -->
    <div class="card">
        <div class="card-body">
            <form method="GET" action="{{ route('attendance.records') }}" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                    <!-- Search -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Search Student</label>
                        <input type="text"
                               name="search"
                               value="{{ request('search') }}"
                               placeholder="Name or admission number..."
                               class="form-input">
                    </div>

                    <!-- Start Date -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Start Date</label>
                        <input type="date"
                               name="start_date"
                               value="{{ request('start_date') }}"
                               class="form-input">
                    </div>

                    <!-- End Date -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">End Date</label>
                        <input type="date"
                               name="end_date"
                               value="{{ request('end_date') }}"
                               class="form-input">
                    </div>

                    <!-- Status -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                        <select name="status" class="form-select">
                            <option value="">All Status</option>
                            <option value="present" {{ request('status') === 'present' ? 'selected' : '' }}>Present</option>
                            <option value="absent" {{ request('status') === 'absent' ? 'selected' : '' }}>Absent</option>
                            <option value="late" {{ request('status') === 'late' ? 'selected' : '' }}>Late</option>
                            <option value="excused" {{ request('status') === 'excused' ? 'selected' : '' }}>Excused</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <!-- Class Filter -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Class</label>
                        <select name="class_id" class="form-select">
                            <option value="">All Classes</option>
                            @foreach($classes as $class)
                                <option value="{{ $class->id }}" {{ request('class_id') == $class->id ? 'selected' : '' }}>
                                    {{ $class->name }} - Section {{ $class->section }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Student Filter -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Student</label>
                        <select name="student_id" class="form-select">
                            <option value="">All Students</option>
                            @foreach($students as $student)
                                <option value="{{ $student->id }}" {{ request('student_id') == $student->id ? 'selected' : '' }}>
                                    {{ $student->full_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Actions -->
                    <div class="flex items-end space-x-2">
                        <button type="submit" class="btn btn-primary flex-1">
                            <i class="fas fa-filter mr-2"></i>
                            Apply Filters
                        </button>
                        <a href="{{ route('attendance.records') }}" class="btn btn-outline">
                            <i class="fas fa-redo"></i>
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Records Table -->
    <div class="card">
        <div class="card-header">
            <h2 class="text-lg font-semibold text-gray-900">
                Attendance History
                @if($attendances->total() > 0)
                    <span class="text-gray-500 font-normal">({{ number_format($attendances->total()) }})</span>
                @endif
            </h2>
            @if(request()->hasAny(['search', 'start_date', 'end_date', 'status', 'class_id', 'student_id']))
                <a href="{{ route('attendance.records') }}" class="text-sm text-gray-600 hover:text-gray-900">
                    <i class="fas fa-times mr-1"></i>
                    Clear all filters
                </a>
            @endif
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
                            <th>Subject</th>
                            <th>Status</th>
                            <th>Notes</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($attendances as $record)
                        <tr>
                            <td>
                                <div class="text-sm">
                                    <div class="text-gray-900">{{ $record->date->format('M d, Y') }}</div>
                                    <div class="text-gray-500 text-xs">{{ $record->date->format('l') }}</div>
                                </div>
                            </td>
                            <td>
                                <a href="{{ route('attendance.student-profile', $record->student) }}"
                                   class="font-medium text-primary-600 hover:text-primary-700 hover:underline">
                                    {{ $record->student->full_name }}
                                </a>
                                <div class="text-xs text-gray-500">{{ $record->student->admission_number }}</div>
                            </td>
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
                                    <span class="text-sm text-gray-600">{{ Str::limit($record->notes, 40) }}</span>
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-8">
                                <div class="text-gray-400">
                                    <i class="fas fa-search text-4xl mb-3"></i>
                                    <p class="text-lg">No records found</p>
                                    @if(request()->hasAny(['search', 'start_date', 'end_date', 'status', 'class_id', 'student_id']))
                                        <p class="text-sm mt-2">Try adjusting your filters</p>
                                        <a href="{{ route('attendance.records') }}" class="text-primary-600 hover:text-primary-700 mt-2 inline-block">
                                            Clear all filters
                                        </a>
                                    @else
                                        <a href="{{ route('attendance.create') }}" class="text-primary-600 hover:text-primary-700 mt-2 inline-block">
                                            Take attendance now
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Mobile Cards -->
            <div class="md:hidden space-y-3 p-4">
                @forelse($attendances as $record)
                <div class="bg-white border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                    <div class="flex items-start justify-between mb-2">
                        <div class="flex-1">
                            <a href="{{ route('attendance.student-profile', $record->student) }}"
                               class="font-medium text-gray-900 hover:text-primary-600">
                                {{ $record->student->full_name }}
                            </a>
                            <p class="text-xs text-gray-500 mt-1">{{ $record->student->admission_number }}</p>
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
                    <div class="space-y-1 text-sm">
                        <p class="text-gray-600">
                            <i class="fas fa-calendar text-gray-400 mr-1"></i>
                            {{ $record->date->format('M d, Y') }} ({{ $record->date->format('l') }})
                        </p>
                        @if($record->schoolClass)
                            <p class="text-gray-600">
                                <i class="fas fa-school text-gray-400 mr-1"></i>
                                {{ $record->schoolClass->name }} - {{ $record->schoolClass->section }}
                            </p>
                        @endif
                        @if($record->subject)
                            <p class="text-gray-600">
                                <i class="fas fa-book text-gray-400 mr-1"></i>
                                {{ $record->subject->name }}
                            </p>
                        @endif
                        @if($record->notes)
                            <p class="text-gray-500 italic mt-2">{{ $record->notes }}</p>
                        @endif
                    </div>
                </div>
                @empty
                <div class="text-center py-8 text-gray-400">
                    <i class="fas fa-search text-4xl mb-3"></i>
                    <p class="text-lg">No records found</p>
                    @if(request()->hasAny(['search', 'start_date', 'end_date', 'status', 'class_id', 'student_id']))
                        <p class="text-sm mt-2">Try adjusting your filters</p>
                        <a href="{{ route('attendance.records') }}" class="text-primary-600 hover:text-primary-700 mt-2 inline-block">
                            Clear all filters
                        </a>
                    @else
                        <a href="{{ route('attendance.create') }}" class="text-primary-600 hover:text-primary-700 mt-2 inline-block">
                            Take attendance now
                        </a>
                    @endif
                </div>
                @endforelse
            </div>
        </div>

        @if($attendances->hasPages())
        <div class="card-footer">
            <div class="pagination">
                <div>
                    <p class="text-sm text-gray-700">
                        Showing <span class="font-medium">{{ $attendances->firstItem() }}</span> to
                        <span class="font-medium">{{ $attendances->lastItem() }}</span> of
                        <span class="font-medium">{{ number_format($attendances->total()) }}</span> results
                    </p>
                </div>
                <div class="flex space-x-2">
                    {{ $attendances->links() }}
                </div>
            </div>
        </div>
        @endif
    </div>
</div>

<!-- Mobile Footer Navigation -->
<div class="md:hidden fixed bottom-0 left-0 right-0 bg-white border-t border-gray-200 shadow-lg z-40">
    <div class="grid grid-cols-3 gap-1 p-2">
        <a href="{{ route('attendance.index') }}" class="flex flex-col items-center py-2 px-3 text-gray-600 hover:bg-gray-50 rounded-lg transition-colors">
            <i class="fas fa-chart-line text-xl mb-1"></i>
            <span class="text-xs font-medium">Dashboard</span>
        </a>
        <a href="{{ route('attendance.create') }}" class="flex flex-col items-center py-2 px-3 text-gray-600 hover:bg-gray-50 rounded-lg transition-colors">
            <i class="fas fa-clipboard-check text-xl mb-1"></i>
            <span class="text-xs font-medium">Take</span>
        </a>
        <a href="{{ route('attendance.records') }}" class="flex flex-col items-center py-2 px-3 text-primary-600 bg-primary-50 rounded-lg">
            <i class="fas fa-history text-xl mb-1"></i>
            <span class="text-xs font-medium">Records</span>
        </a>
    </div>
</div>
@endsection
