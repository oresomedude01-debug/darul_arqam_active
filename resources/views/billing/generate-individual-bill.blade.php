@extends('layouts.spa')

@section('title', 'Generate Bill for ' . $student->user->name)

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100 py-8 px-4 sm:px-6 lg:px-8">
    <div class="max-w-2xl mx-auto">
        <!-- Header -->
        <div class="mb-8">
            <a href="{{ route('billing.student-bills', $student) }}" class="inline-flex items-center text-blue-600 hover:text-blue-700 font-medium mb-6 transition">
                <i class="fas fa-arrow-left mr-2"></i>Back to Student Bills
            </a>
            <div class="bg-white rounded-lg shadow-sm p-6 border-l-4 border-blue-500">
                <h1 class="text-3xl font-bold text-gray-900">Generate Bill</h1>
                <p class="text-gray-600 mt-2">
                    <span class="font-semibold">Student:</span> {{ $student->user->name }}
                    <span class="inline-block ml-4 px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-sm">
                        {{ $student->schoolClass?->name ?? 'No Class' }}
                    </span>
                </p>
            </div>
        </div>

        <!-- Form Card -->
        <div class="bg-white rounded-lg shadow-lg overflow-hidden">
            <form method="POST" action="{{ route('billing.generate-individual-bill', $student) }}" class="p-8">
                @csrf

                <!-- Section 1: Academic Information -->
                <div class="mb-8">
                    <div class="flex items-center mb-6">
                        <div class="w-8 h-8 bg-blue-500 text-white rounded-full flex items-center justify-center font-bold text-sm">1</div>
                        <h2 class="text-xl font-bold text-gray-900 ml-3">Academic Information</h2>
                    </div>

                    <div class="space-y-6">
                        <!-- Academic Session -->
                        <div>
                            <label for="academic_session_id" class="block text-sm font-semibold text-gray-700 mb-2">
                                Academic Session <span class="text-red-500">*</span>
                            </label>
                            <select id="academic_session_id" name="academic_session_id" 
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                                    required>
                                <option value="">-- Select Session --</option>
                                @foreach($sessions as $session)
                                    <option value="{{ $session->id }}" {{ old('academic_session_id') == $session->id ? 'selected' : '' }}>
                                        {{ $session->session }}
                                    </option>
                                @endforeach
                            </select>
                            @error('academic_session_id')
                                <div class="mt-2 p-3 bg-red-50 border border-red-200 rounded-lg text-red-700 text-sm">
                                    <i class="fas fa-exclamation-circle mr-2"></i>{{ $message }}
                                </div>
                            @enderror
                        </div>

                        <!-- Academic Term -->
                        <div>
                            <label for="academic_term_id" class="block text-sm font-semibold text-gray-700 mb-2">
                                Academic Term <span class="text-gray-500 text-xs font-normal">(Optional)</span>
                            </label>
                            <select id="academic_term_id" name="academic_term_id" 
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                                <option value="">-- Select Term --</option>
                                @foreach($terms as $term)
                                    <option value="{{ $term->id }}" {{ old('academic_term_id') == $term->id ? 'selected' : '' }}>
                                        {{ $term->term_name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('academic_term_id')
                                <div class="mt-2 text-red-600 text-sm">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <hr class="my-8">

                <!-- Section 2: Fee Structure -->
                <div class="mb-8">
                    <div class="flex items-center mb-6">
                        <div class="w-8 h-8 bg-blue-500 text-white rounded-full flex items-center justify-center font-bold text-sm">2</div>
                        <h2 class="text-xl font-bold text-gray-900 ml-3">Fee Structure</h2>
                    </div>

                    <div class="space-y-4">
                        <label for="fee_structure_id" class="block text-sm font-semibold text-gray-700 mb-2">
                            Select Fee Structure <span class="text-red-500">*</span>
                        </label>
                        <div class="space-y-3">
                            @forelse($feeStructures as $structure)
                                <label class="relative">
                                    <input type="radio" name="fee_structure_id" value="{{ $structure->id }}" 
                                           {{ old('fee_structure_id') == $structure->id ? 'checked' : '' }}
                                           class="sr-only peer" required>
                                    <div class="p-4 border-2 border-gray-200 rounded-lg cursor-pointer peer-checked:border-blue-500 peer-checked:bg-blue-50 transition hover:border-gray-300">
                                        <div class="flex justify-between items-start">
                                            <div class="flex-1">
                                                <p class="font-semibold text-gray-900">{{ $structure->name }}</p>
                                                @if($structure->description)
                                                    <p class="text-sm text-gray-600 mt-1">{{ $structure->description }}</p>
                                                @endif
                                                <div class="flex gap-4 mt-2 text-sm text-gray-600">
                                                    @if($structure->academicSession)
                                                        <span><i class="fas fa-calendar mr-1"></i>{{ $structure->academicSession->session }}</span>
                                                    @endif
                                                    @if($structure->academicTerm)
                                                        <span><i class="fas fa-bookmark mr-1"></i>{{ $structure->academicTerm->term_name }}</span>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="text-right">
                                                <div class="text-2xl font-bold text-blue-600">{{ number_format($structure->total_amount, 2) }}</div>
                                                <p class="text-xs text-gray-500">Total Amount</p>
                                            </div>
                                        </div>
                                    </div>
                                </label>
                            @empty
                                <div class="p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                                    <p class="text-yellow-800"><i class="fas fa-exclamation-triangle mr-2"></i>No fee structures available. Please create one first.</p>
                                </div>
                            @endforelse
                        </div>
                        @error('fee_structure_id')
                            <div class="mt-2 p-3 bg-red-50 border border-red-200 rounded-lg text-red-700 text-sm">
                                <i class="fas fa-exclamation-circle mr-2"></i>{{ $message }}
                            </div>
                        @enderror
                    </div>
                </div>

                <hr class="my-8">

                <!-- Section 3: Due Date -->
                <div class="mb-8">
                    <div class="flex items-center mb-6">
                        <div class="w-8 h-8 bg-blue-500 text-white rounded-full flex items-center justify-center font-bold text-sm">3</div>
                        <h2 class="text-xl font-bold text-gray-900 ml-3">Due Date</h2>
                    </div>

                    <div>
                        <label for="due_date" class="block text-sm font-semibold text-gray-700 mb-2">
                            Payment Due Date <span class="text-gray-500 text-xs font-normal">(Optional)</span>
                        </label>
                        <input type="date" id="due_date" name="due_date" value="{{ old('due_date') }}" 
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                        <p class="text-xs text-gray-500 mt-2 flex items-center">
                            <i class="fas fa-info-circle mr-2"></i>
                            Leave empty for no specific due date
                        </p>
                        @error('due_date')
                            <div class="mt-2 text-red-600 text-sm">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="flex justify-between gap-4 pt-8 border-t-2 border-gray-200">
                    <a href="{{ route('billing.student-bills', $student) }}" 
                       class="px-6 py-3 border-2 border-gray-300 text-gray-700 font-semibold rounded-lg hover:bg-gray-50 transition">
                        <i class="fas fa-times mr-2"></i>Cancel
                    </a>
                    <button type="submit" class="px-8 py-3 bg-green-600 text-white font-semibold rounded-lg hover:bg-green-700 shadow-lg transition transform hover:scale-105">
                        <i class="fas fa-file-invoice-dollar mr-2"></i>Generate Bill
                    </button>
                </div>
            </form>
        </div>

        <!-- Information Panel -->
        <div class="mt-8 bg-blue-50 border border-blue-200 rounded-lg p-6">
            <h3 class="font-bold text-blue-900 mb-3 flex items-center">
                <i class="fas fa-info-circle mr-2 text-blue-600"></i>
                How Individual Bills Work
            </h3>
            <ul class="space-y-2 text-sm text-blue-800">
                <li class="flex items-start">
                    <i class="fas fa-check text-blue-600 mr-3 mt-0.5"></i>
                    <span>Select a fee structure template that contains all applicable fees for this student</span>
                </li>
                <li class="flex items-start">
                    <i class="fas fa-check text-blue-600 mr-3 mt-0.5"></i>
                    <span>The bill will be created with the total amount from the selected template</span>
                </li>
                <li class="flex items-start">
                    <i class="fas fa-check text-blue-600 mr-3 mt-0.5"></i>
                    <span>You can set an optional due date for payment tracking</span>
                </li>
                <li class="flex items-start">
                    <i class="fas fa-check text-blue-600 mr-3 mt-0.5"></i>
                    <span>After creation, you can record payments and apply discounts as needed</span>
                </li>
            </ul>
        </div>
    </div>
</div>
@endsection
