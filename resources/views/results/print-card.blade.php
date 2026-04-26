<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Result Card - {{ $student->first_name }} {{ $student->last_name }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            body {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
                margin: 0;
                padding: 0;
            }
            .no-print {
                display: none !important;
            }
            @page {
                size: A4 portrait;
                margin: 0.5cm;
            }
            .print-container {
                width: 210mm;
                height: 297mm;
                overflow: hidden;
            }
        }
        
        /* Non-print styles */
        body {
            margin: 0;
            padding: 0;
        }
    </style>
</head>
<body class="bg-white">
    <div class="print-container max-w-4xl mx-auto px-4 py-4">
        <!-- Print Button -->
        <div class="no-print mb-4 flex justify-end space-x-3">
            <button onclick="window.print()" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                <i class="fas fa-print mr-2"></i>Print Result Card
            </button>
            @if(auth()->user()->hasRole('parent'))
                <a href="{{ route('parent-portal.results', ['student' => $student->id, 'session' => $session->id, 'term' => $term->id]) }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">
                    <i class="fas fa-arrow-left mr-2"></i>Back
                </a>
            @else
                <a href="{{ route('results.student.card', [$session, $term, $student]) }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">
                    <i class="fas fa-arrow-left mr-2"></i>Back
                </a>
            @endif
        </div>

        <!-- No Results Message -->
        @if($resultsWithGrades->isEmpty())
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-6 text-center my-8">
                <i class="fas fa-info-circle text-yellow-600 text-4xl mb-4 block"></i>
                <h2 class="text-xl font-bold text-yellow-800 mb-2">No Results Available</h2>
                <p class="text-yellow-700 mb-4">No result records found for {{ $student->first_name }} {{ $student->last_name }} in {{ $term->term }} ({{ $session->session }}).</p>
                <a href="{{ route('results.student.card', [$session, $term, $student]) }}" class="inline-flex items-center px-4 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700">
                    <i class="fas fa-arrow-left mr-2"></i>Back to Student Results
                </a>
            </div>
        @else

        <!-- Header with Logo -->
        <div class="border-b-4 border-blue-600 pb-4 mb-4">
            <div class="flex items-start justify-between gap-4">
                <div class="flex items-center space-x-2">
                    <div class="w-12 h-12 rounded-lg flex items-center justify-center flex-shrink-0">
                        @if($schoolSettings->school_logo)
                            <img src="{{ asset('storage/' . $schoolSettings->school_logo) }}" alt="Logo" class="w-12 h-12 rounded-lg object-cover">
                        @else
                            <svg class="w-8 h-8 text-gray-700" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M10.394 2.08a1 1 0 00-.788 0l-7 3a1 1 0 000 1.84L5.25 8.051a.999.999 0 01.356-.257l4-1.714a1 1 0 11.788 1.838L7.667 9.088l1.94.831a1 1 0 00.787 0l7-3a1 1 0 000-1.838l-7-3zM3.31 9.397L5 10.12v4.102a8.969 8.969 0 00-1.05-.174 1 1 0 01-.89-.89 11.115 11.115 0 01.25-3.762zM9.3 16.573A9.026 9.026 0 007 14.935v-3.957l1.818.78a3 3 0 002.364 0l5.508-2.361a11.026 11.026 0 01.25 3.762 1 1 0 01-.89.89 8.968 8.968 0 00-5.35 2.524 1 1 0 01-1.4 0zM6 18a1 1 0 001-1v-2.065a8.935 8.935 0 00-2-.712V17a1 1 0 001 1z"/>
                            </svg>
                        @endif
                    </div>
                    <div>
                        <h1 class="text-xl font-bold text-gray-900">{{ $schoolSettings->school_name ?? 'School Name' }}</h1>
                        <p class="text-xs text-gray-600">{{ $schoolSettings->school_motto ?? 'School Management System' }}</p>
                    </div>
                </div>
                <div class="flex gap-4 items-start">
                    <div class="text-right text-sm">
                        <p class="text-xs text-gray-600">Printed On:</p>
                        <p class="text-xs font-medium text-gray-900">{{ date('d-m-Y') }}</p>
                    </div>
                    @if($qrCodeDataUri)
                        <div class="flex flex-col items-center">
                            <p class="text-xs text-gray-600 mb-1">Verify Result</p>
                            <img src="{{ $qrCodeDataUri }}" alt="QR Code" class="w-20 h-20 border-2 border-gray-300 rounded">
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Result Card Title -->
        <div class="bg-blue-50 border-l-4 border-blue-600 p-3 mb-4">
            <h2 class="text-lg font-bold text-gray-900">Result Card</h2>
            <p class="text-xs text-gray-600 mt-0.5">{{ $term->term }} - {{ $session->session }} Academic Session</p>
        </div>

        <!-- Student Header Info -->
        <div class="grid grid-cols-4 gap-2 mb-4 text-sm">
            <div class="bg-blue-50 p-2.5 rounded-lg border border-blue-200">
                <p class="text-xs text-gray-600 uppercase font-semibold">Student Name</p>
                <p class="text-sm font-bold text-blue-600 mt-0.5">{{ $student->first_name }} {{ $student->last_name }}</p>
            </div>
            <div class="bg-green-50 p-2.5 rounded-lg border border-green-200">
                <p class="text-xs text-gray-600 uppercase font-semibold">Admission No.</p>
                <p class="text-sm font-bold text-green-600 mt-0.5">{{ $student->admission_number }}</p>
            </div>
            <div class="bg-purple-50 p-2.5 rounded-lg border border-purple-200">
                <p class="text-xs text-gray-600 uppercase font-semibold">Class</p>
                <p class="text-sm font-bold text-purple-600 mt-0.5">{{ $resultsWithGrades->first()['result']->schoolClass?->name ?? 'N/A' }}</p>
            </div>
            <div class="bg-orange-50 p-2.5 rounded-lg border border-orange-200">
                <p class="text-xs text-gray-600 uppercase font-semibold">Session</p>
                <p class="text-sm font-bold text-orange-600 mt-0.5">{{ $session->session }}</p>
            </div>
        </div>

        <!-- Results Table -->
        <div class="mb-4">
            <div class="flex justify-between items-center mb-2">
                <h3 class="text-sm font-bold text-gray-900 pb-1 border-b-2 border-gray-300">Subject Results</h3>
                @if($classStudentCount > 0)
                    <p class="text-xs text-gray-600"><span class="font-semibold">Students in Class:</span> {{ $classStudentCount }}</p>
                @endif
            </div>
            <div class="overflow-x-auto">
                <table class="w-full border-collapse border border-gray-300 text-xs">
                    <thead class="bg-gray-100 border-b-2 border-gray-300">
                        <tr>
                            <th class="px-2 py-1.5 text-left font-semibold text-gray-900">Subject</th>
                            @if(count($resultsWithGrades) > 0)
                                @php
                                    $maxPreviousTerms = collect($resultsWithGrades)->map(fn($item) => count($item['previous_terms'] ?? []))->max();
                                @endphp
                                @for($i = 0; $i < $maxPreviousTerms; $i++)
                                    <th class="px-2 py-1.5 text-center font-semibold text-gray-900">
                                        @php
                                            $termName = $resultsWithGrades[0]['previous_terms'][$i]['term_name'] ?? '';
                                        @endphp
                                        {{ $termName }}
                                    </th>
                                @endfor
                            @endif
                            <th class="px-2 py-1.5 text-center font-semibold text-gray-900">CA</th>
                            <th class="px-2 py-1.5 text-center font-semibold text-gray-900">Exam</th>
                            <th class="px-2 py-1.5 text-center font-semibold text-gray-900">Total</th>
                            <th class="px-2 py-1.5 text-center font-semibold text-gray-900">Class Avg</th>
                            <th class="px-2 py-1.5 text-center font-semibold text-gray-900">Grade</th>
                            <th class="px-2 py-1.5 text-center font-semibold text-gray-900">Remark</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($resultsWithGrades as $item)
                            @php
                                $grade = $item['grade'];
                                // Determine color based on grade
                                $gradeColor = match($grade) {
                                    'A', 'A+' => 'bg-green-100 text-green-800',
                                    'B', 'B+' => 'bg-blue-100 text-blue-800',
                                    'C', 'C+' => 'bg-yellow-100 text-yellow-800',
                                    'D', 'D+' => 'bg-orange-100 text-orange-800',
                                    'E', 'F' => 'bg-red-100 text-red-800',
                                    default => 'bg-gray-100 text-gray-800'
                                };
                                
                                // Get class average for this subject
                                $classAvg = $classStatistics[$item['result']->subject_id] ?? 'N/A';
                            @endphp
                            <tr class="border-b border-gray-300">
                                <td class="px-2 py-1 font-medium text-gray-900">{{ $item['result']->subject->name }}</td>
                                @foreach($item['previous_terms'] ?? [] as $prevTerm)
                                    <td class="px-2 py-1 text-center font-semibold text-gray-900">{{ number_format($prevTerm['total'], 1) }}</td>
                                @endforeach
                                <td class="px-2 py-1 text-center font-semibold text-gray-900">{{ $item['result']->ca_score ?? 0 }}</td>
                                <td class="px-2 py-1 text-center font-semibold text-gray-900">{{ $item['result']->exam_score ?? 0 }}</td>
                                <td class="px-2 py-1 text-center font-bold text-gray-900">{{ number_format($item['cumulative_total'], 1) }}</td>
                                <td class="px-2 py-1 text-center font-semibold text-blue-600">{{ is_numeric($classAvg) ? number_format($classAvg, 1) : $classAvg }}</td>
                                <td class="px-2 py-1 text-center">
                                    <span class="inline-block px-2 py-0.5 rounded text-xs font-bold {{ $gradeColor }}">
                                        {{ $item['grade'] }}
                                    </span>
                                </td>
                                <td class="px-2 py-1 text-center font-medium text-gray-900 text-xs">{{ $item['remark'] }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-2 py-2 text-center text-gray-600">No results available</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Overall Performance -->
        <div class="grid grid-cols-4 gap-2 mb-4 text-xs">
            <div class="bg-gradient-to-br from-blue-50 to-blue-100 p-2 rounded-lg border border-blue-300">
                <p class="text-xs text-gray-600 uppercase font-semibold">Total Subjects</p>
                <p class="text-lg font-bold text-blue-600 mt-0.5">{{ $subjectCount }}</p>
            </div>
            <div class="bg-gradient-to-br from-green-50 to-green-100 p-2 rounded-lg border border-green-300">
                <p class="text-xs text-gray-600 uppercase font-semibold">Passed</p>
                <p class="text-lg font-bold text-green-600 mt-0.5">{{ $passCount }} / {{ $subjectCount }}</p>
            </div>
            <div class="bg-gradient-to-br from-purple-50 to-purple-100 p-2 rounded-lg border border-purple-300">
                <p class="text-xs text-gray-600 uppercase font-semibold">Average Score</p>
                <p class="text-lg font-bold text-purple-600 mt-0.5">{{ number_format($averageScore, 1) }}</p>
            </div>
            <div class="bg-gradient-to-br from-orange-50 to-orange-100 p-2 rounded-lg border border-orange-300">
                <p class="text-xs text-gray-600 uppercase font-semibold">Overall Grade</p>
                <p class="text-lg font-bold text-orange-600 mt-0.5">{{ $overallGrade }}</p>
            </div>
        </div>

        <!-- Attendance & Behaviour/Conduct Row -->
        <div class="grid grid-cols-2 gap-2 mb-4">
            <!-- Attendance Summary -->
            <div>
                <h3 class="text-sm font-bold text-gray-900 mb-2 pb-1 border-b-2 border-gray-300">Attendance Record</h3>
                <div class="bg-green-50 border border-green-200 rounded p-2 text-xs">
                    <div class="flex justify-between items-center border-b border-green-100 pb-1">
                        <span class="font-semibold text-gray-700">Present</span>
                        <span class="font-bold text-green-600">{{ $attendanceData['present'] ?? 0 }}</span>
                    </div>
                    <div class="flex justify-between items-center border-b border-green-100 pb-1">
                        <span class="font-semibold text-gray-700">Absent</span>
                        <span class="font-bold text-red-600">{{ $attendanceData['absent'] ?? 0 }}</span>
                    </div>
                    <div class="flex justify-between items-center border-b border-green-100 pb-1">
                        <span class="font-semibold text-gray-700">Late</span>
                        <span class="font-bold text-yellow-600">{{ $attendanceData['late'] ?? 0 }}</span>
                    </div>
                    <div class="flex justify-between items-center border-b border-green-100 pb-1">
                        <span class="font-semibold text-gray-700">Excused</span>
                        <span class="font-bold text-blue-600">{{ $attendanceData['excused'] ?? 0 }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="font-semibold text-gray-700">Att %</span>
                        <span class="font-bold text-purple-600">{{ $attendanceData['attendance_percentage'] ?? 0 }}%</span>
                    </div>
                </div>
            </div>

            <!-- Behaviour/Conduct Assessment -->
            <div>
                <h3 class="text-sm font-bold text-gray-900 mb-2 pb-1 border-b-2 border-gray-300">Behaviour & Conduct</h3>
                <div class="bg-indigo-50 border border-indigo-200 rounded p-2 text-xs">
                    @php
                        $firstResult = $resultsWithGrades->first()['result'];
                    @endphp
                    <div class="flex justify-between items-center border-b border-indigo-100 pb-1">
                        <span class="font-semibold text-gray-700">Punctuality</span>
                        <span class="font-bold text-indigo-600">{{ $firstResult->behaviour_punctuality ?? 'Very Good' }}</span>
                    </div>
                    <div class="flex justify-between items-center border-b border-indigo-100 pb-1">
                        <span class="font-semibold text-gray-700">Class Participation</span>
                        <span class="font-bold text-indigo-600">{{ $firstResult->behaviour_participation ?? 'Very Good' }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="font-semibold text-gray-700">Respect</span>
                        <span class="font-bold text-indigo-600">{{ $firstResult->behaviour_respect ?? 'Very Good' }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Psychomotor Skills & Affective Domain Row -->
        <div class="grid grid-cols-2 gap-2 mb-4">
            <!-- Psychomotor Skills -->
            <div>
                <h3 class="text-sm font-bold text-gray-900 mb-2 pb-1 border-b-2 border-gray-300">Psychomotor Skills</h3>
                <div class="bg-teal-50 border border-teal-200 rounded p-2 text-xs">
                    @php
                        $firstResult = $resultsWithGrades->first()['result'];
                    @endphp
                    <div class="flex justify-between items-center border-b border-teal-100 pb-1">
                        <span class="font-semibold text-gray-700">Handwriting</span>
                        <span class="font-bold text-teal-600">{{ $firstResult->psychomotor_handwriting ?? 'Very Good' }}</span>
                    </div>
                    <div class="flex justify-between items-center border-b border-teal-100 pb-1">
                        <span class="font-semibold text-gray-700">Drawing & Creativity</span>
                        <span class="font-bold text-teal-600">{{ $firstResult->psychomotor_creativity ?? 'Very Good' }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="font-semibold text-gray-700">Games/Sports</span>
                        <span class="font-bold text-teal-600">{{ $firstResult->psychomotor_sports ?? 'Very Good' }}</span>
                    </div>
                </div>
            </div>

            <!-- Affective Domain -->
            <div>
                <h3 class="text-sm font-bold text-gray-900 mb-2 pb-1 border-b-2 border-gray-300">Affective Domain</h3>
                <div class="bg-pink-50 border border-pink-200 rounded p-2 text-xs">
                    @php
                        $firstResult = $resultsWithGrades->first()['result'];
                    @endphp
                    <div class="flex justify-between items-center border-b border-pink-100 pb-1">
                        <span class="font-semibold text-gray-700">Perseverance</span>
                        <span class="font-bold text-pink-600">{{ $firstResult->affective_perseverance ?? 'Very Good' }}</span>
                    </div>
                    <div class="flex justify-between items-center border-b border-pink-100 pb-1">
                        <span class="font-semibold text-gray-700">Self-Control</span>
                        <span class="font-bold text-pink-600">{{ $firstResult->affective_control ?? 'Very Good' }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="font-semibold text-gray-700">Initiative</span>
                        <span class="font-bold text-pink-600">{{ $firstResult->affective_initiative ?? 'Very Good' }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Comments Section -->
        <div class="mb-3">
            <h3 class="text-sm font-bold text-gray-900 mb-2 pb-1 border-b-2 border-gray-300">Comments</h3>
            <div class="grid grid-cols-2 gap-2">
                <!-- Head Teacher's Comment -->
                <div class="border border-gray-300 rounded p-2">
                    <p class="text-xs font-semibold text-gray-900 mb-1">Head Teacher's Comment</p>
                    <div class="bg-gray-50 p-2 rounded min-h-12 text-xs text-gray-600 leading-tight">
                        @if($resultsWithGrades->first()['result']?->head_teacher_comment)
                            {{ $resultsWithGrades->first()['result']->head_teacher_comment }}
                        @else
                            <span class="text-gray-400 italic">No comment</span>
                        @endif
                    </div>
                </div>

                <!-- Class Teacher's Comment -->
                <div class="border border-gray-300 rounded p-2">
                    <p class="text-xs font-semibold text-gray-900 mb-1">Class Teacher's Comment</p>
                    <div class="bg-gray-50 p-2 rounded min-h-12 text-xs text-gray-600 leading-tight">
                        @if($resultsWithGrades->first()['result']?->class_teacher_comment)
                            {{ $resultsWithGrades->first()['result']->class_teacher_comment }}
                        @else
                            <span class="text-gray-400 italic">No comment</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Signature Section -->
        <div class="grid grid-cols-3 gap-4 mb-2 mt-8 text-xs">
            <div class="min-h-24">
                <p class="text-xs text-gray-600 uppercase font-semibold text-center mb-8">Head Teacher</p>
                <p class="border-t border-gray-900 pt-2 text-xs text-gray-600 text-center">Signature & Date</p>
            </div>
            <div class="min-h-24">
                <p class="text-xs text-gray-600 uppercase font-semibold text-center mb-8">Administrator</p>
                <p class="border-t border-gray-900 pt-2 text-xs text-gray-600 text-center">Signature & Date</p>
            </div>
            <div class="min-h-24">
                <p class="text-xs text-gray-600 uppercase font-semibold text-center mb-8">School Authority</p>
                <p class="border-t border-gray-900 pt-2 text-xs text-gray-600 text-center">Signature & Date</p>
            </div>
        </div>

        <!-- Footer -->
        <div class="mt-4 pt-2 border-t border-gray-300 text-center text-xs text-gray-600">
            <p>{{ $schoolSettings->footer_text ?? '© ' . date('Y') . ' ' . ($schoolSettings->school_name ?? 'School') . ' Management System' }}</p>
        </div>
        @endif
    </div>

    <script>
        // Auto-print option (commented out by default)
        // window.onload = function() { window.print(); }
    </script>
</body>
</html>
