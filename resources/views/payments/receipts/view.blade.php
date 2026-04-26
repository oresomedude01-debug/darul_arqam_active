@extends('layouts.spa')

@section('title', 'Receipt: ' . $receipt->receipt_number)

@section('content')
<div class="max-w-3xl mx-auto">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Receipt</h1>
            <p class="text-gray-600 mt-1">Receipt #{{ $receipt->receipt_number }}</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('payments.receipts.print', $receipt) }}" target="_blank" 
               class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 flex items-center gap-2">
                <i class="fas fa-print"></i> Print
            </a>
            <a href="{{ route('payments.receipts.pdf', $receipt) }}" target="_blank" 
               class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 flex items-center gap-2">
                <i class="fas fa-file-pdf"></i> PDF
            </a>
        </div>
    </div>

    <!-- Receipt Card -->
    <div class="bg-white rounded-lg shadow-lg">
        <div class="p-6">
            <!-- Receipt Header -->
            <div class="border-b pb-6 mb-6">
                <div class="grid grid-cols-2 gap-6">
                    <div>
                        <h3 class="text-lg font-semibold text-primary-600">Receipt Details</h3>
                        <p class="text-gray-600 mt-2"><strong>Receipt #:</strong> {{ $receipt->receipt_number }}</p>
                        <p class="text-gray-600"><strong>Date:</strong> {{ $receipt->generated_at->format('M d, Y H:i A') }}</p>
                        <p class="text-gray-600"><strong>Status:</strong> 
                            <span class="px-2 py-1 text-sm font-medium rounded 
                                @if($receipt->status === 'generated') bg-blue-100 text-blue-800
                                @elseif($receipt->status === 'printed') bg-green-100 text-green-800
                                @elseif($receipt->status === 'sent') bg-purple-100 text-purple-800
                                @elseif($receipt->status === 'void') bg-red-100 text-red-800
                                @endif">
                                {{ ucfirst($receipt->status) }}
                            </span>
                        </p>
                    </div>
                    <div class="text-right">
                        <p class="text-4xl font-bold text-green-600">₦{{ number_format($receipt->payment->amount, 2) }}</p>
                        <p class="text-gray-600 mt-2"><strong>Payment Method:</strong> {{ ucfirst(str_replace('_', ' ', $receipt->payment->payment_method)) }}</p>
                    </div>
                </div>
            </div>

            <!-- Student Information -->
            <div class="grid grid-cols-2 gap-6 mb-6">
                <div>
                    <h4 class="text-sm font-semibold text-gray-700 uppercase mb-3">Student Information</h4>
                    <p class="text-gray-900"><strong>Name:</strong> {{ $receipt->payment->student->full_name }}</p>
                    <p class="text-gray-900"><strong>Admission #:</strong> {{ $receipt->payment->student->admission_number }}</p>
                    <p class="text-gray-900"><strong>Class:</strong> {{ $receipt->payment->student->schoolClass?->name ?? 'N/A' }}</p>
                </div>
                <div>
                    <h4 class="text-sm font-semibold text-gray-700 uppercase mb-3">Payment Information</h4>
                    <p class="text-gray-900"><strong>Payment Date:</strong> {{ $receipt->payment->payment_date?->format('M d, Y H:i A') ?? 'N/A' }}</p>
                    <p class="text-gray-900"><strong>Reference #:</strong> {{ $receipt->payment->reference_number ?? 'N/A' }}</p>
                    @if($receipt->payment->studentBill)
                    <p class="text-gray-900"><strong>Bill #:</strong> {{ $receipt->payment->studentBill->id }}</p>
                    @endif
                </div>
            </div>

            <!-- Bill Items -->
            @if($receipt->payment->studentBill && $receipt->payment->studentBill->billItems->count() > 0)
            <div class="border-t pt-6 mb-6">
                <h4 class="text-sm font-semibold text-gray-700 uppercase mb-3">Payment For</h4>
                <table class="w-full">
                    <thead>
                        <tr class="border-b">
                            <th class="text-left py-2 px-4 text-gray-700">Item</th>
                            <th class="text-right py-2 px-4 text-gray-700">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($receipt->payment->studentBill->billItems as $item)
                        <tr class="border-b">
                            <td class="py-2 px-4 text-gray-900">{{ $item->feeItem?->name ?? $item->name ?? 'Fee Item' }}</td>
                            <td class="py-2 px-4 text-right text-gray-900">₦{{ number_format($item->amount, 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif

            <!-- Payment Summary -->
            <div class="border-t pt-6 mb-6">
                <h4 class="text-sm font-semibold text-gray-700 uppercase mb-3">Payment Summary</h4>
                <div class="grid grid-cols-3 gap-4">
                    <div class="bg-gray-50 p-4 rounded">
                        <p class="text-gray-600 text-sm">Amount Paid</p>
                        <p class="text-2xl font-bold text-green-600">₦{{ number_format($receipt->payment->amount, 2) }}</p>
                    </div>
                    @if($receipt->payment->studentBill)
                    <div class="bg-gray-50 p-4 rounded">
                        <p class="text-gray-600 text-sm">Total Bill</p>
                        <p class="text-2xl font-bold text-gray-900">₦{{ number_format($receipt->payment->studentBill->total_amount, 2) }}</p>
                    </div>
                    <div class="bg-gray-50 p-4 rounded">
                        <p class="text-gray-600 text-sm">Balance Due</p>
                        <p class="text-2xl font-bold @if($receipt->payment->studentBill->balance_due > 0) text-red-600 @else text-green-600 @endif">
                            ₦{{ number_format($receipt->payment->studentBill->balance_due, 2) }}
                        </p>
                    </div>
                    @endif
                </div>
            </div>

            @if($receipt->notes)
            <div class="border-t pt-6">
                <h4 class="text-sm font-semibold text-gray-700 uppercase mb-2">Notes</h4>
                <p class="text-gray-900">{{ $receipt->notes }}</p>
            </div>
            @endif
        </div>
    </div>

    <!-- Back Button -->
    <div class="mt-6">
        <a href="{{ route('payments.receipts.list') }}" class="text-primary-600 hover:text-primary-700 font-medium flex items-center gap-2">
            <i class="fas fa-arrow-left"></i> Back to Receipts
        </a>
    </div>
</div>
@endsection
