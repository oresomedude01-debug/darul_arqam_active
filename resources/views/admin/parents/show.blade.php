@extends('layouts.spa')

@section('title', 'Parent Details - ' . $parent->name)

@section('content')
<div class="p-6">
    <!-- Header with Back Button -->
    <div class="mb-8">
        <div class="flex items-center gap-3 mb-4">
            <a href="{{ route('admin.parents.index') }}" class="text-purple-600 hover:text-purple-800">
                <i class="fas fa-arrow-left"></i> Back to Parents
            </a>
        </div>
        <div class="flex justify-between items-start">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">{{ $parent->name }}</h1>
                <p class="mt-2 text-gray-600">{{ $parent->email }}</p>
            </div>
            <a href="{{ route('admin.parents.edit', $parent) }}" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg transition">
                <i class="fas fa-edit mr-2"></i>Edit Parent
            </a>
        </div>
    </div>

    <!-- Alerts -->
    @if($message = session('success'))
    <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-4 rounded-lg">
        <i class="fas fa-check-circle mr-2"></i>{{ $message }}
    </div>
    @endif

    @if($message = session('error'))
    <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-4 rounded-lg">
        <i class="fas fa-exclamation-circle mr-2"></i>{{ $message }}
    </div>
    @endif

    <!-- Parent Information Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        <!-- Contact Information -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">
                <i class="fas fa-phone text-purple-600 mr-2"></i>Contact Information
            </h3>
            <div class="space-y-3 text-sm">
                <div>
                    <p class="text-gray-600">Email</p>
                    <p class="font-medium text-gray-900">{{ $parent->email }}</p>
                </div>
                <div>
                    <p class="text-gray-600">Phone</p>
                    <p class="font-medium text-gray-900">{{ $parent->profile?->phone ?? 'Not provided' }}</p>
                </div>
                <div>
                    <p class="text-gray-600">Address</p>
                    <p class="font-medium text-gray-900">{{ $parent->profile?->address ?? 'Not provided' }}</p>
                </div>
            </div>
        </div>

        <!-- Occupation Information -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">
                <i class="fas fa-briefcase text-blue-600 mr-2"></i>Occupation
            </h3>
            <div class="space-y-3 text-sm">
                <div>
                    <p class="text-gray-600">Occupation</p>
                    <p class="font-medium text-gray-900">{{ $parent->profile?->occupation ?? 'Not provided' }}</p>
                </div>
            </div>
        </div>

        <!-- Payment Summary -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">
                <i class="fas fa-money-bill text-green-600 mr-2"></i>Payment Summary
            </h3>
            <div class="space-y-3">
                <div class="p-3 bg-red-50 rounded-lg border border-red-200">
                    <p class="text-sm text-red-700">Outstanding Balance</p>
                    <p class="text-2xl font-bold text-red-600">₦{{ number_format($totalOutstanding, 2) }}</p>
                </div>
                <div class="p-3 bg-blue-50 rounded-lg border border-blue-200">
                    <p class="text-sm text-blue-700">Children Count</p>
                    <p class="text-2xl font-bold text-blue-600">{{ $children->count() }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Children Section -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-8">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-gray-900">
                <i class="fas fa-child text-purple-600 mr-2"></i>Assigned Children ({{ $children->count() }})
            </h2>
            <button @click="$dispatch('open-modal', 'assign-child')" class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg transition">
                <i class="fas fa-plus mr-2"></i>Assign Child
            </button>
        </div>

        <!-- Children Table -->
        @if($children->count() > 0)
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-100 border-b">
                    <tr>
                        <th class="px-6 py-4 text-left text-sm font-semibold text-gray-900">Child Name</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold text-gray-900">Email</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold text-gray-900">Class</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold text-gray-900">Status</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold text-gray-900">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($children as $child)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4">
                            <div class="font-medium text-gray-900">{{ $child->name }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-gray-600 text-sm">{{ $child->email }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-gray-600 text-sm">
                                {{ $child->profile?->class?->name ?? '-' }}
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            @if($child->is_active)
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                Active
                            </span>
                            @else
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                Inactive
                            </span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <a href="{{ route('students.show', $child) }}" class="text-blue-600 hover:text-blue-800 text-sm mr-3">
                                <i class="fas fa-eye"></i> View
                            </a>
                            <form action="{{ route('admin.parents.unassign-child', [$parent, $child]) }}" method="POST" class="inline" onsubmit="return confirm('Unassign this child?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-800 text-sm">
                                    <i class="fas fa-times"></i> Unassign
                                </button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="text-center py-8 text-gray-500">
            <i class="fas fa-inbox text-4xl mb-2 block opacity-50"></i>
            <p>No children assigned to this parent</p>
        </div>
        @endif
    </div>

    <!-- Outstanding Bills Section -->
    @if($outstandingBills->count() > 0)
    <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-2xl font-bold text-gray-900 mb-6">
            <i class="fas fa-file-invoice-dollar text-orange-600 mr-2"></i>Outstanding Bills ({{ $outstandingBills->count() }})
        </h2>

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-100 border-b">
                    <tr>
                        <th class="px-6 py-4 text-left text-sm font-semibold text-gray-900">Student</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold text-gray-900">Fee Structure</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold text-gray-900">Total Amount</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold text-gray-900">Paid</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold text-gray-900">Balance</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold text-gray-900">Status</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold text-gray-900">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($outstandingBills as $bill)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4">
                            <div class="font-medium text-gray-900">{{ optional($bill->student)->name ?? 'N/A' }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-gray-600 text-sm">{{ optional($bill->feeStructure)->name ?? 'N/A' }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="font-medium text-gray-900">₦{{ number_format($bill->total_amount, 2) }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-green-600 font-medium">₦{{ number_format($bill->paid_amount, 2) }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-red-600 font-bold">₦{{ number_format($bill->balance_due, 2) }}</div>
                        </td>
                        <td class="px-6 py-4">
                            @if($bill->status === 'pending')
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">Pending</span>
                            @elseif($bill->status === 'partial')
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">Partial</span>
                            @else
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">Paid</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            @if($bill->balance_due > 0)
                            <a href="{{ route('payments.index', ['bill' => $bill->id]) }}" class="text-blue-600 hover:text-blue-800 text-sm">
                                <i class="fas fa-money-bill-wave mr-1"></i>Pay
                            </a>
                            @else
                            <span class="text-green-600 text-sm"><i class="fas fa-check"></i> Paid</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Total Outstanding Summary -->
        @if($totalOutstanding > 0)
        <div class="mt-4 p-4 bg-red-50 border border-red-200 rounded-lg">
            <div class="flex justify-between items-center">
                <p class="text-sm font-semibold text-red-700">Total Outstanding Balance for All Children:</p>
                <p class="text-2xl font-bold text-red-600">₦{{ number_format($totalOutstanding, 2) }}</p>
            </div>
        </div>
        @endif
    </div>
    @else
    <div class="bg-green-50 border border-green-200 rounded-lg p-6 text-center">
        <i class="fas fa-check-circle text-4xl text-green-600 mb-2 block"></i>
        <p class="text-green-700">No outstanding bills - all payments are up to date!</p>
    </div>
    @endif
</div>

<!-- Assign Child Modal -->
<div x-data="{ open: false, searchQuery: '' }" @open-modal.window="open = true" @close-modal.window="open = false">
    <div x-show="open" class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center" @click.self="open = false">
        <div class="bg-white rounded-lg shadow-xl max-w-md w-full mx-4 p-6">
            <h3 class="text-xl font-bold text-gray-900 mb-4">Assign Child to {{ $parent->name }}</h3>
            
            <form action="{{ route('admin.parents.assign-child', $parent) }}" method="POST">
                @csrf

                <div class="mb-4">
                    <label for="student_search" class="block text-sm font-semibold text-gray-900 mb-2">Search Student</label>
                    <input type="text" 
                           id="student_search"
                           x-model="searchQuery"
                           placeholder="Type student name or email..."
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500">
                </div>

                <div class="mb-4">
                    <label for="student_id" class="block text-sm font-semibold text-gray-900 mb-2">Select Student <span class="text-red-500">*</span></label>
                    <select id="student_id" 
                            name="student_id" 
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500"
                            required>
                        <option value="">-- Select a student --</option>
                        @foreach($allStudents as $student)
                        <option value="{{ $student->id }}" 
                                x-show="'{{ strtolower($student->name) }}'.includes(searchQuery.toLowerCase()) || '{{ strtolower($student->email) }}'.includes(searchQuery.toLowerCase()) || searchQuery === ''">
                            {{ $student->name }} ({{ $student->email }})
                        </option>
                        @endforeach
                    </select>
                    @if($allStudents->isEmpty())
                    <p class="text-gray-500 text-sm mt-2">No students available</p>
                    @endif
                </div>

                <div class="flex gap-3">
                    <button type="submit" class="flex-1 bg-purple-600 hover:bg-purple-700 text-white font-medium py-2 rounded-lg transition">
                        <i class="fas fa-check mr-2"></i>Assign
                    </button>
                    <button type="button" @click="open = false; searchQuery = ''" class="flex-1 bg-gray-300 hover:bg-gray-400 text-gray-900 font-medium py-2 rounded-lg transition">
                        <i class="fas fa-times mr-2"></i>Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection
