@extends('layouts.spa')

@section('title', 'Class Results')

@section('breadcrumb')
    <span class="text-gray-400">Teaching</span>
    <span class="text-gray-400">/</span>
    <span class="font-semibold text-gray-900">Results</span>
@endsection

@section('content')
<div class="space-y-6">
    <!-- Hero Header -->
    <div class="bg-gradient-to-r from-purple-50 to-blue-50 rounded-xl shadow-sm border border-purple-100 p-8">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-6">
            <div>
                <h1 class="text-4xl font-bold text-gray-900 mb-2">Class Results</h1>
                <p class="text-gray-600 text-lg flex items-center gap-2">
                    <i class="fas fa-chart-bar text-purple-600"></i>
                    Manage and update student results for your classes
                </p>
            </div>
            <div class="text-right">
                <p class="text-sm text-gray-600 mb-1">Active Term:</p>
                <p class="text-2xl font-bold text-purple-600">
                    {{ $activeTerms->first()?->name ?? 'None' }}
                </p>
            </div>
        </div>
    </div>

    @if($classes->isEmpty())
        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-6 text-center">
            <i class="fas fa-info-circle text-yellow-600 text-3xl mb-3"></i>
            <p class="text-gray-700 font-semibold">No classes assigned</p>
            <p class="text-gray-500 mt-2">You don't have any classes assigned yet. Contact your administrator.</p>
        </div>
    @else
        <!-- Classes Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($classes as $class)
            <div class="bg-white rounded-lg shadow border border-gray-200 hover:shadow-lg hover:border-purple-300 transition">
                <!-- Class Header -->
                <div class="bg-gradient-to-r from-purple-500 to-blue-500 text-white p-4 rounded-t-lg">
                    <h3 class="text-lg font-bold">{{ $class->name }}</h3>
                    <p class="text-purple-100 text-sm">{{ $class->full_name }}</p>
                </div>

                <!-- Class Content -->
                <div class="p-4 space-y-4">
                    <!-- Class Stats -->
                    <div class="grid grid-cols-3 gap-3">
                        <div class="text-center">
                            <p class="text-2xl font-bold text-purple-600">{{ $class->students_count ?? $class->students()->count() }}</p>
                            <p class="text-xs text-gray-500">Students</p>
                        </div>
                        <div class="text-center">
                            <p class="text-2xl font-bold text-blue-600">{{ $class->subjects_count ?? $class->subjects()->count() }}</p>
                            <p class="text-xs text-gray-500">Subjects</p>
                        </div>
                        <div class="text-center">
                            <p class="text-2xl font-bold text-indigo-600">{{ $activeTerms->count() }}</p>
                            <p class="text-xs text-gray-500">Terms</p>
                        </div>
                    </div>

                    <div class="border-t border-gray-200 pt-4">
                        <!-- Active Term Results -->
                        @if($activeTerms->first())
                            <div class="mb-3">
                                <p class="text-sm font-semibold text-gray-700 mb-2">Current Term: <span class="text-purple-600">{{ $activeTerms->first()->name }}</span></p>
                                <a href="{{ route('results.class.manage', ['session' => $activeTerms->first()->academic_session_id, 'term' => $activeTerms->first()->id, 'class' => $class->id]) }}"
                                   class="block w-full px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition text-center text-sm font-semibold">
                                    <i class="fas fa-edit mr-1"></i>
                                    Edit Results
                                </a>
                            </div>
                        @endif

                        <!-- Previous Terms -->
                        @if($allTerms->count() > 1)
                            <div class="border-t border-gray-100 pt-3">
                                <p class="text-xs font-semibold text-gray-600 mb-2 uppercase">Previous Terms</p>
                                <div class="space-y-2 max-h-32 overflow-y-auto">
                                    @foreach($allTerms->skip(1) as $term)
                                    <a href="{{ route('teacher.results.previous', ['class' => $class->id, 'term' => $term->id]) }}"
                                       class="block px-3 py-1.5 bg-gray-50 hover:bg-gray-100 rounded text-xs text-gray-700 transition text-center">
                                        {{ $term->name }}
                                    </a>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <!-- Summary Section -->
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-3 flex items-center gap-2">
                <i class="fas fa-lightbulb text-blue-600"></i>
                How to Use Results Management
            </h3>
            <ul class="space-y-2 text-gray-700">
                <li class="flex gap-2">
                    <span class="text-blue-600 font-bold">1.</span>
                    <span>Click "Edit Results" to enter or update scores for the active term</span>
                </li>
                <li class="flex gap-2">
                    <span class="text-blue-600 font-bold">2.</span>
                    <span>You can only edit results for the currently active academic term</span>
                </li>
                <li class="flex gap-2">
                    <span class="text-blue-600 font-bold">3.</span>
                    <span>View previous released results by clicking on the term name under "Previous Terms"</span>
                </li>
                <li class="flex gap-2">
                    <span class="text-blue-600 font-bold">4.</span>
                    <span>Once results are submitted and approved, they can be released for student viewing</span>
                </li>
            </ul>
        </div>
    @endif
</div>
@endsection
