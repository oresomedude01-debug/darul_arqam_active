@extends('layouts.spa')

@section('title', 'View Results - ' . $class->name)

@section('breadcrumb')
    <span class="text-gray-400">Teaching</span>
    <span class="text-gray-400">/</span>
    <span class="text-gray-400">
        <a href="{{ route('teacher.results.classes') }}" class="hover:text-gray-600">Results</a>
    </span>
    <span class="text-gray-400">/</span>
    <span class="font-semibold text-gray-900">{{ $class->name }} - {{ $term->name }}</span>
@endsection

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="bg-gradient-to-r from-green-50 to-blue-50 rounded-xl shadow-sm border border-green-100 p-8">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-4xl font-bold text-gray-900 mb-2">{{ $class->name }} - Results</h1>
                <p class="text-gray-600">
                    <i class="fas fa-lock text-green-600 mr-1"></i>
                    {{ $term->name }}
                    <span class="ml-3 inline-block px-3 py-1 bg-green-100 text-green-800 text-xs font-semibold rounded-full">
                        <i class="fas fa-check-circle mr-1"></i>RELEASED - READ ONLY
                    </span>
                </p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('teacher.results.classes') }}" class="px-6 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition font-semibold">
                    <i class="fas fa-arrow-left mr-2"></i>Back
                </a>
                <button onclick="window.print()" class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition font-semibold">
                    <i class="fas fa-print mr-2"></i>Print
                </button>
            </div>
        </div>
    </div>

    @if($students->isEmpty())
        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-6 text-center">
            <i class="fas fa-info-circle text-yellow-600 text-3xl mb-3"></i>
            <p class="text-gray-700 font-semibold">No students in class</p>
        </div>
    @else
        <!-- Results Grid -->
        <div class="bg-white rounded-lg shadow border border-gray-200">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-900 uppercase">Student</th>
                            @foreach($subjects as $subject)
                                <th class="px-4 py-3 text-center text-xs font-semibold text-gray-900 uppercase whitespace-nowrap">
                                    <div class="inline-block px-2 py-1 bg-green-100 text-green-700 rounded text-xs">
                                        {{ Str::limit($subject->name, 15) }}
                                    </div>
                                </th>
                            @endforeach
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-900 uppercase">Notes</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($students as $student)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="flex-shrink-0 h-10 w-10">
                                        <img class="h-10 w-10 rounded-full object-cover"
                                             src="{{ $student->profile_photo_path ? asset('storage/' . $student->profile_photo_path) : 'https://ui-avatars.com/api/?name=' . urlencode($student->full_name) }}"
                                             alt="{{ $student->full_name }}">
                                    </div>
                                    <div>
                                        <p class="text-sm font-semibold text-gray-900">{{ $student->full_name }}</p>
                                        <p class="text-xs text-gray-500">{{ $student->admission_number }}</p>
                                    </div>
                                </div>
                            </td>
                            @foreach($subjects as $subject)
                                @php
                                    $result = $results->get($student->id)?->firstWhere('subject_id', $subject->id);
                                    $totalScore = ($result->ca_score ?? 0) + ($result->exam_score ?? 0);
                                    // Dynamic grade color based on typical grade boundaries
                                    $gradeColor = match(true) {
                                        $totalScore >= 80 => 'bg-green-50 text-green-700',
                                        $totalScore >= 70 => 'bg-blue-50 text-blue-700',
                                        $totalScore >= 60 => 'bg-yellow-50 text-yellow-700',
                                        $totalScore >= 50 => 'bg-orange-50 text-orange-700',
                                        default => 'bg-red-50 text-red-700'
                                    };
                                @endphp
                                <td class="px-4 py-4 text-center">
                                    @if($result)
                                        <div class="text-center">
                                            <div class="inline-block px-3 py-2 rounded {{ $gradeColor }}">
                                                <p class="text-sm font-bold">{{ $totalScore }}</p>
                                                <p class="text-xs">{{ $result->ca_score ?? 0 }}/{{ $result->exam_score ?? 0 }}</p>
                                            </div>
                                            @if($result->teacher_comment)
                                            <div class="mt-2 text-xs text-gray-600 italic max-w-xs mx-auto">
                                                "{{ Str::limit($result->teacher_comment, 50) }}"
                                            </div>
                                            @endif
                                        </div>
                                    @else
                                        <p class="text-xs text-gray-400">-</p>
                                    @endif
                                </td>
                            @endforeach
                            <td class="px-4 py-4">
                                @php
                                    $studentResults = $results->get($student->id);
                                    if ($studentResults) {
                                        $avgScore = $studentResults->avg(function($r) { 
                                            return ($r->ca_score ?? 0) + ($r->exam_score ?? 0); 
                                        });
                                        $status = match(true) {
                                            $avgScore >= 70 => 'Excellent',
                                            $avgScore >= 60 => 'Good',
                                            $avgScore >= 50 => 'Pass',
                                            default => 'Needs Improvement'
                                        };
                                    } else {
                                        $status = '-';
                                    }
                                @endphp
                                <p class="text-xs text-gray-600 text-center">{{ $status }}</p>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Summary Stats -->
        <div class="grid grid-cols-4 gap-4">
            <div class="bg-white rounded-lg shadow border border-gray-200 p-6 text-center">
                <p class="text-3xl font-bold text-gray-900">{{ $students->count() }}</p>
                <p class="text-gray-600 text-sm mt-1">Total Students</p>
            </div>
            <div class="bg-white rounded-lg shadow border border-gray-200 p-6 text-center">
                <p class="text-3xl font-bold text-green-600">{{ $results->count() }}</p>
                <p class="text-gray-600 text-sm mt-1">Results Recorded</p>
            </div>
            <div class="bg-white rounded-lg shadow border border-gray-200 p-6 text-center">
                <p class="text-3xl font-bold text-blue-600">{{ $subjects->count() }}</p>
                <p class="text-gray-600 text-sm mt-1">Subjects</p>
            </div>
            <div class="bg-white rounded-lg shadow border border-gray-200 p-6 text-center">
                <p class="text-3xl font-bold text-purple-600">{{ $term->name }}</p>
                <p class="text-gray-600 text-sm mt-1">Academic Term</p>
            </div>
        </div>
    @endif
</div>

<style media="print">
    @page { size: A4 landscape; margin: 1cm; }
    body { background: white; }
    .no-print { display: none !important; }
    table { page-break-inside: avoid; }
</style>
@endsection
