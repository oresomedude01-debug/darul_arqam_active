@extends('layouts.spa')

@section('title', $userProfile->full_name . ' - Teacher Profile')

@section('breadcrumb')
    <span class="text-gray-400">Teachers</span>
    <span class="text-gray-400">/</span>
    <a href="{{ route('teachers.index') }}" class="text-primary-600 hover:text-primary-700">All Teachers</a>
    <span class="text-gray-400">/</span>
    <span class="font-semibold text-gray-900">{{ $userProfile->full_name }}</span>
@endsection

@section('content')
<div class="space-y-6">
    <!-- Header Section -->
    <div class="card">
        <div class="card-body">
            <div class="flex flex-col md:flex-row items-start md:items-center gap-6">
                <!-- Profile Picture -->
                <div class="flex-shrink-0">
                    @if($userProfile->profile_picture)
                        <img src="{{ asset('storage/' . $userProfile->profile_picture) }}" alt="{{ $userProfile->full_name }}" class="w-32 h-32 rounded-full object-cover shadow-lg">
                    @else
                        <div class="w-32 h-32 rounded-full bg-gradient-to-br from-primary-500 to-primary-600 flex items-center justify-center text-white font-bold text-4xl shadow-lg">
                            {{ substr($userProfile->first_name, 0, 1) }}{{ substr($userProfile->last_name, 0, 1) }}
                        </div>
                    @endif
                </div>

                <!-- Teacher Info -->
                <div class="flex-1">
                    <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-4">
                        <div>
                            <h1 class="text-3xl font-bold text-gray-900">{{ $userProfile->full_name }}</h1>
                            <div class="flex items-center gap-2 mt-2">
                                @if($userProfile->status === 'active')
                                    <span class="badge badge-success">Active</span>
                                @else
                                    <span class="badge badge-secondary">Inactive</span>
                                @endif

                                @if($userProfile->qualification)
                                    <span class="badge badge-info">{{ $userProfile->qualification }}</span>
                                @endif
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex flex-wrap gap-2">
                            <a href="{{ route('teachers.assign', $userProfile->id) }}" class="btn btn-info">
                                <i class="fas fa-tasks mr-2"></i>Assign Classes
                            </a>
                            <a href="{{ route('teachers.edit', $userProfile->id) }}" class="btn btn-primary">
                                <i class="fas fa-edit mr-2"></i>Edit
                            </a>
                            <form action="{{ route('teachers.destroy', $userProfile->id) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this teacher?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger">
                                    <i class="fas fa-trash mr-2"></i>Delete
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left Column -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Contact Information -->
            <div class="card">
                <div class="card-header">
                    <h3 class="text-lg font-semibold text-gray-900">
                        <i class="fas fa-address-book mr-2 text-primary-600"></i>Contact Information
                    </h3>
                </div>
                <div class="card-body">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm font-semibold text-gray-600 mb-1">Email</p>
                            <p class="text-gray-900">
                                <i class="fas fa-envelope mr-2 text-gray-400"></i>
                                <a href="mailto:{{ $userProfile->user->email }}" class="text-primary-600 hover:text-primary-700">{{ $userProfile->user->email }}</a>
                            </p>
                        </div>

                        <div>
                            <p class="text-sm font-semibold text-gray-600 mb-1">Phone</p>
                            <p class="text-gray-900">
                                <i class="fas fa-phone mr-2 text-gray-400"></i>
                                <a href="tel:{{ $userProfile->phone }}" class="text-primary-600 hover:text-primary-700">{{ $userProfile->phone }}</a>
                            </p>
                        </div>

                        @if($userProfile->address)
                        <div class="md:col-span-2">
                            <p class="text-sm font-semibold text-gray-600 mb-1">Address</p>
                            <p class="text-gray-900">
                                <i class="fas fa-map-marker-alt mr-2 text-gray-400"></i>
                                {{ $userProfile->address }}
                                @if($userProfile->city || $userProfile->state || $userProfile->country)
                                    <br>
                                    <span class="ml-6">{{ implode(', ', array_filter([$userProfile->city, $userProfile->state, $userProfile->country])) }}</span>
                                @endif
                            </p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Teaching Assignments -->
            <div class="card">
                <div class="card-header flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900">
                        <i class="fas fa-chalkboard mr-2 text-primary-600"></i>Teaching Assignments
                    </h3>
                    <a href="{{ route('teachers.assign', $userProfile->id) }}" class="btn btn-sm btn-outline">
                        <i class="fas fa-edit mr-1"></i>Edit Assignments
                    </a>
                </div>
                <div class="card-body">
                    <div class="space-y-4">
                        <!-- Subjects -->
                        <div>
                            <p class="text-sm font-semibold text-gray-600 mb-2">Subjects</p>
                            @if($userProfile->subjects && count($userProfile->subjects) > 0)
                                <div class="flex flex-wrap gap-2">
                                    @foreach($userProfile->subjects as $subject)
                                        <span class="badge badge-success">{{ $subject }}</span>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-gray-500 text-sm italic">No subjects assigned</p>
                            @endif
                        </div>

                        <!-- Classes -->
                        <div>
                            <p class="text-sm font-semibold text-gray-600 mb-2">Classes</p>
                            @if($userProfile->classes && count($userProfile->classes) > 0)
                                <div class="flex flex-wrap gap-2">
                                    @foreach($userProfile->classes as $class)
                                        <span class="badge badge-primary">{{ $class }}</span>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-gray-500 text-sm italic">No classes assigned</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Additional Notes -->
            @if($userProfile->notes)
            <div class="card">
                <div class="card-header">
                    <h3 class="text-lg font-semibold text-gray-900">
                        <i class="fas fa-sticky-note mr-2 text-primary-600"></i>Notes
                    </h3>
                </div>
                <div class="card-body">
                    <p class="text-gray-900 whitespace-pre-wrap">{{ $userProfile->notes }}</p>
                </div>
            </div>
            @endif
        </div>

        <!-- Right Column -->
        <div class="space-y-6">
            <!-- Basic Information -->
            <div class="card">
                <div class="card-header">
                    <h3 class="text-lg font-semibold text-gray-900">
                        <i class="fas fa-info-circle mr-2 text-primary-600"></i>Basic Information
                    </h3>
                </div>
                <div class="card-body space-y-3">
                    <div>
                        <p class="text-sm font-semibold text-gray-600 mb-1">Gender</p>
                        <p class="text-gray-900">{{ ucfirst($userProfile->gender) }}</p>
                    </div>

                    @if($userProfile->date_of_birth)
                    <div>
                        <p class="text-sm font-semibold text-gray-600 mb-1">Date of Birth</p>
                        <p class="text-gray-900">
                            {{ $userProfile->date_of_birth->format('M d, Y') }}
                            <span class="text-gray-500 text-sm">({{ $userProfile->date_of_birth->age }} years old)</span>
                        </p>
                    </div>
                    @endif

                    @if($userProfile->date_joined)
                    <div>
                        <p class="text-sm font-semibold text-gray-600 mb-1">Date Joined</p>
                        <p class="text-gray-900">
                            {{ $userProfile->date_joined->format('M d, Y') }}
                            <span class="text-gray-500 text-sm">({{ $userProfile->date_joined->diffForHumans() }})</span>
                        </p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Quick Stats -->
            <div class="card bg-gradient-to-br from-primary-50 to-blue-50">
                <div class="card-body">
                    <h4 class="text-sm font-semibold text-gray-700 mb-4">Quick Stats</h4>
                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="text-gray-600 text-sm">Subjects</span>
                            <span class="text-2xl font-bold text-primary-600">{{ $userProfile->subjects ? count($userProfile->subjects) : 0 }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-gray-600 text-sm">Classes</span>
                            <span class="text-2xl font-bold text-primary-600">{{ $userProfile->classes ? count($userProfile->classes) : 0 }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-gray-600 text-sm">Status</span>
                            @if($userProfile->status === 'active')
                                <span class="badge badge-success">Active</span>
                            @else
                                <span class="badge badge-secondary">Inactive</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- System Information -->
            <div class="card">
                <div class="card-header">
                    <h3 class="text-lg font-semibold text-gray-900">
                        <i class="fas fa-database mr-2 text-primary-600"></i>System Information
                    </h3>
                </div>
                <div class="card-body space-y-3">
                    <div>
                        <p class="text-sm font-semibold text-gray-600 mb-1">Created</p>
                        <p class="text-gray-900 text-sm">{{ $userProfile->created_at->format('M d, Y \a\t h:i A') }}</p>
                    </div>

                    <div>
                        <p class="text-sm font-semibold text-gray-600 mb-1">Last Updated</p>
                        <p class="text-gray-900 text-sm">{{ $userProfile->updated_at->format('M d, Y \a\t h:i A') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Back Button -->
    <div class="flex items-center justify-between">
        <a href="{{ route('teachers.index') }}" class="btn btn-outline">
            <i class="fas fa-arrow-left mr-2"></i>Back to Teachers List
        </a>
    </div>
</div>
@endsection
