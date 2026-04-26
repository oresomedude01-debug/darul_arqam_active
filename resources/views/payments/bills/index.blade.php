@extends('layouts.spa')

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Student Bills</h1>
            <p class="text-gray-600 mt-1">View and manage student billing records</p>
        </div>

                    <div class="flex items-center gap-2">
            <a href="{{ route('billing.generate-bills.form') }}" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition flex items-center gap-2">
                <i class="fas fa-file-invoice"></i> Generate Bills
            </a>

        </div>
    </div>



    @if(session('success'))
    <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg">
        {{ session('success') }}
    </div>
    @endif

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow p-4">
        <form action="{{ route('payments.bills.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <!-- Payment Status Filter -->
            <div>
                <label class="block text-sm font-medium text-gray-900 mb-1">Payment Status</label>
                <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                    <option value="">All Status</option>
                    <option value="pending" @selected(request('status') === 'pending')>Pending</option>
                    <option value="partial" @selected(request('status') === 'partial')>Partial</option>
                    <option value="paid" @selected(request('status') === 'paid')>Paid</option>
                    <option value="overdue" @selected(request('status') === 'overdue')>Overdue</option>
                </select>
            </div>

            <!-- Class Filter -->
            <div>
                <label class="block text-sm font-medium text-gray-900 mb-1">Class</label>
                <select name="class_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                    <option value="">All Classes</option>
                    @foreach($classes as $class)
                    <option value="{{ $class->id }}" @selected(request('class_id') == $class->id)>{{ $class->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Search -->
            <div>
                <label class="block text-sm font-medium text-gray-900 mb-1">Student</label>
                <input type="text" name="search" placeholder="Search by name..." class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                       value="{{ request('search') }}">
            </div>

            <!-- Filter Button -->
            <div class="flex items-end gap-2">
                <button type="submit" class="w-full bg-primary-600 text-white px-4 py-2 rounded-lg hover:bg-primary-700 transition">
                    Filter
                </button>
            </div>
        </form>
    </div>

    <!-- Bills Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Student</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Class</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Total Amount</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Paid</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Balance</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($bills as $bill)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">
                            <div class="text-sm font-medium text-gray-900">
                                @if($bill->student)
                                    {{ $bill->student->full_name }}
                                @else
                                    N/A
                                @endif
                            </div>
                            <div class="text-xs text-gray-500">
                                @if($bill->student)
                                    {{ $bill->student->admission_number ?? 'N/A' }}
                                @else
                                    N/A
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">
                            @if($bill->student && $bill->student->schoolClass)
                                {{ $bill->student->schoolClass->name ?? $bill->student->schoolClass->full_name ?? 'N/A' }}
                            @else
                                N/A
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm font-medium text-gray-900">₦{{ number_format($bill->total_amount, 2) }}</td>
                        <td class="px-6 py-4 text-sm font-medium text-green-600">₦{{ number_format($bill->paid_amount, 2) }}</td>
                        <td class="px-6 py-4 text-sm font-medium @if($bill->balance > 0) text-red-600 @else text-green-600 @endif">
                            ₦{{ number_format($bill->balance, 2) }}
                        </td>
                        <td class="px-6 py-4">
                            @switch($bill->status)
                                @case('paid')
                                    <span class="px-3 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800">
                                        <i class="fas fa-check-circle mr-1"></i>Paid
                                    </span>
                                    @break
                                @case('partial')
                                    <span class="px-3 py-1 text-xs font-medium rounded-full bg-blue-100 text-blue-800">
                                        <i class="fas fa-hourglass-half mr-1"></i>Partial
                                    </span>
                                    @break
                                @case('overdue')
                                    <span class="px-3 py-1 text-xs font-medium rounded-full bg-red-100 text-red-800">
                                        <i class="fas fa-exclamation-triangle mr-1"></i>Overdue
                                    </span>
                                    @break
                                @default
                                    <span class="px-3 py-1 text-xs font-medium rounded-full bg-yellow-100 text-yellow-800">
                                        <i class="fas fa-clock mr-1"></i>Pending
                                    </span>
                            @endswitch
                        </td>
                        <td class="px-6 py-4">
                            <a href="{{ route('payments.bills.view', $bill) }}" class="text-primary-600 hover:text-primary-900 text-sm font-medium">
                                View Details
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-4 text-center text-gray-600">No bills found</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($bills->hasPages())
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $bills->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
