@extends('layouts.spa')

@section('title', 'Class Attendance')

@section('breadcrumb')
    <span class="text-gray-400">Teaching</span>
    <span class="text-gray-400">/</span>
    <span class="font-semibold text-gray-900">Class Attendance</span>
@endsection

@section('content')
<div class="space-y-8">
    <!-- Hero Header Section -->
    <div class="bg-gradient-to-r from-green-50 to-emerald-50 rounded-xl shadow-sm border border-green-100 p-8">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-6">
            <div>
                <h1 class="text-4xl font-bold text-gray-900 mb-2">Class Attendance</h1>
                <p class="text-gray-600 text-lg flex items-center gap-2">
                    <i class="fas fa-calendar-check text-green-600"></i>
                    Track and manage attendance for your class(es)
                </p>
            </div>
            <div>
                <a href="/teacher/class/mark-attendance" class="inline-flex items-center gap-2 bg-green-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-green-700 transition shadow-md">
                    <i class="fas fa-check-square"></i>
                    Mark Attendance
                </a>
            </div>
        </div>
    </div>

    <!-- Class Selection (if teacher has multiple classes) -->
    @if($teacherClasses->count() > 1)
    <div class="card shadow-sm border border-gray-200">
        <div class="card-body">
            <form method="GET" action="{{ route('teacher.class.attendance') }}" class="space-y-4">
                <label class="block">
                    <span class="text-sm font-semibold text-gray-900 mb-2 flex items-center gap-2">
                        <i class="fas fa-list text-green-600"></i>
                        Select Your Class
                    </span>
                    <select name="class" onchange="this.form.submit()" class="form-select w-full md:w-96">
                        @foreach($teacherClasses as $class)
                            <option value="{{ $class->id }}" {{ $selectedClass && $selectedClass->id === $class->id ? 'selected' : '' }}>
                                {{ $class->name }}
                                @if($class->section)
                                    - Section {{ $class->section }}
                                @endif
                            </option>
                        @endforeach
                    </select>
                </label>
            </form>
        </div>
    </div>
    @endif

    <!-- No Classes Message -->
    @if($teacherClasses->isEmpty())
    <div class="bg-yellow-50 border-l-4 border-yellow-500 p-6 rounded-lg">
        <div class="flex gap-4">
            <div class="flex-shrink-0 text-2xl text-yellow-600">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            <div>
                <h3 class="font-semibold text-yellow-900">No Classes Assigned</h3>
                <p class="text-yellow-800 mt-1">You are not assigned to any class yet. Contact your administrator to assign you to a class.</p>
            </div>
        </div>
    </div>
    @else
        <!-- Stats Overview -->
        <div class="grid grid-cols-1 md:grid-cols-5 gap-6">
            <!-- Total Records -->
            <div class="card shadow-sm hover:shadow-md transition border border-gray-200">
                <div class="card-body">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600 font-medium">Total Records</p>
                            <p class="text-3xl font-bold text-gray-900 mt-2">{{ number_format($stats['total']) }}</p>
                        </div>
                        <div class="w-14 h-14 bg-gradient-to-br from-gray-100 to-gray-50 rounded-full flex items-center justify-center shadow-sm">
                            <i class="fas fa-list text-gray-600 text-2xl"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Present -->
            <div class="card shadow-sm hover:shadow-md transition border border-gray-200">
                <div class="card-body">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600 font-medium">Present</p>
                            <p class="text-3xl font-bold text-green-600 mt-2">{{ number_format($stats['present']) }}</p>
                        </div>
                        <div class="w-14 h-14 bg-gradient-to-br from-green-100 to-green-50 rounded-full flex items-center justify-center shadow-sm">
                            <i class="fas fa-check-circle text-green-600 text-2xl"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Absent -->
            <div class="card shadow-sm hover:shadow-md transition border border-gray-200">
                <div class="card-body">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600 font-medium">Absent</p>
                            <p class="text-3xl font-bold text-red-600 mt-2">{{ number_format($stats['absent']) }}</p>
                        </div>
                        <div class="w-14 h-14 bg-gradient-to-br from-red-100 to-red-50 rounded-full flex items-center justify-center shadow-sm">
                            <i class="fas fa-times-circle text-red-600 text-2xl"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Late -->
            <div class="card shadow-sm hover:shadow-md transition border border-gray-200">
                <div class="card-body">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600 font-medium">Late</p>
                            <p class="text-3xl font-bold text-yellow-600 mt-2">{{ number_format($stats['late']) }}</p>
                        </div>
                        <div class="w-14 h-14 bg-gradient-to-br from-yellow-100 to-yellow-50 rounded-full flex items-center justify-center shadow-sm">
                            <i class="fas fa-clock text-yellow-600 text-2xl"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Excused -->
            <div class="card shadow-sm hover:shadow-md transition border border-gray-200">
                <div class="card-body">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600 font-medium">Excused</p>
                            <p class="text-3xl font-bold text-blue-600 mt-2">{{ number_format($stats['excused']) }}</p>
                        </div>
                        <div class="w-14 h-14 bg-gradient-to-br from-blue-100 to-blue-50 rounded-full flex items-center justify-center shadow-sm">
                            <i class="fas fa-info-circle text-blue-600 text-2xl"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters & Search -->
        <div class="card shadow-sm border border-gray-200">
            <div class="card-header bg-gradient-to-r from-green-50 to-green-25 border-b border-green-100">
                <h3 class="text-lg font-semibold text-gray-900">
                    <i class="fas fa-filter text-green-600 mr-2"></i>
                    Filter Attendance
                </h3>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('teacher.class.attendance') }}" class="space-y-4">
                    @if($selectedClass)
                        <input type="hidden" name="class" value="{{ $selectedClass->id }}">
                    @endif
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Date Filter -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Date</label>
                            <input type="date"
                                   name="date"
                                   value="{{ request('date') }}"
                                   class="form-input w-full">
                        </div>

                        <!-- Status Filter -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Attendance Status</label>
                            <select name="status" class="form-select w-full">
                                <option value="">All Status</option>
                                <option value="present" {{ request('status') === 'present' ? 'selected' : '' }}>Present</option>
                                <option value="absent" {{ request('status') === 'absent' ? 'selected' : '' }}>Absent</option>
                                <option value="late" {{ request('status') === 'late' ? 'selected' : '' }}>Late</option>
                                <option value="excused" {{ request('status') === 'excused' ? 'selected' : '' }}>Excused</option>
                            </select>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex gap-2 pt-2">
                        <button type="submit" class="inline-flex items-center gap-2 px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition font-semibold">
                            <i class="fas fa-search"></i>
                            Filter
                        </button>
                        <a href="{{ route('teacher.class.attendance') }}" class="inline-flex items-center gap-2 px-6 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition font-semibold">
                            <i class="fas fa-redo"></i>
                            Reset
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Attendance Table -->
        <div class="card shadow-sm border border-gray-200">
            <div class="card-header bg-gradient-to-r from-emerald-50 to-emerald-25 border-b border-emerald-100">
                <div class="flex items-center justify-between">
                    <h2 class="text-xl font-semibold text-gray-900">
                        <span class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-emerald-100 text-emerald-600 mr-3">
                            <i class="fas fa-list"></i>
                        </span>
                        @if($selectedClass)
                            {{ $selectedClass->name }} - Attendance Records
                        @else
                            Attendance Records
                        @endif
                    </h2>
                    @if($stats['total'] > 0)
                        <span class="text-sm font-semibold px-4 py-2 bg-white rounded-full text-emerald-600 border border-emerald-200">
                            {{ number_format($stats['total']) }} record{{ $stats['total'] !== 1 ? 's' : '' }}
                        </span>
                    @endif
                </div>
            </div>
            <div class="card-body p-0">
                <div class="overflow-x-auto">
                    <table class="table">
                        <thead class="bg-gray-50 border-b border-gray-200">
                            <tr>
                                <th class="text-gray-700 font-semibold">Date</th>
                                <th class="text-gray-700 font-semibold">Student</th>
                                <th class="text-gray-700 font-semibold">Status</th>
                                <th class="text-gray-700 font-semibold">Notes</th>
                                <th class="text-gray-700 font-semibold">Recorded By</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($attendance as $record)
                            <tr class="border-b border-gray-100 hover:bg-gray-50 transition">
                                <td>
                                    <span class="font-semibold text-gray-900">
                                        <i class="fas fa-calendar text-green-600 mr-2"></i>
                                        {{ $record->date->format('M d, Y') }}
                                    </span>
                                </td>
                                <td>
                                    <div class="flex items-center gap-3">
                                        <div class="flex-shrink-0 w-10 h-10 rounded-full {{ $record->student->gender === 'male' ? 'bg-blue-100' : 'bg-pink-100' }} flex items-center justify-center overflow-hidden shadow-sm">
                                            @if($record->student->photo)
                                                <img src="{{ Storage::url($record->student->photo) }}"
                                                     alt="{{ $record->student->full_name }}"
                                                     class="w-full h-full object-cover">
                                            @else
                                                <span class="text-sm font-bold {{ $record->student->gender === 'male' ? 'text-blue-600' : 'text-pink-600' }}">
                                                    {{ substr($record->student->first_name, 0, 1) }}{{ substr($record->student->last_name, 0, 1) }}
                                                </span>
                                            @endif
                                        </div>
                                        <div>
                                            <p class="font-semibold text-gray-900">{{ $record->student->full_name }}</p>
                                            <p class="text-xs text-gray-500">{{ $record->student->admission_number }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    @switch($record->status)
                                        @case('present')
                                            <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-green-50 text-green-700 font-semibold border border-green-200">
                                                <i class="fas fa-check-circle"></i>
                                                Present
                                            </span>
                                            @break
                                        @case('absent')
                                            <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-red-50 text-red-700 font-semibold border border-red-200">
                                                <i class="fas fa-times-circle"></i>
                                                Absent
                                            </span>
                                            @break
                                        @case('late')
                                            <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-yellow-50 text-yellow-700 font-semibold border border-yellow-200">
                                                <i class="fas fa-clock"></i>
                                                Late
                                            </span>
                                            @break
                                        @case('excused')
                                            <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-blue-50 text-blue-700 font-semibold border border-blue-200">
                                                <i class="fas fa-info-circle"></i>
                                                Excused
                                            </span>
                                            @break
                                        @default
                                            <span class="text-gray-600 font-semibold">{{ ucfirst($record->status) }}</span>
                                    @endswitch
                                </td>
                                <td>
                                    @if($record->notes)
                                        <span class="px-3 py-1 rounded-full bg-gray-100 text-gray-700 text-sm">{{ $record->notes }}</span>
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="text-sm text-gray-600">
                                        @if($record->recorder)
                                            {{ $record->recorder->name ?? 'Unknown' }}
                                        @else
                                            <span class="text-gray-400">-</span>
                                        @endif
                                    </span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center py-12">
                                    <div class="flex flex-col items-center justify-center">
                                        <i class="fas fa-inbox text-gray-300 text-5xl mb-4"></i>
                                        <p class="text-gray-500 text-lg font-medium">No attendance records found</p>
                                        <p class="text-gray-400 text-sm mt-1">Try adjusting your filters or select another date</p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Pagination -->
        @if($attendance->hasPages())
        <div class="flex justify-center">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
                {{ $attendance->links() }}
            </div>
        </div>
        @endif
    @endif
</div>

@push('scripts')
<script>
    // Initialize date picker if needed
</script>
@endpush

@endsection
