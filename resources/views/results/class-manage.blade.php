@extends('layouts.spa')

@section('content')
<div class="min-h-screen bg-gray-50 px-2 sm:px-3 md:px-4 lg:px-6 py-4 sm:py-6 md:py-8">
    <!-- Breadcrumb & Header -->
    <div class="mb-8">
        <div class="flex items-center gap-2 text-sm text-gray-600 mb-4">
            <a href="{{ route('results.index') }}" class="text-primary-600 hover:text-primary-700">Results</a>
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            <a href="{{ route('results.session.show', $session) }}" class="text-primary-600 hover:text-primary-700">{{ $session->session }}</a>
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            <a href="{{ route('results.term.manage', [$session, $term]) }}" class="text-primary-600 hover:text-primary-700">{{ $term->term }}</a>
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            <span>{{ $class->name }}</span>
        </div>
        <h1 class="text-3xl font-bold text-gray-900">Manage Class Results</h1>
        <p class="text-gray-600 mt-2">{{ $class->name }} | {{ $term->term }} | {{ $session->session }}</p>
    </div>

    @if (session('success'))
        <div class="mb-4 p-4 bg-green-50 border border-green-200 rounded-lg flex items-start justify-between">
            <p class="text-sm text-green-700">{{ session('success') }}</p>
            <button type="button" onclick="this.parentElement.style.display='none'" class="text-green-700 hover:text-green-900">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/></svg>
            </button>
        </div>
    @endif

    @if ($errors->any())
        <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-lg">
            <p class="text-sm text-red-700">{{ $errors->first() }}</p>
        </div>
    @endif

    <!-- Tabs -->
    <div class="mb-6">
        <div class="border-b border-gray-200 flex gap-8">
            <button onclick="switchTab(event, 'results')" class="tab-btn active px-4 py-3 border-b-2 border-primary-600 font-medium text-primary-600 focus:outline-none" data-tab="results">
                <i class="fas fa-edit mr-2"></i>Enter Scores
            </button>
            <button onclick="switchTab(event, 'report')" class="tab-btn px-4 py-3 border-b-2 border-transparent font-medium text-gray-600 hover:text-gray-900 hover:border-gray-300 focus:outline-none" data-tab="report">
                <i class="fas fa-chart-bar mr-2"></i>View Report
            </button>
        </div>
    </div>

    <!-- Tab 1: Enter Scores -->
    <div id="results-tab" class="tab-content">
        <form id="results-form" action="{{ route('results.class.store', [$session, $term, $class]) }}" method="POST" class="space-y-6" onsubmit="handleFormSubmit(event)">
            @csrf
            
            <!-- Hidden Fields for IDs -->
            <input type="hidden" name="academic_session_id" value="{{ $session->id }}">
            <input type="hidden" name="academic_term_id" value="{{ $term->id }}">
            <input type="hidden" name="school_class_id" value="{{ $class->id }}">

            <!-- Subjects Tabs -->
            <div class="bg-white rounded-lg shadow">
                <div class="border-b border-gray-200 p-3 md:p-6">
                    <h2 class="text-base md:text-lg font-semibold text-gray-900 mb-4">Select Subject</h2>
                    <div class="flex flex-wrap gap-2">
                        @foreach ($subjects as $subject)
                            <button type="button" onclick="selectSubject(event, '{{ $subject->id }}')" class="subject-tab px-4 py-2 rounded-lg font-medium text-sm transition-colors" data-subject-id="{{ $subject->id }}" {{ $loop->first ? '' : 'data-inactive="true"' }}>
                                {{ $subject->name }}
                            </button>
                        @endforeach
                    </div>
                    
                    <!-- Admin Omni Input: Bulk score assignment -->
                    @if(auth()->user() && auth()->user()->hasRole('Administrator'))
                    <div class="mt-6 pt-6 border-t border-gray-200">
                        <h3 class="text-sm font-semibold text-gray-900 mb-4 flex items-center gap-2">
                            <i class="fas fa-bolt text-yellow-500"></i>Admin Quick Assign
                        </h3>
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-2">CA Score (/ 50)</label>
                                <input type="number" id="omni-ca-score" min="0" max="50" step="0.5" placeholder="Leave empty to skip" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-center text-sm focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-2">Exam Score (/ 100)</label>
                                <input type="number" id="omni-exam-score" min="0" max="100" step="0.5" placeholder="Leave empty to skip" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-center text-sm focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500">
                            </div>
                            <div class="flex items-end">
                                <button type="button" onclick="applyOmniScores()" class="w-full px-4 py-2 bg-yellow-500 text-white font-medium rounded-lg hover:bg-yellow-600 transition-colors text-sm flex items-center justify-center gap-2">
                                    <i class="fas fa-check"></i>Apply to All
                                </button>
                            </div>
                        </div>
                        <p class="text-xs text-gray-500 mt-2">
                            <i class="fas fa-info-circle"></i>Applies scores to all students in the currently selected subject
                        </p>
                    </div>
                    @endif
                </div>

                <!-- Subject Results - Desktop Table & Mobile Cards -->
                @foreach ($subjects as $subject)
                    <div id="subject-{{ $subject->id }}" class="subject-content {{ !$loop->first ? 'hidden' : '' }}" data-subject-id="{{ $subject->id }}">
                        <!-- Desktop Table View (lg+) -->
                        <div class="hidden lg:block overflow-x-auto">
                            <table class="w-full">
                                <thead class="bg-gray-50 border-b border-gray-200">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-900 uppercase tracking-wider">Student</th>
                                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-900 uppercase tracking-wider">Admission No.</th>
                                        <th class="px-6 py-3 text-center text-xs font-semibold text-gray-900 uppercase tracking-wider">CA (/ 50)</th>
                                        <th class="px-6 py-3 text-center text-xs font-semibold text-gray-900 uppercase tracking-wider">Exam (/ 100)</th>
                                        <th class="px-6 py-3 text-center text-xs font-semibold text-gray-900 uppercase tracking-wider">Total</th>
                                        <th class="px-6 py-3 text-center text-xs font-semibold text-gray-900 uppercase tracking-wider">Grade</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    @foreach ($students as $student)
                                        @php
                                            $studentResult = $resultsWithGrades["{$student->id}_{$subject->id}"][0] ?? null;
                                            $caScore = $studentResult?->ca_score ?? '';
                                            $examScore = $studentResult?->exam_score ?? '';
                                            $totalScore = $studentResult?->computed_cumulative_score ?? '';
                                            $grade = $studentResult?->computed_grade ?? '-';
                                        @endphp
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="flex items-center">
                                                    <div class="w-10 h-10 bg-gray-200 rounded-full flex items-center justify-center flex-shrink-0">
                                                        <i class="fas fa-user text-gray-600 text-xs"></i>
                                                    </div>
                                                    <div class="ml-4">
                                                        <p class="font-medium text-gray-900">{{ $student->first_name }} {{ $student->last_name }}</p>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                                {{ $student->admission_number }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                                <!-- Desktop inputs: enabled only when this subject is active -->
                                                <input type="number" name="results[{{ $student->id }}_{{ $subject->id }}][ca_score]" min="0" max="50" step="0.5" value="{{ $caScore }}" 
                                                    class="w-20 px-3 py-2 border border-gray-300 rounded-lg text-center text-sm focus:ring-primary-500 focus:border-primary-500"
                                                    onchange="calculateTotal(this)" {{ $loop->parent->first ? '' : 'disabled' }}>
                                                <input type="hidden" name="results[{{ $student->id }}_{{ $subject->id }}][student_id]" value="{{ $student->id }}" {{ $loop->parent->first ? '' : 'disabled' }}>
                                                <input type="hidden" name="results[{{ $student->id }}_{{ $subject->id }}][subject_id]" value="{{ $subject->id }}" {{ $loop->parent->first ? '' : 'disabled' }}>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                                <input type="number" name="results[{{ $student->id }}_{{ $subject->id }}][exam_score]" min="0" max="100" step="0.5" value="{{ $examScore }}"
                                                    class="w-20 px-3 py-2 border border-gray-300 rounded-lg text-center text-sm focus:ring-primary-500 focus:border-primary-500"
                                                    onchange="calculateTotal(this)" {{ $loop->parent->first ? '' : 'disabled' }}>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                                <span class="total-score font-semibold text-gray-900">{{ $totalScore !== '' ? number_format($totalScore, 1) : '-' }}</span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                                <span class="grade-badge px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $grade === 'A' ? 'bg-green-100 text-green-800' : ($grade === 'F' ? 'bg-red-100 text-red-800' : 'bg-gray-100 text-gray-800') }}">
                                                    {{ $grade }}
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Mobile Card View (hidden on lg+) -->
                        <div class="lg:hidden space-y-3 p-2 sm:p-3 md:p-4">
                            @foreach ($students as $student)
                                @php
                                    $studentResult = $resultsWithGrades["{$student->id}_{$subject->id}"][0] ?? null;
                                    $caScore = $studentResult?->ca_score ?? '';
                                    $examScore = $studentResult?->exam_score ?? '';
                                    $totalScore = $studentResult?->computed_cumulative_score ?? '';
                                    $grade = $studentResult?->computed_grade ?? '-';
                                @endphp
                                <div class="bg-white rounded-lg border border-gray-200 p-3 md:p-4 shadow-sm" data-student-id="{{ $student->id }}" data-subject-id="{{ $subject->id }}">
                                    <!-- Mobile inputs: enabled only when this subject is active -->
                                    <input type="hidden" name="results[{{ $student->id }}_{{ $subject->id }}][student_id]" value="{{ $student->id }}" {{ $loop->parent->first ? '' : 'disabled' }}>
                                    <input type="hidden" name="results[{{ $student->id }}_{{ $subject->id }}][subject_id]" value="{{ $subject->id }}" {{ $loop->parent->first ? '' : 'disabled' }}>
                                    
                                    <!-- Student Header -->
                                    <div class="flex items-center gap-3 mb-4 pb-4 border-b border-gray-200">
                                        <div class="w-12 h-12 bg-primary-100 rounded-full flex items-center justify-center flex-shrink-0">
                                            <i class="fas fa-user text-primary-600"></i>
                                        </div>
                                        <div class="flex-1">
                                            <p class="font-bold text-gray-900">{{ $student->first_name }} {{ $student->last_name }}</p>
                                            <p class="text-xs text-gray-600">Admission: {{ $student->admission_number }}</p>
                                        </div>
                                    </div>

                                    <!-- Score Inputs -->
                                    <div class="space-y-4 mb-4">
                                        <!-- CA Score -->
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">CA Score (/ 50)</label>
                                            <input type="number" name="results[{{ $student->id }}_{{ $subject->id }}][ca_score]" min="0" max="50" step="0.5" value="{{ $caScore }}" 
                                                class="w-full px-4 py-2 border border-gray-300 rounded-lg text-center font-medium focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                                                onchange="calculateTotal(this)" placeholder="0" {{ $loop->parent->first ? '' : 'disabled' }}>
                                        </div>

                                        <!-- Exam Score -->
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Exam Score (/ 100)</label>
                                            <input type="number" name="results[{{ $student->id }}_{{ $subject->id }}][exam_score]" min="0" max="100" step="0.5" value="{{ $examScore }}"
                                                class="w-full px-4 py-2 border border-gray-300 rounded-lg text-center font-medium focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                                                onchange="calculateTotal(this)" placeholder="0" {{ $loop->parent->first ? '' : 'disabled' }}>
                                        </div>
                                    </div>

                                    <!-- Summary Row -->
                                    <div class="grid grid-cols-2 gap-3 pt-4 border-t border-gray-200">
                                        <div class="bg-gray-50 rounded p-3 text-center">
                                            <p class="text-xs text-gray-600 mb-1">Total</p>
                                            <p class="total-score font-bold text-lg text-gray-900">{{ $totalScore !== '' ? number_format($totalScore, 1) : '-' }}</p>
                                        </div>
                                        <div class="bg-gray-50 rounded p-3 text-center">
                                            <p class="text-xs text-gray-600 mb-1">Grade</p>
                                            <span class="grade-badge inline-block px-3 py-1 rounded-full text-sm font-bold {{ $grade === 'A' ? 'bg-green-100 text-green-800' : ($grade === 'F' ? 'bg-red-100 text-red-800' : 'bg-blue-100 text-blue-800') }}">
                                                {{ $grade }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Actions -->
            <div class="flex gap-3">
                <button type="submit" class="px-6 py-3 bg-primary-600 text-white font-medium rounded-lg hover:bg-primary-700 transition-colors flex items-center gap-2">
                    <i class="fas fa-save"></i>Save Results
                </button>
                <a href="{{ route('results.term.manage', [$session, $term]) }}" class="px-6 py-3 bg-gray-200 text-gray-900 font-medium rounded-lg hover:bg-gray-300 transition-colors flex items-center gap-2">
                    <i class="fas fa-times"></i>Cancel
                </a>
            </div>
        </form>
    </div>

    <!-- Tab 2: View Report -->
    <div id="report-tab" class="tab-content hidden">
        <a href="{{ route('results.class.report', [$session, $term, $class]) }}" class="inline-flex items-center px-6 py-3 bg-primary-600 text-white font-medium rounded-lg hover:bg-primary-700 transition-colors gap-2">
            <i class="fas fa-chart-bar"></i>View Full Report
        </a>
    </div>
</div>

<script>
// ============================================
// 1. FORM SUBMISSION HANDLER
// ============================================
// Ensures only active subject inputs are enabled before submission
function handleFormSubmit(event) {
    const form = document.getElementById('results-form');
    
    // Find the currently active subject
    const activeSubjectContent = form.querySelector('.subject-content:not(.hidden)');
    if (!activeSubjectContent) {
        console.error('No active subject found');
        return false;
    }
    
    const activeSubjectId = activeSubjectContent.getAttribute('data-subject-id');
    
    // Disable all inputs from inactive subjects
    const allSubjectContents = form.querySelectorAll('.subject-content');
    allSubjectContents.forEach(subjectContent => {
        const subjectId = subjectContent.getAttribute('data-subject-id');
        const inputs = subjectContent.querySelectorAll('input[name*="results["]');
        
        if (subjectId !== activeSubjectId) {
            // Disable inputs from inactive subjects
            inputs.forEach(input => {
                input.disabled = true;
            });
        } else {
            // Ensure active subject inputs are enabled
            inputs.forEach(input => {
                input.disabled = false;
            });
        }
    });
    
    // Debug before submission
    debugFormData();
    
    // Allow form to submit
    return true;
}

// ============================================
// 2. SUBJECT SELECTION & INPUT CONTROL
// ============================================
// Manages switching between subjects and enabling/disabling inputs accordingly
function selectSubject(event, subjectId) {
    event.preventDefault();
    
    const form = document.getElementById('results-form');
    
    // Hide all subjects
    form.querySelectorAll('.subject-content').forEach(el => {
        el.classList.add('hidden');
        
        // Disable inputs in hidden subjects
        const inputs = el.querySelectorAll('input[name*="results["]');
        inputs.forEach(input => {
            input.disabled = true;
        });
    });
    
    // Show selected subject
    const activeSubject = form.querySelector(`#subject-${subjectId}`);
    activeSubject.classList.remove('hidden');
    
    // Enable inputs in active subject
    const activeInputs = activeSubject.querySelectorAll('input[name*="results["]');
    activeInputs.forEach(input => {
        input.disabled = false;
    });
    
    // Update tab styling
    form.querySelectorAll('.subject-tab').forEach(el => {
        el.classList.remove('bg-primary-100', 'text-primary-700');
        el.classList.add('bg-gray-100', 'text-gray-700');
    });
    
    const activeTab = form.querySelector(`.subject-tab[data-subject-id="${subjectId}"]`);
    activeTab.classList.remove('bg-gray-100', 'text-gray-700');
    activeTab.classList.add('bg-primary-100', 'text-primary-700');
}

// ============================================
// 3. TAB SWITCHING
// ============================================
// Switches between Enter Scores and View Report tabs
function switchTab(event, tabName) {
    event.preventDefault();
    
    // Hide all tabs
    document.querySelectorAll('.tab-content').forEach(el => el.classList.add('hidden'));
    document.querySelectorAll('.tab-btn').forEach(el => {
        el.classList.remove('active', 'border-primary-600', 'text-primary-600');
        el.classList.add('border-transparent', 'text-gray-600');
    });
    
    // Show selected tab
    document.getElementById(tabName + '-tab').classList.remove('hidden');
    event.target.closest('.tab-btn').classList.add('active', 'border-primary-600', 'text-primary-600');
}

// ============================================
// 4. TOTAL SCORE CALCULATION
// ============================================
// Calculates total score and updates grade display for a student
function calculateTotal(input) {
    // Find the container: either table row or card div
    let container = input.closest('tr');
    
    if (!container) {
        container = input.closest('[data-student-id]');
    }
    
    if (!container) {
        console.error('Could not find container for calculation');
        return;
    }
    
    const caInput = container.querySelector('input[name*="[ca_score]"]');
    const examInput = container.querySelector('input[name*="[exam_score]"]');
    const totalSpan = container.querySelector('.total-score');
    const gradeSpan = container.querySelector('.grade-badge');
    
    if (!caInput || !examInput || !totalSpan || !gradeSpan) {
        console.error('Could not find required elements in container');
        return;
    }
    
    const ca = parseFloat(caInput.value) || 0;
    const exam = parseFloat(examInput.value) || 0;
    const total = ca + exam;
    
    if (ca > 0 || exam > 0) {
        totalSpan.textContent = total.toFixed(1);
        const grade = getGrade(total);
        gradeSpan.textContent = grade;
        updateGradeBadgeColor(gradeSpan, grade);
    } else {
        totalSpan.textContent = '-';
        gradeSpan.textContent = '-';
        gradeSpan.classList.remove('bg-green-100', 'text-green-800', 'bg-red-100', 'text-red-800', 'bg-blue-100', 'text-blue-800');
        gradeSpan.classList.add('bg-gray-100', 'text-gray-800');
    }
}

// ============================================
// 5. GRADE UTILITIES
// ============================================
// Grade boundaries from server
const gradeBoundaries = {!! json_encode($gradeBoundaries) !!};

function getGrade(score) {
    const boundaries = Object.entries(gradeBoundaries)
        .sort((a, b) => parseInt(b[1]) - parseInt(a[1]));
    
    for (const [grade, minScore] of boundaries) {
        if (score >= parseInt(minScore)) {
            return grade;
        }
    }
    return 'N/A';
}

function updateGradeBadgeColor(element, grade) {
    element.classList.remove('bg-green-100', 'text-green-800', 'bg-red-100', 'text-red-800', 'bg-blue-100', 'text-blue-800', 'bg-gray-100', 'text-gray-800');
    
    if (grade === 'A' || grade === 'A+') {
        element.classList.add('bg-green-100', 'text-green-800');
    } else if (grade === 'F' || grade === 'E') {
        element.classList.add('bg-red-100', 'text-red-800');
    } else {
        element.classList.add('bg-blue-100', 'text-blue-800');
    }
}

// ============================================
// 6. DEBUG FUNCTION
// ============================================
// Logs form data state without blocking submission
function debugFormData() {
    const form = document.getElementById('results-form');
    const formData = new FormData(form);
    
    console.log('=== FORM DEBUG ===');
    
    let totalFields = 0;
    let subjects = new Set();
    let sampleResult = null;
    
    for (let [key, value] of formData.entries()) {
        if (key.startsWith('results[')) {
            totalFields++;
            
            const match = key.match(/results\[(\d+)_(\d+)\]/);
            if (match) {
                subjects.add(match[2]);
                
                // Capture first result as sample
                if (!sampleResult) {
                    sampleResult = { key: key, value: value };
                }
            }
        }
    }
    
    console.log(`Total fields: ${totalFields}`);
    console.log(`Subjects: [${Array.from(subjects).join(', ')}]`);
    console.log(`Sample: ${sampleResult?.key || 'none'} = ${sampleResult?.value || 'none'}`);
    console.log('==================');
}

// ============================================
// 7. ADMIN OMNI INPUT - BULK SCORE ASSIGNMENT
// ============================================
// Applies uniform CA and Exam scores to all students in the selected subject
function applyOmniScores() {
    const form = document.getElementById('results-form');
    const caScoreInput = document.getElementById('omni-ca-score');
    const examScoreInput = document.getElementById('omni-exam-score');
    
    const caScore = caScoreInput.value.trim();
    const examScore = examScoreInput.value.trim();
    
    // Validate: at least one score must be provided
    if (!caScore && !examScore) {
        alert('Please enter at least one score (CA or Exam)');
        return;
    }
    
    // Find the currently active subject
    const activeSubjectContent = form.querySelector('.subject-content:not(.hidden)');
    if (!activeSubjectContent) {
        alert('No active subject found');
        return;
    }
    
    // Get all result input fields for this subject (works for both desktop table and mobile cards)
    const allCaInputs = activeSubjectContent.querySelectorAll('input[name*="[ca_score]"]');
    const allExamInputs = activeSubjectContent.querySelectorAll('input[name*="[exam_score]"]');
    
    if (allCaInputs.length === 0 && allExamInputs.length === 0) {
        alert('No students found in this subject');
        return;
    }
    
    let updatedCount = 0;
    const processedStudents = new Set();
    
    // Update CA scores for all students
    if (caScore) {
        allCaInputs.forEach(input => {
            input.value = caScore;
            calculateTotal(input);
            
            // Track unique students
            const container = input.closest('tr') || input.closest('[data-student-id]');
            if (container && container.getAttribute('data-student-id')) {
                processedStudents.add(container.getAttribute('data-student-id'));
            }
        });
    }
    
    // Update Exam scores for all students
    if (examScore) {
        allExamInputs.forEach(input => {
            input.value = examScore;
            calculateTotal(input);
            
            // Track unique students
            const container = input.closest('tr') || input.closest('[data-student-id]');
            if (container && container.getAttribute('data-student-id')) {
                processedStudents.add(container.getAttribute('data-student-id'));
            }
        });
    }
    
    // If we couldn't track via data-student-id, use the number of input fields
    updatedCount = processedStudents.size > 0 ? processedStudents.size : Math.max(allCaInputs.length, allExamInputs.length);
    
    // Clear the omni inputs
    caScoreInput.value = '';
    examScoreInput.value = '';
    
    // Show confirmation
    alert(`Applied scores to ${updatedCount} students in this subject`);
}

// Initialize: set first subject tab to active
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('results-form');
    const firstTab = form.querySelector('.subject-tab');
    
    if (firstTab) {
        const firstSubjectId = firstTab.getAttribute('data-subject-id');
        selectSubject({ preventDefault: () => {}, target: { closest: () => firstTab } }, firstSubjectId);
    }
});
</script>
@endsection
