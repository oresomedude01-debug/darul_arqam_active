@extends('layouts.spa')

@section('title', 'Add Student')

@section('breadcrumb')
    <span class="text-gray-400">Students</span>
    <span class="text-gray-400">/</span>
    <a href="{{ route('students.index') }}" class="text-gray-400 hover:text-gray-600">All Students</a>
    <span class="text-gray-400">/</span>
    <span class="font-semibold text-gray-900">Add New</span>
@endsection

@section('content')
<div class="max-w-5xl mx-auto space-y-6" x-data="studentForm()">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Add New Student</h1>
            <p class="text-gray-600 mt-1">Register a new student in the system (No token required)</p>
        </div>
        <a href="{{ route('students.index') }}" class="btn btn-outline">
            <i class="fas fa-arrow-left mr-2"></i>
            Back to List
        </a>
    </div>

    <!-- Admission Status Selection -->
    <div class="card bg-gradient-to-r from-blue-50 to-indigo-50 border-l-4 border-blue-500">
        <div class="card-body">
            <div class="flex items-start space-x-4">
                <div class="flex-shrink-0">
                    <i class="fas fa-info-circle text-blue-600 text-2xl"></i>
                </div>
                <div class="flex-1">
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Admission Status</h3>
                    <p class="text-gray-600 mb-4">Choose the admission status for this student</p>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <label class="relative flex items-start p-4 bg-white rounded-lg border-2 cursor-pointer hover:border-primary-500 transition-colors"
                               :class="admissionStatus === 'active' ? 'border-primary-500 bg-primary-50' : 'border-gray-200'">
                            <input type="radio" name="status" value="active" x-model="admissionStatus" class="mt-1">
                            <div class="ml-3">
                                <p class="font-semibold text-gray-900">Fully Admitted</p>
                                <p class="text-sm text-gray-600 mt-1">Student is fully registered and can access all services</p>
                            </div>
                        </label>
                        <label class="relative flex items-start p-4 bg-white rounded-lg border-2 cursor-pointer hover:border-yellow-500 transition-colors"
                               :class="admissionStatus === 'pending' ? 'border-yellow-500 bg-yellow-50' : 'border-gray-200'">
                            <input type="radio" name="status" value="pending" x-model="admissionStatus" class="mt-1">
                            <div class="ml-3">
                                <p class="font-semibold text-gray-900">Provisional</p>
                                <p class="text-sm text-gray-600 mt-1">Student is provisionally admitted, pending documentation</p>
                            </div>
                        </label>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Form -->
    <form action="{{ route('students.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf

        <!-- Personal Information -->
        <div class="card">
            <div class="card-header">
                <h2 class="text-lg font-semibold text-gray-900">
                    <i class="fas fa-user mr-2 text-primary-600"></i>
                    Personal Information
                </h2>
            </div>
            <div class="card-body space-y-6">
                <!-- Profile Photo -->
                <div>
                    <label class="form-label">Profile Photo</label>
                    <div x-data="{ preview: null }" class="flex items-center space-x-6">
                        <div class="flex-shrink-0">
                            <div class="w-24 h-24 rounded-full bg-gray-200 overflow-hidden">
                                <img x-show="preview"
                                     :src="preview"
                                     alt="Preview"
                                     class="w-full h-full object-cover">
                                <div x-show="!preview" class="w-full h-full flex items-center justify-center">
                                    <i class="fas fa-user text-gray-400 text-3xl"></i>
                                </div>
                            </div>
                        </div>
                        <div class="flex-1">
                            <input type="file"
                                   name="photo"
                                   accept="image/*"
                                   id="photo-upload"
                                   class="hidden"
                                   @change="preview = URL.createObjectURL($event.target.files[0])">
                            <label for="photo-upload" class="btn btn-outline cursor-pointer">
                                <i class="fas fa-upload mr-2"></i>
                                Choose Photo
                            </label>
                            <p class="text-xs text-gray-500 mt-2">PNG or JPG, maximum 2MB</p>
                        </div>
                    </div>
                    @error('photo')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- First Name -->
                    <div>
                        <label class="form-label">First Name <span class="text-red-500">*</span></label>
                        <input type="text"
                               name="first_name"
                               value="{{ old('first_name') }}"
                               class="form-input"
                               placeholder="John"
                               required>
                        @error('first_name')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Middle Name -->
                    <div>
                        <label class="form-label">Middle Name</label>
                        <input type="text"
                               name="middle_name"
                               value="{{ old('middle_name') }}"
                               class="form-input"
                               placeholder="Michael">
                        @error('middle_name')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Last Name -->
                    <div>
                        <label class="form-label">Last Name <span class="text-red-500">*</span></label>
                        <input type="text"
                               name="last_name"
                               value="{{ old('last_name') }}"
                               class="form-input"
                               placeholder="Doe"
                               required>
                        @error('last_name')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Date of Birth -->
                    <div>
                        <label class="form-label">Date of Birth <span class="text-red-500">*</span></label>
                        <input type="date"
                               name="date_of_birth"
                               value="{{ old('date_of_birth') }}"
                               class="form-input"
                               max="{{ date('Y-m-d') }}"
                               required>
                        @error('date_of_birth')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Gender -->
                    <div>
                        <label class="form-label">Gender <span class="text-red-500">*</span></label>
                        <select name="gender" class="form-select" required>
                            <option value="">Select Gender</option>
                            <option value="male" {{ old('gender') === 'male' ? 'selected' : '' }}>Male</option>
                            <option value="female" {{ old('gender') === 'female' ? 'selected' : '' }}>Female</option>
                        </select>
                        @error('gender')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Blood Group -->
                    <div>
                        <label class="form-label">Blood Group</label>
                        <select name="blood_group" class="form-select">
                            <option value="">Select Blood Group</option>
                            @foreach(['A+', 'A-', 'B+', 'B-', 'O+', 'O-', 'AB+', 'AB-'] as $bg)
                                <option value="{{ $bg }}" {{ old('blood_group') === $bg ? 'selected' : '' }}>{{ $bg }}</option>
                            @endforeach
                        </select>
                        @error('blood_group')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Nationality -->
                    <div>
                        <label class="form-label">Nationality</label>
                        <input type="text"
                               name="nationality"
                               value="{{ old('nationality', 'Nigerian') }}"
                               class="form-input"
                               placeholder="Nigerian">
                        @error('nationality')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Address -->
                    <div class="md:col-span-3">
                        <label class="form-label">Residential Address</label>
                        <textarea name="address"
                                  class="form-textarea"
                                  rows="2"
                                  placeholder="123 Main Street, City, State">{{ old('address') }}</textarea>
                        @error('address')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <!-- Academic Information -->
        <div class="card">
            <div class="card-header">
                <h2 class="text-lg font-semibold text-gray-900">
                    <i class="fas fa-graduation-cap mr-2 text-primary-600"></i>
                    Academic Information
                </h2>
            </div>
            <div class="card-body">
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                    <div class="flex items-start space-x-3">
                        <i class="fas fa-lightbulb text-blue-600 text-xl mt-1"></i>
                        <div>
                            <p class="text-sm text-blue-900 font-medium">Admission Number Auto-Generation</p>
                            <p class="text-xs text-blue-700 mt-1">The admission number will be automatically generated when you save this student</p>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Class Level -->
                    <div>
                        <label class="form-label">Class Level <span class="text-red-500">*</span></label>
                        <select name="school_class_id" class="form-select" required>
                            <option value="">Select Class</option>
                            @forelse($schoolClasses as $class)
                                <option value="{{ $class->id }}" {{ old('school_class_id') == $class->id ? 'selected' : '' }}>
                                    {{ $class->name }} - {{ $class->section }}
                                </option>
                            @empty
                                <option value="">No classes available</option>
                            @endforelse
                        </select>
                        @error('school_class_id')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Admission Date -->
                    <div>
                        <label class="form-label">Admission Date</label>
                        <input type="date"
                               name="admission_date"
                               value="{{ old('admission_date', date('Y-m-d')) }}"
                               class="form-input">
                        @error('admission_date')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Previous School -->
                    <div class="md:col-span-2">
                        <label class="form-label">Previous School</label>
                        <input type="text"
                               name="previous_school"
                               value="{{ old('previous_school') }}"
                               class="form-input"
                               placeholder="Name of previous school">
                        @error('previous_school')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <!-- Contact Information -->
        <div class="card">
            <div class="card-header">
                <h2 class="text-lg font-semibold text-gray-900">
                    <i class="fas fa-address-book mr-2 text-primary-600"></i>
                    Student Contact Information
                </h2>
            </div>
            <div class="card-body">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Email -->
                    <div>
                        <label class="form-label">Email Address</label>
                        <input type="email"
                               name="email"
                               value="{{ old('email') }}"
                               class="form-input"
                               placeholder="student@example.com">
                        @error('email')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Phone -->
                    <div>
                        <label class="form-label">Phone Number</label>
                        <input type="tel"
                               name="phone"
                               value="{{ old('phone') }}"
                               class="form-input"
                               placeholder="+234 XXX XXX XXXX">
                        @error('phone')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <!-- Parent/Guardian Information -->
        <div class="card">
            <div class="card-header">
                <h2 class="text-lg font-semibold text-gray-900">
                    <i class="fas fa-users mr-2 text-primary-600"></i>
                    Parent/Guardian Information
                </h2>
            </div>
            <div class="card-body space-y-8">
                <!-- Primary Parent/Guardian -->
                <div>
                    <h3 class="text-md font-semibold text-gray-800 mb-4 flex items-center">
                        <span class="bg-primary-100 text-primary-700 px-3 py-1 rounded-full text-sm mr-2">Primary Contact</span>
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="form-label">Full Name</label>
                            <input type="text"
                                   name="parent1_name"
                                   value="{{ old('parent1_name') }}"
                                   class="form-input"
                                   placeholder="John Doe">
                            @error('parent1_name')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="form-label">Relationship</label>
                            <select name="parent1_relationship" class="form-select">
                                <option value="">Select Relationship</option>
                                @foreach(['Father', 'Mother', 'Guardian', 'Other'] as $rel)
                                    <option value="{{ $rel }}" {{ old('parent1_relationship') === $rel ? 'selected' : '' }}>{{ $rel }}</option>
                                @endforeach
                            </select>
                            @error('parent1_relationship')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="form-label">Phone Number</label>
                            <input type="tel"
                                   name="parent1_phone"
                                   value="{{ old('parent1_phone') }}"
                                   class="form-input"
                                   placeholder="+234 XXX XXX XXXX">
                            @error('parent1_phone')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="form-label">Email Address</label>
                            <input type="email"
                                   name="parent1_email"
                                   value="{{ old('parent1_email') }}"
                                   class="form-input"
                                   placeholder="parent@example.com">
                            @error('parent1_email')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="md:col-span-2">
                            <label class="form-label">Occupation</label>
                            <input type="text"
                                   name="parent1_occupation"
                                   value="{{ old('parent1_occupation') }}"
                                   class="form-input"
                                   placeholder="Engineer">
                            @error('parent1_occupation')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Secondary Parent/Guardian -->
                <div>
                    <h3 class="text-md font-semibold text-gray-800 mb-4 flex items-center">
                        <span class="bg-gray-100 text-gray-700 px-3 py-1 rounded-full text-sm mr-2">Secondary Contact (Optional)</span>
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="form-label">Full Name</label>
                            <input type="text"
                                   name="parent2_name"
                                   value="{{ old('parent2_name') }}"
                                   class="form-input"
                                   placeholder="Jane Doe">
                            @error('parent2_name')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="form-label">Relationship</label>
                            <select name="parent2_relationship" class="form-select">
                                <option value="">Select Relationship</option>
                                @foreach(['Father', 'Mother', 'Guardian', 'Other'] as $rel)
                                    <option value="{{ $rel }}" {{ old('parent2_relationship') === $rel ? 'selected' : '' }}>{{ $rel }}</option>
                                @endforeach
                            </select>
                            @error('parent2_relationship')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="form-label">Phone Number</label>
                            <input type="tel"
                                   name="parent2_phone"
                                   value="{{ old('parent2_phone') }}"
                                   class="form-input"
                                   placeholder="+234 XXX XXX XXXX">
                            @error('parent2_phone')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="form-label">Email Address</label>
                            <input type="email"
                                   name="parent2_email"
                                   value="{{ old('parent2_email') }}"
                                   class="form-input"
                                   placeholder="parent2@example.com">
                            @error('parent2_email')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="md:col-span-2">
                            <label class="form-label">Occupation</label>
                            <input type="text"
                                   name="parent2_occupation"
                                   value="{{ old('parent2_occupation') }}"
                                   class="form-input"
                                   placeholder="Teacher">
                            @error('parent2_occupation')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Medical Information -->
        <div class="card">
            <div class="card-header">
                <h2 class="text-lg font-semibold text-gray-900">
                    <i class="fas fa-heartbeat mr-2 text-primary-600"></i>
                    Health & Medical Information
                </h2>
            </div>
            <div class="card-body space-y-6">
                <!-- Medical Conditions -->
                <div>
                    <label class="form-label">Medical Conditions</label>
                    <textarea name="medical_conditions"
                              class="form-textarea"
                              rows="3"
                              placeholder="Any chronic illnesses, conditions, or disabilities...">{{ old('medical_conditions') }}</textarea>
                    @error('medical_conditions')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Allergies -->
                <div>
                    <label class="form-label">Allergies</label>
                    <div x-data="{ allergies: {{ old('allergies') ? json_encode(old('allergies')) : '[]' }}, newAllergy: '' }">
                        <div class="flex items-center space-x-2 mb-3">
                            <input type="text"
                                   x-model="newAllergy"
                                   @keydown.enter.prevent="if(newAllergy.trim()) { allergies.push(newAllergy.trim()); newAllergy = ''; }"
                                   class="form-input flex-1"
                                   placeholder="Type allergy and press Enter (e.g., Peanuts, Dust)">
                            <button type="button"
                                    @click="if(newAllergy.trim()) { allergies.push(newAllergy.trim()); newAllergy = ''; }"
                                    class="btn btn-outline">
                                <i class="fas fa-plus"></i>
                            </button>
                        </div>
                        <div class="flex flex-wrap gap-2">
                            <template x-for="(allergy, index) in allergies" :key="index">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm bg-red-100 text-red-800">
                                    <span x-text="allergy"></span>
                                    <button type="button"
                                            @click="allergies.splice(index, 1)"
                                            class="ml-2 text-red-600 hover:text-red-800">
                                        <i class="fas fa-times text-xs"></i>
                                    </button>
                                    <input type="hidden" name="allergies[]" :value="allergy">
                                </span>
                            </template>
                            <template x-if="allergies.length === 0">
                                <span class="text-sm text-gray-500 italic">No allergies added</span>
                            </template>
                        </div>
                    </div>
                    <p class="text-xs text-gray-500 mt-2">Add any known allergies the student has</p>
                    @error('allergies')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Current Medications -->
                <div>
                    <label class="form-label">Current Medications</label>
                    <textarea name="medications"
                              class="form-textarea"
                              rows="2"
                              placeholder="List any medications the student is currently taking...">{{ old('medications') }}</textarea>
                    @error('medications')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Emergency Medical Consent -->
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                    <label class="flex items-start space-x-3 cursor-pointer">
                        <input type="checkbox"
                               name="emergency_medical_consent"
                               value="1"
                               {{ old('emergency_medical_consent') ? 'checked' : '' }}
                               class="form-checkbox mt-1">
                        <div>
                            <p class="font-medium text-gray-900">Emergency Medical Consent</p>
                            <p class="text-sm text-gray-600 mt-1">I authorize the school to seek emergency medical treatment for my child if I cannot be reached</p>
                        </div>
                    </label>
                    @error('emergency_medical_consent')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Special Needs -->
                <div>
                    <label class="form-label">Special Needs/Accommodations</label>
                    <textarea name="special_needs"
                              class="form-textarea"
                              rows="3"
                              placeholder="Any special educational needs, accommodations, or support required...">{{ old('special_needs') }}</textarea>
                    @error('special_needs')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Additional Notes -->
        <div class="card">
            <div class="card-header">
                <h2 class="text-lg font-semibold text-gray-900">
                    <i class="fas fa-sticky-note mr-2 text-primary-600"></i>
                    Additional Notes
                </h2>
            </div>
            <div class="card-body">
                <textarea name="notes"
                          class="form-textarea"
                          rows="4"
                          placeholder="Any additional information about the student...">{{ old('notes') }}</textarea>
                @error('notes')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Form Actions -->
        <div class="bg-gray-50 border border-gray-200 rounded-lg p-6">
            <div class="flex items-center justify-between">
                <div class="text-sm text-gray-600">
                    <i class="fas fa-info-circle mr-2"></i>
                    <span class="text-red-500">*</span> indicates required fields
                </div>
                <div class="flex items-center space-x-4">
                    <a href="{{ route('students.index') }}" class="btn btn-outline">
                        <i class="fas fa-times mr-2"></i>
                        Cancel
                    </a>
                    <button type="reset" class="btn btn-secondary" onclick="return confirm('Are you sure you want to reset the form? All entered data will be lost.')">
                        <i class="fas fa-redo mr-2"></i>
                        Reset Form
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save mr-2"></i>
                        Save Student
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
    function studentForm() {
        return {
            admissionStatus: 'active',
            showPreviousSchool: false,

            init() {
                // Set max date for date of birth to today
                const dobInput = document.querySelector('input[name="date_of_birth"]');
                if (dobInput) {
                    dobInput.setAttribute('max', new Date().toISOString().split('T')[0]);
                }
            }
        }
    }
</script>
@endpush
@endsection
