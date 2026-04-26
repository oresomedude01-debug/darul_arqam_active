@extends('layouts.spa')

@section('title', 'My Bills')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 via-purple-50 to-slate-100 py-4 md:py-6 px-3 md:px-4 lg:px-6">
    <div class="max-w-6xl mx-auto space-y-4 md:space-y-5">
        <!-- Header -->
        <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between md:gap-4">
            <div>
                <h1 class="text-3xl md:text-4xl font-bold text-gray-900">School Bills</h1>
                <p class="text-gray-600 mt-1 md:mt-2">View and manage your school bills</p>
            </div>
            <div class="flex flex-col sm:flex-row gap-3 md:gap-4">
                <a href="{{ route('parent-portal.payment-history') }}" class="inline-flex items-center justify-center px-4 md:px-6 py-2.5 md:py-3 bg-gradient-to-r from-purple-600 to-purple-700 text-white font-semibold rounded-xl hover:from-purple-700 hover:to-purple-800 transition-all shadow-lg text-sm md:text-base whitespace-nowrap">
                    <i class="fas fa-credit-card mr-2"></i>Payment History
                </a>
                @if($bills->count() > 0)
                <button onclick="openPaymentModal()" class="inline-flex items-center justify-center px-4 md:px-6 py-2.5 md:py-3 bg-gradient-to-r from-purple-600 to-purple-700 text-white font-semibold rounded-xl hover:from-purple-700 hover:to-purple-800 transition-all shadow-lg text-sm md:text-base whitespace-nowrap">
                    <i class="fas fa-credit-card mr-2"></i>Pay Selected
                </button>
                @endif
            </div>
        </div>

        <!-- Outstanding Balance Summary -->
        @php
            $totalOutstanding = $bills->where('status', '!=', 'paid')->sum('total_amount');
        @endphp
        @if($totalOutstanding > 0)
        <div class="bg-gradient-to-r from-red-50 to-orange-50 rounded-xl border-2 border-red-200 p-4 md:p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-red-600 font-semibold text-sm md:text-base">Total Outstanding Balance</p>
                    <p class="text-3xl md:text-4xl font-bold text-red-900 mt-1">₦{{ number_format($totalOutstanding, 2) }}</p>
                </div>
                <button onclick="openPaymentModal()" class="inline-flex items-center px-4 py-2.5 bg-red-600 text-white font-semibold rounded-lg hover:bg-red-700 transition-colors text-sm md:text-base">
                    <i class="fas fa-money-bill-wave mr-2"></i>Pay Now
                </button>
            </div>
        </div>
        @endif

        <!-- Filters -->
        <div class="bg-white rounded-xl shadow-lg border border-purple-100 p-3 md:p-4">
            <form method="GET" class="flex flex-col md:flex-row gap-2 md:gap-3">
                <!-- Student Filter -->
                <select name="student" class="flex-1 px-3 md:px-4 py-2 text-sm md:text-base border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                    <option value="">All Students</option>
                    @foreach($children as $child)
                    <option value="{{ $child->id }}" {{ $selectedStudent?->id === $child->id ? 'selected' : '' }}>
                        {{ $child->first_name }} {{ $child->last_name }}
                    </option>
                    @endforeach
                </select>

                <!-- Status Filter -->
                <select name="status" class="flex-1 px-3 md:px-4 py-2 text-sm md:text-base border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                    <option value="">All Status</option>
                    <option value="paid" {{ request('status') === 'paid' ? 'selected' : '' }}>Paid</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="overdue" {{ request('status') === 'overdue' ? 'selected' : '' }}>Overdue</option>
                </select>

                <button type="submit" class="px-3 md:px-6 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors font-medium text-sm md:text-base whitespace-nowrap">
                    <i class="fas fa-filter mr-1 md:mr-2"></i><span class="hidden md:inline">Filter</span>
                </button>
            </form>
        </div>

        <!-- Bills Table -->
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden border border-purple-100">
            <div class="bg-gradient-to-r from-purple-600 to-indigo-600 px-4 md:px-6 py-3 md:py-4">
                <h2 class="text-lg md:text-xl font-bold text-white flex items-center gap-2">
                    <i class="fas fa-file-invoice"></i>Bills Overview
                </h2>
            </div>

            <!-- Grid View (Mobile) -->
            <div class="md:hidden p-3 space-y-3">
                @if($bills->count() > 0)
                    @foreach($bills as $bill)
                    <div class="bg-gradient-to-br from-white to-purple-50 rounded-lg shadow border border-purple-100 p-3 hover:shadow-md transition-all">
                        <div class="space-y-3">
                            <!-- Header -->
                            <div class="border-b border-purple-100 pb-3">
                                <p class="font-bold text-gray-900 text-sm">{{ $bill->student->first_name }} {{ $bill->student->last_name }}</p>
                                <p class="text-xs text-gray-600 mt-0.5">{{ $bill->student->admission_number }}</p>
                            </div>

                            <!-- Session/Term -->
                            <div>
                                <p class="text-xs text-gray-600 font-semibold">{{ $bill->academicSession->session }} - {{ $bill->academicTerm->term }}</p>
                            </div>

                            <!-- Amount and Status Row -->
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-xs text-gray-600">Amount Due</p>
                                    <p class="text-xl font-bold text-gray-900">₦{{ number_format($bill->total_amount, 2) }}</p>
                                </div>
                                <span class="inline-block px-2 py-1 rounded-lg font-semibold text-xs {{ 
                                    $bill->status === 'paid' ? 'bg-green-100 text-green-800' :
                                    ($bill->status === 'overdue' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800')
                                }}">
                                    {{ ucfirst($bill->status) }}
                                </span>
                            </div>

                            <!-- Due Date -->
                            <p class="text-xs text-gray-600">Due: <span class="font-semibold">{{ $bill->due_date->format('M d, Y') }}</span></p>

                            <!-- Actions -->
                            <div class="flex gap-2 pt-2">
                                @if($bill->status !== 'paid')
                                    <button onclick="payIndividualBill({{ $bill->id }})" class="flex-1 px-3 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors font-medium text-xs">
                                        <i class="fas fa-money-bill-wave mr-1"></i>Pay
                                    </button>
                                @endif
                                <a href="#" class="flex-1 px-3 py-2 bg-blue-100 text-blue-700 rounded-lg hover:bg-blue-200 transition-colors font-medium text-xs text-center">
                                    <i class="fas fa-eye mr-1"></i>View
                                </a>
                            </div>
                        </div>
                    </div>
                    @endforeach
                @else
                    <div class="p-8 text-center bg-white rounded-lg border border-gray-200">
                        <i class="fas fa-inbox text-4xl text-gray-300 mb-3 block"></i>
                        <h3 class="text-lg font-bold text-gray-900 mb-1">No Bills Found</h3>
                        <p class="text-sm text-gray-600">All fees are paid up!</p>
                    </div>
                @endif
            </div>

            <!-- Table View (Desktop) -->
            <div class="hidden md:block overflow-x-auto">
                @if($bills->count() > 0)
                <form id="paymentForm" method="POST" action="{{ route('parent-payments.paystack') }}">
                    @csrf
                    <table class="w-full">
                        <thead class="bg-gradient-to-r from-purple-50 to-indigo-50 border-b-2 border-purple-200">
                            <tr>
                                <th class="px-3 md:px-6 py-3 md:py-4 text-left"><input type="checkbox" id="selectAll" onchange="toggleAll()" class="w-4 h-4"></th>
                                <th class="px-3 md:px-6 py-3 md:py-4 text-left text-xs md:text-sm font-bold text-purple-900">Student</th>
                                <th class="px-3 md:px-6 py-3 md:py-4 text-left text-xs md:text-sm font-bold text-purple-900">Session/Term</th>
                                <th class="px-3 md:px-6 py-3 md:py-4 text-center text-xs md:text-sm font-bold text-purple-900">Amount</th>
                                <th class="px-3 md:px-6 py-3 md:py-4 text-center text-xs md:text-sm font-bold text-purple-900">Due Date</th>
                                <th class="px-3 md:px-6 py-3 md:py-4 text-center text-xs md:text-sm font-bold text-purple-900">Status</th>
                                <th class="px-3 md:px-6 py-3 md:py-4 text-center text-xs md:text-sm font-bold text-purple-900">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach($bills as $bill)
                            <tr class="hover:bg-purple-50/50 transition-colors">
                                <td class="px-3 md:px-6 py-3 md:py-4">
                                    <input type="checkbox" name="bills[]" value="{{ $bill->id }}" class="billCheckbox w-4 h-4">
                                </td>
                                <td class="px-3 md:px-6 py-3 md:py-4">
                                    <div>
                                        <p class="font-semibold text-gray-900 text-sm md:text-base">{{ $bill->student->first_name }} {{ $bill->student->last_name }}</p>
                                        <p class="text-xs text-gray-600 mt-0.5">{{ $bill->student->admission_number }}</p>
                                    </div>
                                </td>
                                <td class="px-3 md:px-6 py-3 md:py-4">
                                    <div class="text-xs md:text-sm">
                                        <p class="font-medium text-gray-900">{{ $bill->academicSession->session }}</p>
                                        <p class="text-gray-600">{{ $bill->academicTerm->term }}</p>
                                    </div>
                                </td>
                                <td class="px-3 md:px-6 py-3 md:py-4 text-center">
                                    <span class="font-bold text-base md:text-lg text-gray-900">₦{{ number_format($bill->total_amount, 2) }}</span>
                                </td>
                                <td class="px-3 md:px-6 py-3 md:py-4 text-center">
                                    <span class="text-xs md:text-sm text-gray-600">{{ $bill->due_date->format('M d, Y') }}</span>
                                </td>
                                <td class="px-3 md:px-6 py-3 md:py-4 text-center">
                                    <span class="inline-block px-2 md:px-3 py-1 rounded-lg font-semibold text-xs md:text-sm {{ 
                                        $bill->status === 'paid' ? 'bg-green-100 text-green-800' :
                                        ($bill->status === 'overdue' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800')
                                    }}">
                                        {{ ucfirst($bill->status) }}
                                    </span>
                                </td>
                                <td class="px-3 md:px-6 py-3 md:py-4 text-center">
                                    <div class="flex items-center justify-center gap-1.5">
                                        @if($bill->status !== 'paid')
                                            <button type="button" onclick="payIndividualBill({{ $bill->id }})" class="text-purple-600 hover:text-white hover:bg-purple-600 px-2 py-1 rounded transition-colors font-medium text-xs">
                                                <i class="fas fa-money-bill-wave"></i>
                                            </button>
                                        @endif
                                        <a href="#" class="text-blue-600 hover:text-blue-700 font-medium text-xs">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </form>

                <!-- Pagination -->
                <div class="px-3 md:px-6 py-3 md:py-4 border-t border-gray-200 bg-gray-50">
                    {{ $bills->links() }}
                </div>
                @else
                <div class="px-6 py-8 md:py-12 text-center">
                    <i class="fas fa-inbox text-4xl md:text-5xl text-gray-300 mb-4 block"></i>
                    <h3 class="text-lg md:text-xl font-bold text-gray-900 mb-2">No Bills Found</h3>
                    <p class="text-sm md:text-base text-gray-600">There are no bills to display. All fees are paid up!</p>
                </div>
                @endif
            </div>
        </div>

        <!-- Summary Card -->
        @if($bills->count() > 0)
        <div class="bg-white rounded-2xl shadow-lg border border-purple-100 p-3 md:p-4">
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-2 md:gap-4">
                <div class="p-3 md:p-4 bg-yellow-50 rounded-lg border border-yellow-200">
                    <p class="text-yellow-600 font-medium text-xs md:text-sm mb-1">Pending Bills</p>
                    <p class="text-2xl md:text-3xl font-bold text-yellow-900">{{ $bills->where('status', 'pending')->count() }}</p>
                </div>
                <div class="p-3 md:p-4 bg-red-50 rounded-lg border border-red-200">
                    <p class="text-red-600 font-medium text-xs md:text-sm mb-1">Overdue Bills</p>
                    <p class="text-2xl md:text-3xl font-bold text-red-900">{{ $bills->where('status', 'overdue')->count() }}</p>
                </div>
                <div class="p-3 md:p-4 bg-green-50 rounded-lg border border-green-200">
                    <p class="text-green-600 font-medium text-xs md:text-sm mb-1">Paid Bills</p>
                    <p class="text-2xl md:text-3xl font-bold text-green-900">{{ $bills->where('status', 'paid')->count() }}</p>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>

<!-- Payment Method Modal -->
<div id="paymentModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm hidden z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full">
        <div class="bg-gradient-to-r from-purple-600 to-indigo-600 px-6 py-5">
            <h2 class="text-xl font-bold text-white">Select Payment Method</h2>
        </div>

        <div class="p-6 space-y-4">
            <!-- Paystack Option -->
            <button onclick="initiatePaystackPayment()" class="w-full p-4 border-2 border-purple-200 rounded-xl hover:bg-purple-50 transition-colors text-left group">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="font-bold text-gray-900">Paystack</h3>
                        <p class="text-sm text-gray-600">Debit Card, Bank Transfer</p>
                    </div>
                    <i class="fas fa-arrow-right text-purple-600 group-hover:translate-x-1 transition-transform"></i>
                </div>
            </button>

            @php
                $schoolSetting = \App\Models\SchoolSetting::first();
                $hasBank = $schoolSetting && $schoolSetting->bank_name && $schoolSetting->account_holder_name && $schoolSetting->account_number;
            @endphp

            @if($hasBank)
            <!-- Bank Transfer Option -->
            <button onclick="openBankTransferModal()" class="w-full p-4 border-2 border-blue-200 rounded-xl hover:bg-blue-50 transition-colors text-left group">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="font-bold text-gray-900">Bank Transfer</h3>
                        <p class="text-sm text-gray-600">Direct bank payment</p>
                    </div>
                    <i class="fas fa-arrow-right text-blue-600 group-hover:translate-x-1 transition-transform"></i>
                </div>
            </button>
            @endif

            <button onclick="closePaymentModal()" class="w-full mt-6 px-4 py-2 text-gray-600 hover:text-gray-900 font-medium">
                Cancel
            </button>
        </div>
    </div>
</div>

<!-- Bank Transfer Details Modal -->
@php
    $schoolSetting = \App\Models\SchoolSetting::first();
@endphp
<div id="bankTransferModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm hidden z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full">
        <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-6 py-5">
            <h2 class="text-xl font-bold text-white flex items-center gap-2">
                <i class="fas fa-building"></i>Bank Transfer Details
            </h2>
        </div>

        <div class="p-6 space-y-4">
            <!-- Bank Account Details -->
            <div class="bg-blue-50 rounded-lg border border-blue-200 p-4 space-y-3">
                <div>
                    <p class="text-xs text-gray-600 font-semibold">Account Name</p>
                    <p class="text-lg font-bold text-gray-900 mt-0.5">{{ $schoolSetting->bank_name ?? 'N/A' }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-600 font-semibold">Account Holder</p>
                    <p class="text-lg font-bold text-gray-900 mt-0.5">{{ $schoolSetting->account_holder_name ?? 'N/A' }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-600 font-semibold">Account Number</p>
                    <p class="text-lg font-bold text-gray-900 mt-0.5 font-mono">{{ $schoolSetting->account_number ?? 'N/A' }}</p>
                </div>
            </div>

            <!-- Confirmation Message -->
            <div class="bg-amber-50 rounded-lg border border-amber-200 p-4">
                <p class="text-sm text-amber-900 leading-relaxed">
                    <i class="fas fa-info-circle mr-2 text-amber-600"></i>
                    <strong>Payment Confirmation:</strong> After completing your bank transfer, please provide proof of payment (receipt) to the school. A confirmation will be sent to the school administration for verification and bill update.
                </p>
            </div>

            <!-- Action Buttons -->
            <!-- <button onclick="proceedBankTransfer()" class="w-full px-4 py-3 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition-colors">
                <i class="fas fa-check mr-2"></i>I'll Transfer Now
            </button> -->
            <button onclick="closeBankTransferModal()" class="w-full px-4 py-2 text-gray-600 hover:text-gray-900 font-medium">
                Back
            </button>
        </div>
    </div>
</div>

<script>
function openPaymentModal() {
    const checkedBills = document.querySelectorAll('input[name="bills[]"]:checked');
    if (checkedBills.length === 0) {
        alert('Please select at least one bill to pay');
        return;
    }
    document.getElementById('paymentModal').classList.remove('hidden');
}

function closePaymentModal() {
    document.getElementById('paymentModal').classList.add('hidden');
}

function openBankTransferModal() {
    document.getElementById('paymentModal').classList.add('hidden');
    document.getElementById('bankTransferModal').classList.remove('hidden');
}

function closeBankTransferModal() {
    document.getElementById('bankTransferModal').classList.add('hidden');
    document.getElementById('paymentModal').classList.remove('hidden');
}

function proceedBankTransfer() {
    const bills = getSelectedBills();
    if (bills.length === 0) {
        alert('Please select bills to pay');
        return;
    }

    const formData = new FormData();
    formData.append('_token', '{{ csrf_token() }}');
    formData.append('payment_method', 'bank_transfer');
    formData.append('reference', '');
    bills.forEach(bill => formData.append('bills[]', bill));

    fetch('{{ route("parent-payments.manual") }}', {
        method: 'POST',
        body: formData
    }).then(response => {
        if (response.ok) {
            alert('Bank transfer details recorded. Please complete the transfer and submit proof of payment to the school office. A confirmation will be sent to the administration team.');
            window.location.href = '{{ route("parent-portal.payment-history") }}';
        } else {
            alert('Error recording payment. Please try again.');
        }
    }).catch(error => {
        alert('Error: ' + error.message);
    });
}

function toggleAll() {
    const checkboxes = document.querySelectorAll('input[name="bills[]"]');
    const selectAll = document.getElementById('selectAll');
    checkboxes.forEach(checkbox => checkbox.checked = selectAll.checked);
}

function getSelectedBills() {
    return Array.from(document.querySelectorAll('input[name="bills[]"]:checked')).map(cb => cb.value);
}

function payIndividualBill(billId) {
    // Check the specific bill checkbox and open payment modal
    const checkbox = document.querySelector(`input[name="bills[]"][value="${billId}"]`);
    if (checkbox) {
        // Uncheck all first
        document.querySelectorAll('input[name="bills[]"]').forEach(cb => cb.checked = false);
        // Check only this one
        checkbox.checked = true;
    }
    openPaymentModal();
}

async function initiatePaystackPayment() {
    const bills = getSelectedBills();
    if (bills.length === 0) {
        alert('Please select bills to pay');
        return;
    }

    try {
        const response = await fetch('{{ route("parent-payments.paystack") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ bills })
        });

        const data = await response.json();

        if (data.success) {
            window.location.href = data.authorization_url;
        } else {
            alert('Error: ' + (data.error || 'Payment initiation failed'));
        }
    } catch (error) {
        alert('Error: ' + error.message);
    }
}

function recordManualPayment(method) {
    const bills = getSelectedBills();
    if (bills.length === 0) {
        alert('Please select bills to pay');
        return;
    }

    const reference = prompt('Enter payment reference (optional):');
    
    const formData = new FormData();
    formData.append('_token', '{{ csrf_token() }}');
    formData.append('payment_method', method);
    formData.append('reference', reference || '');
    bills.forEach(bill => formData.append('bills[]', bill));

    fetch('{{ route("parent-payments.manual") }}', {
        method: 'POST',
        body: formData
    }).then(response => {
        if (response.ok) {
            window.location.href = '{{ route("parent-portal.payment-history") }}';
        } else {
            alert('Error recording payment');
        }
    });
}
</script>
@endsection
