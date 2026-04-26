@extends('layouts.spa')

@section('title', $student->full_name)

@section('breadcrumb')
    <span class="text-gray-400">Teaching</span>
    <span class="text-gray-400">/</span>
    <a href="{{ route('teacher.class.students') }}" class="text-gray-400 hover:text-gray-600">My Class Students</a>
    <span class="text-gray-400">/</span>
    <span class="font-semibold text-gray-900">{{ $student->full_name }}</span>
@endsection

@section('content')
<div class="space-y-8">
    <!-- Hero Header Section -->
    <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-xl shadow-sm border border-blue-100 p-8">
        <div class="flex items-start justify-between">
            <div class="flex items-start gap-6">
                <!-- Student Avatar -->
                <div class="flex-shrink-0 relative">
                    <div class="w-32 h-32 rounded-full {{ $student->gender === 'male' ? 'bg-gradient-to-br from-blue-100 to-blue-200' : 'bg-gradient-to-br from-pink-100 to-pink-200' }} flex items-center justify-center overflow-hidden shadow-lg ring-4 ring-white">
                        @if($student->photo)
                            <img src="{{ Storage::url($student->photo) }}"
                                 alt="{{ $student->full_name }}"
                                 class="w-full h-full object-cover">
                        @else
                            <span class="text-4xl font-bold {{ $student->gender === 'male' ? 'text-blue-600' : 'text-pink-600' }}">
                                {{ substr($student->first_name, 0, 1) }}{{ substr($student->last_name, 0, 1) }}
                            </span>
                        @endif
                    </div>
                    <div class="absolute -bottom-2 -right-2 bg-{{ $student->status === 'active' ? 'green' : 'yellow' }}-500 rounded-full w-8 h-8 flex items-center justify-center text-white text-xs font-bold shadow-md">
                        <i class="fas fa-{{ $student->status === 'active' ? 'check' : 'hourglass-half' }}"></i>
                    </div>
                </div>

                <!-- Student Info -->
                <div class="pt-2">
                    <h1 class="text-4xl font-bold text-gray-900 mb-2">{{ $student->full_name }}</h1>
                    <p class="text-gray-600 text-lg mb-4">
                        <i class="fas fa-id-badge text-blue-600 mr-2"></i>
                        <span class="font-medium">{{ $student->admission_number }}</span>
                    </p>
                    <div class="flex gap-2 flex-wrap">
                        @if($student->schoolClass)
                            <span class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-white text-blue-700 font-semibold shadow-sm border border-blue-200">
                                <i class="fas fa-book"></i>
                                {{ $student->schoolClass->name }}
                            </span>
                        @endif
                        <span class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-white {{ $student->status === 'active' ? 'text-green-700 border border-green-200' : ($student->status === 'pending' ? 'text-yellow-700 border border-yellow-200' : 'text-red-700 border border-red-200') }} font-semibold shadow-sm">
                            <i class="fas fa-{{ $student->status === 'active' ? 'check-circle' : ($student->status === 'pending' ? 'clock' : 'ban') }}"></i>
                            {{ ucfirst($student->status) }}
                        </span>
                    </div>
                </div>
            </div>
            <div>
                <a href="{{ route('teacher.class.students') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-white text-gray-700 rounded-lg shadow-md border border-gray-200 hover:shadow-lg hover:border-gray-300 transition font-semibold">
                    <i class="fas fa-arrow-left"></i>
                    Back to Class
                </a>
            </div>
        </div>
    </div>

    <!-- Information Cards Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Personal Details -->
        <div class="card shadow-sm hover:shadow-md transition border border-gray-200">
            <div class="card-header bg-gradient-to-r from-blue-50 to-blue-25 border-b border-blue-100">
                <h2 class="text-lg font-semibold text-gray-900">
                    <span class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-blue-100 text-blue-600 mr-3">
                        <i class="fas fa-user"></i>
                    </span>
                    Personal Details
                </h2>
            </div>
            <div class="card-body space-y-4">
                <div class="flex justify-between items-center py-2 border-b border-gray-100">
                    <span class="text-gray-600 font-medium">Full Name</span>
                    <span class="text-gray-900 font-semibold">{{ $student->full_name }}</span>
                </div>
                <div class="flex justify-between items-center py-2 border-b border-gray-100">
                    <span class="text-gray-600 font-medium">Gender</span>
                    <span class="text-gray-900 font-semibold capitalize">{{ $student->gender }}</span>
                </div>
                <div class="flex justify-between items-center py-2">
                    <span class="text-gray-600 font-medium">Date of Birth</span>
                    <span class="text-gray-900 font-semibold">
                        @if($student->date_of_birth)
                            {{ $student->date_of_birth->format('M d, Y') }}
                            <span class="text-gray-500 text-sm ml-2">({{ $student->age ?? 'N/A' }} yrs)</span>
                        @else
                            <span class="text-gray-400">-</span>
                        @endif
                    </span>
                </div>
            </div>
        </div>

        <!-- Contact Details -->
        <div class="card shadow-sm hover:shadow-md transition border border-gray-200">
            <div class="card-header bg-gradient-to-r from-green-50 to-green-25 border-b border-green-100">
                <h2 class="text-lg font-semibold text-gray-900">
                    <span class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-green-100 text-green-600 mr-3">
                        <i class="fas fa-phone"></i>
                    </span>
                    Contact Information
                </h2>
            </div>
            <div class="card-body space-y-4">
                <div class="py-2 border-b border-gray-100">
                    <span class="text-gray-600 font-medium block mb-2">Email</span>
                    @if($student->user && $student->user->email)
                        <a href="mailto:{{ $student->user->email }}" class="text-blue-600 hover:text-blue-800 font-semibold break-all">
                            {{ $student->user->email }}
                        </a>
                    @else
                        <span class="text-gray-400">-</span>
                    @endif
                </div>
                <div class="py-2 border-b border-gray-100">
                    <span class="text-gray-600 font-medium block mb-2">Phone</span>
                    @if($student->phone)
                        <a href="tel:{{ $student->phone }}" class="text-blue-600 hover:text-blue-800 font-semibold">
                            {{ $student->phone }}
                        </a>
                    @else
                        <span class="text-gray-400">-</span>
                    @endif
                </div>
                <div class="py-2">
                    <span class="text-gray-600 font-medium block mb-2">Address</span>
                    <span class="text-gray-900">
                        @if($student->address)
                            {{ $student->address }}
                        @else
                            <span class="text-gray-400">-</span>
                        @endif
                    </span>
                </div>
            </div>
        </div>

        <!-- Enrollment Details -->
        <div class="card shadow-sm hover:shadow-md transition border border-gray-200">
            <div class="card-header bg-gradient-to-r from-purple-50 to-purple-25 border-b border-purple-100">
                <h2 class="text-lg font-semibold text-gray-900">
                    <span class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-purple-100 text-purple-600 mr-3">
                        <i class="fas fa-book"></i>
                    </span>
                    Enrollment Details
                </h2>
            </div>
            <div class="card-body space-y-4">
                <div class="flex justify-between items-center py-2 border-b border-gray-100">
                    <span class="text-gray-600 font-medium">Class</span>
                    <span class="text-gray-900 font-semibold">
                        @if($student->schoolClass)
                            {{ $student->schoolClass->name }}
                        @else
                            <span class="text-gray-400">-</span>
                        @endif
                    </span>
                </div>
                <div class="flex justify-between items-center py-2 border-b border-gray-100">
                    <span class="text-gray-600 font-medium">Admission Date</span>
                    <span class="text-gray-900 font-semibold">
                        @if($student->admission_date)
                            {{ $student->admission_date->format('M d, Y') }}
                        @else
                            <span class="text-gray-400">-</span>
                        @endif
                    </span>
                </div>
                <div class="flex justify-between items-center py-2">
                    <span class="text-gray-600 font-medium">Current Status</span>
                    <span class="badge badge-{{ $student->status === 'active' ? 'success' : ($student->status === 'pending' ? 'warning' : 'danger') }}">
                        {{ ucfirst($student->status) }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Additional Information -->
        <div class="card shadow-sm hover:shadow-md transition border border-gray-200">
            <div class="card-header bg-gradient-to-r from-amber-50 to-amber-25 border-b border-amber-100">
                <h2 class="text-lg font-semibold text-gray-900">
                    <span class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-amber-100 text-amber-600 mr-3">
                        <i class="fas fa-info-circle"></i>
                    </span>
                    Additional Information
                </h2>
            </div>
            <div class="card-body space-y-4">
                <div class="flex justify-between items-center py-2 border-b border-gray-100">
                    <span class="text-gray-600 font-medium">Blood Group</span>
                    <span class="text-gray-900 font-semibold">
                        @if($student->blood_group)
                            <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-red-50 text-red-700 border border-red-200">
                                <i class="fas fa-droplet"></i>
                                {{ $student->blood_group }}
                            </span>
                        @else
                            <span class="text-gray-400">-</span>
                        @endif
                    </span>
                </div>
                <div class="flex justify-between items-center py-2">
                    <span class="text-gray-600 font-medium">Nationality</span>
                    <span class="text-gray-900 font-semibold">
                        @if($student->nationality)
                            {{ $student->nationality }}
                        @else
                            <span class="text-gray-400">-</span>
                        @endif
                    </span>
                </div>
            </div>
        </div>

        <!-- Medical Information -->
        <div class="card shadow-sm hover:shadow-md transition border border-gray-200 md:col-span-2">
            <div class="card-header bg-gradient-to-r from-red-50 to-red-25 border-b border-red-100">
                <h2 class="text-lg font-semibold text-gray-900">
                    <span class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-red-100 text-red-600 mr-3">
                        <i class="fas fa-hospital-user"></i>
                    </span>
                    Medical Information
                </h2>
            </div>
            <div class="card-body">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="py-3 px-4 bg-gray-50 rounded-lg border border-gray-200">
                        <span class="text-gray-600 font-medium block mb-2">
                            <i class="fas fa-exclamation-triangle text-red-600 mr-2"></i>
                            Allergies
                        </span>
                        <span class="text-gray-900 font-semibold">
                            @if($student->allergies)
                                @if(is_array($student->allergies))
                                    {{ implode(', ', $student->allergies) }}
                                @else
                                    {{ $student->allergies }}
                                @endif
                            @else
                                <span class="text-gray-400">None</span>
                            @endif
                        </span>
                    </div>
                    <div class="py-3 px-4 bg-gray-50 rounded-lg border border-gray-200">
                        <span class="text-gray-600 font-medium block mb-2">
                            <i class="fas fa-prescription-bottle text-red-600 mr-2"></i>
                            Current Medications
                        </span>
                        <span class="text-gray-900 font-semibold">
                            @if($student->medications)
                                {{ $student->medications }}
                            @else
                                <span class="text-gray-400">None</span>
                            @endif
                        </span>
                    </div>
                    <div class="py-3 px-4 bg-gray-50 rounded-lg border border-gray-200">
                        <span class="text-gray-600 font-medium block mb-2">
                            <i class="fas fa-stethoscope text-red-600 mr-2"></i>
                            Medical Conditions
                        </span>
                        <span class="text-gray-900 font-semibold">
                            @if($student->medical_conditions)
                                {{ $student->medical_conditions }}
                            @else
                                <span class="text-gray-400">None</span>
                            @endif
                        </span>
                    </div>
                    <div class="py-3 px-4 bg-gray-50 rounded-lg border border-gray-200">
                        <span class="text-gray-600 font-medium block mb-2">
                            <i class="fas fa-clipboard-check text-red-600 mr-2"></i>
                            Emergency Medical Consent
                        </span>
                        <span class="badge badge-{{ $student->emergency_medical_consent ? 'success' : 'danger' }}">
                            {{ $student->emergency_medical_consent ? 'Granted' : 'Not Granted' }}
                        </span>
                    </div>
                    <div class="py-3 px-4 bg-gray-50 rounded-lg border border-gray-200 md:col-span-2">
                        <span class="text-gray-600 font-medium block mb-2">
                            <i class="fas fa-accessibility text-red-600 mr-2"></i>
                            Special Needs
                        </span>
                        <span class="text-gray-900 font-semibold">
                            @if($student->special_needs)
                                {{ $student->special_needs }}
                            @else
                                <span class="text-gray-400">None</span>
                            @endif
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Attendance Section -->
    @if($attendance->count() > 0)
    <div class="card shadow-sm border border-gray-200">
        <div class="card-header bg-gradient-to-r from-blue-50 to-cyan-50 border-b border-blue-100">
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold text-gray-900">
                    <span class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-blue-100 text-blue-600 mr-3">
                        <i class="fas fa-calendar-check"></i>
                    </span>
                    Recent Attendance (Last 10 Records)
                </h2>
                <span class="text-sm text-gray-600 bg-white px-3 py-1 rounded-full">
                    {{ $attendance->count() }} records
                </span>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="overflow-x-auto">
                <table class="table">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="text-gray-700 font-semibold">Date</th>
                            <th class="text-gray-700 font-semibold">Status</th>
                            <th class="text-gray-700 font-semibold">Notes</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($attendance as $record)
                        <tr class="border-b border-gray-100 hover:bg-gray-50 transition">
                            <td class="font-semibold text-gray-900">
                                <i class="fas fa-calendar text-blue-600 mr-2"></i>
                                {{ $record->date->format('M d, Y') }}
                            </td>
                            <td>
                                @switch($record->status)
                                    @case('present')
                                        <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-green-50 text-green-700 font-semibold border border-green-200">
                                            <i class="fas fa-check-circle"></i>
                                            Present
                                        </span>
                                        @break
                                    @case('absent')
                                        <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-red-50 text-red-700 font-semibold border border-red-200">
                                            <i class="fas fa-times-circle"></i>
                                            Absent
                                        </span>
                                        @break
                                    @case('late')
                                        <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-yellow-50 text-yellow-700 font-semibold border border-yellow-200">
                                            <i class="fas fa-clock"></i>
                                            Late
                                        </span>
                                        @break
                                    @case('excused')
                                        <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-blue-50 text-blue-700 font-semibold border border-blue-200">
                                            <i class="fas fa-info-circle"></i>
                                            Excused
                                        </span>
                                        @break
                                    @default
                                        <span class="text-gray-600 font-semibold">{{ ucfirst($record->status) }}</span>
                                @endswitch
                            </td>
                            <td class="text-gray-700">
                                @if($record->notes)
                                    <span class="px-3 py-1 rounded-full bg-gray-100 text-gray-700 text-sm">{{ $record->notes }}</span>
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @else
    <div class="bg-blue-50 border-l-4 border-blue-500 p-6 rounded-lg">
        <div class="flex gap-4">
            <div class="flex-shrink-0 text-2xl text-blue-600">
                <i class="fas fa-info-circle"></i>
            </div>
            <div>
                <h3 class="font-semibold text-blue-900 mb-1">No Attendance Records</h3>
                <p class="text-blue-800">No attendance records are available yet for this student.</p>
            </div>
        </div>
    </div>
    @endif
</div>

@endsection
