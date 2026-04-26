@extends('layouts.spa')

@section('title', $student->full_name . ' - Result Profile')

@section('breadcrumb')
    <span class="text-gray-400">Grades</span>
    <span class="text-gray-400">/</span>
    <a href="{{ route('grades.class-results') }}" class="text-gray-400 hover:text-gray-600">Results</a>
    <span class="text-gray-400">/</span>
    <span class="font-semibold text-gray-900">{{ $student->full_name }}</span>
@endsection

@section('content')
<div class="space-y-6">
    <!-- Student Header -->
    <div class="card">
        <div class="card-body">
            <div class="flex flex-col md:flex-row items-start md:items-center gap-4">
                <div class="avatar avatar-lg {{ $student->gender === 'male' ? 'bg-blue-500' : 'bg-pink-500' }}">
                    @if($student->photo_path)
                        <img src="{{ Storage::url($student->photo_path) }}" alt="{{ $student->full_name }}" class="w-full h-full rounded-full object-cover">
                    @else
                        <span class="text-3xl">{{ substr($student->first_name, 0, 1) }}{{ substr($student->last_name, 0, 1) }}</span>
                    @endif
                </div>
                <div class="flex-1">
                    <h1 class="text-2xl font-bold text-gray-900">{{ $student->full_name }}</h1>
                    <div class="flex flex-wrap gap-3 mt-2 text-sm text-gray-600">
                        <span><i class="fas fa-id-card mr-1 text-gray-400"></i> {{ $student->admission_number }}</span>
                        <span><i class="fas fa-school mr-1 text-gray-400"></i> {{ $student->class_level }} - Section {{ $student->section }}</span>
                        <span><i class="fas fa-{{ $student->gender === 'male' ? 'male' : 'female' }} mr-1 text-gray-400"></i> {{ ucfirst($student->gender) }}</span>
                    </div>
                </div>
                <button onclick="window.print()" class="btn btn-outline">
                    <i class="fas fa-print mr-2"></i>
                    Print
                </button>
            </div>
        </div>
    </div>

    <!-- Tabs -->
    <div class="card">
        <div class="border-b border-gray-200">
            <nav class="-mb-px flex space-x-8 px-6" aria-label="Tabs">
                <a href="#current" onclick="showTab('current')" id="tab-current"
                   class="tab-link active border-primary-500 text-primary-600 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                    Current Term
                </a>
                <a href="#history" onclick="showTab('history')" id="tab-history"
                   class="tab-link border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                    History
                </a>
            </nav>
        </div>

        <!-- Current Term Tab -->
        <div id="content-current" class="tab-content">
            <div class="p-6 space-y-6">
                <!-- Summary Stats -->
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div class="bg-blue-50 rounded-lg p-4 text-center">
                        <div class="text-2xl font-bold text-blue-900">{{ $currentResults->count() }}</div>
                        <div class="text-sm text-blue-700 mt-1">Subjects</div>
                    </div>
                    <div class="bg-green-50 rounded-lg p-4 text-center">
                        <div class="text-2xl font-bold text-green-900">{{ number_format($currentTotal, 2) }}</div>
                        <div class="text-sm text-green-700 mt-1">Total Score</div>
                    </div>
                    <div class="bg-purple-50 rounded-lg p-4 text-center">
                        <div class="text-2xl font-bold text-purple-900">{{ number_format($currentAverage, 2) }}</div>
                        <div class="text-sm text-purple-700 mt-1">Average</div>
                    </div>
                    <div class="bg-orange-50 rounded-lg p-4 text-center">
                        <div class="text-3xl font-bold text-orange-900">{{ $currentGrade }}</div>
                        <div class="text-sm text-orange-700 mt-1">Overall Grade</div>
                    </div>
                </div>

                <!-- Subject Results Table -->
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Subject Breakdown</h3>
                    <div class="overflow-x-auto">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Subject</th>
                                    <th>Exam Type</th>
                                    <th>Score</th>
                                    <th>Grade</th>
                                    <th>Remark</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($currentResults as $result)
                                <tr>
                                    <td class="font-medium">{{ $result->subject->name }}</td>
                                    <td>
                                        <span class="badge badge-primary text-xs">
                                            {{ $result->examType->name }}
                                        </span>
                                    </td>
                                    <td class="font-semibold text-lg">{{ number_format($result->score, 2) }}</td>
                                    <td>
                                        <span class="badge badge-{{ $result->grade == 'F' ? 'danger' : 'success' }} text-lg">
                                            {{ $result->grade }}
                                        </span>
                                    </td>
                                    <td class="text-sm text-gray-600">{{ $result->remark ?? '-' }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center py-8 text-gray-400">
                                        <i class="fas fa-inbox text-4xl mb-2"></i>
                                        <p>No results for current term</p>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- History Tab -->
        <div id="content-history" class="tab-content hidden">
            <div class="p-6 space-y-6">
                @forelse($historicalResults as $termKey => $termResults)
                <div class="border border-gray-200 rounded-lg overflow-hidden">
                    <div class="bg-gray-50 px-4 py-3 border-b border-gray-200">
                        <h4 class="font-semibold text-gray-900">{{ $termKey }}</h4>
                        <p class="text-sm text-gray-600 mt-1">
                            {{ $termResults->count() }} subjects •
                            Average: {{ number_format($termResults->avg('score'), 2) }} •
                            Grade: {{ App\Models\Grade::calculateGrade($termResults->avg('score')) }}
                        </p>
                    </div>
                    <div class="p-4">
                        <div class="overflow-x-auto">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Subject</th>
                                        <th>Exam</th>
                                        <th>Score</th>
                                        <th>Grade</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($termResults as $result)
                                    <tr>
                                        <td>{{ $result->subject->name }}</td>
                                        <td>
                                            <span class="badge badge-info text-xs">
                                                {{ $result->examType->name }}
                                            </span>
                                        </td>
                                        <td class="font-semibold">{{ number_format($result->score, 2) }}</td>
                                        <td>
                                            <span class="badge badge-{{ $result->grade == 'F' ? 'danger' : 'success' }}">
                                                {{ $result->grade }}
                                            </span>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                @empty
                <div class="text-center py-12 text-gray-400">
                    <i class="fas fa-history text-6xl mb-4"></i>
                    <p class="text-lg">No historical results found</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

<script>
function showTab(tab) {
    // Hide all tabs
    document.querySelectorAll('.tab-content').forEach(el => el.classList.add('hidden'));
    document.querySelectorAll('.tab-link').forEach(el => {
        el.classList.remove('active', 'border-primary-500', 'text-primary-600');
        el.classList.add('border-transparent', 'text-gray-500');
    });

    // Show selected tab
    document.getElementById('content-' + tab).classList.remove('hidden');
    document.getElementById('tab-' + tab).classList.add('active', 'border-primary-500', 'text-primary-600');
    document.getElementById('tab-' + tab).classList.remove('border-transparent', 'text-gray-500');
}
</script>

<style>
@media print {
    .btn, nav, footer, .tab-link {
        display: none !important;
    }
    .tab-content {
        display: block !important;
    }
}
</style>
@endsection
