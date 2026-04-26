@extends('layouts.spa')

@section('title', 'Mark Attendance')

@section('breadcrumb')
    <span class="text-gray-400">Teaching</span>
    <span class="text-gray-400">/</span>
    <a href="{{ route('teacher.class.attendance') }}" class="text-gray-400 hover:text-gray-600">Class Attendance</a>
    <span class="text-gray-400">/</span>
    <span class="font-semibold text-gray-900">Mark Attendance</span>
@endsection

@section('content')
<div class="space-y-8">
    <!-- Hero Header Section -->
    <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-xl shadow-sm border border-blue-100 p-8">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-6">
            <div>
                <h1 class="text-4xl font-bold text-gray-900 mb-2">Mark Attendance</h1>
                <p class="text-gray-600 text-lg flex items-center gap-2">
                    <i class="fas fa-check-square text-blue-600"></i>
                    Record attendance for your class students
                </p>
            </div>
            <a href="{{ route('teacher.class.attendance') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-white text-gray-700 rounded-lg shadow-md border border-gray-200 hover:shadow-lg transition font-semibold">
                <i class="fas fa-arrow-left"></i>
                Back
            </a>
        </div>
    </div>

    <!-- Error Alert -->
    @if($errors->any())
    <div class="bg-red-50 border-l-4 border-red-500 p-6 rounded-lg">
        <div class="flex gap-4">
            <div class="flex-shrink-0 text-2xl text-red-600">
                <i class="fas fa-exclamation-circle"></i>
            </div>
            <div>
                <h3 class="font-semibold text-red-900">Validation Error</h3>
                <ul class="text-red-800 mt-2 space-y-1">
                    @foreach($errors->all() as $error)
                        <li>• {{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
    @endif

    <!-- Operating Day Warning -->
    @if(!$isSelectedDateOperating)
    <div class="bg-yellow-50 border-l-4 border-yellow-500 p-6 rounded-lg">
        <div class="flex gap-4">
            <div class="flex-shrink-0 text-2xl text-yellow-600">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            <div>
                <h3 class="font-semibold text-yellow-900">Not a School Operating Day</h3>
                <p class="text-yellow-800 mt-1">{{ \Carbon\Carbon::parse($selectedDate)->format('l, M d, Y') }} is not a school operating day. Attendance can only be marked on school operating days.</p>
            </div>
        </div>
    </div>
    @else
    <!-- No Students Alert -->
    @if($students->isEmpty())
    <div class="bg-blue-50 border-l-4 border-blue-500 p-6 rounded-lg">
        <div class="flex gap-4">
            <div class="flex-shrink-0 text-2xl text-blue-600">
                <i class="fas fa-info-circle"></i>
            </div>
            <div>
                <h3 class="font-semibold text-blue-900">No Students Available</h3>
                <p class="text-blue-800 mt-1">There are no active students in the selected class to mark attendance for.</p>
            </div>
        </div>
    </div>
    @else

    <!-- Attendance Form -->
    <form method="POST" action="{{ route('teacher.store-attendance') }}" class="space-y-6">
        @csrf

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Class Selection -->
            <div class="card shadow-sm border border-gray-200">
                <div class="card-header bg-gradient-to-r from-blue-50 to-blue-25 border-b border-blue-100">
                    <h3 class="text-lg font-semibold text-gray-900">
                        <i class="fas fa-book text-blue-600 mr-2"></i>
                        Class Information
                    </h3>
                </div>
                <div class="card-body space-y-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-900 mb-2">Select Class</label>
                        <select name="class_id" required onchange="location.href='{{ route('teacher.mark-attendance') }}?class=' + this.value + '&date={{ $selectedDate }}'" class="form-select w-full">
                            @foreach($teacherClasses as $class)
                                <option value="{{ $class->id }}" {{ $selectedClass && $selectedClass->id === $class->id ? 'selected' : '' }}>
                                    {{ $class->name }}
                                    @if($class->section)
                                        - Section {{ $class->section }}
                                    @endif
                                </option>
                            @endforeach
                        </select>
                    </div>

                    @if($selectedClass)
                        <div>
                            <label class="block text-sm font-semibold text-gray-900 mb-2">Date</label>
                            <input type="date"
                                   name="date"
                                   value="{{ $selectedDate }}"
                                   onchange="location.href='{{ route('teacher.mark-attendance') }}?class={{ $selectedClass->id }}&date=' + this.value"
                                   class="form-input w-full">
                            <p class="text-sm text-gray-600 mt-2">
                                <i class="fas fa-calendar text-blue-600 mr-2"></i>
                                <strong>{{ \Carbon\Carbon::parse($selectedDate)->format('l, F j, Y') }}</strong>
                            </p>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-900 mb-2">Total Students</label>
                            <div class="text-3xl font-bold text-blue-600">{{ $students->count() }}</div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Legend -->
            <div class="card shadow-sm border border-gray-200">
                <div class="card-header bg-gradient-to-r from-green-50 to-green-25 border-b border-green-100">
                    <h3 class="text-lg font-semibold text-gray-900">
                        <i class="fas fa-list text-green-600 mr-2"></i>
                        Attendance Status Legend
                    </h3>
                </div>
                <div class="card-body space-y-3">
                    <div class="flex items-center gap-3">
                        <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-green-50 text-green-700 font-semibold border border-green-200 text-sm">
                            <i class="fas fa-check-circle"></i>
                            Present
                        </span>
                        <span class="text-gray-600">Student attended</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-red-50 text-red-700 font-semibold border border-red-200 text-sm">
                            <i class="fas fa-times-circle"></i>
                            Absent
                        </span>
                        <span class="text-gray-600">Student was absent</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-yellow-50 text-yellow-700 font-semibold border border-yellow-200 text-sm">
                            <i class="fas fa-clock"></i>
                            Late
                        </span>
                        <span class="text-gray-600">Student arrived late</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-blue-50 text-blue-700 font-semibold border border-blue-200 text-sm">
                            <i class="fas fa-info-circle"></i>
                            Excused
                        </span>
                        <span class="text-gray-600">Absence excused</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Students Table -->
        <div class="card shadow-sm border border-gray-200">
            <div class="card-header bg-gradient-to-r from-indigo-50 to-indigo-25 border-b border-indigo-100">
                <h2 class="text-xl font-semibold text-gray-900">
                    <span class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-indigo-100 text-indigo-600 mr-3">
                        <i class="fas fa-list"></i>
                    </span>
                    Mark Student Attendance
                </h2>
            </div>
            <div class="card-body p-0">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50 border-b border-gray-200">
                            <tr>
                                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Student</th>
                                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Admission #</th>
                                <th class="px-6 py-3 text-center text-sm font-semibold text-gray-700">Status</th>
                                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Notes</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($students as $index => $student)
                            <tr class="border-b border-gray-100 hover:bg-gray-50 transition">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="flex-shrink-0 w-10 h-10 rounded-full {{ $student->gender === 'male' ? 'bg-blue-100' : 'bg-pink-100' }} flex items-center justify-center overflow-hidden shadow-sm">
                                            @if($student->photo)
                                                <img src="{{ Storage::url($student->photo) }}"
                                                     alt="{{ $student->full_name }}"
                                                     class="w-full h-full object-cover">
                                            @else
                                                <span class="text-sm font-bold {{ $student->gender === 'male' ? 'text-blue-600' : 'text-pink-600' }}">
                                                    {{ substr($student->first_name, 0, 1) }}{{ substr($student->last_name, 0, 1) }}
                                                </span>
                                            @endif
                                        </div>
                                        <div>
                                            <p class="font-semibold text-gray-900">{{ $student->full_name }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="font-semibold text-gray-900 px-3 py-1 rounded-full bg-gray-100">
                                        {{ $student->admission_number }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex justify-center">
                                        <select name="attendance[{{ $index }}][status]" required class="form-select w-32">
                                            <option value="">-- Select --</option>
                                            <option value="present" {{ isset($existingAttendance[$student->id]) && $existingAttendance[$student->id]->status === 'present' ? 'selected' : '' }}>Present</option>
                                            <option value="absent" {{ isset($existingAttendance[$student->id]) && $existingAttendance[$student->id]->status === 'absent' ? 'selected' : '' }}>Absent</option>
                                            <option value="late" {{ isset($existingAttendance[$student->id]) && $existingAttendance[$student->id]->status === 'late' ? 'selected' : '' }}>Late</option>
                                            <option value="excused" {{ isset($existingAttendance[$student->id]) && $existingAttendance[$student->id]->status === 'excused' ? 'selected' : '' }}>Excused</option>
                                        </select>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <input type="hidden" name="attendance[{{ $index }}][student_id]" value="{{ $student->id }}">
                                    <input type="text"
                                           name="attendance[{{ $index }}][notes]"
                                           value="{{ isset($existingAttendance[$student->id]) ? $existingAttendance[$student->id]->notes : '' }}"
                                           placeholder="e.g., Sick, Doctor's appointment..."
                                           class="form-input w-full text-sm">
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Submit Button -->
        <div class="flex justify-end gap-3">
            <a href="{{ route('teacher.class.attendance') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition font-semibold">
                <i class="fas fa-times"></i>
                Cancel
            </a>
            <button type="submit" class="inline-flex items-center gap-2 px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-semibold">
                <i class="fas fa-save"></i>
                Save Attendance
            </button>
        </div>
    </form>

    @endif
    @endif
</div>

@endsection
