@extends('layouts.spa')

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Payment Receipts</h1>
            <p class="text-gray-600 mt-1">View and manage payment receipts</p>
        </div>
        <a href="{{ route('payments.index') }}" class="bg-gray-200 text-gray-900 px-4 py-2 rounded-lg hover:bg-gray-300 transition">
            Back to Dashboard
        </a>
    </div>

    <!-- Info Box -->
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-6">
        <div class="flex gap-4">
            <div class="text-blue-600 text-xl">
                <i class="fas fa-info-circle"></i>
            </div>
            <div>
                <h3 class="font-semibold text-blue-900">Receipts Feature</h3>
                <p class="text-sm text-blue-800 mt-1">
                    Receipt management is not currently active in the simplified billing system. 
                    All payment records are automatically tracked and can be viewed in the payment history report.
                </p>
            </div>
        </div>
    </div>

    <!-- Quick Links -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <a href="{{ route('payments.payment-history') }}" class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition">
            <div class="flex items-center gap-4">
                <div class="bg-indigo-100 p-3 rounded-lg">
                    <i class="fas fa-history text-indigo-600 text-xl"></i>
                </div>
                <div>
                    <h3 class="font-semibold text-gray-900">Payment History</h3>
                    <p class="text-sm text-gray-600">View all completed payments</p>
                </div>
            </div>
        </a>

        <a href="{{ route('payments.bills.index') }}" class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition">
            <div class="flex items-center gap-4">
                <div class="bg-green-100 p-3 rounded-lg">
                    <i class="fas fa-file-invoice-dollar text-green-600 text-xl"></i>
                </div>
                <div>
                    <h3 class="font-semibold text-gray-900">Student Bills</h3>
                    <p class="text-sm text-gray-600">View and manage student bills</p>
                </div>
            </div>
        </a>
    </div>
</div>
@endsection
