@extends('layouts.spa')

@section('title', 'Child Details - ' . $child->first_name . ' ' . $child->last_name)

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-slate-100 py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
        <!-- Header -->
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                <a href="{{ route('parent-portal.children') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                    <i class="fas fa-arrow-left mr-2"></i>Back
                </a>
                <div>
                    <h1 class="text-4xl font-bold text-gray-900">{{ $child->first_name }} {{ $child->last_name }}</h1>
                    <p class="text-gray-600 mt-2">Student Details & Information</p>
                </div>
            </div>
        </div>

        <!-- Main Card -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Profile Section -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-2xl shadow-lg border border-blue-100 overflow-hidden">
                    <!-- Header -->
                    <div class="bg-gradient-to-r from-blue-600 to-indigo-600 px-6 py-8">
                        <div class="flex items-center gap-4">
                            <div class="w-20 h-20 rounded-full bg-white/20 backdrop-blur-sm flex items-center justify-center">
                                <span class="text-4xl font-bold text-white">{{ substr($child->first_name, 0, 1) }}</span>
                            </div>
                            <div class="text-white">
                                <h2 class="text-2xl font-bold">{{ $child->first_name }} {{ $child->last_name }}</h2>
                                <p class="text-blue-100">{{ $child->admission_number }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Content -->
                    <div class="px-6 py-6 space-y-6">
                        <!-- Personal Information -->
                        <div class="space-y-4">
                            <h3 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                                <i class="fas fa-user text-blue-600"></i>Personal Information
                            </h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="bg-gray-50 rounded-lg p-4">
                                    <p class="text-sm text-gray-600 font-medium">First Name</p>
                                    <p class="text-lg font-semibold text-gray-900 mt-1">{{ $child->first_name }}</p>
                                </div>
                                <div class="bg-gray-50 rounded-lg p-4">
                                    <p class="text-sm text-gray-600 font-medium">Last Name</p>
                                    <p class="text-lg font-semibold text-gray-900 mt-1">{{ $child->last_name }}</p>
                                </div>
                                @if($child->date_of_birth)
                                <div class="bg-gray-50 rounded-lg p-4">
                                    <p class="text-sm text-gray-600 font-medium">Date of Birth</p>
                                    <p class="text-lg font-semibold text-gray-900 mt-1">{{ $child->date_of_birth->format('M d, Y') }}</p>
                                </div>
                                @endif
                                @if($child->gender)
                                <div class="bg-gray-50 rounded-lg p-4">
                                    <p class="text-sm text-gray-600 font-medium">Gender</p>
                                    <p class="text-lg font-semibold text-gray-900 mt-1">{{ ucfirst($child->gender) }}</p>
                                </div>
                                @endif
                            </div>
                        </div>

                        <hr class="border-gray-200">

                        <!-- Academic Information -->
                        <div class="space-y-4">
                            <h3 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                                <i class="fas fa-graduation-cap text-purple-600"></i>Academic Information
                            </h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="bg-gradient-to-br from-purple-50 to-purple-100 rounded-lg p-4 border border-purple-200">
                                    <p class="text-sm text-gray-600 font-medium">Student ID</p>
                                    <p class="text-lg font-semibold text-gray-900 mt-1">{{ $child->admission_number }}</p>
                                </div>
                                <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-lg p-4 border border-blue-200">
                                    <p class="text-sm text-gray-600 font-medium">Class</p>
                                    <p class="text-lg font-semibold text-gray-900 mt-1">{{ $child->schoolClass?->name ?? 'N/A' }}</p>
                                </div>
                                <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-lg p-4 border border-green-200">
                                    <p class="text-sm text-gray-600 font-medium">Status</p>
                                    <p class="text-lg font-semibold {{ $child->status === 'active' ? 'text-green-700' : 'text-red-700' }} mt-1">
                                        {{ ucfirst($child->status) }}
                                    </p>
                                </div>
                                @if($child->registration_number)
                                <div class="bg-gradient-to-br from-indigo-50 to-indigo-100 rounded-lg p-4 border border-indigo-200">
                                    <p class="text-sm text-gray-600 font-medium">Registration #</p>
                                    <p class="text-lg font-semibold text-gray-900 mt-1">{{ $child->registration_number }}</p>
                                </div>
                                @endif
                            </div>
                        </div>

                        @if($child->middle_name || $child->phone || $child->email)
                        <hr class="border-gray-200">

                        <!-- Contact Information -->
                        <div class="space-y-4">
                            <h3 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                                <i class="fas fa-phone text-green-600"></i>Contact Information
                            </h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                @if($child->middle_name)
                                <div class="bg-gray-50 rounded-lg p-4">
                                    <p class="text-sm text-gray-600 font-medium">Middle Name</p>
                                    <p class="text-lg font-semibold text-gray-900 mt-1">{{ $child->middle_name }}</p>
                                </div>
                                @endif
                                @if($child->phone)
                                <div class="bg-gray-50 rounded-lg p-4">
                                    <p class="text-sm text-gray-600 font-medium">Phone</p>
                                    <p class="text-lg font-semibold text-gray-900 mt-1">{{ $child->phone }}</p>
                                </div>
                                @endif
                                @if($child->email)
                                <div class="bg-gray-50 rounded-lg p-4">
                                    <p class="text-sm text-gray-600 font-medium">Email</p>
                                    <p class="text-lg font-semibold text-gray-900 mt-1">{{ $child->email }}</p>
                                </div>
                                @endif
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Outstanding Balance -->
                <div class="bg-white rounded-2xl shadow-lg border border-red-100 p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                        <i class="fas fa-money-bill-wave text-red-600"></i>Outstanding Balance
                    </h3>
                    <p class="text-4xl font-bold {{ $outstanding > 0 ? 'text-red-600' : 'text-green-600' }}">
                        ₦{{ number_format($outstanding, 2) }}
                    </p>
                    <p class="text-sm text-gray-600 mt-2">
                        @if($outstanding > 0)
                            Amount due from this student
                        @else
                            All bills paid
                        @endif
                    </p>
                </div>

                <!-- Quick Actions -->
                <div class="bg-white rounded-2xl shadow-lg border border-blue-100 p-6 space-y-3">
                    <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                        <i class="fas fa-bolt text-blue-600"></i>Quick Actions
                    </h3>
                    <a href="{{ route('parent-portal.bills', ['student' => $child->id]) }}" 
                       class="w-full inline-flex items-center justify-center px-4 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium">
                        <i class="fas fa-file-invoice-dollar mr-2"></i>View Bills
                    </a>
                    @if($outstanding > 0)
                    <a href="{{ route('parent-portal.bills', ['student' => $child->id, 'status' => 'pending']) }}" 
                       class="w-full inline-flex items-center justify-center px-4 py-3 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors font-medium">
                        <i class="fas fa-money-bill-wave mr-2"></i>Pay Outstanding
                    </a>
                    @endif
                    <a href="{{ route('parent-portal.payment-history', ['student' => $child->id]) }}" 
                       class="w-full inline-flex items-center justify-center px-4 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors font-medium">
                        <i class="fas fa-history mr-2"></i>Payment History
                    </a>
                </div>

                <!-- Status Badge -->
                <div class="bg-gradient-to-br {{ $child->status === 'active' ? 'from-green-50 to-green-100 border-green-200' : 'from-red-50 to-red-100 border-red-200' }} rounded-2xl shadow-lg border p-6 text-center">
                    <span class="inline-block px-4 py-2 {{ $child->status === 'active' ? 'bg-green-500' : 'bg-red-500' }} text-white rounded-lg font-bold">
                        {{ ucfirst($child->status) }}
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
