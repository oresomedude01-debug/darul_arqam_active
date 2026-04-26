@extends('layouts.spa')

@section('content')
<div class="min-h-screen bg-gray-50 px-6 py-8">
    <!-- Breadcrumb & Header -->
    <div class="mb-8">
        <div class="flex items-center gap-2 text-sm text-gray-600 mb-4">
            <a href="{{ route('results.index') }}" class="text-primary-600 hover:text-primary-700">Results</a>
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            <span>{{ $session->session }}</span>
        </div>
        <h1 class="text-3xl font-bold text-gray-900">{{ $session->session }} - Select Term</h1>
        <p class="text-gray-600 mt-2">Choose a term to manage results</p>
    </div>

    @if ($errors->any())
        <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-lg">
            <p class="text-sm text-red-700">{{ $errors->first() }}</p>
        </div>
    @endif

    <!-- Session Summary at Top -->
    <div class="mb-8 grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white rounded-lg shadow p-6">
            <p class="text-gray-600 text-sm font-medium">Status</p>
            <p class="text-2xl font-bold mt-2">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold {{ $session->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                    {{ $session->is_active ? 'Active' : 'Inactive' }}
                </span>
            </p>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <p class="text-gray-600 text-sm font-medium">Terms</p>
            <p class="text-2xl font-bold text-gray-900 mt-2">{{ $terms->count() }}</p>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <p class="text-gray-600 text-sm font-medium">Total Results</p>
            <p class="text-2xl font-bold text-gray-900 mt-2">{{ $terms->sum('result_count') }}</p>
        </div>
    </div>

    <!-- Terms Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse ($terms as $term)
            <div class="bg-white rounded-lg shadow hover:shadow-lg transition-shadow p-6 border-l-4 border-blue-500">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-bold text-gray-900">
                        {{ $term['term']->term }}
                    </h3>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                        {{ $term['result_count'] }} Results
                    </span>
                </div>

                <div class="space-y-2 mb-4">
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-gray-600">Start Date:</span>
                        <span class="font-medium">{{ $term['term']->start_date ? $term['term']->start_date->format('M d, Y') : 'N/A' }}</span>
                    </div>
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-gray-600">End Date:</span>
                        <span class="font-medium">{{ $term['term']->end_date ? $term['term']->end_date->format('M d, Y') : 'N/A' }}</span>
                    </div>
                </div>

                <!-- Release Status Toggle -->
                <div class="bg-gray-50 rounded p-3 mb-4">
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-medium text-gray-900">Release Results</span>
                        <div class="flex gap-2">
                            @php
                                $releasedCount = \App\Models\Result::where('academic_session_id', $session->id)
                                    ->where('academic_term_id', $term['term']->id)
                                    ->where('is_released', true)
                                    ->count();
                            @endphp
                            @if($releasedCount > 0)
                                <span class="text-xs bg-green-100 text-green-800 px-2 py-1 rounded font-medium">Released</span>
                                <form action="{{ route('results.term.recall', [$session, $term['term']]) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="text-xs bg-red-100 text-red-700 hover:bg-red-200 px-3 py-1 rounded font-medium transition-colors" onclick="return confirm('Recall results? Students will no longer see them.');">
                                        <i class="fas fa-times mr-1"></i>Recall
                                    </button>
                                </form>
                            @else
                                <span class="text-xs bg-gray-300 text-gray-700 px-2 py-1 rounded font-medium">Not Released</span>
                                <form action="{{ route('results.term.release', [$session, $term['term']]) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="text-xs bg-green-100 text-green-700 hover:bg-green-200 px-3 py-1 rounded font-medium transition-colors" onclick="return confirm('Release all results for this term? Students will be able to view them.');">
                                        <i class="fas fa-check mr-1"></i>Release
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>

                <a href="{{ route('results.term.manage', [$session, $term['term']]) }}" class="inline-flex items-center text-blue-600 font-medium text-sm hover:translate-x-1 transition-transform">
                    Manage Classes
                    <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>
            </div>
        @empty
            <div class="col-span-full bg-yellow-50 border border-yellow-200 rounded-lg p-6">
                <p class="text-yellow-800">No terms found for this session.</p>
            </div>
        @endforelse
    </div>

    <!-- Session Summary -->
    <div class="mt-12 grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white rounded-lg shadow p-6">
            <p class="text-gray-600 text-sm font-medium">Status</p>
            <p class="text-2xl font-bold mt-2">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold {{ $session->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                    {{ $session->is_active ? 'Active' : 'Inactive' }}
                </span>
            </p>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <p class="text-gray-600 text-sm font-medium">Terms</p>
            <p class="text-2xl font-bold text-gray-900 mt-2">{{ $terms->count() }}</p>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <p class="text-gray-600 text-sm font-medium">Total Results</p>
            <p class="text-2xl font-bold text-gray-900 mt-2">{{ $terms->sum('result_count') }}</p>
        </div>
    </div>
</div>
@endsection
