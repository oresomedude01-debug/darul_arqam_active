<!-- Summary Statistics -->
<div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-6">
    <div class="bg-blue-50 rounded-lg p-4 border border-blue-200">
        <p class="text-blue-600 text-sm font-medium">Total Subjects</p>
        <p class="text-2xl font-bold text-blue-900">{{ $resultsWithGrades->count() }}</p>
    </div>
    <div class="bg-green-50 rounded-lg p-4 border border-green-200">
        <p class="text-green-600 text-sm font-medium">Average Score</p>
        <p class="text-2xl font-bold text-green-900">{{ $resultsWithGrades->count() > 0 ? number_format($averageScore, 1) : '0' }}</p>
    </div>
    <div class="bg-purple-50 rounded-lg p-4 border border-purple-200">
        <p class="text-purple-600 text-sm font-medium">Highest Score</p>
        <p class="text-2xl font-bold text-purple-900">{{ $resultsWithGrades->count() > 0 ? number_format($highestScore, 1) : '0' }}</p>
    </div>
    <div class="bg-yellow-50 rounded-lg p-4 border border-yellow-200">
        <p class="text-yellow-600 text-sm font-medium">Lowest Score</p>
        <p class="text-2xl font-bold text-yellow-900">{{ $resultsWithGrades->count() > 0 ? number_format($lowestScore, 1) : '0' }}</p>
    </div>
    <div class="bg-indigo-50 rounded-lg p-4 border border-indigo-200">
        <p class="text-indigo-600 text-sm font-medium">Pass Rate</p>
        <p class="text-2xl font-bold text-indigo-900">
            @if ($resultsWithGrades->count() > 0)
                {{ round(($passCount / $resultsWithGrades->count()) * 100) }}%
            @else
                0%
            @endif
        </p>
    </div>
</div>

<!-- Results Table -->
<div class="bg-white shadow-md rounded-lg overflow-hidden mb-6">
    <div class="px-6 py-4 bg-gradient-to-r from-blue-600 to-blue-700 flex justify-between items-center">
        <h2 class="text-lg font-semibold text-white">
            Subject Results - {{ $selectedChild->first_name }} {{ $selectedChild->last_name }}
        </h2>
        <a href="{{ route('parent-portal.results.print.params', [$selectedSession->id, $selectedTerm->id, $selectedChild->id]) }}" target="_blank" class="inline-flex items-center px-4 py-2 bg-white text-blue-600 rounded-lg font-medium hover:bg-gray-50 transition">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4H9a2 2 0 00-2 2v2a2 2 0 002 2h10a2 2 0 002-2v-2a2 2 0 00-2-2h-2m-4-4V9m0 4v10m0 0H9m4 0h4"/>
            </svg>
            Print
        </a>
    </div>

    @if($resultsWithGrades->count() > 0)
        <div class="overflow-x-auto">
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
    @else
        <div class="px-6 py-12 text-center bg-gray-50">
            <p class="text-gray-500 text-lg">No grades found for the selected filters</p>
        </div>
    @endif
</div>

<!-- Performance Summary -->
<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
    <div class="bg-green-50 rounded-lg border border-green-200 p-6">
        <p class="font-semibold text-gray-900 mb-2">Subjects Passed</p>
        <p class="text-3xl font-bold text-green-600">{{ $passCount }} / {{ $resultsWithGrades->count() }}</p>
    </div>
    <div class="bg-red-50 rounded-lg border border-red-200 p-6">
        <p class="font-semibold text-gray-900 mb-2">Subjects Failed</p>
        <p class="text-3xl font-bold text-red-600">{{ $resultsWithGrades->count() - $passCount }} / {{ $resultsWithGrades->count() }}</p>
    </div>
</div>

<!-- Session & Term Info -->
<div class="bg-blue-50 border border-blue-200 rounded-lg p-6 mb-6">
    <div class="grid grid-cols-3 gap-6">
        <div>
            <p class="text-sm text-gray-600 font-semibold mb-1">Academic Session</p>
            <p class="text-lg font-bold text-gray-900">{{ $selectedSession->session }}</p>
        </div>
        <div>
            <p class="text-sm text-gray-600 font-semibold mb-1">Term</p>
            <p class="text-lg font-bold text-gray-900">{{ $selectedTerm->term }}</p>
        </div>
        <div>
            <p class="text-sm text-gray-600 font-semibold mb-1">Student</p>
            <p class="text-lg font-bold text-gray-900">{{ $selectedChild->first_name }} {{ $selectedChild->last_name }}</p>
        </div>
    </div>
</div>
