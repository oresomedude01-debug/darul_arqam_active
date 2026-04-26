@extends('layouts.spa')

@section('content')
<div class="min-h-screen bg-gray-50 px-6 py-8">
    <!-- Breadcrumb & Header -->
    <div class="mb-8">
        <div class="flex items-center gap-2 text-sm text-gray-600 mb-4">
            <a href="{{ route('results.index') }}" class="text-primary-600 hover:text-primary-700">Results</a>
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            <span>Print Result Card</span>
        </div>
        <h1 class="text-3xl font-bold text-gray-900">Print Result Card</h1>
        <p class="text-gray-600 mt-2">Select exam year, term, class, and student to print their result card</p>
    </div>

    <!-- Selection Form -->
    <div class="bg-white rounded-lg shadow p-8">
        <form id="selectionForm" method="GET">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Academic Session -->
                <div>
                    <label class="block text-sm font-semibold text-gray-900 mb-2">
                        <i class="fas fa-calendar mr-2 text-primary-600"></i>Academic Session / Year
                    </label>
                    <select id="sessionSelect" name="session_id" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-primary-500 focus:border-primary-500">
                        <option value="">-- Select Academic Session --</option>
                        @foreach($sessions as $session)
                            <option value="{{ $session->id }}">{{ $session->session }}</option>
                        @endforeach
                    </select>
                    <p class="text-xs text-gray-500 mt-1">Only sessions with released results are available</p>
                </div>

                <!-- Term -->
                <div>
                    <label class="block text-sm font-semibold text-gray-900 mb-2">
                        <i class="fas fa-bookmark mr-2 text-primary-600"></i>Term
                    </label>
                    <select id="termSelect" name="term_id" required disabled class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-primary-500 focus:border-primary-500 disabled:bg-gray-50 disabled:text-gray-500">
                        <option value="">-- Select Term --</option>
                    </select>
                    <p class="text-xs text-gray-500 mt-1">Select session first</p>
                </div>

                <!-- Class -->
                <div>
                    <label class="block text-sm font-semibold text-gray-900 mb-2">
                        <i class="fas fa-chalkboard-user mr-2 text-primary-600"></i>Class
                    </label>
                    <select id="classSelect" name="class_id" required disabled class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-primary-500 focus:border-primary-500 disabled:bg-gray-50 disabled:text-gray-500">
                        <option value="">-- Select Class --</option>
                    </select>
                    <p class="text-xs text-gray-500 mt-1">Select term first</p>
                </div>

                <!-- Student -->
                <div>
                    <label class="block text-sm font-semibold text-gray-900 mb-2">
                        <i class="fas fa-user-graduate mr-2 text-primary-600"></i>Student
                    </label>
                    <select id="studentSelect" name="student_id" required disabled class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-primary-500 focus:border-primary-500 disabled:bg-gray-50 disabled:text-gray-500">
                        <option value="">-- Select Student --</option>
                    </select>
                    <p class="text-xs text-gray-500 mt-1">Select class first</p>
                </div>
            </div>

            <!-- Buttons -->
            <div class="mt-8 flex gap-3">
                <button type="submit" id="printBtn" disabled class="px-6 py-3 bg-primary-600 text-white font-medium rounded-lg hover:bg-primary-700 transition-colors disabled:bg-gray-400 disabled:cursor-not-allowed flex items-center gap-2">
                    <i class="fas fa-print"></i>Print Result Card
                </button>
                <a href="{{ route('results.index') }}" class="px-6 py-3 bg-gray-200 text-gray-900 font-medium rounded-lg hover:bg-gray-300 transition-colors flex items-center gap-2">
                    <i class="fas fa-times"></i>Cancel
                </a>
            </div>
        </form>
    </div>

    <!-- Info Box -->
    <div class="mt-8 bg-blue-50 border border-blue-200 rounded-lg p-6">
        <div class="flex gap-4">
            <div class="text-blue-600 text-2xl">
                <i class="fas fa-info-circle"></i>
            </div>
            <div>
                <h3 class="font-semibold text-blue-900 mb-2">How to Print Result Card</h3>
                <ul class="text-sm text-blue-800 space-y-1">
                    <li><i class="fas fa-check text-green-600 mr-2"></i>Only results that have been released by the administrator are available</li>
                    <li><i class="fas fa-check text-green-600 mr-2"></i>Select the academic session, term, class, and student</li>
                    <li><i class="fas fa-check text-green-600 mr-2"></i>Click "Print Result Card" to view and print the result</li>
                    <li><i class="fas fa-check text-green-600 mr-2"></i>The result card includes all subjects, grades, and attendance information</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<script>
// Session selection
document.getElementById('sessionSelect').addEventListener('change', function() {
    const sessionId = this.value;
    const termSelect = document.getElementById('termSelect');
    
    termSelect.innerHTML = '<option value="">-- Loading terms --</option>';
    termSelect.disabled = true;
    document.getElementById('classSelect').innerHTML = '<option value="">-- Select Class --</option>';
    document.getElementById('classSelect').disabled = true;
    document.getElementById('studentSelect').innerHTML = '<option value="">-- Select Student --</option>';
    document.getElementById('studentSelect').disabled = true;
    document.getElementById('printBtn').disabled = true;

    if (!sessionId) {
        termSelect.innerHTML = '<option value="">-- Select Term --</option>';
        return;
    }

    // Fetch terms for this session
    fetch(`/results/ajax/terms/${sessionId}`)
        .then(response => response.json())
        .then(terms => {
            if (terms.length === 0) {
                termSelect.innerHTML = '<option value="">No terms with released results</option>';
                termSelect.disabled = true;
            } else {
                let html = '<option value="">-- Select Term --</option>';
                terms.forEach(term => {
                    html += `<option value="${term.id}">${term.term}</option>`;
                });
                termSelect.innerHTML = html;
                termSelect.disabled = false;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            termSelect.innerHTML = '<option value="">Error loading terms</option>';
        });
});

// Term selection
document.getElementById('termSelect').addEventListener('change', function() {
    const termId = this.value;
    const classSelect = document.getElementById('classSelect');
    
    classSelect.innerHTML = '<option value="">-- Loading classes --</option>';
    classSelect.disabled = true;
    document.getElementById('studentSelect').innerHTML = '<option value="">-- Select Student --</option>';
    document.getElementById('studentSelect').disabled = true;
    document.getElementById('printBtn').disabled = true;

    if (!termId) {
        classSelect.innerHTML = '<option value="">-- Select Class --</option>';
        return;
    }

    // Fetch classes for this term
    fetch(`/results/ajax/classes/${termId}`)
        .then(response => response.json())
        .then(classes => {
            if (classes.length === 0) {
                classSelect.innerHTML = '<option value="">No classes with released results</option>';
                classSelect.disabled = true;
            } else {
                let html = '<option value="">-- Select Class --</option>';
                classes.forEach(cls => {
                    html += `<option value="${cls.id}">${cls.name}</option>`;
                });
                classSelect.innerHTML = html;
                classSelect.disabled = false;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            classSelect.innerHTML = '<option value="">Error loading classes</option>';
        });
});

// Class selection
document.getElementById('classSelect').addEventListener('change', function() {
    const studentSelect = document.getElementById('studentSelect');
    
    studentSelect.innerHTML = '<option value="">-- Loading students --</option>';
    studentSelect.disabled = true;
    document.getElementById('printBtn').disabled = true;

    const sessionId = document.getElementById('sessionSelect').value;
    const termId = document.getElementById('termSelect').value;
    const classId = this.value;

    if (!classId) {
        studentSelect.innerHTML = '<option value="">-- Select Student --</option>';
        return;
    }

    // Fetch students for this class
    fetch(`/results/ajax/students?session_id=${sessionId}&term_id=${termId}&class_id=${classId}`)
        .then(response => response.json())
        .then(students => {
            if (students.length === 0) {
                studentSelect.innerHTML = '<option value="">No students with available results</option>';
                studentSelect.disabled = true;
            } else {
                let html = '<option value="">-- Select Student --</option>';
                students.forEach(student => {
                    html += `<option value="${student.id}">${student.name} (${student.admission_number})</option>`;
                });
                studentSelect.innerHTML = html;
                studentSelect.disabled = false;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            studentSelect.innerHTML = '<option value="">Error loading students</option>';
        });
});

// Student selection
document.getElementById('studentSelect').addEventListener('change', function() {
    const printBtn = document.getElementById('printBtn');
    printBtn.disabled = !this.value;
});

// Form submission
document.getElementById('selectionForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const sessionId = document.getElementById('sessionSelect').value;
    const termId = document.getElementById('termSelect').value;
    const studentId = document.getElementById('studentSelect').value;

    if (sessionId && termId && studentId) {
        // Need to get the class ID from the selected class
        const classId = document.getElementById('classSelect').value;
        window.location.href = `/results/session/${sessionId}/term/${termId}/student/${studentId}/print`;
    }
});
</script>
@endsection
