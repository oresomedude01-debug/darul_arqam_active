@extends('layouts.spa')

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Student Bill</h1>
            <p class="text-gray-600 mt-1">{{ $bill->student->first_name }} {{ $bill->student->last_name }} ({{ $bill->student->admission_number }})</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('payments.bills.print-invoice', $bill) }}" target="_blank" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition flex items-center gap-2">
                <i class="fas fa-print"></i> Print Invoice
            </a>
            <a href="{{ route('payments.bills.index') }}" class="bg-gray-200 text-gray-900 px-4 py-2 rounded-lg hover:bg-gray-300 transition">
                Back
            </a>
        </div>
    </div>

    @if(session('success'))
    <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg">
        {{ session('success') }}
    </div>
    @endif

    <!-- Bill Summary -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-lg shadow p-4">
            <p class="text-gray-600 text-sm">Total Amount Due</p>
            <p class="text-2xl font-bold text-gray-900 mt-1">₦{{ number_format($bill->total_amount, 2) }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <p class="text-gray-600 text-sm">Amount Paid</p>
            <p class="text-2xl font-bold text-green-600 mt-1">₦{{ number_format($bill->paid_amount, 2) }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <p class="text-gray-600 text-sm">Balance Due</p>
            <p class="text-2xl font-bold @if($bill->balance_due > 0) text-red-600 @else text-green-600 @endif mt-1">₦{{ number_format($bill->balance_due, 2) }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <p class="text-gray-600 text-sm">Payment Status</p>
            <p class="text-lg font-bold mt-1">
                @if($bill->status === 'paid')
                    <span class="text-green-600">Fully Paid</span>
                @elseif($bill->status === 'partial')
                    <span class="text-yellow-600">Partially Paid</span>
                @else
                    <span class="text-red-600">Pending</span>
                @endif
            </p>
        </div>
    </div>

    <!-- Status Badge -->
    <div class="bg-white rounded-lg shadow p-4">
        <p class="text-sm font-medium text-gray-600">Payment Status</p>
        <div class="mt-2">
            @if($bill->status === 'paid')
            <span class="px-4 py-2 text-sm font-semibold rounded-full bg-green-100 text-green-800">✓ Fully Paid</span>
            @elseif($bill->status === 'partial')
            <span class="px-4 py-2 text-sm font-semibold rounded-full bg-yellow-100 text-yellow-800">⚠ Partially Paid</span>
            @else
            <span class="px-4 py-2 text-sm font-semibold rounded-full bg-red-100 text-red-800">✕ Pending</span>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Fee Breakdown -->
        <div class="lg:col-span-2 bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">Fee Breakdown</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Fee Item</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Amount</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($bill->billItems as $item)
                        <tr>
                            <td class="px-6 py-3 text-sm text-gray-900">{{ $item->feeItem->name ?? 'Unknown Fee' }}</td>
                            <td class="px-6 py-3 text-sm font-medium text-gray-900">₦{{ number_format($item->amount, 2) }}</td>
                        </tr>
                        @endforeach
                        <tr class="bg-gray-50 font-semibold">
                            <td class="px-6 py-3 text-sm">Total</td>
                            <td class="px-6 py-3 text-sm">₦{{ number_format($bill->billItems->sum('amount'), 2) }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Actions Sidebar -->
        <div class="space-y-4">
            @if($bill->balance_due > 0)
            <!-- Add Payment Form -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Record Payment</h3>
                <form action="{{ route('payments.bills.add-payment', $bill) }}" method="POST" class="space-y-4">
                    @csrf

                    <div>
                        <label class="block text-sm font-medium text-gray-900 mb-1">Amount</label>
                        <input type="number" name="amount" step="0.01" max="{{ $bill->balance_due }}" placeholder="0.00" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-900 mb-1">Payment Method</label>
                        <select name="payment_method" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                            <option value="cash">Cash</option>
                            <option value="bank_transfer">Bank Transfer</option>
                            <option value="cheque">Cheque</option>
                            <option value="online">Online</option>
                            <option value="waiver">Waiver</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-900 mb-1">Notes</label>
                        <textarea name="notes" rows="2" placeholder="Optional notes..." class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent"></textarea>
                    </div>

                    <button type="submit" class="w-full bg-primary-600 text-white px-4 py-2 rounded-lg hover:bg-primary-700 transition font-medium">
                        <i class="fas fa-plus mr-2"></i>Record Payment
                    </button>
                </form>
            </div>

            <!-- Online Payment with Paystack -->
            @php
                $paystackConfigured = \App\Models\SchoolSetting::getInstance()->paystack_public_key && \App\Models\SchoolSetting::getInstance()->paystack_secret_key;
            @endphp
            @if($paystackConfigured)
            <div class="bg-gradient-to-br from-blue-50 to-indigo-50 border border-indigo-200 rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Pay Online</h3>
                <p class="text-sm text-gray-600 mb-4">Secure payment via Paystack</p>
                <a href="{{ route('payments.bills.paystack-form', $bill) }}" 
                   class="w-full bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 transition font-medium flex items-center justify-center gap-2">
                    <i class="fas fa-credit-card"></i>Pay with Paystack
                </a>
            </div>
            @endif

            <!-- Add Discount Form -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Apply Discount</h3>
                <form action="{{ route('payments.bills.add-discount', $bill) }}" method="POST" class="space-y-4">
                    @csrf

                    <div>
                        <label class="block text-sm font-medium text-gray-900 mb-1">Discount Amount</label>
                        <input type="number" name="discount_amount" step="0.01" max="{{ $bill->balance }}" placeholder="0.00" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-900 mb-1">Reason</label>
                        <textarea name="notes" rows="2" placeholder="e.g., Scholarship, Hardship..." class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent"></textarea>
                    </div>

                    <button type="submit" class="w-full bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition font-medium">
                        <i class="fas fa-percent mr-2"></i>Apply Discount
                    </button>
                </form>
            </div>
            @else
            <div class="bg-green-50 border border-green-200 rounded-lg p-6">
                <p class="text-green-800 font-semibold">✓ Bill Fully Paid</p>
                <p class="text-green-700 text-sm mt-2">No further action needed for this bill.</p>
            </div>
            @endif
        </div>
    </div>

    <!-- Payment History -->
    @if($bill->payments->count() > 0)
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900">Payment History</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Amount</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Method</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Receipt #</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Notes</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($bill->payments as $payment)
                    <tr>
                        <td class="px-6 py-3 text-sm font-medium text-gray-900">₦{{ number_format($payment->amount, 2) }}</td>
                        <td class="px-6 py-3 text-sm text-gray-600">{{ $payment->payment_method }}</td>
                        <td class="px-6 py-3 text-sm text-gray-600">{{ $payment->payment_date?->format('d M Y H:i') ?? 'N/A' }}</td>
                        <td class="px-6 py-3 text-sm font-medium text-primary-600">{{ $payment->receipt_number }}</td>
                        <td class="px-6 py-3 text-sm text-gray-600">{{ $payment->notes ?? '-' }}</td>
                        <td class="px-6 py-3 text-sm">
                            @if($payment->receipt)
                                <div class="flex gap-2">
                                    <a href="{{ route('payments.receipts.view', $payment->receipt->id) }}" 
                                       class="text-primary-600 hover:text-primary-800 font-medium" title="View Receipt">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('payments.receipts.print', $payment->receipt->id) }}" 
                                       target="_blank" class="text-blue-600 hover:text-blue-800 font-medium" title="Print Receipt">
                                        <i class="fas fa-print"></i>
                                    </a>
                                    <a href="{{ route('payments.receipts.pdf', $payment->receipt->id) }}" 
                                       target="_blank" class="text-red-600 hover:text-red-800 font-medium" title="PDF">
                                        <i class="fas fa-file-pdf"></i>
                                    </a>
                                </div>
                            @else
                                <form action="{{ route('payments.receipts.generate', $payment) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="text-yellow-600 hover:text-yellow-800 font-medium" title="Generate Receipt">
                                        <i class="fas fa-receipt"></i> Generate
                                    </button>
                                </form>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif
</div>
@endsection
