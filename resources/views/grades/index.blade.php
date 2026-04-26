@extends('layouts.spa')

@section('title', 'Gradebook - Enter Grades')

@section('breadcrumb')
    <span class="text-gray-400">Grades</span>
    <span class="text-gray-400">/</span>
    <span class="font-semibold text-gray-900">Gradebook</span>
@endsection

@section('content')
<div class="space-y-6 pb-20 md:pb-6">
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
            <h1 class="text-2xl font-bold text-gray-900">Gradebook</h1>
            <p class="text-gray-600 mt-1">Enter and manage student grades</p>
        </div>
        <div class="flex items-center space-x-3">
            <a href="{{ route('grades.class-results') }}" class="btn btn-outline">
                <i class="fas fa-chart-bar mr-2"></i>
                Class Results
            </a>
            <a href="{{ route('grades.scales') }}" class="btn btn-outline">
                <i class="fas fa-cog mr-2"></i>
                Setup
            </a>
        </div>
    </div>

    <!-- Filters -->
    <div class="card">
        <div class="card-body">
            <form method="GET" action="{{ route('grades.index') }}" id="filterForm" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                    <!-- Term -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Term <span class="text-red-500">*</span></label>
                        <select name="term" class="form-select" required onchange="document.getElementById('filterForm').submit()">
                            <option value="First Term" {{ $term === 'First Term' ? 'selected' : '' }}>First Term</option>
                            <option value="Second Term" {{ $term === 'Second Term' ? 'selected' : '' }}>Second Term</option>
                            <option value="Third Term" {{ $term === 'Third Term' ? 'selected' : '' }}>Third Term</option>
                        </select>
                    </div>

                    <!-- Session -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Session <span class="text-red-500">*</span></label>
                        <select name="session" class="form-select" required onchange="document.getElementById('filterForm').submit()">
                            <option value="2024/2025" {{ $session === '2024/2025' ? 'selected' : '' }}>2024/2025</option>
                            <option value="2025/2026" {{ $session === '2025/2026' ? 'selected' : '' }}>2025/2026</option>
                            <option value="2023/2024" {{ $session === '2023/2024' ? 'selected' : '' }}>2023/2024</option>
                        </select>
                    </div>

                    <!-- Exam Type -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Exam Type <span class="text-red-500">*</span></label>
                        <select name="exam_type_id" class="form-select" required onchange="document.getElementById('filterForm').submit()">
                            <option value="">Select exam...</option>
                            @foreach($examTypes as $examType)
                                <option value="{{ $examType->id }}" {{ $examTypeId == $examType->id ? 'selected' : '' }}>
                                    {{ $examType->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Class -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Class <span class="text-red-500">*</span></label>
                        <select name="class_id" class="form-select" required onchange="document.getElementById('filterForm').submit()">
                            <option value="">Select class...</option>
                            @foreach($classes as $class)
                                <option value="{{ $class->id }}" {{ $classId == $class->id ? 'selected' : '' }}>
                                    {{ $class->name }} - Section {{ $class->section }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Subject -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Subject <span class="text-red-500">*</span></label>
                        <select name="subject_id" class="form-select" required onchange="document.getElementById('filterForm').submit()">
                            <option value="">Select subject...</option>
                            @foreach($subjects as $subject)
                                <option value="{{ $subject->id }}" {{ $subjectId == $subject->id ? 'selected' : '' }}>
                                    {{ $subject->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </form>

            @if($students)
            <!-- Grade Scale Reference -->
            <div class="mt-4 p-3 bg-blue-50 border-l-4 border-blue-500 rounded">
                <p class="text-sm font-medium text-blue-900 mb-2">Grade Scale Reference:</p>
                <div class="flex flex-wrap gap-3 text-xs text-blue-800">
                    @foreach($gradeScales as $scale)
                        <span class="bg-white px-2 py-1 rounded border border-blue-200">
                            <strong>{{ $scale->grade }}:</strong> {{ $scale->min_score }}-{{ $scale->max_score }} ({{ $scale->remark }})
                        </span>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
    </div>

    @if($students)
    <!-- Grade Entry Form -->
    <form method="POST" action="{{ route('grades.store') }}" id="gradeForm">
        @csrf
        <input type="hidden" name="term" value="{{ $term }}">
        <input type="hidden" name="session" value="{{ $session }}">
        <input type="hidden" name="class_id" value="{{ $classId }}">
        <input type="hidden" name="subject_id" value="{{ $subjectId }}">
        <input type="hidden" name="exam_type_id" value="{{ $examTypeId }}">

        <div class="card">
            <div class="card-header">
                <div>
                    <h2 class="text-lg font-semibold text-gray-900">
                        Students
                        <span class="text-gray-500 font-normal">({{ $students->count() }})</span>
                    </h2>
                    <p class="text-sm text-gray-600 mt-1">{{ $term }} - {{ $session }}</p>
                </div>
                <div class="text-sm text-gray-600">
                    <i class="fas fa-info-circle mr-1"></i>
                    Scores are out of 100
                </div>
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
                                <th class="w-32">Score</th>
                                <th class="w-24">Grade</th>
                                <th>Remark (Optional)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($students as $index => $student)
                            @php
                                $existingGrade = $existingGrades[$student->id] ?? null;
                                $score = $existingGrade ? $existingGrade->score : '';
                            @endphp
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
                                    <input type="hidden" name="grades[{{ $index }}][student_id]" value="{{ $student->id }}">
                                    <input type="number"
                                           name="grades[{{ $index }}][score]"
                                           value="{{ $score }}"
                                           min="0"
                                           max="100"
                                           step="0.01"
                                           class="form-input"
                                           placeholder="0-100"
                                           required
                                           onchange="calculateGrade({{ $index }}, this.value)"
                                           id="score_{{ $index }}">
                                </td>
                                <td>
                                    <span id="grade_{{ $index }}" class="font-semibold text-lg"></span>
                                </td>
                                <td>
                                    <input type="text"
                                           name="grades[{{ $index }}][remark]"
                                           value="{{ $existingGrade ? $existingGrade->remark : '' }}"
                                           class="form-input text-sm"
                                           placeholder="Optional remark...">
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Mobile Cards -->
                <div class="md:hidden space-y-3 p-4">
                    @foreach($students as $index => $student)
                    @php
                        $existingGrade = $existingGrades[$student->id] ?? null;
                        $score = $existingGrade ? $existingGrade->score : '';
                    @endphp
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

                        <input type="hidden" name="grades[{{ $index }}][student_id]" value="{{ $student->id }}">
                        
                        <div class="space-y-3">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Score (0-100)</label>
                                <div class="flex items-center space-x-3">
                                    <input type="number"
                                           name="grades[{{ $index }}][score]"
                                           value="{{ $score }}"
                                           min="0"
                                           max="100"
                                           step="0.01"
                                           class="form-input flex-1"
                                           placeholder="0-100"
                                           required
                                           onchange="calculateGrade({{ $index }}, this.value)"
                                           id="score_mobile_{{ $index }}">
                                    <span id="grade_mobile_{{ $index }}" class="font-bold text-xl text-gray-900"></span>
                                </div>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Remark (Optional)</label>
                                <input type="text"
                                       name="grades[{{ $index }}][remark]"
                                       value="{{ $existingGrade ? $existingGrade->remark : '' }}"
                                       class="form-input w-full text-sm"
                                       placeholder="Optional remark...">
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            <div class="card-footer">
                <div class="flex justify-between items-center">
                    <a href="{{ route('grades.index') }}" class="btn btn-outline">
                        Reset
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save mr-2"></i>
                        Save All Grades
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
                <h3 class="text-lg font-medium text-gray-900 mb-2">Select Filters to Begin</h3>
                <p class="text-gray-600">Choose term, session, exam type, class, and subject from the filters above</p>
            </div>
        </div>
    </div>
    @endif
</div>

<!-- Mobile Footer Navigation -->
<div class="md:hidden fixed bottom-0 left-0 right-0 bg-white border-t border-gray-200 shadow-lg z-40">
    <div class="grid grid-cols-4 gap-1 p-2">
        <a href="{{ route('grades.index') }}" class="flex flex-col items-center py-2 px-3 text-primary-600 bg-primary-50 rounded-lg">
            <i class="fas fa-edit text-xl mb-1"></i>
            <span class="text-xs font-medium">Gradebook</span>
        </a>
        <a href="{{ route('grades.class-results') }}" class="flex flex-col items-center py-2 px-3 text-gray-600 hover:bg-gray-50 rounded-lg transition-colors">
            <i class="fas fa-chart-bar text-xl mb-1"></i>
            <span class="text-xs font-medium">Results</span>
        </a>
        <a href="{{ route('students.index') }}" class="flex flex-col items-center py-2 px-3 text-gray-600 hover:bg-gray-50 rounded-lg transition-colors">
            <i class="fas fa-users text-xl mb-1"></i>
            <span class="text-xs font-medium">Students</span>
        </a>
        <a href="{{ route('grades.scales') }}" class="flex flex-col items-center py-2 px-3 text-gray-600 hover:bg-gray-50 rounded-lg transition-colors">
            <i class="fas fa-cog text-xl mb-1"></i>
            <span class="text-xs font-medium">Setup</span>
        </a>
    </div>
</div>

<script>
const gradeScales = @json($gradeScales);

function calculateGrade(index, score) {
    if (!score || score < 0 || score > 100) {
        document.getElementById('grade_' + index).textContent = '';
        const mobileGrade = document.getElementById('grade_mobile_' + index);
        if (mobileGrade) mobileGrade.textContent = '';
        return;
    }

    let grade = 'F';
    for (let scale of gradeScales) {
        if (parseFloat(score) >= parseFloat(scale.min_score) && parseFloat(score) <= parseFloat(scale.max_score)) {
            grade = scale.grade;
            break;
        }
    }

    document.getElementById('grade_' + index).textContent = grade;
    const mobileGrade = document.getElementById('grade_mobile_' + index);
    if (mobileGrade) mobileGrade.textContent = grade;
}

// Calculate grades on page load for existing scores
window.addEventListener('DOMContentLoaded', () => {
    @if($students)
        @foreach($students as $index => $student)
            @php
                $existingGrade = $existingGrades[$student->id] ?? null;
                $score = $existingGrade ? $existingGrade->score : '';
            @endphp
            @if($score)
                calculateGrade({{ $index }}, {{ $score }});
            @endif
        @endforeach
    @endif
});

// Warn before leaving with unsaved changes
let formChanged = false;
document.getElementById('gradeForm')?.addEventListener('change', () => {
    formChanged = true;
});

window.addEventListener('beforeunload', (e) => {
    if (formChanged) {
        e.preventDefault();
        e.returnValue = '';
    }
});

document.getElementById('gradeForm')?.addEventListener('submit', () => {
    formChanged = false;
});
</script>
@endsection
