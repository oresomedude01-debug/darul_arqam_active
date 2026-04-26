@extends('layouts.spa')

@section('content')
<div class="min-h-screen bg-gray-50 px-6 py-8">
    <!-- Breadcrumb & Header -->
    <div class="mb-8">
        <div class="flex items-center gap-2 text-sm text-gray-600 mb-4">
            <a href="{{ route('results.index') }}" class="text-primary-600 hover:text-primary-700">Results</a>
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            <span>Subject Results</span>
        </div>
        <h1 class="text-3xl font-bold text-gray-900">{{ $subject->name }} - Subject Results</h1>
        <p class="text-gray-600 mt-2">{{ $class->name }} | {{ $term->term }} | {{ $session->session }}</p>
    </div>

    <!-- Statistics -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
        <div class="bg-white rounded-lg shadow p-4">
            <p class="text-gray-600 text-sm">Total Students</p>
            <p class="text-2xl font-bold text-gray-900">{{ $stats['count'] }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <p class="text-gray-600 text-sm">Average Score</p>
            <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['average'], 2) }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <p class="text-gray-600 text-sm">Highest Score</p>
            <p class="text-2xl font-bold text-green-600">{{ number_format($stats['highest'], 2) }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <p class="text-gray-600 text-sm">Lowest Score</p>
            <p class="text-2xl font-bold text-red-600">{{ number_format($stats['lowest'], 2) }}</p>
        </div>
    </div>

    <!-- Results Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="w-full">
            <thead class="bg-gray-100 border-b border-gray-200">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-900 uppercase tracking-wider">Student</th>
                    <th class="px-6 py-3 text-center text-xs font-semibold text-gray-900 uppercase tracking-wider">Admission No.</th>
                    <th class="px-6 py-3 text-center text-xs font-semibold text-gray-900 uppercase tracking-wider">CA (/ {{ $maxCaScore }})</th>
                    <th class="px-6 py-3 text-center text-xs font-semibold text-gray-900 uppercase tracking-wider">Exam (/ {{ $maxExamScore }})</th>
                    <th class="px-6 py-3 text-center text-xs font-semibold text-gray-900 uppercase tracking-wider">Total</th>
                    <th class="px-6 py-3 text-center text-xs font-semibold text-gray-900 uppercase tracking-wider">Grade</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @foreach ($studentsResults as $student)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">
                            <div class="flex items-center">
                                <div class="w-10 h-10 bg-gray-200 rounded-full flex items-center justify-center flex-shrink-0">
                                    <i class="fas fa-user text-gray-600 text-xs"></i>
                                </div>
                                <div class="ml-4">
                                    <p class="font-medium text-gray-900">{{ $student->first_name }} {{ $student->last_name }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-center text-sm text-gray-600">
                            {{ $student->admission_number }}
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="font-semibold text-gray-900">{{ $student->ca_score }} / {{ $maxCaScore }}</span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="font-semibold text-gray-900">{{ $student->exam_score }} / {{ $maxExamScore }}</span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="font-bold text-lg text-gray-900">{{ $student->total_score }}</span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold bg-gray-100 text-gray-800">
                                -
                            </span>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Back Button -->
    <div class="mt-6">
        <a href="{{ route('results.class.manage', [$session, $term, $class]) }}" class="inline-flex items-center px-4 py-2 bg-gray-200 text-gray-900 font-medium rounded-lg hover:bg-gray-300 transition-colors gap-2">
            <i class="fas fa-arrow-left"></i>Back
        </a>
    </div>
</div>
@endsection
