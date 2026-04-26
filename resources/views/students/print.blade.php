<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Profile - {{ $student->full_name }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            body {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            .no-print {
                display: none !important;
            }
            @page {
                margin: 1cm;
            }
        }
    </style>
</head>
<body class="bg-white">
    <div class="max-w-5xl mx-auto p-8">
        <!-- Print Button -->
        <div class="no-print mb-6 flex justify-end space-x-3">
            <button onclick="window.print()" class="px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700">
                <i class="fas fa-print mr-2"></i>Print Profile
            </button>
            <a href="{{ route('students.show', $student->id) }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">
                <i class="fas fa-arrow-left mr-2"></i>Back
            </a>
        </div>

        <!-- Header with Logo -->
        <div class="border-b-4 border-blue-600 pb-6 mb-6">
            <div class="flex items-start justify-between">
                <div class="flex items-center space-x-4">
                    <div class="w-16 h-16 bg-blue-600 rounded-lg flex items-center justify-center">
                        <svg class="w-10 h-10 text-white" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M10.394 2.08a1 1 0 00-.788 0l-7 3a1 1 0 000 1.84L5.25 8.051a.999.999 0 01.356-.257l4-1.714a1 1 0 11.788 1.838L7.667 9.088l1.94.831a1 1 0 00.787 0l7-3a1 1 0 000-1.838l-7-3zM3.31 9.397L5 10.12v4.102a8.969 8.969 0 00-1.05-.174 1 1 0 01-.89-.89 11.115 11.115 0 01.25-3.762zM9.3 16.573A9.026 9.026 0 007 14.935v-3.957l1.818.78a3 3 0 002.364 0l5.508-2.361a11.026 11.026 0 01.25 3.762 1 1 0 01-.89.89 8.968 8.968 0 00-5.35 2.524 1 1 0 01-1.4 0zM6 18a1 1 0 001-1v-2.065a8.935 8.935 0 00-2-.712V17a1 1 0 001 1z"/>
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900">Darul Arqam School</h1>
                        <p class="text-sm text-gray-600">School Management System</p>
                    </div>
                </div>
                <div class="text-right">
                    <p class="text-sm text-gray-600">Printed On:</p>
                    <p class="text-sm font-medium text-gray-900">{{ date('F d, Y') }}</p>
                </div>
            </div>
        </div>

        <!-- Student Profile Title -->
        <div class="bg-blue-50 border-l-4 border-blue-600 p-4 mb-6">
            <h2 class="text-2xl font-bold text-gray-900">Student Profile</h2>
            <p class="text-sm text-gray-600 mt-1">Complete information for {{ $student->full_name }}</p>
        </div>

        <!-- Photo and Basic Info -->
        <div class="grid grid-cols-12 gap-6 mb-6">
            <div class="col-span-3">
                @if($student->photo_path)
                    <img src="{{ asset('storage/' . $student->photo_path) }}" alt="{{ $student->full_name }}" class="w-full h-auto rounded-lg border-4 border-gray-200">
                @else
                    <div class="w-full aspect-square bg-gray-200 rounded-lg border-4 border-gray-300 flex items-center justify-center">
                        <svg class="w-20 h-20 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                @endif
            </div>
            <div class="col-span-9">
                <div class="grid grid-cols-2 gap-4">
                    <div class="bg-blue-50 p-4 rounded-lg">
                        <p class="text-xs text-gray-600 uppercase font-semibold">Admission Number</p>
                        <p class="text-xl font-bold text-blue-600 mt-1">{{ $student->admission_number }}</p>
                    </div>
                    <div class="bg-green-50 p-4 rounded-lg">
                        <p class="text-xs text-gray-600 uppercase font-semibold">Status</p>
                        <p class="text-xl font-bold text-green-600 mt-1 capitalize">{{ $student->status }}</p>
                    </div>
                    <div class="bg-purple-50 p-4 rounded-lg">
                        <p class="text-xs text-gray-600 uppercase font-semibold">Class</p>
                        <p class="text-xl font-bold text-purple-600 mt-1">{{ $student->class_level }} {{ $student->section ? '- ' . $student->section : '' }}</p>
                    </div>
                    <div class="bg-orange-50 p-4 rounded-lg">
                        <p class="text-xs text-gray-600 uppercase font-semibold">Session Year</p>
                        <p class="text-xl font-bold text-orange-600 mt-1">{{ $student->session_year }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Personal Information -->
        <div class="mb-6">
            <h3 class="text-lg font-bold text-gray-900 mb-4 pb-2 border-b-2 border-gray-300">Personal Information</h3>
            <div class="grid grid-cols-3 gap-x-6 gap-y-4">
                <div>
                    <p class="text-xs text-gray-600 font-semibold">Full Name</p>
                    <p class="text-sm text-gray-900 font-medium">{{ $student->full_name }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-600 font-semibold">Date of Birth</p>
                    <p class="text-sm text-gray-900 font-medium">{{ $student->date_of_birth ? $student->date_of_birth->format('F d, Y') : 'N/A' }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-600 font-semibold">Age</p>
                    <p class="text-sm text-gray-900 font-medium">{{ $student->date_of_birth ? $student->date_of_birth->age . ' years' : 'N/A' }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-600 font-semibold">Gender</p>
                    <p class="text-sm text-gray-900 font-medium capitalize">{{ $student->gender }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-600 font-semibold">Blood Group</p>
                    <p class="text-sm text-gray-900 font-medium">{{ $student->blood_group ?: 'N/A' }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-600 font-semibold">Nationality</p>
                    <p class="text-sm text-gray-900 font-medium">{{ $student->nationality ?: 'N/A' }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-600 font-semibold">Religion</p>
                    <p class="text-sm text-gray-900 font-medium">{{ $student->religion ?: 'N/A' }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-600 font-semibold">Place of Birth</p>
                    <p class="text-sm text-gray-900 font-medium">{{ $student->place_of_birth ?: 'N/A' }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-600 font-semibold">Admission Date</p>
                    <p class="text-sm text-gray-900 font-medium">{{ $student->admission_date ? $student->admission_date->format('F d, Y') : 'N/A' }}</p>
                </div>
                @if($student->address)
                <div class="col-span-3">
                    <p class="text-xs text-gray-600 font-semibold">Address</p>
                    <p class="text-sm text-gray-900 font-medium">{{ $student->address }}</p>
                </div>
                @endif
            </div>
        </div>

        <!-- Contact Information -->
        @if($student->email || $student->phone)
        <div class="mb-6">
            <h3 class="text-lg font-bold text-gray-900 mb-4 pb-2 border-b-2 border-gray-300">Contact Information</h3>
            <div class="grid grid-cols-2 gap-6">
                @if($student->email)
                <div>
                    <p class="text-xs text-gray-600 font-semibold">Email</p>
                    <p class="text-sm text-gray-900 font-medium">{{ $student->email }}</p>
                </div>
                @endif
                @if($student->phone)
                <div>
                    <p class="text-xs text-gray-600 font-semibold">Phone</p>
                    <p class="text-sm text-gray-900 font-medium">{{ $student->phone }}</p>
                </div>
                @endif
            </div>
        </div>
        @endif

        <!-- Parent/Guardian Information -->
        @if($student->parent1_name || $student->parent2_name)
        <div class="mb-6">
            <h3 class="text-lg font-bold text-gray-900 mb-4 pb-2 border-b-2 border-gray-300">Parent/Guardian Information</h3>
            <div class="grid grid-cols-2 gap-6">
                @if($student->parent1_name)
                <div class="border border-gray-200 rounded-lg p-4">
                    <p class="text-xs text-primary-600 font-bold uppercase mb-3">Primary Contact</p>
                    <div class="space-y-2">
                        <div>
                            <p class="text-xs text-gray-600 font-semibold">Name</p>
                            <p class="text-sm text-gray-900 font-medium">{{ $student->parent1_name }}</p>
                        </div>
                        @if($student->parent1_relationship)
                        <div>
                            <p class="text-xs text-gray-600 font-semibold">Relationship</p>
                            <p class="text-sm text-gray-900 font-medium">{{ $student->parent1_relationship }}</p>
                        </div>
                        @endif
                        @if($student->parent1_phone)
                        <div>
                            <p class="text-xs text-gray-600 font-semibold">Phone</p>
                            <p class="text-sm text-gray-900 font-medium">{{ $student->parent1_phone }}</p>
                        </div>
                        @endif
                        @if($student->parent1_email)
                        <div>
                            <p class="text-xs text-gray-600 font-semibold">Email</p>
                            <p class="text-sm text-gray-900 font-medium">{{ $student->parent1_email }}</p>
                        </div>
                        @endif
                        @if($student->parent1_occupation)
                        <div>
                            <p class="text-xs text-gray-600 font-semibold">Occupation</p>
                            <p class="text-sm text-gray-900 font-medium">{{ $student->parent1_occupation }}</p>
                        </div>
                        @endif
                    </div>
                </div>
                @endif

                @if($student->parent2_name)
                <div class="border border-gray-200 rounded-lg p-4">
                    <p class="text-xs text-gray-600 font-bold uppercase mb-3">Secondary Contact</p>
                    <div class="space-y-2">
                        <div>
                            <p class="text-xs text-gray-600 font-semibold">Name</p>
                            <p class="text-sm text-gray-900 font-medium">{{ $student->parent2_name }}</p>
                        </div>
                        @if($student->parent2_relationship)
                        <div>
                            <p class="text-xs text-gray-600 font-semibold">Relationship</p>
                            <p class="text-sm text-gray-900 font-medium">{{ $student->parent2_relationship }}</p>
                        </div>
                        @endif
                        @if($student->parent2_phone)
                        <div>
                            <p class="text-xs text-gray-600 font-semibold">Phone</p>
                            <p class="text-sm text-gray-900 font-medium">{{ $student->parent2_phone }}</p>
                        </div>
                        @endif
                        @if($student->parent2_email)
                        <div>
                            <p class="text-xs text-gray-600 font-semibold">Email</p>
                            <p class="text-sm text-gray-900 font-medium">{{ $student->parent2_email }}</p>
                        </div>
                        @endif
                        @if($student->parent2_occupation)
                        <div>
                            <p class="text-xs text-gray-600 font-semibold">Occupation</p>
                            <p class="text-sm text-gray-900 font-medium">{{ $student->parent2_occupation }}</p>
                        </div>
                        @endif
                    </div>
                </div>
                @endif
            </div>
        </div>
        @endif

        <!-- Previous School Information -->
        @if($student->previous_school_name)
        <div class="mb-6">
            <h3 class="text-lg font-bold text-gray-900 mb-4 pb-2 border-b-2 border-gray-300">Previous School Information</h3>
            <div class="grid grid-cols-2 gap-6">
                <div>
                    <p class="text-xs text-gray-600 font-semibold">School Name</p>
                    <p class="text-sm text-gray-900 font-medium">{{ $student->previous_school_name }}</p>
                </div>
                @if($student->previous_school_grade)
                <div>
                    <p class="text-xs text-gray-600 font-semibold">Last Grade</p>
                    <p class="text-sm text-gray-900 font-medium">{{ $student->previous_school_grade }}</p>
                </div>
                @endif
                @if($student->previous_school_year)
                <div>
                    <p class="text-xs text-gray-600 font-semibold">Year Left</p>
                    <p class="text-sm text-gray-900 font-medium">{{ $student->previous_school_year }}</p>
                </div>
                @endif
            </div>
        </div>
        @endif

        <!-- Health Information -->
        @if($student->allergies || $student->medical_conditions || $student->medications)
        <div class="mb-6">
            <h3 class="text-lg font-bold text-gray-900 mb-4 pb-2 border-b-2 border-gray-300">Health & Medical Information</h3>
            <div class="space-y-3">
                @if($student->allergies)
                <div>
                    <p class="text-xs text-gray-600 font-semibold">Allergies</p>
                    <p class="text-sm text-gray-900 font-medium">{{ is_array(json_decode($student->allergies)) ? implode(', ', json_decode($student->allergies)) : 'None' }}</p>
                </div>
                @endif
                @if($student->medical_conditions)
                <div>
                    <p class="text-xs text-gray-600 font-semibold">Medical Conditions</p>
                    <p class="text-sm text-gray-900 font-medium">{{ $student->medical_conditions }}</p>
                </div>
                @endif
                @if($student->medications)
                <div>
                    <p class="text-xs text-gray-600 font-semibold">Current Medications</p>
                    <p class="text-sm text-gray-900 font-medium">{{ $student->medications }}</p>
                </div>
                @endif
                @if($student->emergency_medical_consent)
                <div>
                    <p class="text-xs text-gray-600 font-semibold">Emergency Medical Consent</p>
                    <p class="text-sm text-green-600 font-medium">✓ Authorized</p>
                </div>
                @endif
            </div>
        </div>
        @endif

        <!-- Footer -->
        <div class="mt-8 pt-6 border-t-2 border-gray-300 text-center text-xs text-gray-600">
            <p>© {{ date('Y') }} Darul Arqam School Management System. All rights reserved.</p>
            <p class="mt-1">This is an official student profile document.</p>
        </div>
    </div>

    <script>
        // Auto-print option (commented out by default)
        // window.onload = function() { window.print(); }
    </script>
</body>
</html>
