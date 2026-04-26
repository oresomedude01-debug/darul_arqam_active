@extends('layouts.spa')

@section('title', 'Take Attendance')

@section('breadcrumb')
    <span class="text-gray-400">Attendance</span>
    <span class="text-gray-400">/</span>
    <span class="font-semibold text-gray-900">Take Attendance</span>
@endsection

@section('content')
<div class="space-y-6 pb-20 md:pb-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Take Attendance</h1>
            <p class="text-gray-600 mt-1">Record student attendance for the selected class</p>
        </div>
        <a href="{{ route('attendance.index') }}" class="btn btn-outline">
            <i class="fas fa-arrow-left mr-2"></i>
            Back to Dashboard
        </a>
    </div>

    <!-- Class & Date Selection -->
    <div class="card">
        <div class="card-body">
            <form method="GET" action="{{ route('attendance.create') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4" id="filterForm">
                <!-- Date -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Date <span class="text-red-500">*</span></label>
                    <input type="date"
                           name="date"
                           value="{{ $date }}"
                           class="form-input"
                           required
                           onchange="document.getElementById('filterForm').submit()">
                </div>

                <!-- Class -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Class <span class="text-red-500">*</span></label>
                    <select name="class_id" class="form-select" required onchange="document.getElementById('filterForm').submit()">
                        <option value="">Select a class...</option>
                        @foreach($classes as $class)
                            <option value="{{ $class->id }}" {{ $classId == $class->id ? 'selected' : '' }}>
                                {{ $class->name }} - Section {{ $class->section }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Load Button -->
                <div class="flex items-end">
                    <button type="submit" class="btn btn-primary w-full">
                        <i class="fas fa-sync-alt mr-2"></i>
                        Load Students
                    </button>
                </div>
            </form>
        </div>
    </div>

    @if($students)
    <!-- Attendance Form -->
    <form method="POST" action="{{ route('attendance.store') }}" id="attendanceForm">
        @csrf
        <input type="hidden" name="date" value="{{ $date }}">
        <input type="hidden" name="class_id" value="{{ $classId }}">

        <div class="card">
            <div class="card-header">
                <div>
                    <h2 class="text-lg font-semibold text-gray-900">
                        Students List
                        <span class="text-gray-500 font-normal">({{ $students->count() }})</span>
                    </h2>
                    <p class="text-sm text-gray-600 mt-1">{{ \Carbon\Carbon::parse($date)->format('l, F j, Y') }}</p>
                </div>
                <button type="button" onclick="markAllPresent()" class="btn btn-sm btn-success">
                    <i class="fas fa-check-double mr-2"></i>
                    Mark All Present
                </button>
            </div>
            <div class="card-body p-0">
                <!-- Desktop Table -->
                <div class="hidden md:block overflow-x-auto">
                    <table class="table">
                        <thead>
                            <tr>
                                <th class="w-12">#</th>
                                <th>Student Name</th>
                                <th>Admission No.</th>
                                <th>Status</th>
                                <th>Notes</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($students as $index => $student)
                            <tr>
                                <td class="text-gray-500">{{ $index + 1 }}</td>
                                <td>
                                    <div class="flex items-center space-x-3">
                                        <div class="avatar avatar-sm {{ $student->gender === 'male' ? 'bg-blue-500' : 'bg-pink-500' }}">
                                            <span>{{ substr($student->first_name, 0, 1) }}{{ substr($student->last_name, 0, 1) }}</span>
                                        </div>
                                        <span class="font-medium text-gray-900">{{ $student->full_name }}</span>
                                    </div>
                                </td>
                                <td class="text-gray-600">{{ $student->admission_number }}</td>
                                <td>
                                    <input type="hidden" name="attendance[{{ $index }}][student_id]" value="{{ $student->id }}">
                                    <div class="flex space-x-2">
                                        @php
                                            $existingStatus = $existingAttendance[$student->id]->status ?? 'present';
                                        @endphp
                                        <button type="button"
                                                onclick="setStatus({{ $index }}, 'present')"
                                                class="status-btn px-3 py-1 rounded-md text-sm font-medium transition-all {{ $existingStatus === 'present' ? 'bg-green-500 text-white' : 'bg-gray-100 text-gray-600 hover:bg-green-100' }}"
                                                data-status="present"
                                                data-index="{{ $index }}">
                                            <i class="fas fa-check-circle mr-1"></i> Present
                                        </button>
                                        <button type="button"
                                                onclick="setStatus({{ $index }}, 'absent')"
                                                class="status-btn px-3 py-1 rounded-md text-sm font-medium transition-all {{ $existingStatus === 'absent' ? 'bg-red-500 text-white' : 'bg-gray-100 text-gray-600 hover:bg-red-100' }}"
                                                data-status="absent"
                                                data-index="{{ $index }}">
                                            <i class="fas fa-times-circle mr-1"></i> Absent
                                        </button>
                                        <button type="button"
                                                onclick="setStatus({{ $index }}, 'late')"
                                                class="status-btn px-3 py-1 rounded-md text-sm font-medium transition-all {{ $existingStatus === 'late' ? 'bg-orange-500 text-white' : 'bg-gray-100 text-gray-600 hover:bg-orange-100' }}"
                                                data-status="late"
                                                data-index="{{ $index }}">
                                            <i class="fas fa-clock mr-1"></i> Late
                                        </button>
                                        <button type="button"
                                                onclick="setStatus({{ $index }}, 'excused')"
                                                class="status-btn px-3 py-1 rounded-md text-sm font-medium transition-all {{ $existingStatus === 'excused' ? 'bg-blue-500 text-white' : 'bg-gray-100 text-gray-600 hover:bg-blue-100' }}"
                                                data-status="excused"
                                                data-index="{{ $index }}">
                                            <i class="fas fa-user-shield mr-1"></i> Excused
                                        </button>
                                        <input type="hidden" name="attendance[{{ $index }}][status]" value="{{ $existingStatus }}" id="status_{{ $index }}">
                                    </div>
                                </td>
                                <td>
                                    <input type="text"
                                           name="attendance[{{ $index }}][notes]"
                                           value="{{ $existingAttendance[$student->id]->notes ?? '' }}"
                                           placeholder="Optional notes..."
                                           class="form-input text-sm">
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Mobile Cards -->
                <div class="md:hidden space-y-3 p-4">
                    @foreach($students as $index => $student)
                    <div class="bg-white border border-gray-200 rounded-lg p-4">
                        <div class="flex items-start justify-between mb-3">
                            <div class="flex items-center space-x-3">
                                <div class="avatar avatar-sm {{ $student->gender === 'male' ? 'bg-blue-500' : 'bg-pink-500' }}">
                                    <span>{{ substr($student->first_name, 0, 1) }}{{ substr($student->last_name, 0, 1) }}</span>
                                </div>
                                <div>
                                    <p class="font-medium text-gray-900">{{ $student->full_name }}</p>
                                    <p class="text-sm text-gray-500">{{ $student->admission_number }}</p>
                                </div>
                            </div>
                            <span class="text-gray-500 text-sm">#{{ $index + 1 }}</span>
                        </div>

                        <input type="hidden" name="attendance[{{ $index }}][student_id]" value="{{ $student->id }}">
                        @php
                            $existingStatus = $existingAttendance[$student->id]->status ?? 'present';
                        @endphp
                        <div class="grid grid-cols-2 gap-2 mb-3">
                            <button type="button"
                                    onclick="setStatus({{ $index }}, 'present')"
                                    class="status-btn px-3 py-2 rounded-md text-sm font-medium transition-all {{ $existingStatus === 'present' ? 'bg-green-500 text-white' : 'bg-gray-100 text-gray-600 hover:bg-green-100' }}"
                                    data-status="present"
                                    data-index="{{ $index }}">
                                <i class="fas fa-check-circle mr-1"></i> Present
                            </button>
                            <button type="button"
                                    onclick="setStatus({{ $index }}, 'absent')"
                                    class="status-btn px-3 py-2 rounded-md text-sm font-medium transition-all {{ $existingStatus === 'absent' ? 'bg-red-500 text-white' : 'bg-gray-100 text-gray-600 hover:bg-red-100' }}"
                                    data-status="absent"
                                    data-index="{{ $index }}">
                                <i class="fas fa-times-circle mr-1"></i> Absent
                            </button>
                            <button type="button"
                                    onclick="setStatus({{ $index }}, 'late')"
                                    class="status-btn px-3 py-2 rounded-md text-sm font-medium transition-all {{ $existingStatus === 'late' ? 'bg-orange-500 text-white' : 'bg-gray-100 text-gray-600 hover:bg-orange-100' }}"
                                    data-status="late"
                                    data-index="{{ $index }}">
                                <i class="fas fa-clock mr-1"></i> Late
                            </button>
                            <button type="button"
                                    onclick="setStatus({{ $index }}, 'excused')"
                                    class="status-btn px-3 py-2 rounded-md text-sm font-medium transition-all {{ $existingStatus === 'excused' ? 'bg-blue-500 text-white' : 'bg-gray-100 text-gray-600 hover:bg-blue-100' }}"
                                    data-status="excused"
                                    data-index="{{ $index }}">
                                <i class="fas fa-user-shield mr-1"></i> Excused
                            </button>
                        </div>
                        <input type="hidden" name="attendance[{{ $index }}][status]" value="{{ $existingStatus }}" id="status_{{ $index }}">
                        <input type="text"
                               name="attendance[{{ $index }}][notes]"
                               value="{{ $existingAttendance[$student->id]->notes ?? '' }}"
                               placeholder="Optional notes..."
                               class="form-input text-sm w-full">
                    </div>
                    @endforeach
                </div>
            </div>

            <div class="card-footer">
                <div class="flex justify-between items-center">
                    <a href="{{ route('attendance.index') }}" class="btn btn-outline">
                        Cancel
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save mr-2"></i>
                        Save Attendance
                    </button>
                </div>
            </div>
        </div>
    </form>
    @else
    <!-- Empty State -->
    <div class="card">
        <div class="card-body">
            <div class="text-center py-12">
                <i class="fas fa-clipboard-list text-gray-300 text-6xl mb-4"></i>
                <h3 class="text-lg font-medium text-gray-900 mb-2">Select a Class to Begin</h3>
                <p class="text-gray-600">Choose a date and class from the filters above to load students</p>
            </div>
        </div>
    </div>
    @endif
</div>

<!-- Mobile Footer Navigation -->
<div class="md:hidden fixed bottom-0 left-0 right-0 bg-white border-t border-gray-200 shadow-lg z-40">
    <div class="grid grid-cols-3 gap-1 p-2">
        <a href="{{ route('attendance.index') }}" class="flex flex-col items-center py-2 px-3 text-gray-600 hover:bg-gray-50 rounded-lg transition-colors">
            <i class="fas fa-chart-line text-xl mb-1"></i>
            <span class="text-xs font-medium">Dashboard</span>
        </a>
        <a href="{{ route('attendance.create') }}" class="flex flex-col items-center py-2 px-3 text-primary-600 bg-primary-50 rounded-lg">
            <i class="fas fa-clipboard-check text-xl mb-1"></i>
            <span class="text-xs font-medium">Take</span>
        </a>
        <a href="{{ route('attendance.records') }}" class="flex flex-col items-center py-2 px-3 text-gray-600 hover:bg-gray-50 rounded-lg transition-colors">
            <i class="fas fa-history text-xl mb-1"></i>
            <span class="text-xs font-medium">Records</span>
        </a>
    </div>
</div>

<script>
function setStatus(index, status) {
    // Update hidden input
    document.getElementById('status_' + index).value = status;
    
    // Update button styles
    const buttons = document.querySelectorAll(`[data-index="${index}"]`);
    buttons.forEach(btn => {
        btn.classList.remove('bg-green-500', 'bg-red-500', 'bg-orange-500', 'bg-blue-500', 'text-white');
        btn.classList.add('bg-gray-100', 'text-gray-600');
        
        if (btn.dataset.status === status) {
            btn.classList.remove('bg-gray-100', 'text-gray-600');
            btn.classList.add('text-white');
            
            switch(status) {
                case 'present':
                    btn.classList.add('bg-green-500');
                    break;
                case 'absent':
                    btn.classList.add('bg-red-500');
                    break;
                case 'late':
                    btn.classList.add('bg-orange-500');
                    break;
                case 'excused':
                    btn.classList.add('bg-blue-500');
                    break;
            }
        }
    });
}

function markAllPresent() {
    const studentCount = {{ $students ? $students->count() : 0 }};
    for (let i = 0; i < studentCount; i++) {
        setStatus(i, 'present');
    }
}

// Warn before leaving with unsaved changes
let formChanged = false;
document.getElementById('attendanceForm')?.addEventListener('change', () => {
    formChanged = true;
});

window.addEventListener('beforeunload', (e) => {
    if (formChanged) {
        e.preventDefault();
        e.returnValue = '';
    }
});

document.getElementById('attendanceForm')?.addEventListener('submit', () => {
    formChanged = false;
});
</script>
@endsection
