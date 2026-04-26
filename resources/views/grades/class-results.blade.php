@extends('layouts.spa')

@section('title', 'Class Results')

@section('breadcrumb')
    <span class="text-gray-400">Grades</span>
    <span class="text-gray-400">/</span>
    <span class="font-semibold text-gray-900">Class Results</span>
@endsection

@section('content')
<div class="space-y-6 pb-20 md:pb-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Class Results</h1>
            <p class="text-gray-600 mt-1">View student performance and rankings</p>
        </div>
        <div class="flex items-center space-x-3">
            <a href="{{ route('grades.index') }}" class="btn btn-outline">
                <i class="fas fa-edit mr-2"></i>
                Gradebook
            </a>
            <button onclick="window.print()" class="btn btn-primary">
                <i class="fas fa-print mr-2"></i>
                Print
            </button>
        </div>
    </div>

    <!-- Filters -->
    <div class="card">
        <div class="card-body">
            <form method="GET" action="{{ route('grades.class-results') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <!-- Term -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Term</label>
                    <select name="term" class="form-select" required>
                        <option value="First Term" {{ $term === 'First Term' ? 'selected' : '' }}>First Term</option>
                        <option value="Second Term" {{ $term === 'Second Term' ? 'selected' : '' }}>Second Term</option>
                        <option value="Third Term" {{ $term === 'Third Term' ? 'selected' : '' }}>Third Term</option>
                    </select>
                </div>

                <!-- Session -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Session</label>
                    <select name="session" class="form-select" required>
                        <option value="2024/2025" {{ $session === '2024/2025' ? 'selected' : '' }}>2024/2025</option>
                        <option value="2025/2026" {{ $session === '2025/2026' ? 'selected' : '' }}>2025/2026</option>
                        <option value="2023/2024" {{ $session === '2023/2024' ? 'selected' : '' }}>2023/2024</option>
                    </select>
                </div>

                <!-- Exam Type -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Exam Type</label>
                    <select name="exam_type_id" class="form-select" required>
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
                    <label class="block text-sm font-medium text-gray-700 mb-2">Class</label>
                    <select name="class_id" class="form-select" required>
                        <option value="">Select class...</option>
                        @foreach($classes as $class)
                            <option value="{{ $class->id }}" {{ $classId == $class->id ? 'selected' : '' }}>
                                {{ $class->name }} - Section {{ $class->section }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Submit Button -->
                <div class="md:col-span-4 flex justify-end">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search mr-2"></i>
                        View Results
                    </button>
                </div>
            </form>
        </div>
    </div>

    @if($results)
    <!-- Class Statistics -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="card">
            <div class="card-body text-center">
                <div class="text-3xl font-bold text-gray-900">{{ $results->count() }}</div>
                <div class="text-sm text-gray-600 mt-1">Total Students</div>
            </div>
        </div>
        <div class="card">
            <div class="card-body text-center">
                <div class="text-3xl font-bold text-green-600">{{ number_format($results->avg('average'), 2) }}</div>
                <div class="text-sm text-gray-600 mt-1">Class Average</div>
            </div>
        </div>
        <div class="card">
            <div class="card-body text-center">
                <div class="text-3xl font-bold text-blue-600">{{ number_format($results->max('average'), 2) }}</div>
                <div class="text-sm text-gray-600 mt-1">Highest Score</div>
            </div>
        </div>
        <div class="card">
            <div class="card-body text-center">
                <div class="text-3xl font-bold text-orange-600">{{ number_format($results->min('average'), 2) }}</div>
                <div class="text-sm text-gray-600 mt-1">Lowest Score</div>
            </div>
        </div>
    </div>

    <!-- Results Table -->
    <div class="card">
        <div class="card-header">
            <h2 class="text-lg font-semibold text-gray-900">Student Results</h2>
        </div>
        <div class="card-body p-0">
            <!-- Desktop Table -->
            <div class="hidden md:block overflow-x-auto">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Position</th>
                            <th>Student Name</th>
                            <th>Admission No.</th>
                            <th>Total Score</th>
                            <th>Average</th>
                            <th>Grade</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($results as $result)
                        <tr>
                            <td>
                                @if($result['position'] <= 3)
                                    <span class="inline-flex items-center justify-center w-8 h-8 rounded-full {{ $result['position'] == 1 ? 'bg-yellow-100 text-yellow-800' : ($result['position'] == 2 ? 'bg-gray-100 text-gray-800' : 'bg-orange-100 text-orange-800') }} font-bold">
                                        {{ $result['position'] }}
                                    </span>
                                @else
                                    <span class="font-medium text-gray-700">{{ $result['position'] }}</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('grades.student-profile', $result['student']) }}"
                                   class="font-medium text-primary-600 hover:text-primary-700 hover:underline">
                                    {{ $result['student']->full_name }}
                                </a>
                            </td>
                            <td class="text-gray-600">{{ $result['student']->admission_number }}</td>
                            <td class="font-semibold">{{ number_format($result['total_score'], 2) }}</td>
                            <td class="font-semibold text-lg">{{ number_format($result['average'], 2) }}</td>
                            <td>
                                <span class="badge badge-{{ $result['overall_grade'] == 'F' ? 'danger' : 'success' }} text-lg">
                                    {{ $result['overall_grade'] }}
                                </span>
                            </td>
                            <td>
                                <a href="{{ route('grades.student-profile', $result['student']) }}"
                                   class="text-blue-600 hover:text-blue-700"
                                   data-tooltip="View Profile">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Mobile Cards -->
            <div class="md:hidden space-y-3 p-4">
                @foreach($results as $result)
                <div class="bg-white border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                    <div class="flex items-start justify-between mb-3">
                        <div class="flex items-center space-x-3">
                            @if($result['position'] <= 3)
                                <span class="inline-flex items-center justify-center w-10 h-10 rounded-full {{ $result['position'] == 1 ? 'bg-yellow-100 text-yellow-800' : ($result['position'] == 2 ? 'bg-gray-100 text-gray-800' : 'bg-orange-100 text-orange-800') }} font-bold text-lg">
                                    {{ $result['position'] }}
                                </span>
                            @else
                                <span class="font-semibold text-gray-700 text-lg">{{ $result['position'] }}</span>
                            @endif
                            <div>
                                <a href="{{ route('grades.student-profile', $result['student']) }}"
                                   class="font-medium text-gray-900 hover:text-primary-600">
                                    {{ $result['student']->full_name }}
                                </a>
                                <p class="text-sm text-gray-500">{{ $result['student']->admission_number }}</p>
                            </div>
                        </div>
                        <span class="badge badge-{{ $result['overall_grade'] == 'F' ? 'danger' : 'success' }} text-lg">
                            {{ $result['overall_grade'] }}
                        </span>
                    </div>
                    <div class="grid grid-cols-2 gap-3 text-sm">
                        <div>
                            <span class="text-gray-600">Average:</span>
                            <span class="font-semibold text-gray-900 ml-1">{{ number_format($result['average'], 2) }}</span>
                        </div>
                        <div>
                            <span class="text-gray-600">Total:</span>
                            <span class="font-semibold text-gray-900 ml-1">{{ number_format($result['total_score'], 2) }}</span>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    @else
    <!-- Empty State -->
    <div class="card">
        <div class="card-body">
            <div class="text-center py-12">
                <i class="fas fa-chart-bar text-gray-300 text-6xl mb-4"></i>
                <h3 class="text-lg font-medium text-gray-900 mb-2">Select Filters to View Results</h3>
                <p class="text-gray-600">Choose term, session, exam type, and class from the filters above</p>
            </div>
        </div>
    </div>
    @endif
</div>

<!-- Mobile Footer Navigation -->
<div class="md:hidden fixed bottom-0 left-0 right-0 bg-white border-t border-gray-200 shadow-lg z-40">
    <div class="grid grid-cols-4 gap-1 p-2">
        <a href="{{ route('grades.index') }}" class="flex flex-col items-center py-2 px-3 text-gray-600 hover:bg-gray-50 rounded-lg transition-colors">
            <i class="fas fa-edit text-xl mb-1"></i>
            <span class="text-xs font-medium">Gradebook</span>
        </a>
        <a href="{{ route('grades.class-results') }}" class="flex flex-col items-center py-2 px-3 text-primary-600 bg-primary-50 rounded-lg">
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

<style>
@media print {
    .btn, .card-header button, nav, .md\:hidden, footer {
        display: none !important;
    }
}
</style>
@endsection
