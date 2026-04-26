@extends('layouts.spa')

@section('title', 'Select Payment Method')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 via-purple-50 to-slate-100 py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
        <!-- Header -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-4xl font-bold text-gray-900">Payment Methods</h1>
                <p class="text-gray-600 mt-2">Select how you'd like to pay for school bills</p>
            </div>
            <a href="{{ route('parent-portal.bills') }}" class="inline-flex items-center px-6 py-3 bg-gray-200 text-gray-700 font-semibold rounded-xl hover:bg-gray-300 transition-all">
                <i class="fas fa-arrow-left mr-2"></i>Back to Bills
            </a>
        </div>

        <!-- Summary Card -->
        <div class="bg-gradient-to-r from-purple-600 to-purple-700 rounded-2xl shadow-lg p-8 text-white">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <p class="text-purple-200 text-sm font-medium uppercase tracking-wide">Bills to Pay</p>
                    <p class="text-4xl font-bold mt-2">{{ $bills->count() }}</p>
                </div>
                <div>
                    <p class="text-purple-200 text-sm font-medium uppercase tracking-wide">Total Amount</p>
                    <p class="text-4xl font-bold mt-2">₦{{ number_format($totalAmount, 2) }}</p>
                </div>
                <div>
                    <p class="text-purple-200 text-sm font-medium uppercase tracking-wide">Students</p>
                    <p class="text-4xl font-bold mt-2">{{ $bills->groupBy('student_id')->count() }}</p>
                </div>
            </div>
        </div>

        <!-- Bills Summary -->
        <div class="bg-white rounded-2xl shadow-lg border border-gray-200 p-6">
            <h2 class="text-xl font-bold text-gray-900 mb-4">Bills to Pay</h2>
            <div class="space-y-3">
                @foreach($bills->groupBy('student_id') as $studentId => $studentBills)
                    <div class="flex items-start justify-between p-3 bg-gray-50 rounded-lg">
                        <div>
                            <p class="font-semibold text-gray-900">{{ $studentBills->first()->student->first_name }} {{ $studentBills->first()->student->last_name }}</p>
                            <p class="text-sm text-gray-600 mt-1">
                                <i class="fas fa-file-invoice mr-1"></i>{{ $studentBills->count() }} bill(s)
                            </p>
                        </div>
                        <p class="font-bold text-lg text-purple-600">₦{{ number_format($studentBills->sum('amount'), 2) }}</p>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Payment Methods -->
        @if(!empty($paymentMethods))
        <div>
            <h2 class="text-2xl font-bold text-gray-900 mb-4">Available Payment Methods</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @foreach($paymentMethods as $method => $details)
                    @if($method === 'paystack')
                        <!-- Paystack Payment Form -->
                        <form id="paystackForm" method="POST" action="{{ route('parent-payments.paystack') }}" class="group">
                            @csrf
                            
                            <!-- Hidden bill IDs -->
                            @foreach($bills as $bill)
                            <input type="hidden" name="bills[]" value="{{ $bill->id }}">
                            @endforeach
                            
                            <button type="submit" class="w-full h-full bg-white rounded-2xl shadow-lg border-2 border-gray-200 hover:border-blue-400 hover:shadow-xl transition-all p-8 text-left group">
                                <div class="flex items-start justify-between">
                                    <div class="flex-1">
                                        <div class="flex items-center gap-3 mb-2">
                                            <i class="fas {{ $details['icon'] }} text-3xl text-blue-600"></i>
                                            <h3 class="text-2xl font-bold text-gray-900">{{ $details['label'] }}</h3>
                                        </div>
                                        <p class="text-gray-600 text-sm mt-4">{{ $details['description'] }}</p>
                                    </div>
                                    <div class="ml-4">
                                        <i class="fas fa-chevron-right text-2xl text-gray-300 group-hover:text-blue-600 transition-colors"></i>
                                    </div>
                                </div>
                                
                                <div class="mt-6 pt-6 border-t border-gray-200 text-sm text-gray-600">
                                    <i class="fas fa-lock mr-1 text-green-600"></i>Secure payment powered by Paystack
                                </div>
                            </button>
                        </form>
                    @else
                        <!-- Other Payment Methods -->
                        <form method="POST" action="{{ route('parent-payments.manual') }}" class="group">
                            @csrf
                            
                            <!-- Hidden bill IDs -->
                            @foreach($bills as $bill)
                            <input type="hidden" name="bills[]" value="{{ $bill->id }}">
                            @endforeach
                            <input type="hidden" name="payment_method" value="{{ $method }}">
                            
                            <button type="submit" class="w-full h-full bg-white rounded-2xl shadow-lg border-2 border-gray-200 hover:border-purple-400 hover:shadow-xl transition-all p-8 text-left group">
                                <div class="flex items-start justify-between">
                                    <div class="flex-1">
                                        <div class="flex items-center gap-3 mb-2">
                                            <i class="fas {{ $details['icon'] }} text-3xl text-purple-600"></i>
                                            <h3 class="text-2xl font-bold text-gray-900">{{ $details['label'] }}</h3>
                                        </div>
                                        <p class="text-gray-600 text-sm mt-4">{{ $details['description'] }}</p>
                                    </div>
                                    <div class="ml-4">
                                        <i class="fas fa-chevron-right text-2xl text-gray-300 group-hover:text-purple-600 transition-colors"></i>
                                    </div>
                                </div>
                                
                                <!-- Method specific details -->
                                @if($method === 'bank_transfer')
                                <div class="mt-6 pt-6 border-t border-gray-200 text-sm text-gray-600">
                                    <i class="fas fa-info-circle mr-1"></i>You'll receive bank details for transfer
                                </div>
                                @elseif($method === 'cash')
                                <div class="mt-6 pt-6 border-t border-gray-200 text-sm text-gray-600">
                                    <i class="fas fa-map-marker-alt mr-1"></i>Pay at school office during business hours
                                </div>
                                @elseif($method === 'cheque')
                                <div class="mt-6 pt-6 border-t border-gray-200 text-sm text-gray-600">
                                    <i class="fas fa-file-signature mr-1"></i>Cheque should be made to school name
                                </div>
                                @endif
                            </button>
                        </form>
                    @endif
                @endforeach
            </div>
        </div>
        @else
        <div class="bg-yellow-50 border-2 border-yellow-200 rounded-2xl p-8 text-center">
            <i class="fas fa-exclamation-triangle text-4xl text-yellow-600 mb-4 block"></i>
            <h3 class="text-xl font-bold text-yellow-900 mb-2">No Payment Methods Available</h3>
            <p class="text-yellow-700 mb-4">You don't have access to any payment methods at the moment.</p>
            <a href="{{ route('parent-portal.bills') }}" class="inline-flex items-center px-6 py-3 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 transition-colors">
                <i class="fas fa-arrow-left mr-2"></i>Back to Bills
            </a>
        </div>
        @endif

        <!-- Help Section -->
        <div class="bg-white rounded-2xl shadow-lg border border-gray-200 p-8">
            <h2 class="text-xl font-bold text-gray-900 mb-4">Need Help?</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-sm text-gray-600">
                <div>
                    <p class="font-semibold text-gray-900 mb-2"><i class="fas fa-circle text-purple-600 mr-2"></i>Payment Security</p>
                    <p>All payments are encrypted and processed securely. Your financial information is protected.</p>
                </div>
                <div>
                    <p class="font-semibold text-gray-900 mb-2"><i class="fas fa-circle text-purple-600 mr-2"></i>Confirmation</p>
                    <p>You'll receive an instant confirmation after payment. Check your email for receipt.</p>
                </div>
                <div>
                    <p class="font-semibold text-gray-900 mb-2"><i class="fas fa-circle text-purple-600 mr-2"></i>Payment History</p>
                    <p>All your payments are tracked in your payment history for record keeping.</p>
                </div>
                <div>
                    <p class="font-semibold text-gray-900 mb-2"><i class="fas fa-circle text-purple-600 mr-2"></i>Support</p>
                    <p>Contact the school office if you have questions about any payment.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    button[type="submit"] {
        transition: all 0.3s ease;
    }
    
    button[type="submit"]:hover {
        transform: translateY(-2px);
    }
</style>

<script>
    // Handle Paystack payment
    document.getElementById('paystackForm')?.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const billIds = formData.getAll('bills[]');
        
        try {
            const response = await fetch('{{ route("parent-payments.paystack") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    bills: billIds
                })
            });

            const data = await response.json();

            if (data.success && data.authorization_url) {
                // Redirect to Paystack payment page
                window.location.href = data.authorization_url;
            } else {
                alert('Error: ' + (data.error || 'Payment initialization failed'));
            }
        } catch (error) {
            alert('Error: ' + error.message);
        }
    });
</script>
@endsection

