@extends('layouts.spa')

@section('title', 'Academic Sessions Management')

@section('breadcrumb')
    <span class="text-gray-400">Settings</span>
    <span class="text-gray-400">/</span>
    <a href="{{ route('settings.school.index') }}" class="text-primary-600 hover:text-primary-700">School Settings</a>
    <span class="text-gray-400">/</span>
    <span class="font-semibold text-gray-900">Academic Sessions</span>
@endsection

@section('content')
<div class="max-w-5xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Academic Sessions</h1>
            <p class="text-sm text-gray-600 mt-1">Create and manage academic sessions with term calendars</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('settings.school.index') }}" class="btn btn-outline">
                <i class="fas fa-arrow-left mr-2"></i>Back
            </a>
            <a href="{{ route('settings.school.academic.create') }}" class="btn btn-primary">
                <i class="fas fa-plus mr-2"></i>Create New Session
            </a>
        </div>
    </div>

    <!-- Search and Filter -->
    <div class="card">
        <div class="card-body">
            <form action="{{ route('settings.school.academic.sessions') }}" method="GET" class="flex gap-3">
                <div class="flex-1">
                    <div class="relative">
                        <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
                        <input type="text" 
                               name="search" 
                               placeholder="Search by session name (e.g., 2026/2027)" 
                               value="{{ request('search') }}"
                               class="form-input pl-10 w-full">
                    </div>
                </div>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-search mr-2"></i>Search
                </button>
                @if(request('search'))
                    <a href="{{ route('settings.school.academic.sessions') }}" class="btn btn-outline">
                        <i class="fas fa-times mr-2"></i>Clear
                    </a>
                @endif
            </form>
        </div>
    </div>

    @if(request('search'))
        <div class="alert alert-info">
            <i class="fas fa-filter mr-2"></i>
            Showing results for: <strong>"{{ request('search') }}"</strong>
        </div>
    @endif

    @if (empty($sessions))
        <!-- Empty State -->
        <div class="card text-center py-12">
            <div class="mb-4">
                <i class="fas fa-calendar-times text-gray-300 text-5xl"></i>
            </div>
            <h3 class="text-lg font-semibold text-gray-900 mb-2">No Academic Sessions</h3>
            <p class="text-gray-600 mb-6">Get started by creating your first academic session with term calendars</p>
            <a href="{{ route('settings.school.academic.create') }}" class="btn btn-primary">
                <i class="fas fa-plus mr-2"></i>Create First Session
            </a>
        </div>
    @else
        <!-- Sessions List -->
        <div class="space-y-4">
            @foreach ($sessions as $session)
                @php
                    $sessionTerms = $session->terms;
                    $isCurrentSession = $session->is_active;
                @endphp
                
                <div class="card hover:shadow-md transition-shadow @if($isCurrentSession) border-2 border-green-500 @endif">
                    <div class="card-body">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <div class="flex items-center gap-3">
                                    <h3 class="text-xl font-bold text-gray-900">
                                        Academic Session {{ $session->session }}
                                    </h3>
                                    @if ($isCurrentSession)
                                        <span class="badge badge-success">
                                            <i class="fas fa-check mr-1"></i>Current
                                        </span>
                                    @endif
                                </div>
                                
                                <!-- Terms Summary -->
                                <div class="mt-4 space-y-2">
                                    @forelse ($sessionTerms as $term)
                                        <div class="flex items-center justify-between text-sm bg-gray-50 px-3 py-2 rounded">
                                            <div>
                                                <span class="font-medium text-gray-900">{{ $term->name }}</span>
                                                <span class="text-gray-600 ml-2">
                                                    {{ $term->start_date->format('M d') }} - {{ $term->end_date->format('M d, Y') }}
                                                </span>
                                            </div>
                                            @if ($term->is_active)
                                                <span class="badge badge-info text-xs">
                                                    <i class="fas fa-running"></i> Active
                                                </span>
                                            @endif
                                        </div>
                                    @empty
                                        <p class="text-gray-500 italic">No terms configured</p>
                                    @endforelse
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="flex gap-2 ml-4 flex-col">
                                <a href="{{ route('settings.school.academic.edit', $session->id) }}" 
                                   class="btn btn-sm btn-primary whitespace-nowrap"
                                   title="Edit terms dates">
                                    <i class="fas fa-edit"></i> Edit Terms
                                </a>
                                
                                @if (!$isCurrentSession)
                                    <form action="{{ route('settings.school.academic.set-current', $session->id) }}" 
                                          method="POST" 
                                          style="display: inline;">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-success w-full whitespace-nowrap">
                                            <i class="fas fa-star"></i> Set Current
                                        </button>
                                    </form>

                                    <form action="{{ route('settings.school.academic.delete', $session->id) }}" 
                                          method="POST" 
                                          style="display: inline;"
                                          onsubmit="return confirm('Are you sure you want to delete this session and all its terms? This cannot be undone.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger w-full whitespace-nowrap">
                                            <i class="fas fa-trash"></i> Delete
                                        </button>
                                    </form>
                                @else
                                    <button class="btn btn-sm btn-gray w-full whitespace-nowrap opacity-50 cursor-not-allowed" disabled>
                                        <i class="fas fa-lock"></i> Current
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Current Settings Info -->
        <div class="card bg-blue-50 border-blue-200">
            <div class="card-body">
                <div class="flex items-start gap-3">
                    <i class="fas fa-info-circle text-blue-600 mt-1"></i>
                    <div>
                        <h4 class="font-semibold text-blue-900">Currently Active</h4>
                        @if($activeSession)
                            <p class="text-sm text-blue-800 mt-1">
                                <strong>Session:</strong> {{ $activeSession->session }}
                            </p>
                            <p class="text-sm text-blue-800">
                                <strong>Current Term:</strong> {{ $activeTerm->name ?? 'Not set' }}
                            </p>
                        @else
                            <p class="text-sm text-blue-800 mt-1">
                                <strong>Session:</strong> Not set
                            </p>
                            <p class="text-sm text-blue-800">
                                <strong>Current Term:</strong> Not set
                            </p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection
