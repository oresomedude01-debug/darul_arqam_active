@extends('layouts.spa')

@section('title', 'Student Profile')

@section('breadcrumb')
    <span class="text-gray-400">Students</span>
    <span class="text-gray-400">/</span>
    <a href="{{ route('students.index') }}" class="text-gray-400 hover:text-gray-600">All Students</a>
    <span class="text-gray-400">/</span>
    <span class="font-semibold text-gray-900">{{ $student->full_name }}</span>
@endsection

@section('content')
<div class="max-w-6xl mx-auto space-y-6">
    <!-- Success Message -->
    @if(session('success'))
    <div class="alert alert-success fade-in">
        <i class="fas fa-check-circle text-xl"></i>
        <p>{{ session('success') }}</p>
    </div>
    @endif

    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Student Profile</h1>
            <p class="text-gray-600 mt-1">Complete information about {{$student->full_name }}</p>
        </div>
        <div class="flex space-x-3">
            <a href="{{ route('students.print', $student->id) }}" target="_blank" class="btn btn-outline">
                <i class="fas fa-print mr-2"></i>
                Print
            </a>
            <a href="{{ route('students.edit', $student->id) }}" class="btn btn-primary">
                <i class="fas fa-edit mr-2"></i>
                Edit Profile
            </a>
            <a href="{{ route('students.index') }}" class="btn btn-outline">
                <i class="fas fa-arrow-left mr-2"></i>
                Back
            </a>
        </div>
    </div>

    <!-- Student Header Card -->
    <div class="card">
        <div class="card-body">
            <div class="flex flex-col md:flex-row items-start md:items-center space-y-4 md:space-y-0 md:space-x-6">
                <!-- Photo -->
                <div class="flex-shrink-0">
                    <div class="w-32 h-32 rounded-full {{ $student->gender === 'male' ? 'bg-blue-100' : 'bg-pink-100' }} flex items-center justify-center overflow-hidden">
                        @if($student->photo_path)
                            <img src="{{ Storage::url($student->photo_path) }}"
                                 alt="{{ $student->full_name }}"
                                 class="w-full h-full object-cover">
                        @else
                            <span class="text-4xl font-bold {{ $student->gender === 'male' ? 'text-blue-600' : 'text-pink-600' }}">
                                {{ substr($student->first_name, 0, 1) }}{{ substr($student->last_name, 0, 1) }}
                            </span>
                        @endif
                    </div>
                </div>

                <!-- Info -->
                <div class="flex-1">
                    <h2 class="text-3xl font-bold text-gray-900">{{ $student->full_name }}</h2>
                    <p class="text-gray-600 mt-1">{{ $student->admission_number }}</p>

                    <div class="flex flex-wrap items-center gap-3 mt-4">
                        @switch($student->status)
                            @case('active')
                                <span class="badge badge-success text-base px-4 py-2">
                                    <i class="fas fa-check-circle mr-2"></i> Active
                                </span>
                                @break
                            @case('pending')
                                <span class="badge badge-warning text-base px-4 py-2">
                                    <i class="fas fa-clock mr-2"></i> Pending
                                </span>
                                @break
                            @case('graduated')
                                <span class="badge badge-info text-base px-4 py-2">
                                    <i class="fas fa-graduation-cap mr-2"></i> Graduated
                                </span>
                                @break
                            @case('withdrawn')
                                <span class="badge badge-danger text-base px-4 py-2">
                                    <i class="fas fa-times-circle mr-2"></i> Withdrawn
                                </span>
                                @break
                        @endswitch

                        @if($student->class_level)
                            <span class="badge badge-primary text-base px-4 py-2">
                                <i class="fas fa-book mr-2"></i> {{ $student->class_level }}
                                @if($student->section)
                                    - Section {{ $student->section }}
                                @endif
                            </span>
                        @endif

                        <span class="badge badge-secondary text-base px-4 py-2">
                            <i class="fas fa-{{ $student->gender === 'male' ? 'mars' : 'venus' }} mr-2"></i>
                            {{ ucfirst($student->gender) }}
                        </span>
                    </div>

                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-6">
                        <div>
                            <p class="text-xs text-gray-500">Age</p>
                            <p class="text-sm font-medium text-gray-900">{{ $student->age }} years</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500">Admission Date</p>
                            <p class="text-sm font-medium text-gray-900">{{ $student->admission_date->format('M d, Y') }}</p>
                        </div>
                        @if($student->session_year)
                        <div>
                            <p class="text-xs text-gray-500">Session</p>
                            <p class="text-sm font-medium text-gray-900">{{ $student->session_year }}</p>
                        </div>
                        @endif
                        @if($student->roll_number)
                        <div>
                            <p class="text-xs text-gray-500">Roll Number</p>
                            <p class="text-sm font-medium text-gray-900">{{ $student->roll_number }}</p>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex flex-col space-y-2">
                    <form method="POST" action="{{ route('students.update-status', $student->id) }}" x-data="{ status: '{{ $student->status }}' }">
                        @csrf
                        @method('PUT')
                        <select name="status" x-model="status" @change="$el.form.submit()" class="form-select text-sm">
                            <option value="pending">Pending</option>
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                            <option value="graduated">Graduated</option>
                            <option value="withdrawn">Withdrawn</option>
                            <option value="suspended">Suspended</option>
                        </select>
                    </form>
                    <button class="btn btn-sm btn-outline">
                        <i class="fas fa-print mr-2"></i> Print
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left Column -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Personal Information -->
            <div class="card">
                <div class="card-header">
                    <h3 class="text-lg font-semibold text-gray-900">
                        <i class="fas fa-user mr-2 text-primary-600"></i>
                        Personal Information
                    </h3>
                </div>
                <div class="card-body">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <p class="text-sm text-gray-600">Full Name</p>
                            <p class="text-gray-900 font-medium mt-1">{{ $student->full_name }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Date of Birth</p>
                            <p class="text-gray-900 font-medium mt-1">
                                {{ $student->date_of_birth->format('F d, Y') }}
                                <span class="text-xs text-gray-500">({{ $student->age }} years old)</span>
                            </p>
                        </div>
                        @if($student->nationality)
                        <div>
                            <p class="text-sm text-gray-600">Nationality</p>
                            <p class="text-gray-900 font-medium mt-1">{{ $student->nationality }}</p>
                        </div>
                        @endif
                        @if($student->blood_group)
                        <div>
                            <p class="text-sm text-gray-600">Blood Group</p>
                            <p class="text-gray-900 font-medium mt-1">{{ $student->blood_group }}</p>
                        </div>
                        @endif
                        @if($student->address)
                        <div class="md:col-span-2">
                            <p class="text-sm text-gray-600">Address</p>
                            <p class="text-gray-900 mt-1">{{ $student->address }}</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Parent/Guardian Information -->
            <div class="card">
                <div class="card-header">
                    <h3 class="text-lg font-semibold text-gray-900">
                        <i class="fas fa-users mr-2 text-primary-600"></i>
                        Parent/Guardian Information
                    </h3>
                </div>
                <div class="card-body space-y-6">
                    <!-- Primary Guardian (from parent_id relationship) -->
                    @if($student->parent)
                    <div class="pb-6 border-b border-gray-200">
                        <h4 class="font-semibold text-gray-900 mb-4">Primary Guardian</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <p class="text-sm text-gray-600">Name</p>
                                <p class="text-gray-900 font-medium mt-1">{{ $student->parent->name }}</p>
                            </div>
                            @if($student->relationship)
                            <div>
                                <p class="text-sm text-gray-600">Relationship</p>
                                <p class="text-gray-900 font-medium mt-1">{{ ucfirst($student->relationship) }}</p>
                            </div>
                            @endif
                            <div>
                                <p class="text-sm text-gray-600">Email</p>
                                <p class="text-gray-900 font-medium mt-1">
                                    <a href="mailto:{{ $student->parent->email }}" class="text-primary-600 hover:underline">
                                        {{ $student->parent->email }}
                                    </a>
                                </p>
                            </div>
                            @if($student->parent->profile && $student->parent->profile->phone)
                            <div>
                                <p class="text-sm text-gray-600">Phone</p>
                                <p class="text-gray-900 font-medium mt-1">
                                    <a href="tel:{{ $student->parent->profile->phone }}" class="text-primary-600 hover:underline">
                                        {{ $student->parent->profile->phone }}
                                    </a>
                                </p>
                            </div>
                            @endif
                            @if($student->parent->profile && $student->parent->profile->occupation)
                            <div>
                                <p class="text-sm text-gray-600">Occupation</p>
                                <p class="text-gray-900 mt-1">{{ $student->parent->profile->occupation }}</p>
                            </div>
                            @endif
                        </div>
                    </div>
                    @else
                    <div class="text-center py-8">
                        <p class="text-gray-500">
                            <i class="fas fa-info-circle mr-2"></i>
                            No guardian/parent information on file
                        </p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Previous School Information -->
            @if($student->previous_school)
            <div class="card">
                <div class="card-header">
                    <h3 class="text-lg font-semibold text-gray-900">
                        <i class="fas fa-school mr-2 text-primary-600"></i>
                        Previous School Information
                    </h3>
                </div>
                <div class="card-body">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="md:col-span-2">
                            <p class="text-sm text-gray-600">School Name</p>
                            <p class="text-gray-900 font-medium mt-1">{{ $student->previous_school }}</p>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Health & Allergy Information -->
            <div class="card">
                <div class="card-header">
                    <h3 class="text-lg font-semibold text-gray-900">
                        <i class="fas fa-heartbeat mr-2 text-primary-600"></i>
                        Health & Medical Information
                    </h3>
                </div>
                <div class="card-body space-y-4">
                    @if($student->allergies)
                    <div>
                        <p class="text-sm text-gray-600 mb-2">Known Allergies</p>
                        <div class="flex flex-wrap gap-2">
                            @forelse((array)$student->allergies as $allergy)
                                @if($allergy)
                                <span class="badge badge-danger">
                                    <i class="fas fa-exclamation-triangle mr-1"></i> {{ $allergy }}
                                </span>
                                @endif
                            @empty
                                <p class="text-sm text-gray-500 italic">No allergies recorded</p>
                            @endforelse
                        </div>
                    </div>
                    @endif

                    @if($student->medical_conditions)
                    <div>
                        <p class="text-sm text-gray-600">Medical Conditions</p>
                        <p class="text-gray-900 mt-1">{{ $student->medical_conditions }}</p>
                    </div>
                    @endif

                    @if($student->medications)
                    <div>
                        <p class="text-sm text-gray-600">Medications</p>
                        <p class="text-gray-900 mt-1">{{ $student->medications }}</p>
                    </div>
                    @endif

                    @if($student->special_needs)
                    <div>
                        <p class="text-sm text-gray-600">Special Needs/Accommodations</p>
                        <p class="text-gray-900 mt-1">{{ $student->special_needs }}</p>
                    </div>
                    @endif

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm text-gray-600">Emergency Medical Consent</p>
                            <p class="mt-1">
                                @if($student->emergency_medical_consent)
                                    <span class="badge badge-success">
                                        <i class="fas fa-check mr-1"></i> Granted
                                    </span>
                                @else
                                    <span class="badge badge-danger">
                                        <i class="fas fa-times mr-1"></i> Not Granted
                                    </span>
                                @endif
                            </p>
                        </div>
                    </div>

                    @if(!$student->allergies && !$student->medical_conditions && !$student->emergency_contact_name)
                    <p class="text-gray-500 text-center py-4">No medical information recorded</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Right Column -->
        <div class="space-y-6">
            <!-- Quick Actions -->
            <div class="card">
                <div class="card-header">
                    <h3 class="text-lg font-semibold text-gray-900">Quick Actions</h3>
                </div>
                <div class="card-body space-y-3">
                    <a href="{{ route('students.edit', $student->id) }}"
                       class="btn btn-primary w-full justify-center">
                        <i class="fas fa-edit mr-2"></i>
                        Edit Profile
                    </a>
                    <form action="{{ route('billing.generate-individual-bill') }}" method="POST" style="display: inline-block; width: 100%;">
                        @csrf
                        <input type="hidden" name="student_id" value="{{ $student->id }}">
                        <button type="submit" class="btn btn-outline w-full justify-center">
                            <i class="fas fa-file-invoice-dollar mr-2"></i>
                            Generate Bill
                        </button>
                    </form>
                    <button class="btn btn-outline w-full justify-center">
                        <i class="fas fa-file-alt mr-2"></i>
                        Generate Report
                    </button>
                    <button class="btn btn-outline w-full justify-center">
                        <i class="fas fa-envelope mr-2"></i>
                        Send Message
                    </button>
                    <button class="btn btn-outline w-full justify-center">
                        <i class="fas fa-print mr-2"></i>
                        Print Profile
                    </button>
                </div>
            </div>

            <!-- Academic Information -->
            <div class="card">
                <div class="card-header">
                    <h3 class="text-lg font-semibold text-gray-900">Academic Details</h3>
                </div>
                <div class="card-body space-y-4">
                    <div>
                        <p class="text-sm text-gray-600">Admission Number</p>
                        <p class="text-gray-900 font-mono font-medium mt-1">{{ $student->admission_number }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Session Year</p>
                        <p class="text-gray-900 font-medium mt-1">{{ $student->session_year ?? 'Not assigned' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Class Level</p>
                        <p class="text-gray-900 font-medium mt-1">{{ $student->class_level ?? 'Not assigned' }}</p>
                    </div>
                    @if($student->section)
                    <div>
                        <p class="text-sm text-gray-600">Section</p>
                        <p class="text-gray-900 font-medium mt-1">{{ $student->section }}</p>
                    </div>
                    @endif
                    @if($student->roll_number)
                    <div>
                        <p class="text-sm text-gray-600">Roll Number</p>
                        <p class="text-gray-900 font-medium mt-1">{{ $student->roll_number }}</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Enrollment Details -->
            <div class="card">
                <div class="card-header">
                    <h3 class="text-lg font-semibold text-gray-900">Enrollment Details</h3>
                </div>
                <div class="card-body space-y-4">
                    <div>
                        <p class="text-sm text-gray-600">Admission Date</p>
                        <p class="text-gray-900 font-medium mt-1">{{ $student->admission_date->format('F d, Y') }}</p>
                        <p class="text-xs text-gray-500">{{ $student->admission_date->diffForHumans() }}</p>
                    </div>
                    @if($student->registrationToken)
                    <div>
                        <p class="text-sm text-gray-600">Registration Token</p>
                        <p class="text-gray-900 font-mono text-sm mt-1">
                            <a href="{{ route('tokens.show', $student->registrationToken->id) }}"
                               class="text-primary-600 hover:underline">
                                {{ $student->registrationToken->token_code }}
                            </a>
                        </p>
                    </div>
                    @endif
                    <div>
                        <p class="text-sm text-gray-600">Created</p>
                        <p class="text-gray-900 mt-1">{{ $student->created_at->format('M d, Y h:i A') }}</p>
                    </div>
                    @if($student->updated_at != $student->created_at)
                    <div>
                        <p class="text-sm text-gray-600">Last Updated</p>
                        <p class="text-gray-900 mt-1">{{ $student->updated_at->format('M d, Y h:i A') }}</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Outstanding Payments -->
            @php
                $outstandingBills = \App\Models\StudentBill::where('student_id', $student->id)
                    ->whereIn('status', ['pending', 'partial', 'overdue'])
                    ->get();
                $totalOutstanding = $outstandingBills->sum('balance_due');
                $overdueCount = $outstandingBills->where('status', 'overdue')->count();
            @endphp
            <div class="card {{ $totalOutstanding > 0 ? 'border-l-4 border-l-red-500' : '' }}">
                <div class="card-header">
                    <h3 class="text-lg font-semibold text-gray-900">
                        <i class="fas fa-credit-card mr-2 {{ $totalOutstanding > 0 ? 'text-red-600' : 'text-green-600' }}"></i>
                        Outstanding Payments
                    </h3>
                </div>
                <div class="card-body space-y-4">
                    @if($totalOutstanding > 0)
                        <div class="bg-red-50 rounded-lg p-4 border border-red-100">
                            <p class="text-sm text-red-600 font-medium mb-1">Total Outstanding Balance</p>
                            <p class="text-2xl font-bold text-red-700">₦{{ number_format($totalOutstanding, 2) }}</p>
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <p class="text-xs text-gray-500">Pending Bills</p>
                                <p class="text-lg font-semibold text-gray-900">{{ $outstandingBills->where('status', 'pending')->count() }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500">Partial Bills</p>
                                <p class="text-lg font-semibold text-gray-900">{{ $outstandingBills->where('status', 'partial')->count() }}</p>
                            </div>
                        </div>

                        @if($overdueCount > 0)
                        <div class="bg-orange-50 rounded-lg p-3 border border-orange-100">
                            <p class="text-sm text-orange-700">
                                <i class="fas fa-exclamation-triangle mr-2"></i>
                                <strong>{{ $overdueCount }}</strong> overdue {{ $overdueCount === 1 ? 'bill' : 'bills' }}
                            </p>
                        </div>
                        @endif

                        <div class="pt-2">
                            <a href="{{ route('billing.student-bills', $student->id) }}" class="text-primary-600 hover:text-primary-700 text-sm font-medium">
                                <i class="fas fa-eye mr-1"></i>
                                View All Bills
                            </a>
                        </div>
                    @else
                        <div class="bg-green-50 rounded-lg p-4 border border-green-100 text-center">
                            <p class="text-green-700 font-medium">
                                <i class="fas fa-check-circle mr-2"></i>
                                No outstanding payments
                            </p>
                        </div>
                    @endif
                </div>
            </div>

            @if($student->notes)
            <!-- Notes -->
            <div class="card">
                <div class="card-header">
                    <h3 class="text-lg font-semibold text-gray-900">
                        <i class="fas fa-sticky-note mr-2 text-primary-600"></i>
                        Notes
                    </h3>
                </div>
                <div class="card-body">
                    <p class="text-gray-700 text-sm">{{ $student->notes }}</p>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Add any student profile specific scripts here
</script>
@endpush
@endsection
