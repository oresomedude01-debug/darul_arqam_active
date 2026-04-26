@extends('student-portal.layout')

@section('portal-title', 'My Results')

@section('student-content')
<div class="space-y-6">
    <!-- Filters -->
    <div class="bg-white rounded-2xl shadow-md border border-gray-200 p-6">
        <h3 class="font-bold text-gray-900 mb-4">Filter Results</h3>
        <form method="GET" action="{{ route('student-portal.results') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Session</label>
                <select name="session" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                    <option value="">All Sessions</option>
                    @foreach($sessions as $session)
                        <option value="{{ $session->id }}" {{ $selectedSession == $session->id ? 'selected' : '' }}>
                            {{ $session->session }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Term</label>
                <select name="term" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                    <option value="">All Terms</option>
                    @foreach($terms as $term)
                        <option value="{{ $term->id }}" {{ $selectedTerm == $term->id ? 'selected' : '' }}>
                            {{ $term->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="flex items-end">
                <button type="submit" class="w-full px-4 py-2 bg-indigo-600 text-white font-medium rounded-lg hover:bg-indigo-700 transition-colors">
                    <i class="fas fa-search mr-2"></i>Search
                </button>
            </div>
        </form>
    </div>

    <!-- Results Table -->
    <div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden hover:shadow-xl transition-all">
        <div class="bg-gradient-to-r from-indigo-600 to-purple-600 px-6 py-5 relative overflow-hidden">
            <div class="absolute right-0 top-0 w-40 h-40 bg-white/5 rounded-full -mr-20 -mt-20 blur-2xl"></div>
            <h2 class="text-xl font-bold text-white flex items-center gap-3 relative">
                <i class="fas fa-list-check text-purple-200"></i>All Results
            </h2>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gradient-to-r from-indigo-50 to-purple-50 border-b-2 border-indigo-100">
                    <tr>
                        <th class="px-6 py-4 text-left text-sm font-bold text-indigo-900">Subject</th>
                        <th class="px-6 py-4 text-left text-sm font-bold text-indigo-900">Session</th>
                        <th class="px-6 py-4 text-left text-sm font-bold text-indigo-900">Term</th>
                        <th class="px-6 py-4 text-center text-sm font-bold text-indigo-900">Score</th>
                        <th class="px-6 py-4 text-center text-sm font-bold text-indigo-900">Grade</th>
                        <th class="px-6 py-4 text-center text-sm font-bold text-indigo-900">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($results as $result)
                        <tr class="hover:bg-indigo-50/50 transition-colors group">
                            <td class="px-6 py-4">
                                <p class="font-semibold text-gray-900">{{ $result->subject_name }}</p>
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-sm text-gray-600">{{ $result->academicSession->session }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-blue-50 text-blue-700 text-sm rounded-lg font-medium border border-blue-200">
                                    {{ $result->academicTerm->name }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="text-2xl font-bold text-indigo-600">{{ $result->total_score }}</span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="text-lg font-bold text-gray-900">{{ $result->grade }}</span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if($result->total_score >= 50)
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-green-50 text-green-700 text-sm rounded-lg font-medium border border-green-200">
                                        <span class="w-2 h-2 bg-green-500 rounded-full"></span>Pass
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-red-50 text-red-700 text-sm rounded-lg font-medium border border-red-200">
                                        <span class="w-2 h-2 bg-red-500 rounded-full"></span>Fail
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <div class="w-16 h-16 rounded-full bg-gray-100 flex items-center justify-center mb-4">
                                        <i class="fas fa-inbox text-3xl text-gray-400"></i>
                                    </div>
                                    <p class="text-gray-600 font-medium">No results available</p>
                                    <p class="text-gray-400 text-sm mt-1">Your results will appear here once they are released</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Stats Summary -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-gradient-to-br from-green-50 to-emerald-50 rounded-2xl border border-green-200 p-6">
            <h3 class="font-bold text-green-900 mb-3 flex items-center gap-2">
                <i class="fas fa-check-circle text-green-600"></i>Passed Subjects
            </h3>
            <p class="text-4xl font-bold text-green-600">{{ $results->where('total_score', '>=', 50)->count() }}</p>
        </div>
        <div class="bg-gradient-to-br from-red-50 to-rose-50 rounded-2xl border border-red-200 p-6">
            <h3 class="font-bold text-red-900 mb-3 flex items-center gap-2">
                <i class="fas fa-times-circle text-red-600"></i>Failed Subjects
            </h3>
            <p class="text-4xl font-bold text-red-600">{{ $results->where('total_score', '<', 50)->count() }}</p>
        </div>
        <div class="bg-gradient-to-br from-blue-50 to-cyan-50 rounded-2xl border border-blue-200 p-6">
            <h3 class="font-bold text-blue-900 mb-3 flex items-center gap-2">
                <i class="fas fa-chart-line text-blue-600"></i>Average Score
            </h3>
            <p class="text-4xl font-bold text-blue-600">{{ round($results->avg('total_score'), 1) ?? 'N/A' }}</p>
        </div>
    </div>
</div>
@endsection
