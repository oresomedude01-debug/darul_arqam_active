@extends('layouts.spa')

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Payment History</h1>
            <p class="text-gray-600 mt-1">View all payment records and generate reports</p>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow p-4">
        <form action="{{ route('payments.reports.payment-history') }}" method="GET" class="grid grid-cols-1 md:grid-cols-5 gap-4">
            <!-- Date From -->
            <div>
                <label class="block text-sm font-medium text-gray-900 mb-1">From Date</label>
                <input type="date" name="from_date" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                       value="{{ request('from_date') }}">
            </div>

            <!-- Date To -->
            <div>
                <label class="block text-sm font-medium text-gray-900 mb-1">To Date</label>
                <input type="date" name="to_date" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                       value="{{ request('to_date') }}">
            </div>

            <!-- Payment Method -->
            <div>
                <label class="block text-sm font-medium text-gray-900 mb-1">Payment Method</label>
                <select name="payment_method" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                    <option value="">All Methods</option>
                    <option value="Online" @selected(request('payment_method') === 'Online')>Online</option>
                    <option value="Cash" @selected(request('payment_method') === 'Cash')>Cash</option>
                    <option value="Bank Transfer" @selected(request('payment_method') === 'Bank Transfer')>Bank Transfer</option>
                    <option value="Cheque" @selected(request('payment_method') === 'Cheque')>Cheque</option>
                </select>
            </div>

            <!-- Class -->
            <div>
                <label class="block text-sm font-medium text-gray-900 mb-1">Class</label>
                <select name="class_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                    <option value="">All Classes</option>
                    @foreach($classes as $class)
                    <option value="{{ $class->id }}" @selected(request('class_id') == $class->id)>{{ $class->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Filter Button -->
            <div class="flex items-end gap-2">
                <button type="submit" class="w-full bg-primary-600 text-white px-4 py-2 rounded-lg hover:bg-primary-700 transition">
                    Filter
                </button>
            </div>
        </form>
    </div>

    <!-- Payment Statistics -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-white rounded-lg shadow p-4">
            <p class="text-gray-600 text-sm">Total Payments</p>
            <p class="text-3xl font-bold text-gray-900 mt-2">{{ $payments->total() }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <p class="text-gray-600 text-sm">Total Amount</p>
            <p class="text-3xl font-bold text-green-600 mt-2">₦{{ number_format($payments->sum('amount'), 2) }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <p class="text-gray-600 text-sm">Average Payment</p>
            <p class="text-3xl font-bold text-blue-600 mt-2">₦{{ number_format($payments->avg('amount'), 2) }}</p>
        </div>
    </div>

    <!-- Payments Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Student</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Admission No</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Amount</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Method</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Receipt #</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($payments as $payment)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 text-sm font-medium text-gray-900">
                            {{ $payment->student->first_name }} {{ $payment->student->last_name }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $payment->student->admission_number }}</td>
                        <td class="px-6 py-4 text-sm font-medium text-gray-900">₦{{ number_format($payment->amount, 2) }}</td>
                        <td class="px-6 py-4">
                            <span class="px-3 py-1 text-xs font-medium rounded-full
                                @if($payment->payment_method === 'Online') bg-blue-100 text-blue-800
                                @elseif($payment->payment_method === 'Cash') bg-green-100 text-green-800
                                @elseif($payment->payment_method === 'Bank Transfer') bg-purple-100 text-purple-800
                                @else bg-gray-100 text-gray-800 @endif">
                                {{ $payment->payment_method }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">
                            {{ $payment->paid_at?->format('d M Y H:i') ?? 'N/A' }}
                        </td>
                        <td class="px-6 py-4 text-sm font-medium text-primary-600">{{ $payment->receipt_number }}</td>
                        <td class="px-6 py-4">
                            @if($payment->billItem)
                                <a href="{{ route('payments.bills.view', $payment->billItem->id) }}" class="text-primary-600 hover:text-primary-900 text-sm font-medium">
                                    View Bill
                                </a>
                            @else
                                <span class="text-gray-400 text-sm">N/A</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-4 text-center text-gray-600">No payments found</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($payments->hasPages())
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $payments->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
