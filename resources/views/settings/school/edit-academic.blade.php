@extends('layouts.spa')

@section('title', 'Edit Academic Settings')

@section('breadcrumb')
    <span class="text-gray-400">Settings</span>
    <span class="text-gray-400">/</span>
    <a href="{{ route('settings.school.index') }}" class="text-primary-600 hover:text-primary-700">School Settings</a>
    <span class="text-gray-400">/</span>
    <span class="font-semibold text-gray-900">Academic Settings</span>
@endsection

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Academic Settings</h1>
            <p class="text-sm text-gray-600 mt-1">Current session and term information</p>
        </div>
        <a href="{{ route('settings.school.index') }}" class="btn btn-outline">
            <i class="fas fa-arrow-left mr-2"></i>Back to Settings
        </a>
    </div>

    <!-- Manage Sessions Link - Always Visible -->
    <div class="alert alert-info border-blue-300 bg-blue-50">
        <div class="flex items-start justify-between">
            <div class="flex items-start gap-3">
                <i class="fas fa-cogs text-blue-600 mt-1"></i>
                <div>
                    <h4 class="font-semibold text-blue-900">Manage Academic Sessions</h4>
                    <p class="text-sm text-blue-800 mt-1">
                        Create new sessions, edit term dates, or change the current active session.
                    </p>
                </div>
            </div>
            <a href="{{ route('settings.school.academic.sessions') }}" class="btn btn-sm btn-primary whitespace-nowrap ml-4">
                <i class="fas fa-calendar-alt mr-2"></i>Go to Sessions
            </a>
        </div>
    </div>

    <!-- Current Session Details -->
    @php
        // Find the active term directly from database
        $activeTerm = \App\Models\AcademicTerm::where('is_active', true)->first();
        $hasActiveTerm = $activeTerm !== null;
    @endphp

    @if ($hasActiveTerm)
        @php
            $currentSession = $activeTerm->session;
            $currentTerms = \App\Models\AcademicTerm::where('session', $currentSession)
                ->orderBy('term')
                ->get()
                ->toArray();
        @endphp
        <div class="space-y-6">
            <!-- Session Header -->
            <div class="card border-2 border-primary-200 bg-primary-50">
                <div class="card-body">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-2xl font-bold text-gray-900">
                                Academic Session {{ $currentSession }}
                            </h3>
                            <p class="text-sm text-gray-600 mt-1">
                                <i class="fas fa-star text-amber-500"></i> Current Active Session
                            </p>
                        </div>
                        <span class="badge badge-success text-lg px-4 py-2">
                            <i class="fas fa-check-circle mr-2"></i>Active
                        </span>
                    </div>
                </div>
            </div>

            @if ($activeTerm)
                <div class="card border-2 border-green-200">
                    <div class="card-header bg-green-50">
                        <h3 class="text-xl font-semibold text-gray-900">
                            <i class="fas fa-running text-green-600 mr-2"></i>Current Active Term
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="bg-white px-6 py-6 rounded-lg border-2 border-green-200">
                            <div class="flex items-start justify-between mb-4">
                                <div>
                                    <h4 class="text-2xl font-bold text-gray-900">{{ $activeTerm->name }}</h4>
                                    <p class="text-sm text-gray-600 mt-2">{{ $activeTerm->term }}</p>
                                </div>
                                <span class="badge badge-success">
                                    <i class="fas fa-fire"></i> Active
                                </span>
                            </div>
                            
                            <div class="space-y-3 mt-6">
                                <div class="flex items-center gap-3">
                                    <i class="fas fa-calendar-check text-primary-600 w-5"></i>
                                    <div>
                                        <p class="text-xs text-gray-500 uppercase tracking-wide">Start Date</p>
                                        <p class="text-lg font-semibold text-gray-900">
                                            {{ $activeTerm->start_date->format('F d, Y') }}
                                        </p>
                                    </div>
                                </div>
                                
                                <div class="flex items-center gap-3">
                                    <i class="fas fa-calendar-times text-primary-600 w-5"></i>
                                    <div>
                                        <p class="text-xs text-gray-500 uppercase tracking-wide">End Date</p>
                                        <p class="text-lg font-semibold text-gray-900">
                                            {{ $activeTerm->end_date->format('F d, Y') }}
                                        </p>
                                    </div>
                                </div>

                                <div class="flex items-center gap-3">
                                    <i class="fas fa-hourglass-half text-primary-600 w-5"></i>
                                    <div>
                                        <p class="text-xs text-gray-500 uppercase tracking-wide">School Opening Days</p>
                                        <p class="text-lg font-semibold text-gray-900">
                                            {{ $activeTerm->school_opening_days }} days
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- All Terms in Session -->
            <div class="card">
                <div class="card-header">
                    <h3 class="text-lg font-semibold text-gray-900">
                        <i class="fas fa-bookmark mr-2 text-primary-600"></i>All Terms in This Session
                    </h3>
                </div>
                <div class="card-body space-y-3">
                    @forelse ($currentTerms as $term)
                        <div class="flex items-center justify-between bg-gray-50 px-4 py-4 rounded-lg border border-gray-200 hover:border-primary-300 transition">
                            <div>
                                <h4 class="font-semibold text-gray-900">{{ $term['name'] }}</h4>
                                <p class="text-sm text-gray-600 mt-1">
                                    {{ \Carbon\Carbon::parse($term['start_date'])->format('M d') }} → {{ \Carbon\Carbon::parse($term['end_date'])->format('M d, Y') }}
                                </p>
                            </div>
                            @if ($activeTerm && $activeTerm->id === $term['id'])
                                <span class="badge badge-success">
                                    <i class="fas fa-check-circle mr-1"></i>Active
                                </span>
                            @else
                                <span class="text-xs text-gray-500 font-medium">{{ $term['term'] }}</span>
                            @endif
                        </div>
                    @empty
                        <p class="text-gray-500 italic text-center py-4">No terms configured</p>
                    @endforelse
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="flex gap-3">
                <a href="{{ route('settings.school.academic.sessions') }}" class="flex-1 btn btn-primary">
                    <i class="fas fa-edit mr-2"></i>Manage Sessions & Terms
                </a>
                <a href="{{ route('settings.school.index') }}" class="flex-1 btn btn-outline">
                    <i class="fas fa-arrow-left mr-2"></i>Back to Settings
                </a>
            </div>
        </div>
    @elseif ($hasActiveTerm && empty($currentTerms))
        <!-- Session Set But No Terms -->
        <div class="card text-center py-12 bg-amber-50 border-2 border-amber-200">
            <div class="mb-4">
                <i class="fas fa-exclamation-triangle text-amber-600 text-5xl"></i>
            </div>
            <h3 class="text-lg font-semibold text-amber-900 mb-2">Session "{{ $currentSession }}" Has No Terms</h3>
            <p class="text-amber-800 mb-6">The configured session doesn't have any terms. Go to Manage Sessions to create them or select a different session.</p>
            <a href="{{ route('settings.school.academic.sessions') }}" class="btn btn-primary">
                <i class="fas fa-calendar-alt mr-2"></i>Manage Sessions
            </a>
        </div>
    @else
        <!-- No Active Term Set -->
        <div class="card text-center py-12 bg-amber-50 border-2 border-amber-200">
            <div class="mb-4">
                <i class="fas fa-exclamation-circle text-amber-600 text-5xl"></i>
            </div>
            <h3 class="text-lg font-semibold text-amber-900 mb-2">No Active Session Set</h3>
            <p class="text-amber-800 mb-6">Go to Manage Sessions to create and activate an academic session.</p>
            <a href="{{ route('settings.school.academic.sessions') }}" class="btn btn-primary">
                <i class="fas fa-calendar-alt mr-2"></i>Manage Sessions
            </a>
        </div>
    @endif
</div>
@endsection
