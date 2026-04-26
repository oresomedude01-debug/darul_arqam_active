@extends('layouts.spa')

@section('content')
<div class="space-y-6">
    <!-- Alert Messages -->
    @if(session('success'))
    <div class="bg-green-50 border border-green-200 rounded-lg p-4 flex items-start gap-3">
        <div class="flex-shrink-0">
            <i class="fas fa-check-circle text-green-600 text-lg mt-0.5"></i>
        </div>
        <div class="flex-1">
            <h3 class="font-semibold text-green-900">Success</h3>
            <p class="text-green-800 text-sm mt-1">{{ session('success') }}</p>
        </div>
        @if(session('failedParents'))
        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mt-3 w-full">
            <h4 class="font-semibold text-yellow-900 text-sm mb-2">Failed to send to:</h4>
            <ul class="text-yellow-800 text-xs space-y-1">
                @foreach(session('failedParents') as $email)
                    <li class="flex items-center gap-2">
                        <i class="fas fa-times-circle text-yellow-600"></i>
                        {{ $email }}
                    </li>
                @endforeach
            </ul>
        </div>
        @endif
    </div>
    @endif

    @if(session('info'))
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 flex items-start gap-3">
        <div class="flex-shrink-0">
            <i class="fas fa-info-circle text-blue-600 text-lg mt-0.5"></i>
        </div>
        <div class="flex-1">
            <h3 class="font-semibold text-blue-900">Information</h3>
            <p class="text-blue-800 text-sm mt-1">{{ session('info') }}</p>
        </div>
    </div>
    @endif

    @if(session('error'))
    <div class="bg-red-50 border border-red-200 rounded-lg p-4 flex items-start gap-3">
        <div class="flex-shrink-0">
            <i class="fas fa-exclamation-circle text-red-600 text-lg mt-0.5"></i>
        </div>
        <div class="flex-1">
            <h3 class="font-semibold text-red-900">Error</h3>
            <p class="text-red-800 text-sm mt-1">{{ session('error') }}</p>
        </div>
    </div>
    @endif

    <!-- Page Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Payment Management</h1>
            <p class="text-gray-600 mt-1">Manage fees, bills, and payments</p>
        </div>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <!-- Total Collected -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm font-medium">Total Collected</p>
                    <p class="text-3xl font-bold text-green-600 mt-2">
                        {{ $settings->currency_symbol ?? '₦' }}{{ number_format($totalCollected, 2) }}
                    </p>
                </div>
                <div class="bg-green-100 p-3 rounded-full">
                    <i class="fas fa-money-bill-wave text-green-600 text-2xl"></i>
                </div>
            </div>
        </div>

        <!-- Pending Payments -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm font-medium">Pending Payments</p>
                    <p class="text-3xl font-bold text-yellow-600 mt-2">
                        {{ $settings->currency_symbol ?? '₦' }}{{ number_format($pendingPayments, 2) }}
                    </p>
                </div>
                <div class="bg-yellow-100 p-3 rounded-full">
                    <i class="fas fa-clock text-yellow-600 text-2xl"></i>
                </div>
            </div>
        </div>

        <!-- Total Students Billed -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm font-medium">Students Billed</p>
                    <p class="text-3xl font-bold text-blue-600 mt-2">{{ $totalStudentsBilled }}</p>
                </div>
                <div class="bg-blue-100 p-3 rounded-full">
                    <i class="fas fa-users text-blue-600 text-2xl"></i>
                </div>
            </div>
        </div>

        <!-- Paid Students -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm font-medium">Students Paid</p>
                    <p class="text-3xl font-bold text-indigo-600 mt-2">{{ $studentsPaid }}</p>
                </div>
                <div class="bg-indigo-100 p-3 rounded-full">
                    <i class="fas fa-check-circle text-indigo-600 text-2xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <a href="{{ route('billing.fee-structures.index') }}" class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition">
            <div class="flex items-center">
                <div class="bg-primary-100 p-3 rounded-lg mr-4">
                    <i class="fas fa-list text-primary-600 text-2xl"></i>
                </div>
                <div>
                    <h3 class="font-semibold text-gray-900">Fee Structures</h3>
                    <p class="text-gray-600 text-sm">Manage fee items</p>
                </div>
                <i class="fas fa-arrow-right ml-auto text-gray-400"></i>
            </div>
        </a>

        <a href="{{ route('payments.bills.index') }}" class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition">
            <div class="flex items-center">
                <div class="bg-primary-100 p-3 rounded-lg mr-4">
                    <i class="fas fa-receipt text-primary-600 text-2xl"></i>
                </div>
                <div>
                    <h3 class="font-semibold text-gray-900">Student Bills</h3>
                    <p class="text-gray-600 text-sm">View and manage bills</p>
                </div>
                <i class="fas fa-arrow-right ml-auto text-gray-400"></i>
            </div>
        </a>

        <a href="{{ route('payments.reports.debt-management') }}" class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition">
            <div class="flex items-center">
                <div class="bg-primary-100 p-3 rounded-lg mr-4">
                    <i class="fas fa-exclamation-circle text-primary-600 text-2xl"></i>
                </div>
                <div>
                    <h3 class="font-semibold text-gray-900">Debt Management</h3>
                    <p class="text-gray-600 text-sm">Students owing fees</p>
                </div>
                <i class="fas fa-arrow-right ml-auto text-gray-400"></i>
            </div>
        </a>

        <form action="{{ route('payments.send-payment-reminders') }}" method="POST" class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition border-2 border-dashed border-blue-300">
            @csrf
            <button type="submit" class="w-full h-full flex items-center gap-4 cursor-pointer">
                <div class="bg-blue-100 p-3 rounded-lg">
                    <i class="fas fa-envelope text-blue-600 text-2xl"></i>
                </div>
                <div class="text-left flex-1">
                    <h3 class="font-semibold text-gray-900">Send Reminders</h3>
                    <p class="text-gray-600 text-sm">Email payment reminders</p>
                </div>
                <i class="fas fa-arrow-right text-gray-400"></i>
            </button>
        </form>
    </div>

    <!-- Recent Payments -->
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900">Recent Payments</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Student</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Amount</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Method</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Receipt</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($recentPayments as $payment)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">
                            <div class="text-sm font-medium text-gray-900">{{ $payment->student->first_name }} {{ $payment->student->last_name }}</div>
                            <div class="text-xs text-gray-500">Admission: {{ $payment->student->admission_number }}</div>
                        </td>
                        <td class="px-6 py-4 text-sm font-medium text-gray-900">
                            {{ $settings->currency_symbol ?? '₦' }}{{ number_format($payment->amount, 2) }}
                        </td>
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
                        <td class="px-6 py-4 text-sm text-gray-600">
                            {{ $payment->receipt_number ?? 'Pending' }}
                        </td>
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
                        <td colspan="6" class="px-6 py-4 text-center text-gray-600">No payments recorded yet</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
