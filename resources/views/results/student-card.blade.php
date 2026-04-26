@extends('layouts.spa')

@section('content')
<div class="min-h-screen bg-gray-50 px-6 py-8">
    <!-- Breadcrumb & Header -->
    <div class="mb-8 flex items-start justify-between">
        <div>
            <div class="flex items-center gap-2 text-sm text-gray-600 mb-4">
                <a href="{{ route('results.index') }}" class="text-primary-600 hover:text-primary-700">Results</a>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                <span>Student Report Card</span>
            </div>
            <h1 class="text-2xl font-bold text-gray-900">Report Card</h1>
            <p class="text-gray-600 mt-2">{{ $student->first_name }} {{ $student->last_name }}</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('results.student.print', [$session, $term, $student]) }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 flex items-center gap-2 font-medium">
                <i class="fas fa-print"></i>Print Card
            </a>
        </div>
    </div>

    <!-- Header Card -->
    <div class="bg-white rounded-lg shadow mb-6 p-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <div>
                <p class="text-gray-600 text-sm">Student Name</p>
                <p class="font-semibold text-gray-900 mt-1">{{ $student->first_name }} {{ $student->last_name }}</p>
            </div>
            <div>
                <p class="text-gray-600 text-sm">Admission No.</p>
                <p class="font-semibold text-gray-900 mt-1">{{ $student->admission_number }}</p>
            </div>
            <div>
                <p class="text-gray-600 text-sm">Class</p>
                <p class="font-semibold text-gray-900 mt-1">{{ $student->schoolClass?->name ?? 'N/A' }}</p>
            </div>
            <div>
                <p class="text-gray-600 text-sm">Session/Term</p>
                <p class="font-semibold text-gray-900 mt-1">{{ $session->session }} | {{ $term->term }}</p>
            </div>
        </div>
    </div>

    <!-- Summary Statistics -->
    <div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-6">
        <div class="bg-blue-50 rounded-lg p-4 border border-blue-200">
            <p class="text-blue-600 text-sm font-medium">Total Subjects</p>
            <p class="text-2xl font-bold text-blue-900">{{ $summary['total_subjects'] }}</p>
        </div>
        <div class="bg-green-50 rounded-lg p-4 border border-green-200">
            <p class="text-green-600 text-sm font-medium">Average Score</p>
            <p class="text-2xl font-bold text-green-900">{{ number_format($summary['average_score'], 2) }}</p>
        </div>
        <div class="bg-purple-50 rounded-lg p-4 border border-purple-200">
            <p class="text-purple-600 text-sm font-medium">Highest Score</p>
            <p class="text-2xl font-bold text-purple-900">{{ number_format($summary['highest_score'], 2) }}</p>
        </div>
        <div class="bg-yellow-50 rounded-lg p-4 border border-yellow-200">
            <p class="text-yellow-600 text-sm font-medium">Lowest Score</p>
            <p class="text-2xl font-bold text-yellow-900">{{ number_format($summary['lowest_score'], 2) }}</p>
        </div>
        <div class="bg-indigo-50 rounded-lg p-4 border border-indigo-200">
            <p class="text-indigo-600 text-sm font-medium">Pass Rate</p>
            <p class="text-2xl font-bold text-indigo-900">
                @if ($summary['total_subjects'] > 0)
                    {{ round(($summary['pass_count'] / $summary['total_subjects']) * 100) }}%
                @else
                    0%
                @endif
            </p>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="bg-white rounded-lg shadow mb-6 p-4 border-l-4 border-primary-600">
        <div class="flex items-center justify-between flex-wrap gap-4">
            <div class="flex items-center gap-4 flex-wrap">
                <div>
                    <label class="text-sm font-medium text-gray-700 block mb-1">Search Subject</label>
                    <input type="text" id="searchSubject" placeholder="Search by subject name..." 
                        class="px-3 py-2 border border-gray-300 rounded-lg text-sm w-64 focus:ring-primary-500 focus:border-primary-500">
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-700 block mb-1">Filter by Grade</label>
                    <select id="gradeFilter" class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-primary-500 focus:border-primary-500">
                        <option value="">All Grades</option>
                        <option value="A">A (Excellent)</option>
                        <option value="B">B (Good)</option>
                        <option value="C">C (Satisfactory)</option>
                        <option value="D">D (Pass)</option>
                        <option value="F">F (Fail)</option>
                    </select>
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-700 block mb-1">Sort By</label>
                    <select id="sortSubject" class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-primary-500 focus:border-primary-500">
                        <option value="score-high">Score (High to Low)</option>
                        <option value="score-low">Score (Low to High)</option>
                        <option value="name">Name (A-Z)</option>
                    </select>
                </div>
            </div>
            <button onclick="resetSubjectFilters()" class="px-4 py-2 bg-gray-300 text-gray-900 rounded-lg hover:bg-gray-400 transition-colors text-sm font-medium">
                <i class="fas fa-redo mr-2"></i>Reset
            </button>
        </div>
    </div>
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="w-full">
            <thead class="bg-gray-100 border-b border-gray-200">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-900 uppercase tracking-wider">Subject</th>
                    @if(count($resultsWithGrades) > 0)
                        @php
                            $maxPreviousTerms = $resultsWithGrades->map(fn($item) => count($item['previous_terms'] ?? []))->max();
                        @endphp
                        @for($i = 0; $i < $maxPreviousTerms; $i++)
                            <th class="px-6 py-3 text-center text-xs font-semibold text-gray-900 uppercase tracking-wider">
                                @php
                                    $termName = $resultsWithGrades->first()['previous_terms'][$i]['term_name'] ?? '';
                                @endphp
                                {{ $termName }}
                            </th>
                        @endfor
                    @endif
                    <th class="px-6 py-3 text-center text-xs font-semibold text-gray-900 uppercase tracking-wider">CA</th>
                    <th class="px-6 py-3 text-center text-xs font-semibold text-gray-900 uppercase tracking-wider">Exam</th>
                    <th class="px-6 py-3 text-center text-xs font-semibold text-gray-900 uppercase tracking-wider">Total</th>
                    <th class="px-6 py-3 text-center text-xs font-semibold text-gray-900 uppercase tracking-wider">Grade</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-900 uppercase tracking-wider">Remark</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @foreach ($resultsWithGrades as $item)
                    @php
                        $isPassing = $item['grade'] != 'F';
                    @endphp
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">
                            <p class="font-medium text-gray-900">{{ $item['result']->subject->name }}</p>
                        </td>
                        @foreach($item['previous_terms'] ?? [] as $prevTerm)
                            <td class="px-6 py-4 text-center">
                                <span class="font-semibold text-gray-900">{{ number_format($prevTerm['total'], 1) }}</span>
                            </td>
                        @endforeach
                        <td class="px-6 py-4 text-center">
                            <span class="font-semibold text-gray-900">{{ $item['result']->ca_score ?? 0 }}</span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="font-semibold text-gray-900">{{ $item['result']->exam_score ?? 0 }}</span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="font-bold text-lg {{ $isPassing ? 'text-green-600' : 'text-red-600' }}">
                                {{ number_format($item['cumulative_score'], 1) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold {{ 
                                match($item['grade']) {
                                    'A', 'A+' => 'bg-green-100 text-green-800',
                                    'B', 'B+' => 'bg-blue-100 text-blue-800',
                                    'C', 'C+' => 'bg-yellow-100 text-yellow-800',
                                    'D', 'D+' => 'bg-orange-100 text-orange-800',
                                    'E', 'F' => 'bg-red-100 text-red-800',
                                    default => 'bg-gray-100 text-gray-800'
                                }
                            }}">
                                {{ $item['grade'] }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <p class="text-sm text-gray-600">{{ $item['remark'] }}</p>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Performance Summary -->
    <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="bg-green-50 rounded-lg border border-green-200 p-6">
            <p class="font-semibold text-gray-900 mb-2">Subjects Passed</p>
            <p class="text-3xl font-bold text-green-600">{{ $summary['pass_count'] }} / {{ $summary['total_subjects'] }}</p>
        </div>
        <div class="bg-red-50 rounded-lg border border-red-200 p-6">
            <p class="font-semibold text-gray-900 mb-2">Subjects Failed</p>
            <p class="text-3xl font-bold text-red-600">{{ $summary['fail_count'] }} / {{ $summary['total_subjects'] }}</p>
        </div>
    </div>

    <!-- Comments Section -->
    <div class="mt-6 bg-white rounded-lg shadow p-6">
        <h2 class="text-lg font-bold text-gray-900 mb-4 pb-3 border-b border-gray-200">Comments & Remarks</h2>
        
        <form id="commentsForm" class="space-y-6">
            @csrf
            <input type="hidden" name="session_id" value="{{ $session->id }}">
            <input type="hidden" name="term_id" value="{{ $term->id }}">
            <input type="hidden" name="student_id" value="{{ $student->id }}">
            
            <!-- Class Teacher's Comment - for Teachers only (not Admins) -->
            @hasPermission('write-teacher-comment')
                <div>
                    <label class="block text-sm font-semibold text-gray-900 mb-2">
                        <i class="fas fa-chalkboard-user text-blue-600 mr-2"></i>Class Teacher's Comment
                    </label>
                    <p class="text-xs text-gray-600 mb-2">Enter your feedback and remarks about the student's overall performance, behavior, and progress</p>
                    <textarea id="classTeacherComment" name="class_teacher_comment" rows="4" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent resize-none"
                        placeholder="Your comment about the student's performance, behavior, and progress...">{{ $resultsWithGrades->first()['result']?->class_teacher_comment ?? '' }}</textarea>
                </div>
            @endhasPermission
            
            <!-- Head Teacher's Comment - for Admins only -->
            @hasPermission('write-headteacher-comment')
                <div>
                    <label class="block text-sm font-semibold text-gray-900 mb-2">
                        <i class="fas fa-user-tie text-purple-600 mr-2"></i>Head Teacher's Comment
                    </label>
                    <p class="text-xs text-gray-600 mb-2">Enter feedback and remarks from the head teacher about the student's overall performance</p>
                    <textarea id="headTeacherComment" name="head_teacher_comment" rows="4" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent resize-none"
                        placeholder="Head teacher's comment about the student's performance, behavior, and progress...">{{ $resultsWithGrades->first()['result']?->head_teacher_comment ?? '' }}</textarea>
                </div>
            @endhasPermission

            <!-- Assessment Ratings Section -->
            <div class="bg-gradient-to-r from-indigo-50 to-blue-50 border border-indigo-200 rounded-lg p-6">
                <h3 class="text-sm font-bold text-gray-900 mb-4 flex items-center gap-2">
                    <i class="fas fa-star text-yellow-500"></i>Assessment Ratings
                </h3>
                
                <!-- Behaviour & Conduct (3 items) -->
                <div class="mb-6">
                    <h4 class="text-xs font-semibold text-indigo-700 mb-3 pb-2 border-b border-indigo-200 flex items-center gap-2">
                        <span class="inline-block w-3 h-3 rounded-full bg-indigo-600"></span>Behaviour & Conduct
                    </h4>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1">Punctuality</label>
                            <select name="behaviour_punctuality" class="w-full px-2 py-1.5 text-xs border border-gray-300 rounded focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                                <option value="Excellent" {{ $behaviourAssessments['punctuality'] === 'Excellent' ? 'selected' : '' }}>Excellent</option>
                                <option value="Very Good" {{ ($behaviourAssessments['punctuality'] === 'Very Good' || !$behaviourAssessments['punctuality']) ? 'selected' : '' }}>Very Good</option>
                                <option value="Good" {{ $behaviourAssessments['punctuality'] === 'Good' ? 'selected' : '' }}>Good</option>
                                <option value="Fair" {{ $behaviourAssessments['punctuality'] === 'Fair' ? 'selected' : '' }}>Fair</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1">Class Participation</label>
                            <select name="behaviour_participation" class="w-full px-2 py-1.5 text-xs border border-gray-300 rounded focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                                <option value="Excellent" {{ $behaviourAssessments['participation'] === 'Excellent' ? 'selected' : '' }}>Excellent</option>
                                <option value="Very Good" {{ ($behaviourAssessments['participation'] === 'Very Good' || !$behaviourAssessments['participation']) ? 'selected' : '' }}>Very Good</option>
                                <option value="Good" {{ $behaviourAssessments['participation'] === 'Good' ? 'selected' : '' }}>Good</option>
                                <option value="Fair" {{ $behaviourAssessments['participation'] === 'Fair' ? 'selected' : '' }}>Fair</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1">Respect</label>
                            <select name="behaviour_respect" class="w-full px-2 py-1.5 text-xs border border-gray-300 rounded focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                                <option value="Excellent" {{ $behaviourAssessments['respect'] === 'Excellent' ? 'selected' : '' }}>Excellent</option>
                                <option value="Very Good" {{ ($behaviourAssessments['respect'] === 'Very Good' || !$behaviourAssessments['respect']) ? 'selected' : '' }}>Very Good</option>
                                <option value="Good" {{ $behaviourAssessments['respect'] === 'Good' ? 'selected' : '' }}>Good</option>
                                <option value="Fair" {{ $behaviourAssessments['respect'] === 'Fair' ? 'selected' : '' }}>Fair</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Psychomotor Skills (3 items) -->
                <div class="mb-6">
                    <h4 class="text-xs font-semibold text-teal-700 mb-3 pb-2 border-b border-teal-200 flex items-center gap-2">
                        <span class="inline-block w-3 h-3 rounded-full bg-teal-600"></span>Psychomotor Skills
                    </h4>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1">Handwriting</label>
                            <select name="psychomotor_handwriting" class="w-full px-2 py-1.5 text-xs border border-gray-300 rounded focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                                <option value="Excellent" {{ $psychomotorAssessments['handwriting'] === 'Excellent' ? 'selected' : '' }}>Excellent</option>
                                <option value="Very Good" {{ ($psychomotorAssessments['handwriting'] === 'Very Good' || !$psychomotorAssessments['handwriting']) ? 'selected' : '' }}>Very Good</option>
                                <option value="Good" {{ $psychomotorAssessments['handwriting'] === 'Good' ? 'selected' : '' }}>Good</option>
                                <option value="Fair" {{ $psychomotorAssessments['handwriting'] === 'Fair' ? 'selected' : '' }}>Fair</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1">Drawing & Creativity</label>
                            <select name="psychomotor_creativity" class="w-full px-2 py-1.5 text-xs border border-gray-300 rounded focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                                <option value="Excellent" {{ $psychomotorAssessments['creativity'] === 'Excellent' ? 'selected' : '' }}>Excellent</option>
                                <option value="Very Good" {{ ($psychomotorAssessments['creativity'] === 'Very Good' || !$psychomotorAssessments['creativity']) ? 'selected' : '' }}>Very Good</option>
                                <option value="Good" {{ $psychomotorAssessments['creativity'] === 'Good' ? 'selected' : '' }}>Good</option>
                                <option value="Fair" {{ $psychomotorAssessments['creativity'] === 'Fair' ? 'selected' : '' }}>Fair</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1">Games/Sports</label>
                            <select name="psychomotor_sports" class="w-full px-2 py-1.5 text-xs border border-gray-300 rounded focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                                <option value="Excellent" {{ $psychomotorAssessments['sports'] === 'Excellent' ? 'selected' : '' }}>Excellent</option>
                                <option value="Very Good" {{ ($psychomotorAssessments['sports'] === 'Very Good' || !$psychomotorAssessments['sports']) ? 'selected' : '' }}>Very Good</option>
                                <option value="Good" {{ $psychomotorAssessments['sports'] === 'Good' ? 'selected' : '' }}>Good</option>
                                <option value="Fair" {{ $psychomotorAssessments['sports'] === 'Fair' ? 'selected' : '' }}>Fair</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Affective Domain (3 items) -->
                <div>
                    <h4 class="text-xs font-semibold text-pink-700 mb-3 pb-2 border-b border-pink-200 flex items-center gap-2">
                        <span class="inline-block w-3 h-3 rounded-full bg-pink-600"></span>Affective Domain
                    </h4>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1">Perseverance</label>
                            <select name="affective_perseverance" class="w-full px-2 py-1.5 text-xs border border-gray-300 rounded focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                                <option value="Excellent" {{ $affectiveAssessments['perseverance'] === 'Excellent' ? 'selected' : '' }}>Excellent</option>
                                <option value="Very Good" {{ ($affectiveAssessments['perseverance'] === 'Very Good' || !$affectiveAssessments['perseverance']) ? 'selected' : '' }}>Very Good</option>
                                <option value="Good" {{ $affectiveAssessments['perseverance'] === 'Good' ? 'selected' : '' }}>Good</option>
                                <option value="Fair" {{ $affectiveAssessments['perseverance'] === 'Fair' ? 'selected' : '' }}>Fair</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1">Self-Control</label>
                            <select name="affective_control" class="w-full px-2 py-1.5 text-xs border border-gray-300 rounded focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                                <option value="Excellent" {{ $affectiveAssessments['control'] === 'Excellent' ? 'selected' : '' }}>Excellent</option>
                                <option value="Very Good" {{ ($affectiveAssessments['control'] === 'Very Good' || !$affectiveAssessments['control']) ? 'selected' : '' }}>Very Good</option>
                                <option value="Good" {{ $affectiveAssessments['control'] === 'Good' ? 'selected' : '' }}>Good</option>
                                <option value="Fair" {{ $affectiveAssessments['control'] === 'Fair' ? 'selected' : '' }}>Fair</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1">Initiative</label>
                            <select name="affective_initiative" class="w-full px-2 py-1.5 text-xs border border-gray-300 rounded focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                                <option value="Excellent" {{ $affectiveAssessments['initiative'] === 'Excellent' ? 'selected' : '' }}>Excellent</option>
                                <option value="Very Good" {{ ($affectiveAssessments['initiative'] === 'Very Good' || !$affectiveAssessments['initiative']) ? 'selected' : '' }}>Very Good</option>
                                <option value="Good" {{ $affectiveAssessments['initiative'] === 'Good' ? 'selected' : '' }}>Good</option>
                                <option value="Fair" {{ $affectiveAssessments['initiative'] === 'Fair' ? 'selected' : '' }}>Fair</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                <p class="text-sm text-blue-900">
                    <i class="fas fa-info-circle mr-2"></i>

                        <span class="font-medium">Note:</span> Your  comment and assessment ratings will be saved and displayed on the student's report card

                </p>
            </div>

            <div class="flex gap-3">
                <button type="submit" class="px-6 py-2 bg-primary-600 text-white font-medium rounded-lg hover:bg-primary-700 transition-colors flex items-center gap-2">
                    <i class="fas fa-save"></i>Save Comments & Ratings
                </button>
                <button type="button" onclick="resetCommentsForm()" class="px-6 py-2 bg-gray-200 text-gray-900 font-medium rounded-lg hover:bg-gray-300 transition-colors">
                    Cancel
                </button>
            </div>
        </form>
    </div>

    <!-- Back Button -->
    <div class="mt-6">
        <a href="javascript:history.back()" class="inline-flex items-center px-4 py-2 bg-gray-200 text-gray-900 font-medium rounded-lg hover:bg-gray-300 transition-colors gap-2">
            <i class="fas fa-arrow-left"></i>Back
        </a>
    </div>
</div>

<script>
function applySubjectFilters() {
    const searchValue = document.getElementById('searchSubject').value.toLowerCase();
    const gradeValue = document.getElementById('gradeFilter').value;
    const sortValue = document.getElementById('sortSubject').value;
    
    let rows = Array.from(document.querySelectorAll('tbody tr'));
    
    // Filter by search
    if (searchValue) {
        rows = rows.filter(row => {
            const subjectName = row.cells[0].textContent.toLowerCase();
            return subjectName.includes(searchValue);
        });
    }
    
    // Filter by grade
    if (gradeValue) {
        rows = rows.filter(row => {
            const grade = row.cells[4].textContent.trim();
            return grade === gradeValue;
        });
    }
    
    // Sort
    rows.sort((a, b) => {
        if (sortValue === 'score-high') {
            const aScore = parseFloat(a.cells[3].textContent);
            const bScore = parseFloat(b.cells[3].textContent);
            return bScore - aScore;
        } else if (sortValue === 'score-low') {
            const aScore = parseFloat(a.cells[3].textContent);
            const bScore = parseFloat(b.cells[3].textContent);
            return aScore - bScore;
        } else if (sortValue === 'name') {
            const aName = a.cells[0].textContent.toLowerCase();
            const bName = b.cells[0].textContent.toLowerCase();
            return aName.localeCompare(bName);
        }
        return 0;
    });
    
    // Clear and re-add filtered rows
    const tbody = document.querySelector('tbody');
    tbody.innerHTML = '';
    rows.forEach(row => tbody.appendChild(row));
    
    // Update counter
    updateSubjectRowCount(rows.length);
}

function updateSubjectRowCount(count) {
    let counter = document.getElementById('subjectRowCounter');
    if (!counter) {
        const table = document.querySelector('table');
        counter = document.createElement('p');
        counter.id = 'subjectRowCounter';
        counter.className = 'text-sm text-gray-600 mt-2';
        table.parentElement.appendChild(counter);
    }
    counter.textContent = `Showing ${count} subject${count !== 1 ? 's' : ''}`;
}

function resetSubjectFilters() {
    document.getElementById('searchSubject').value = '';
    document.getElementById('gradeFilter').value = '';
    document.getElementById('sortSubject').value = 'score-high';
    
    // Show all rows
    document.querySelectorAll('tbody tr').forEach(row => {
        row.style.display = '';
    });
    
    applySubjectFilters();
}

// Add event listeners
document.getElementById('searchSubject').addEventListener('keyup', applySubjectFilters);
document.getElementById('gradeFilter').addEventListener('change', applySubjectFilters);
document.getElementById('sortSubject').addEventListener('change', applySubjectFilters);

// Initial row count
updateSubjectRowCount(document.querySelectorAll('tbody tr').length);

// Comments Form Handler
const commentsForm = document.getElementById('commentsForm');
if (commentsForm) {
    commentsForm.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        // Get comments from both fields if they exist
        const classTeacherCommentField = document.getElementById('classTeacherComment');
        const headTeacherCommentField = document.getElementById('headTeacherComment');
        
        const classTeacherComment = classTeacherCommentField ? classTeacherCommentField.value : '';
        const headTeacherComment = headTeacherCommentField ? headTeacherCommentField.value : '';
        
        // Collect individual assessment values
        const assessmentData = {
            behaviour_punctuality: document.querySelector('select[name="behaviour_punctuality"]')?.value,
            behaviour_participation: document.querySelector('select[name="behaviour_participation"]')?.value,
            behaviour_respect: document.querySelector('select[name="behaviour_respect"]')?.value,
            psychomotor_handwriting: document.querySelector('select[name="psychomotor_handwriting"]')?.value,
            psychomotor_creativity: document.querySelector('select[name="psychomotor_creativity"]')?.value,
            psychomotor_sports: document.querySelector('select[name="psychomotor_sports"]')?.value,
            affective_perseverance: document.querySelector('select[name="affective_perseverance"]')?.value,
            affective_control: document.querySelector('select[name="affective_control"]')?.value,
            affective_initiative: document.querySelector('select[name="affective_initiative"]')?.value,
        };
        
        try {
            const response = await fetch('{{ route("results.save-comments") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                },
                body: JSON.stringify({
                    session_id: document.querySelector('input[name="session_id"]').value,
                    term_id: document.querySelector('input[name="term_id"]').value,
                    student_id: document.querySelector('input[name="student_id"]').value,
                    class_teacher_comment: classTeacherComment,
                    head_teacher_comment: headTeacherComment,
                    ...assessmentData,
                })
            });
            
            const result = await response.json();
            
            if (result.success) {
                // Show success message
                showNotification('Comments and ratings saved successfully!', 'success');
            } else {
                showNotification('Error: ' + (result.message || 'Failed to save comments'), 'error');
            }
        } catch (error) {
            console.error('Error:', error);
            showNotification('Error saving comments', 'error');
        }
    });
}

function resetCommentsForm() {
    const classTeacherCommentField = document.getElementById('classTeacherComment');
    const headTeacherCommentField = document.getElementById('headTeacherComment');
    
    // Reset comments from their respective fields
    if (classTeacherCommentField) {
        classTeacherCommentField.value = '{{ $resultsWithGrades->first()['result']?->class_teacher_comment ?? '' }}';
    }
    if (headTeacherCommentField) {
        headTeacherCommentField.value = '{{ $resultsWithGrades->first()['result']?->head_teacher_comment ?? '' }}';
    }
    
    // Reset assessment fields to defaults
    document.querySelectorAll('select[name^="behaviour_"], select[name^="psychomotor_"], select[name^="affective_"]').forEach(select => {
        select.value = 'Very Good';
    });
}

function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 px-6 py-3 rounded-lg text-white font-medium z-50 ${
        type === 'success' ? 'bg-green-500' : type === 'error' ? 'bg-red-500' : 'bg-blue-500'
    }`;
    notification.textContent = message;
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.remove();
    }, 3000);
}
</script>
@endsection
