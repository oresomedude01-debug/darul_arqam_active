@extends('layouts.spa')

@section('title', 'Student Bills')

@section('breadcrumb')
    <span class="text-gray-400">Billing</span>
    <span class="text-gray-400">/</span>
    <a href="{{ route('students.index') }}" class="text-gray-400 hover:text-gray-600">Students</a>
    <span class="text-gray-400">/</span>
    <span class="font-semibold text-gray-900">{{ $student->full_name }} - Bills</span>
@endsection

@section('content')
<div class="max-w-6xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Student Bills</h1>
            <p class="text-gray-600 mt-1">Billing records for {{ $student->full_name }}</p>
        </div>
        <div class="flex space-x-3">
            <a href="{{ route('billing.generate-individual-bill.form', $student->id) }}" class="btn btn-primary">
                <i class="fas fa-plus mr-2"></i>
                Generate New Bill
            </a>
            <a href="{{ route('students.show', $student->id) }}" class="btn btn-outline">
                <i class="fas fa-arrow-left mr-2"></i>
                Back to Profile
            </a>
        </div>
    </div>

    <!-- Student Information Card -->
    <div class="card">
        <div class="card-body">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                <div>
                    <p class="text-sm text-gray-600 font-medium">Student Name</p>
                    <p class="text-lg font-semibold text-gray-900">{{ $student->full_name }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600 font-medium">Registration Number</p>
                    <p class="text-lg font-semibold text-gray-900">{{ $student->registration_number ?? 'N/A' }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600 font-medium">Current Class</p>
                    <p class="text-lg font-semibold text-gray-900">
                        @if($student->schoolClass)
                            <span class="badge badge-primary">{{ $student->schoolClass->class_name }}</span>
                        @else
                            <span class="text-gray-400">Not Assigned</span>
                        @endif
                    </p>
                </div>
                <div>
                    <p class="text-sm text-gray-600 font-medium">Email</p>
                    <p class="text-lg font-semibold text-gray-900">{{ $student->email ?? 'N/A' }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Bills Summary -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="card">
            <div class="card-body">
                <p class="text-sm text-gray-600 font-medium">Total Bills</p>
                <p class="text-3xl font-bold text-gray-900">{{ $bills->count() }}</p>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <p class="text-sm text-gray-600 font-medium">Pending</p>
                <p class="text-3xl font-bold text-orange-600">
                    {{ $bills->where('status', 'pending')->count() }}
                </p>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <p class="text-sm text-gray-600 font-medium">Partial Payment</p>
                <p class="text-3xl font-bold text-blue-600">
                    {{ $bills->where('status', 'partial')->count() }}
                </p>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <p class="text-sm text-gray-600 font-medium">Paid</p>
                <p class="text-3xl font-bold text-green-600">
                    {{ $bills->where('status', 'paid')->count() }}
                </p>
            </div>
        </div>
    </div>

    <!-- Bills Table -->
    @if($bills->count() > 0)
        <div class="card">
            <div class="card-header">
                <h3 class="text-lg font-semibold text-gray-900">All Bills</h3>
            </div>
            <div class="card-body p-0">
                <div class="overflow-x-auto">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Bill ID</th>
                                <th>Session</th>
                                <th>Term</th>
                                <th>Class</th>
                                <th>Total Amount</th>
                                <th>Paid Amount</th>
                                <th>Balance</th>
                                <th>Status</th>
                                <th>Due Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($bills as $bill)
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="font-mono text-sm font-semibold">#{{ $bill->id }}</td>
                                    <td class="font-medium text-gray-900">
                                        {{ $bill->academicSession->session ?? 'N/A' }}
                                    </td>
                                    <td class="text-gray-600">
                                        {{ $bill->academicTerm->name ?? 'N/A' }}
                                    </td>
                                    <td>
                                        <span class="badge badge-secondary">
                                            {{ $bill->schoolClass->class_name ?? 'N/A' }}
                                        </span>
                                    </td>
                                    <td class="font-semibold text-gray-900">
                                        ₦{{ number_format($bill->total_amount, 2) }}
                                    </td>
                                    <td class="font-semibold text-green-600">
                                        ₦{{ number_format($bill->paid_amount, 2) }}
                                    </td>
                                    <td class="font-semibold">
                                        @if($bill->balance_due > 0)
                                            <span class="text-red-600">₦{{ number_format($bill->balance_due, 2) }}</span>
                                        @else
                                            <span class="text-green-600">₦0.00</span>
                                        @endif
                                    </td>
                                    <td>
                                        @switch($bill->status)
                                            @case('paid')
                                                <span class="badge badge-success">
                                                    <i class="fas fa-check-circle mr-1"></i>Paid
                                                </span>
                                                @break
                                            @case('partial')
                                                <span class="badge badge-info">
                                                    <i class="fas fa-hourglass-half mr-1"></i>Partial
                                                </span>
                                                @break
                                            @case('pending')
                                                <span class="badge badge-warning">
                                                    <i class="fas fa-clock mr-1"></i>Pending
                                                </span>
                                                @break
                                            @case('overdue')
                                                <span class="badge badge-danger">
                                                    <i class="fas fa-exclamation-triangle mr-1"></i>Overdue
                                                </span>
                                                @break
                                            @default
                                                <span class="badge badge-secondary">{{ ucfirst($bill->status) }}</span>
                                        @endswitch
                                    </td>
                                    <td class="text-gray-600 text-sm">
                                        @if($bill->due_date)
                                            {{ $bill->due_date->format('M d, Y') }}
                                        @else
                                            <span class="text-gray-400">No Due Date</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="flex items-center space-x-2">
                                            <a href="{{ route('billing.view-bill', $bill->id) }}" 
                                               class="btn btn-sm btn-outline" title="View Details">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            @if($bill->balance_due > 0)
                                                <a href="{{ route('billing.view-bill', $bill->id) }}" 
                                                   class="btn btn-sm btn-primary" title="Record Payment">
                                                    <i class="fas fa-dollar-sign"></i>
                                                </a>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @else
        <!-- Empty State -->
        <div class="card">
            <div class="card-body text-center py-12">
                <div class="mb-4">
                    <i class="fas fa-file-invoice text-6xl text-gray-300"></i>
                </div>
                <h3 class="text-2xl font-semibold text-gray-900 mb-2">No Bills Yet</h3>
                <p class="text-gray-600 mb-6">This student doesn't have any bills yet.</p>
                <a href="{{ route('billing.generate-individual-bill.form', $student->id) }}" class="btn btn-primary">
                    <i class="fas fa-plus mr-2"></i>
                    Generate First Bill
                </a>
            </div>
        </div>
    @endif
</div>
@endsection
