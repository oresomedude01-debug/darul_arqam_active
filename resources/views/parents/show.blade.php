@extends('layouts.spa')

@section('content')
<div class="min-h-screen bg-gray-50 px-6 py-8">
    <!-- Breadcrumb -->
    <div class="flex items-center gap-2 text-sm text-gray-600 mb-6">
        <a href="{{ route('dashboard') }}" class="text-primary-600 hover:text-primary-700">Home</a>
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        <a href="{{ route('admin.parents.index') }}" class="text-primary-600 hover:text-primary-700">Parents</a>
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        <span class="text-gray-900 font-medium">{{ $parent->first_name }} {{ $parent->last_name }}</span>
    </div>

    <!-- Header -->
    <div class="mb-8 flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">{{ $parent->first_name }} {{ $parent->last_name }}</h1>
            <p class="text-gray-600 mt-2">Parent/Guardian profile information</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('admin.parents.edit', $parent->id) }}" class="inline-flex items-center px-6 py-3 bg-primary-600 text-white font-medium rounded-lg hover:bg-primary-700 transition-colors gap-2">
                <i class="fas fa-edit"></i>Edit
            </a>
            <a href="{{ route('admin.parents.children', $parent->id) }}" class="inline-flex items-center px-6 py-3 bg-green-600 text-white font-medium rounded-lg hover:bg-green-700 transition-colors gap-2">
                <i class="fas fa-users"></i>Manage Children
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Profile Information -->
        <div class="md:col-span-2 bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-bold text-gray-900 mb-6">Profile Information</h2>
            
            <div class="space-y-6">
                <div class="grid grid-cols-2 gap-6">
                    <div>
                        <label class="text-sm font-medium text-gray-500">First Name</label>
                        <p class="text-lg font-semibold text-gray-900">{{ $parent->first_name }}</p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-500">Last Name</label>
                        <p class="text-lg font-semibold text-gray-900">{{ $parent->last_name }}</p>
                    </div>
                </div>

                <div>
                    <label class="text-sm font-medium text-gray-500">Email</label>
                    <p class="text-lg font-semibold text-gray-900">{{ $parent->user->email }}</p>
                </div>

                <div class="grid grid-cols-2 gap-6">
                    <div>
                        <label class="text-sm font-medium text-gray-500">Phone</label>
                        <p class="text-lg font-semibold text-gray-900">{{ $parent->phone ?? 'Not provided' }}</p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-500">Occupation</label>
                        <p class="text-lg font-semibold text-gray-900">{{ $parent->occupation ?? 'Not provided' }}</p>
                    </div>
                </div>

                <div>
                    <label class="text-sm font-medium text-gray-500">Address</label>
                    <p class="text-lg font-semibold text-gray-900">{{ $parent->address ?? 'Not provided' }}</p>
                </div>

                <div>
                    <label class="text-sm font-medium text-gray-500">Status</label>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold {{ $parent->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                        {{ ucfirst($parent->status) }}
                    </span>
                </div>

                <div class="grid grid-cols-2 gap-6 pt-4 border-t border-gray-200">
                    <div>
                        <label class="text-sm font-medium text-gray-500">Created</label>
                        <p class="text-sm text-gray-600">{{ $parent->created_at->format('d M Y H:i') }}</p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-500">Last Updated</label>
                        <p class="text-sm text-gray-600">{{ $parent->updated_at->format('d M Y H:i') }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-bold text-gray-900 mb-6">Actions</h2>
            
            <div class="space-y-3">
                <a href="{{ route('admin.parents.children', $parent->id) }}" class="block w-full px-4 py-3 bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-100 transition-colors text-center font-medium">
                    <i class="fas fa-users mr-2"></i>View Children
                </a>
                <a href="{{ route('admin.parents.edit', $parent->id) }}" class="block w-full px-4 py-3 bg-primary-50 text-primary-600 rounded-lg hover:bg-primary-100 transition-colors text-center font-medium">
                    <i class="fas fa-edit mr-2"></i>Edit Information
                </a>
                <form action="{{ route('admin.parents.destroy', $parent->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this parent account?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="block w-full px-4 py-3 bg-red-50 text-red-600 rounded-lg hover:bg-red-100 transition-colors text-center font-medium">
                        <i class="fas fa-trash mr-2"></i>Delete Account
                    </button>
                </form>
            </div>

            <div class="mt-6 pt-6 border-t border-gray-200">
                <a href="{{ route('admin.parents.index') }}" class="text-primary-600 hover:text-primary-700 text-sm font-medium">
                    <i class="fas fa-arrow-left mr-2"></i>Back to Parents
                </a>
            </div>
        </div>
    </div>

    <!-- Financial Records Section -->
    <div class="mt-8">
        <h2 class="text-2xl font-bold text-gray-900 mb-6">Financial Records</h2>
        
        @php
            // Get all children of this parent
            $children = $parent->children ?? collect();
            
            // Get all bills for these children
            $bills = \App\Models\StudentBill::whereIn('student_id', $children->pluck('id'))
                ->with('student')
                ->latest()
                ->get();
            
            // Calculate financial summary
            $totalBills = $bills->sum('total_amount');
            $totalPaid = $bills->sum('paid_amount');
            $totalOutstanding = $bills->sum('balance_due');
        @endphp

        <!-- Financial Summary Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm font-medium">Total Billed</p>
                        <p class="text-3xl font-bold text-gray-900 mt-2">₦{{ number_format($totalBills, 2) }}</p>
                    </div>
                    <div class="text-4xl text-blue-100">
                        <i class="fas fa-file-invoice"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm font-medium">Amount Paid</p>
                        <p class="text-3xl font-bold text-green-600 mt-2">₦{{ number_format($totalPaid, 2) }}</p>
                    </div>
                    <div class="text-4xl text-green-100">
                        <i class="fas fa-check-circle"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm font-medium">Outstanding</p>
                        <p class="text-3xl font-bold {{ $totalOutstanding > 0 ? 'text-red-600' : 'text-green-600' }} mt-2">₦{{ number_format($totalOutstanding, 2) }}</p>
                    </div>
                    <div class="text-4xl {{ $totalOutstanding > 0 ? 'text-red-100' : 'text-green-100' }}">
                        <i class="fas fa-exclamation-circle"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm font-medium">Total Bills</p>
                        <p class="text-3xl font-bold text-gray-900 mt-2">{{ $bills->count() }}</p>
                    </div>
                    <div class="text-4xl text-purple-100">
                        <i class="fas fa-list"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bills Table -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Student Bills</h3>
            </div>
            
            <table class="w-full">
                <thead class="bg-gray-100 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-900 uppercase tracking-wider">Student</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-900 uppercase tracking-wider">Bill Reference</th>
                        <th class="px-6 py-3 text-right text-xs font-semibold text-gray-900 uppercase tracking-wider">Total Amount</th>
                        <th class="px-6 py-3 text-right text-xs font-semibold text-gray-900 uppercase tracking-wider">Amount Paid</th>
                        <th class="px-6 py-3 text-right text-xs font-semibold text-gray-900 uppercase tracking-wider">Outstanding</th>
                        <th class="px-6 py-3 text-center text-xs font-semibold text-gray-900 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-center text-xs font-semibold text-gray-900 uppercase tracking-wider">Date</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse ($bills as $bill)
                        @php
                            $outstanding = $bill->amount - $bill->amount_paid;
                        @endphp
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4">
                                <p class="font-semibold text-gray-900">{{ $bill->student->first_name }} {{ $bill->student->last_name }}</p>
                                <p class="text-sm text-gray-600">{{ $bill->student->admission_number }}</p>
                            </td>
                            <td class="px-6 py-4">
                                <p class="text-sm text-gray-600">{{ $bill->bill_number ?? 'N/A' }}</p>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <p class="font-semibold text-gray-900">₦{{ number_format($bill->total_amount, 2) }}</p>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <p class="font-semibold text-green-600">₦{{ number_format($bill->paid_amount, 2) }}</p>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <p class="font-semibold {{ $bill->balance_due > 0 ? 'text-red-600' : 'text-green-600' }}">₦{{ number_format($bill->balance_due, 2) }}</p>
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if($bill->status === 'paid')
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800">
                                        Paid
                                    </span>
                                @elseif($bill->status === 'pending')
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-800">
                                        Unpaid
                                    </span>
                                @elseif($bill->status === 'outstanding')
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-800">
                                        Outstanding
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-800">
                                        {{ ucfirst($bill->status) }}
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center">
                                <p class="text-sm text-gray-600">{{ $bill->created_at->format('d M Y') }}</p>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-8 text-center">
                                <div class="text-gray-500">
                                    <i class="fas fa-inbox text-4xl mb-2"></i>
                                    <p class="mt-2">No bills found for this parent's children.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

