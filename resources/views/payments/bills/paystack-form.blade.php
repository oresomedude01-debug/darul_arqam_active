@extends('layouts.spa')

@section('title', 'Pay with Paystack')

@section('content')
<div class="max-w-2xl mx-auto">
    <!-- Header -->
    <div class="mb-6">
        <a href="{{ route('payments.bills.view', $bill->id) }}" class="text-primary-600 hover:text-primary-700 flex items-center gap-1 mb-4">
            <i class="fas fa-arrow-left"></i>Back to Bill
        </a>
        <h1 class="text-3xl font-bold text-gray-900">Online Payment with Paystack</h1>
        <p class="text-gray-600 mt-2">Secure payment processing</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Bill Summary -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <h2 class="text-xl font-bold text-gray-900 mb-4">Student Information</h2>
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Student Name:</span>
                        <span class="font-semibold text-gray-900">{{ $bill->student->full_name }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Admission Number:</span>
                        <span class="font-semibold text-gray-900">{{ $bill->student->admission_number }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Class:</span>
                        <span class="font-semibold text-gray-900">{{ $bill->student->schoolClass->name }}</span>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <h2 class="text-xl font-bold text-gray-900 mb-4">Bill Items</h2>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700">Item</th>
                                <th class="px-4 py-2 text-right text-sm font-semibold text-gray-700">Amount</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y">
                            @foreach($bill->billItems as $item)
                                <tr>
                                    <td class="px-4 py-3 text-gray-900">{{ $item->name }}</td>
                                    <td class="px-4 py-3 text-right text-gray-900">{{ number_format($item->amount, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-bold text-gray-900 mb-4">Payment Amount</h2>
                
                <form id="paystack-form" action="{{ route('payments.bills.paystack-initialize', $bill->id) }}" method="POST" class="space-y-4">
                    @csrf

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Amount to Pay</label>
                        <div class="relative">
                            <span class="absolute left-3 top-2.5 text-gray-600 text-lg">₦</span>
                            <input type="number" 
                                   name="amount" 
                                   id="amount"
                                   step="0.01"
                                   min="0.01"
                                   max="{{ $bill->balance }}"
                                   value="{{ $bill->balance }}"
                                   class="w-full pl-8 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                                   required>
                        </div>
                        <p class="text-sm text-gray-500 mt-2">Maximum amount: ₦{{ number_format($bill->balance, 2) }}</p>
                    </div>

                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                        <p class="text-sm text-blue-900">
                            <i class="fas fa-info-circle mr-2"></i>
                            <strong>Security Note:</strong> This payment is processed through Paystack's secure payment gateway. Your card details are never stored on our servers.
                        </p>
                    </div>

                    <button type="button" 
                            id="pay-with-paystack"
                            class="w-full bg-primary-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-primary-700 transition flex items-center justify-center gap-2">
                        <i class="fas fa-lock"></i>
                        Pay with Paystack
                    </button>

                    <a href="{{ route('payments.bills.view', $bill->id) }}" 
                       class="w-full border border-gray-300 text-gray-700 px-6 py-3 rounded-lg font-semibold hover:bg-gray-50 transition text-center">
                        Cancel
                    </a>
                </form>
            </div>
        </div>

        <!-- Payment Summary Sidebar -->
        <div>
            <div class="bg-white rounded-lg shadow-md p-6 sticky top-6">
                <h2 class="text-lg font-bold text-gray-900 mb-4">Payment Summary</h2>
                
                <div class="space-y-4 pb-4 border-b border-gray-200">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Total Due:</span>
                        <span class="font-semibold text-gray-900">₦{{ number_format($bill->total_amount, 2) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Already Paid:</span>
                        <span class="font-semibold text-green-600">-₦{{ number_format($bill->paid_amount, 2) }}</span>
                    </div>
                    @if($bill->discount_amount > 0)
                        <div class="flex justify-between">
                            <span class="text-gray-600">Discount:</span>
                            <span class="font-semibold text-green-600">-₦{{ number_format($bill->discount_amount, 2) }}</span>
                        </div>
                    @endif
                </div>

                <div class="flex justify-between items-center py-4 mb-4">
                    <span class="text-lg font-bold text-gray-900">Balance Due:</span>
                    <span class="text-2xl font-bold text-primary-600">₦{{ number_format($bill->balance, 2) }}</span>
                </div>

                <div class="bg-gray-50 rounded-lg p-4">
                    <h3 class="font-semibold text-gray-900 mb-3">Payment Status</h3>
                    <div class="space-y-2 text-sm">
                        <div class="flex items-center gap-2">
                            <i class="fas fa-{{ $bill->payment_status === 'Paid' ? 'check-circle text-green-600' : 'clock text-yellow-600' }}"></i>
                            <span>Status: <span class="font-semibold">{{ $bill->payment_status }}</span></span>
                        </div>
                        <div class="flex items-center gap-2">
                            <i class="fas fa-percentage text-gray-400"></i>
                            <span>
                                <span class="font-semibold">{{ $bill->paid_amount > 0 ? round(($bill->paid_amount / $bill->total_amount) * 100) : 0 }}%</span> paid
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Paystack JS Library -->
<script src="https://js.paystack.co/v1/inline.js"></script>

<script>
    document.getElementById('pay-with-paystack').addEventListener('click', function() {
        const amount = document.getElementById('amount').value;
        
        if (!amount || parseFloat(amount) <= 0) {
            alert('Please enter a valid amount');
            return;
        }

        // Submit the form to initialize payment
        document.getElementById('paystack-form').submit();
    });
</script>
@endsection
