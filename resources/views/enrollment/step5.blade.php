@extends('layouts.public')

@section('title', 'Review & Confirm - Step 5 of 5')

@section('content')
<div class="max-w-4xl mx-auto">
    <!-- Progress Stepper -->
    @include('enrollment._stepper', [
        'currentStep' => 5,
        'stepTitle' => 'Review & Confirm',
        'stepDescription' => 'Review all information before submitting'
    ])

    <!-- Header -->
    <div class="card bg-gradient-to-r from-green-50 to-emerald-50 border-l-4 border-green-500 mb-6">
        <div class="card-body">
            <div class="flex items-center space-x-3">
                <div class="flex-shrink-0 w-12 h-12 bg-green-600 text-white rounded-full flex items-center justify-center">
                    <i class="fas fa-check-circle text-xl"></i>
                </div>
                <div>
                    <h2 class="text-xl font-bold text-gray-900">Review & Confirm</h2>
                    <p class="text-gray-700 text-sm">Please review all information before submitting your enrollment application</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Form -->
    <form action="{{ route('enrollment.submit') }}" method="POST">
        @csrf

        {{-- Display validation errors --}}
        @if ($errors->any())
        <div class="mb-8">
            <div class="bg-red-50 border-l-4 border-red-500 rounded-lg p-6 shadow-soft">
                <div class="flex items-start gap-4">
                    <div class="flex-shrink-0">
                        <i class="fa-solid fa-circle-exclamation text-red-600 text-2xl mt-1"></i>
                    </div>
                    <div class="flex-1">
                        <h3 class="text-lg font-bold text-red-900 mb-3">Please correct the following issues:</h3>
                        <ul class="space-y-2">
                            @foreach ($errors->all() as $error)
                                <li class="text-red-700 font-medium flex items-start gap-2">
                                    <span class="text-red-500 mt-1">•</span>
                                    <span>{{ $error }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Student Information -->
        <div class="card mb-6">
            <div class="card-header flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-900">
                    <i class="fas fa-user mr-2 text-primary-600"></i>
                    Student Information
                </h3>
                <a href="{{ route('enrollment.step1') }}" class="btn btn-sm btn-outline">
                    <i class="fas fa-edit mr-1"></i>Edit
                </a>
            </div>
            <div class="card-body">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="md:col-span-2 flex items-center space-x-4 pb-4 border-b border-gray-200">
                        @if(isset($data['photo_path']) && $data['photo_path'])
                            <img src="{{ asset('storage/' . $data['photo_path']) }}"
                                 alt="Student Photo"
                                 class="w-20 h-20 rounded-full object-cover border-2 border-primary-200">
                        @else
                            <div class="w-20 h-20 rounded-full bg-gray-200 flex items-center justify-center">
                                <i class="fas fa-user text-gray-400 text-2xl"></i>
                            </div>
                        @endif
                        <div>
                            <p class="text-xl font-bold text-gray-900">
                                {{ $data['first_name'] ?? '' }}
                                {{ $data['middle_name'] ?? '' }}
                                {{ $data['last_name'] ?? '' }}
                            </p>
                            <div class="flex items-center gap-3 mt-1">
                                <span class="badge badge-primary">{{ ucfirst($data['gender'] ?? '') }}</span>
                                @if(isset($data['date_of_birth']))
                                    <span class="text-sm text-gray-600">
                                        {{ \Carbon\Carbon::parse($data['date_of_birth'])->format('M d, Y') }}
                                        ({{ \Carbon\Carbon::parse($data['date_of_birth'])->age }} years old)
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>

                    @if(isset($data['nationality']))
                    <div>
                        <p class="text-sm text-gray-600">Nationality</p>
                        <p class="text-gray-900 font-medium mt-1">{{ $data['nationality'] }}</p>
                    </div>
                    @endif

                    @if(isset($data['blood_group']))
                    <div>
                        <p class="text-sm text-gray-600">Blood Group</p>
                        <p class="text-gray-900 font-medium mt-1">{{ $data['blood_group'] }}</p>
                    </div>
                    @endif

                    @if(isset($data['religion']))
                    <div>
                        <p class="text-sm text-gray-600">Religion</p>
                        <p class="text-gray-900 font-medium mt-1">{{ $data['religion'] }}</p>
                    </div>
                    @endif

                    @if(isset($data['place_of_birth']))
                    <div>
                        <p class="text-sm text-gray-600">Place of Birth</p>
                        <p class="text-gray-900 font-medium mt-1">{{ $data['place_of_birth'] }}</p>
                    </div>
                    @endif

                    @if(isset($data['email']) || isset($data['phone']))
                    <div class="md:col-span-2">
                        <p class="text-sm text-gray-600 mb-2">Contact Information</p>
                        <div class="flex flex-wrap gap-3">
                            @if(isset($data['email']))
                                <span class="inline-flex items-center text-sm">
                                    <i class="fas fa-envelope text-gray-400 mr-2"></i>
                                    {{ $data['email'] }}
                                </span>
                            @endif
                            @if(isset($data['phone']))
                                <span class="inline-flex items-center text-sm">
                                    <i class="fas fa-phone text-gray-400 mr-2"></i>
                                    {{ $data['phone'] }}
                                </span>
                            @endif
                        </div>
                    </div>
                    @endif

                    @if(isset($data['address']))
                    <div class="md:col-span-2">
                        <p class="text-sm text-gray-600">Residential Address</p>
                        <p class="text-gray-900 mt-1">{{ $data['address'] }}</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Previous School Information -->
        @if(isset($data['previous_school_name']) && $data['previous_school_name'])
        <div class="card mb-6">
            <div class="card-header flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-900">
                    <i class="fas fa-school mr-2 text-primary-600"></i>
                    Previous School Information
                </h3>
                <a href="{{ route('enrollment.step2') }}" class="btn btn-sm btn-outline">
                    <i class="fas fa-edit mr-1"></i>Edit
                </a>
            </div>
            <div class="card-body">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="md:col-span-2">
                        <p class="text-sm text-gray-600">School Name</p>
                        <p class="text-gray-900 font-medium mt-1">{{ $data['previous_school_name'] }}</p>
                    </div>
                    @if(isset($data['previous_school_address']))
                    <div class="md:col-span-2">
                        <p class="text-sm text-gray-600">School Address</p>
                        <p class="text-gray-900 mt-1">{{ $data['previous_school_address'] }}</p>
                    </div>
                    @endif
                    @if(isset($data['previous_school_grade']))
                    <div>
                        <p class="text-sm text-gray-600">Last Grade/Class Attended</p>
                        <p class="text-gray-900 font-medium mt-1">{{ $data['previous_school_grade'] }}</p>
                    </div>
                    @endif
                    @if(isset($data['previous_school_year']))
                    <div>
                        <p class="text-sm text-gray-600">Year Left</p>
                        <p class="text-gray-900 font-medium mt-1">{{ $data['previous_school_year'] }}</p>
                    </div>
                    @endif
                    @if(isset($data['previous_school_reason']))
                    <div class="md:col-span-2">
                        <p class="text-sm text-gray-600">Reason for Leaving</p>
                        <p class="text-gray-900 mt-1">{{ $data['previous_school_reason'] }}</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
        @endif

        <!-- Health Information -->
        <div class="card mb-6">
            <div class="card-header flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-900">
                    <i class="fas fa-heartbeat mr-2 text-primary-600"></i>
                    Health & Medical Information
                </h3>
                <a href="{{ route('enrollment.step3') }}" class="btn btn-sm btn-outline">
                    <i class="fas fa-edit mr-1"></i>Edit
                </a>
            </div>
            <div class="card-body">
                <div class="space-y-4">
                    @if(isset($data['allergies']) && count($data['allergies']) > 0)
                    <div>
                        <p class="text-sm text-gray-600 mb-2">Known Allergies</p>
                        <div class="flex flex-wrap gap-2">
                            @foreach($data['allergies'] as $allergy)
                                <span class="badge badge-danger">
                                    <i class="fas fa-exclamation-triangle mr-1"></i>{{ $allergy }}
                                </span>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    @if(isset($data['medical_conditions']) && $data['medical_conditions'])
                    <div>
                        <p class="text-sm text-gray-600">Medical Conditions</p>
                        <p class="text-gray-900 mt-1">{{ $data['medical_conditions'] }}</p>
                    </div>
                    @endif

                    @if(isset($data['medications']) && $data['medications'])
                    <div>
                        <p class="text-sm text-gray-600">Current Medications</p>
                        <p class="text-gray-900 mt-1">{{ $data['medications'] }}</p>
                    </div>
                    @endif

                    @if(isset($data['special_needs']) && $data['special_needs'])
                    <div>
                        <p class="text-sm text-gray-600">Special Needs/Accommodations</p>
                        <p class="text-gray-900 mt-1">{{ $data['special_needs'] }}</p>
                    </div>
                    @endif

                    <div>
                        <p class="text-sm text-gray-600">Emergency Medical Consent</p>
                        <p class="mt-1">
                            @if(isset($data['emergency_medical_consent']) && $data['emergency_medical_consent'])
                                <span class="badge badge-success">
                                    <i class="fas fa-check mr-1"></i>Granted
                                </span>
                            @else
                                <span class="badge badge-secondary">
                                    <i class="fas fa-times mr-1"></i>Not Granted
                                </span>
                            @endif
                        </p>
                    </div>

                    @if(
                        (!isset($data['allergies']) || count($data['allergies']) == 0) &&
                        (!isset($data['medical_conditions']) || !$data['medical_conditions']) &&
                        (!isset($data['medications']) || !$data['medications']) &&
                        (!isset($data['special_needs']) || !$data['special_needs'])
                    )
                    <p class="text-gray-500 text-sm italic">No medical information provided</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Parent/Guardian Information -->
        <div class="card mb-6">
            <div class="card-header flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-900">
                    <i class="fas fa-users mr-2 text-primary-600"></i>
                    Parent/Guardian Information
                </h3>
                <a href="{{ route('enrollment.step4') }}" class="btn btn-sm btn-outline">
                    <i class="fas fa-edit mr-1"></i>Edit
                </a>
            </div>
            <div class="card-body space-y-6">
                <!-- Primary Contact -->
                @if(isset($data['parent1_name']))
                <div class="pb-6 border-b border-gray-200">
                    <h4 class="font-semibold text-gray-900 mb-4">
                        <span class="bg-primary-100 text-primary-700 px-3 py-1 rounded-full text-sm">Primary Contact</span>
                    </h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm text-gray-600">Name</p>
                            <p class="text-gray-900 font-medium mt-1">{{ $data['parent1_name'] }}</p>
                        </div>
                        @if(isset($data['parent1_relationship']))
                        <div>
                            <p class="text-sm text-gray-600">Relationship</p>
                            <p class="text-gray-900 font-medium mt-1">{{ $data['parent1_relationship'] }}</p>
                        </div>
                        @endif
                        @if(isset($data['parent1_phone']))
                        <div>
                            <p class="text-sm text-gray-600">Phone</p>
                            <p class="text-gray-900 font-medium mt-1">{{ $data['parent1_phone'] }}</p>
                        </div>
                        @endif
                        @if(isset($data['parent1_email']))
                        <div>
                            <p class="text-sm text-gray-600">Email</p>
                            <p class="text-gray-900 font-medium mt-1">{{ $data['parent1_email'] }}</p>
                        </div>
                        @endif
                        @if(isset($data['parent1_occupation']))
                        <div class="md:col-span-2">
                            <p class="text-sm text-gray-600">Occupation</p>
                            <p class="text-gray-900 mt-1">{{ $data['parent1_occupation'] }}</p>
                        </div>
                        @endif
                    </div>
                </div>
                @endif

                <!-- Secondary Contact -->
                @if(isset($data['parent2_name']) && $data['parent2_name'])
                <div>
                    <h4 class="font-semibold text-gray-900 mb-4">
                        <span class="bg-gray-100 text-gray-700 px-3 py-1 rounded-full text-sm">Secondary Contact</span>
                    </h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm text-gray-600">Name</p>
                            <p class="text-gray-900 font-medium mt-1">{{ $data['parent2_name'] }}</p>
                        </div>
                        @if(isset($data['parent2_relationship']))
                        <div>
                            <p class="text-sm text-gray-600">Relationship</p>
                            <p class="text-gray-900 font-medium mt-1">{{ $data['parent2_relationship'] }}</p>
                        </div>
                        @endif
                        @if(isset($data['parent2_phone']))
                        <div>
                            <p class="text-sm text-gray-600">Phone</p>
                            <p class="text-gray-900 font-medium mt-1">{{ $data['parent2_phone'] }}</p>
                        </div>
                        @endif
                        @if(isset($data['parent2_email']))
                        <div>
                            <p class="text-sm text-gray-600">Email</p>
                            <p class="text-gray-900 font-medium mt-1">{{ $data['parent2_email'] }}</p>
                        </div>
                        @endif
                        @if(isset($data['parent2_occupation']))
                        <div class="md:col-span-2">
                            <p class="text-sm text-gray-600">Occupation</p>
                            <p class="text-gray-900 mt-1">{{ $data['parent2_occupation'] }}</p>
                        </div>
                        @endif
                    </div>
                </div>
                @endif
            </div>
        </div>

        <!-- Terms & Conditions -->
        <div class="card mb-6">
            <div class="card-body">
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-4">
                    <div class="flex items-start space-x-3">
                        <i class="fas fa-info-circle text-yellow-600 text-xl mt-0.5"></i>
                        <div class="text-sm text-yellow-900">
                            <p class="font-semibold mb-2">Important Notice</p>
                            <p>By submitting this enrollment application, you confirm that:</p>
                            <ul class="list-disc list-inside mt-2 space-y-1 text-yellow-800">
                                <li>All information provided is accurate and truthful</li>
                                <li>You agree to the school's terms and conditions</li>
                                <li>You will provide required documents upon request</li>
                                <li>You understand the school's admission policies</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <label class="flex items-start space-x-3 cursor-pointer">
                    <input type="checkbox"
                           name="terms_accepted"
                           class="form-checkbox mt-1"
                           required>
                    <div>
                        <p class="font-medium text-gray-900">
                            I confirm that all information provided is accurate
                            <span class="text-red-500">*</span>
                        </p>
                        <p class="text-sm text-gray-600 mt-1">
                            I understand that providing false information may result in rejection of this application
                        </p>
                    </div>
                </label>
            </div>
        </div>

        <!-- Actions -->
        <div class="flex items-center justify-between">
            <a href="{{ route('enrollment.step4') }}" class="btn btn-outline">
                <i class="fas fa-arrow-left mr-2"></i>Back
            </a>
            <button type="submit" class="btn btn-primary btn-lg">
                <i class="fas fa-check-circle mr-2"></i>
                Submit Enrollment Application
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
    // Auto-scroll to top on page load
    window.scrollTo(0, 0);
</script>
@endpush
@endsection
