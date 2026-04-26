@extends('layouts.spa')

@section('content')
<div class="min-h-screen bg-gray-50 px-4 sm:px-6 py-6 sm:py-8">
    <!-- Breadcrumb & Header -->
    <div class="mb-8">
        <div class="flex items-center gap-2 text-xs sm:text-sm text-gray-600 mb-4 overflow-x-auto pb-2">
            <a href="{{ route('results.index') }}" class="text-primary-600 hover:text-primary-700 whitespace-nowrap">Results</a>
            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            <a href="{{ route('results.session.show', $session) }}" class="text-primary-600 hover:text-primary-700 whitespace-nowrap">{{ $session->session }}</a>
            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            <span class="whitespace-nowrap">{{ $term->term }}</span>
        </div>
        <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">{{ $term->term }} - Select Class</h1>
        <p class="text-xs sm:text-base text-gray-600 mt-2">{{ $session->session }} | Choose a class to manage results</p>
    </div>

    @if (session('success'))
        <div class="mb-4 p-3 sm:p-4 bg-green-50 border border-green-200 rounded-lg">
            <p class="text-xs sm:text-sm text-green-700">{{ session('success') }}</p>
        </div>
    @endif

    <!-- KPI Stats at Top (Hidden on mobile) -->
    <div class="mb-8 hidden md:grid md:grid-cols-3 gap-4 md:gap-6">
        <div class="bg-white rounded-lg shadow p-4 md:p-6">
            <p class="text-gray-600 text-xs md:text-sm font-medium">Total Classes</p>
            <p class="text-2xl md:text-3xl font-bold text-gray-900 mt-2">{{ $classes->count() }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4 md:p-6">
            <p class="text-gray-600 text-xs md:text-sm font-medium">Total Students</p>
            <p class="text-2xl md:text-3xl font-bold text-gray-900 mt-2">{{ $classes->sum('student_count') }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4 md:p-6">
            <p class="text-gray-600 text-xs md:text-sm font-medium">Results Entered</p>
            <p class="text-2xl md:text-3xl font-bold text-gray-900 mt-2">{{ $classes->sum('result_count') }}</p>
        </div>
    </div>

    <!-- Classes Section -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <!-- Desktop Table View -->
        <div class="hidden md:block overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-100 border-b border-gray-200">
                    <tr>
                        <th class="px-4 md:px-6 py-2 md:py-3 text-left text-xs font-semibold text-gray-900 uppercase tracking-wider">Class Name</th>
                        <th class="px-4 md:px-6 py-2 md:py-3 text-center text-xs font-semibold text-gray-900 uppercase tracking-wider">Students</th>
                        <th class="px-4 md:px-6 py-2 md:py-3 text-center text-xs font-semibold text-gray-900 uppercase tracking-wider">Results</th>
                        <th class="px-4 md:px-6 py-2 md:py-3 text-center text-xs font-semibold text-gray-900 uppercase tracking-wider">Completion</th>
                        <th class="px-4 md:px-6 py-2 md:py-3 text-center text-xs font-semibold text-gray-900 uppercase tracking-wider">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse ($classes as $class)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 md:px-6 py-3 md:py-4 whitespace-nowrap">
                                <p class="font-semibold text-sm md:text-base text-gray-900">{{ $class->name }}</p>
                            </td>
                            <td class="px-4 md:px-6 py-3 md:py-4 text-center">
                                <span class="inline-flex items-center justify-center px-2 md:px-3 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-800">
                                    {{ $class->student_count }}
                                </span>
                            </td>
                            <td class="px-4 md:px-6 py-3 md:py-4 text-center">
                                <span class="inline-flex items-center justify-center px-2 md:px-3 py-1 rounded-full text-xs font-semibold bg-purple-100 text-purple-800">
                                    {{ $class->result_count }}
                                </span>
                            </td>
                            <td class="px-4 md:px-6 py-3 md:py-4">
                                <div class="flex items-center justify-center gap-2">
                                    <div class="w-24 md:w-32 bg-gray-200 rounded-full h-2">
                                        <div class="bg-indigo-600 h-2 rounded-full transition-all" style="width: {{ min($class->completion_percentage, 100) }}%"></div>
                                    </div>
                                    <span class="text-xs font-semibold text-gray-900 w-10 md:w-12 text-right">{{ min($class->completion_percentage, 100) }}%</span>
                                </div>
                            </td>
                            <td class="px-4 md:px-6 py-3 md:py-4 text-center">
                                <a href="{{ route('results.class.manage', [$session, $term, $class]) }}" class="inline-flex items-center px-3 md:px-4 py-2 bg-primary-600 text-white text-xs font-semibold rounded-lg hover:bg-primary-700 transition-colors gap-1 md:gap-2">
                                    <i class="fas fa-arrow-right hidden md:inline"></i><span>Manage</span>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-8 text-center">
                                <div class="text-yellow-800 bg-yellow-50 border border-yellow-200 rounded-lg p-4 inline-block">
                                    <p class="text-xs md:text-sm"><i class="fas fa-info-circle mr-2"></i>No classes found.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Mobile Card Grid View -->
        <div class="md:hidden">
            @forelse ($classes as $class)
                <div class="p-4 border-b border-gray-200 last:border-b-0">
                    <div class="flex items-start justify-between mb-3">
                        <div>
                            <p class="font-bold text-lg text-gray-900">{{ $class->name }}</p>
                            <div class="flex gap-2 flex-wrap mt-2">
                                <span class="inline-flex items-center justify-center px-2 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-800">
                                    {{ $class->student_count }} Students
                                </span>
                                <span class="inline-flex items-center justify-center px-2 py-1 rounded-full text-xs font-semibold bg-purple-100 text-purple-800">
                                    {{ $class->result_count }} Results
                                </span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <div class="flex items-center justify-between mb-1">
                            <p class="text-xs text-gray-600 font-medium">Completion Progress</p>
                            <span class="text-sm font-bold text-indigo-600">{{ min($class->completion_percentage, 100) }}%</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-indigo-600 h-2 rounded-full transition-all" style="width: {{ min($class->completion_percentage, 100) }}%"></div>
                        </div>
                    </div>

                    <a href="{{ route('results.class.manage', [$session, $term, $class]) }}" class="w-full block text-center px-4 py-2 bg-primary-600 text-white text-sm font-semibold rounded-lg hover:bg-primary-700 transition-colors">
                        Manage Results
                    </a>
                </div>
            @empty
                <div class="px-4 py-8 text-center">
                    <div class="text-yellow-800 bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                        <p class="text-sm"><i class="fas fa-info-circle mr-2"></i>No classes found.</p>
                    </div>
                </div>
            @endforelse
        </div>
    </div>

    <!-- Quick Access Links -->
    <div class="mt-8 md:mt-12 grid grid-cols-1 md:grid-cols-2 gap-3 md:gap-6">
        <a href="{{ route('results.term.summary', [$session, $term]) }}" class="bg-white rounded-lg shadow hover:shadow-md p-4 md:p-6 transition-shadow flex items-center justify-between group">
            <div>
                <p class="font-semibold text-sm md:text-base text-gray-900">View Term Summary</p>
                <p class="text-xs md:text-sm text-gray-600 mt-1">Overall performance across all classes</p>
            </div>
            <div class="text-gray-400 group-hover:translate-x-1 transition-transform flex-shrink-0 ml-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </div>
        </a>

        <a href="{{ route('results.session.show', $session) }}" class="bg-white rounded-lg shadow hover:shadow-md p-4 md:p-6 transition-shadow flex items-center justify-between group">
            <div>
                <p class="font-semibold text-sm md:text-base text-gray-900">Back to Terms</p>
                <p class="text-xs md:text-sm text-gray-600 mt-1">Select a different term</p>
            </div>
            <div class="text-gray-400 group-hover:translate-x-1 transition-transform flex-shrink-0 ml-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </div>
        </a>
    </div>
</div>
@endsection
