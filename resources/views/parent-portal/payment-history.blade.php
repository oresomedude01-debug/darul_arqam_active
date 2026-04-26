@extends('layouts.spa')

@section('title', 'Payment History')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 via-green-50 to-slate-100 py-8">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
        <!-- Header -->
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-4xl font-bold text-gray-900">Payment History</h1>
                <p class="text-gray-600 mt-2">Track all your school payments</p>
            </div>
        </div>

        <!-- Filters -->
        <div class="bg-white rounded-xl shadow-lg border border-green-100 p-6">
            <form method="GET" class="flex flex-col md:flex-row gap-4">
                <!-- Student Filter -->
                <select name="student" class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                    <option value="">All Students</option>
                    @foreach($children as $child)
                    <option value="{{ $child->id }}" {{ $selectedStudent?->id === $child->id ? 'selected' : '' }}>
                        {{ $child->first_name }} {{ $child->last_name }}
                    </option>
                    @endforeach
                </select>

                <button type="submit" class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors font-medium">
                    <i class="fas fa-filter mr-2"></i>Filter
                </button>
            </form>
        </div>

        <!-- Payments Table -->
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden border border-green-100">
            <div class="bg-gradient-to-r from-green-600 to-emerald-600 px-6 py-5">
                <h2 class="text-xl font-bold text-white flex items-center gap-2">
                    <i class="fas fa-history"></i>Payment Transactions
                </h2>
            </div>

            <div class="overflow-x-auto">
                @if($payments->count() > 0)
                <table class="w-full">
                    <thead class="bg-gradient-to-r from-green-50 to-emerald-50 border-b-2 border-green-200">
                        <tr>
                            <th class="px-6 py-4 text-left text-sm font-bold text-green-900">Student</th>
                            <th class="px-6 py-4 text-left text-sm font-bold text-green-900">Bill ID</th>
                            <th class="px-6 py-4 text-center text-sm font-bold text-green-900">Amount</th>
                            <th class="px-6 py-4 text-center text-sm font-bold text-green-900">Date</th>
                            <th class="px-6 py-4 text-center text-sm font-bold text-green-900">Method</th>
                            <th class="px-6 py-4 text-center text-sm font-bold text-green-900">Status</th>
                            <th class="px-6 py-4 text-center text-sm font-bold text-green-900">Reference</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($payments as $payment)
                        <tr class="hover:bg-green-50/50 transition-colors">
                            <td class="px-6 py-4">
                                <div>
                                    <p class="font-semibold text-gray-900">{{ $payment->student->first_name }} {{ $payment->student->last_name }}</p>
                                    <p class="text-sm text-gray-600">{{ $payment->student->admission_number }}</p>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-sm text-gray-600 font-mono">{{ substr($payment->bill_id, 0, 8) }}...</span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="font-bold text-lg text-gray-900">₦{{ number_format($payment->amount, 2) }}</span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="text-sm text-gray-600">{{ $payment->paid_at->format('M d, Y') }}</span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="inline-block px-3 py-1 bg-gray-100 text-gray-800 text-sm rounded-lg font-semibold">
                                    @switch($payment->payment_method)
                                        @case('paystack')
                                            <i class="fas fa-credit-card mr-1"></i>Paystack
                                            @break
                                        @case('bank_transfer')
                                            <i class="fas fa-university mr-1"></i>Bank Transfer
                                            @break
                                        @case('cash')
                                            <i class="fas fa-money-bill mr-1"></i>Cash
                                            @break
                                        @case('cheque')
                                            <i class="fas fa-file-alt mr-1"></i>Cheque
                                            @break
                                        @default
                                            {{ ucfirst(str_replace('_', ' ', $payment->payment_method)) }}
                                    @endswitch
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="inline-block px-3 py-1 rounded-lg font-semibold text-sm {{ 
                                    $payment->status === 'successful' ? 'bg-green-100 text-green-800' :
                                    ($payment->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800')
                                }}">
                                    {{ ucfirst($payment->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="text-sm text-gray-600 font-mono">{{ $payment->reference ?? 'N/A' }}</span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>

                <!-- Pagination -->
                <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
                    {{ $payments->links() }}
                </div>
                @else
                <div class="px-6 py-12 text-center">
                    <i class="fas fa-inbox text-5xl text-gray-300 mb-4 block"></i>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">No Payments Yet</h3>
                    <p class="text-gray-600 mb-6">You haven't made any payments yet.</p>
                    <a href="{{ route('parent-portal.bills') }}" class="inline-flex items-center px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors font-medium">
                        <i class="fas fa-file-invoice-dollar mr-2"></i>View Bills
                    </a>
                </div>
                @endif
            </div>
        </div>

        <!-- Summary Cards -->
        @if($payments->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-white rounded-2xl shadow-lg border border-green-100 overflow-hidden">
                <div class="bg-gradient-to-r from-green-600 to-emerald-600 px-6 py-4">
                    <p class="text-green-100 text-sm font-medium">Total Paid</p>
                    <p class="text-3xl font-bold text-white mt-1">₦{{ number_format($payments->sum('amount'), 2) }}</p>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-lg border border-blue-100 overflow-hidden">
                <div class="bg-gradient-to-r from-blue-600 to-indigo-600 px-6 py-4">
                    <p class="text-blue-100 text-sm font-medium">Successful Payments</p>
                    <p class="text-3xl font-bold text-white mt-1">{{ $payments->where('status', 'successful')->count() }}</p>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-lg border border-yellow-100 overflow-hidden">
                <div class="bg-gradient-to-r from-yellow-600 to-orange-600 px-6 py-4">
                    <p class="text-yellow-100 text-sm font-medium">Pending Verification</p>
                    <p class="text-3xl font-bold text-white mt-1">{{ $payments->where('status', 'pending')->count() }}</p>
                </div>
            </div>
        </div>
        @endif

        <!-- Back Button -->
        <div class="flex gap-2">
            <a href="{{ route('parent-portal.dashboard') }}" class="inline-flex items-center px-4 py-2 text-gray-600 hover:text-gray-900 font-medium">
                <i class="fas fa-arrow-left mr-2"></i>Back to Dashboard
            </a>
        </div>
    </div>
</div>
@endsection
