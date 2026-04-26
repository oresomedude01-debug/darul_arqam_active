@extends('student-portal.layout')

@section('portal-title', 'My Profile')

@section('student-content')
<div class="space-y-6">
    <!-- Success Message -->
    @if(session('success'))
        <div class="bg-green-50 border-l-4 border-green-600 p-4 rounded-lg flex items-start gap-3">
            <i class="fas fa-check-circle text-green-600 text-xl mt-0.5"></i>
            <div>
                <p class="font-semibold text-green-900">Success</p>
                <p class="text-green-700 text-sm">{{ session('success') }}</p>
            </div>
        </div>
    @endif

    <!-- Profile Header -->
    <div class="bg-gradient-to-r from-indigo-600 to-purple-600 rounded-2xl p-8 text-white shadow-lg">
        <div class="flex items-center gap-6">
            <div class="w-24 h-24 rounded-full bg-white/20 flex items-center justify-center text-4xl font-bold backdrop-blur-sm">
                {{ substr($user->name, 0, 1) }}
            </div>
            <div>
                <h1 class="text-3xl font-bold">{{ $user->name }}</h1>
                <p class="text-indigo-100 mt-1">Student ID: {{ $student->admission_number ?? 'N/A' }}</p>
                <p class="text-indigo-100">Class: {{ $student->schoolClass->name ?? 'Not assigned' }}</p>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Profile Info Card -->
        <div class="lg:col-span-2">
            <form action="{{ route('student-portal.profile.update') }}" method="POST" class="space-y-6">
                @csrf
                @method('PUT')

                <div class="bg-white rounded-2xl shadow-md border border-gray-200 overflow-hidden">
                    <div class="bg-gradient-to-r from-indigo-600 to-purple-600 px-6 py-5 relative overflow-hidden">
                        <div class="absolute right-0 top-0 w-40 h-40 bg-white/5 rounded-full -mr-20 -mt-20 blur-2xl"></div>
                        <h2 class="text-xl font-bold text-white flex items-center gap-3 relative">
                            <i class="fas fa-user text-purple-200"></i>Personal Information
                        </h2>
                    </div>

                    <div class="p-6 space-y-6">
                        <!-- Full Name -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-900 mb-2">Full Name</label>
                            <input type="text" value="{{ $user->name }}" disabled class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-50 text-gray-600 cursor-not-allowed">
                            <p class="text-xs text-gray-500 mt-1">Contact administrator to change name</p>
                        </div>

                        <!-- Email -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-900 mb-2">Email Address</label>
                            <input type="email" name="email" value="{{ $user->email }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                            @error('email')
                                <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Phone -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-900 mb-2">Phone Number</label>
                            <input type="tel" name="phone" value="{{ $student->phone ?? '' }}" placeholder="+234..." class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                            @error('phone')
                                <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Address -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-900 mb-2">Address</label>
                            <textarea name="address" rows="3" placeholder="Your residential address" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">{{ $student->address ?? '' }}</textarea>
                            @error('address')
                                <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Date of Birth -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-900 mb-2">Date of Birth</label>
                            <input type="date" name="date_of_birth" value="{{ $student->date_of_birth ?? '' }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                            @error('date_of_birth')
                                <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Save Button -->
                        <div class="pt-4 border-t border-gray-200">
                            <button type="submit" class="w-full px-6 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 text-white font-semibold rounded-lg hover:from-indigo-700 hover:to-purple-700 transition-all shadow-md hover:shadow-lg flex items-center justify-center gap-2">
                                <i class="fas fa-save"></i>Save Changes
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <!-- Quick Info Sidebar -->
        <div class="space-y-6">
            <!-- Academic Info -->
            <div class="bg-gradient-to-br from-blue-50 to-cyan-50 rounded-2xl border border-blue-200 p-6">
                <h3 class="font-bold text-blue-900 mb-4 flex items-center gap-2">
                    <i class="fas fa-book text-blue-600"></i>Academic Info
                </h3>
                <div class="space-y-3">
                    <div>
                        <p class="text-xs text-blue-700 font-semibold uppercase">Class</p>
                        <p class="text-lg font-bold text-blue-900">{{ $student->schoolClass->name ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-blue-700 font-semibold uppercase">Admission No.</p>
                        <p class="text-lg font-bold text-blue-900">{{ $student->admission_number ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-blue-700 font-semibold uppercase">Status</p>
                        <p class="text-lg font-bold text-green-600">Active</p>
                    </div>
                </div>
            </div>

            <!-- Account Security -->
            <div class="bg-gradient-to-br from-purple-50 to-pink-50 rounded-2xl border border-purple-200 p-6">
                <h3 class="font-bold text-purple-900 mb-4 flex items-center gap-2">
                    <i class="fas fa-lock text-purple-600"></i>Account Security
                </h3>
                <div class="space-y-3">
                    <p class="text-sm text-purple-800">
                        <span class="font-semibold">Status:</span> Secure
                    </p>
                    <button class="w-full px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-lg font-semibold transition-all text-sm">
                        Change Password
                    </button>
                </div>
            </div>

            <!-- Help & Support -->
            <div class="bg-gradient-to-br from-amber-50 to-orange-50 rounded-2xl border border-amber-200 p-6">
                <h3 class="font-bold text-amber-900 mb-4 flex items-center gap-2">
                    <i class="fas fa-life-ring text-amber-600"></i>Support
                </h3>
                <p class="text-sm text-amber-800 mb-3">Need help with your account or have questions?</p>
                <button class="w-full px-4 py-2 bg-amber-600 hover:bg-amber-700 text-white rounded-lg font-semibold transition-all text-sm">
                    Contact Support
                </button>
            </div>
        </div>
    </div>
</div>
@endsection
