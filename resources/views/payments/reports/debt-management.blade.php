@extends('layouts.spa')

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Debt Management</h1>
            <p class="text-gray-600 mt-1">Students with outstanding balances</p>
        </div>
        <form action="{{ route('payments.send-payment-reminders') }}" method="POST" class="inline">
            @csrf
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition inline-flex items-center gap-2">
                <i class="fas fa-envelope"></i>
                Send Payment Reminders
            </button>
        </form>
    </div>

    <!-- Debt Statistics KPIs -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Total Outstanding -->
        <div class="bg-white rounded-lg shadow p-4 border-l-4 border-red-600">
            <p class="text-gray-600 text-xs font-medium uppercase tracking-wide">Total Outstanding</p>
            <p class="text-2xl font-bold text-red-600 mt-2">₦{{ number_format($totalOutstanding, 2) }}</p>
            <p class="text-xs text-gray-500 mt-1">Across all bills</p>
        </div>

        <!-- Outstanding Bills Count -->
        <div class="bg-white rounded-lg shadow p-4 border-l-4 border-orange-500">
            <p class="text-gray-600 text-xs font-medium uppercase tracking-wide">Outstanding Bills</p>
            <p class="text-2xl font-bold text-orange-500 mt-2">{{ $bills->total() }}</p>
            <p class="text-xs text-gray-500 mt-1">Students with debt</p>
        </div>

        <!-- Overdue Bills Count -->
        <div class="bg-white rounded-lg shadow p-4 border-l-4 border-red-700">
            <p class="text-gray-600 text-xs font-medium uppercase tracking-wide">Overdue Bills</p>
            <p class="text-2xl font-bold text-red-700 mt-2">{{ $overdueCount }}</p>
            <p class="text-xs text-gray-500 mt-1">Past due date</p>
        </div>

        <!-- Overdue Amount -->
        <div class="bg-white rounded-lg shadow p-4 border-l-4 border-red-800">
            <p class="text-gray-600 text-xs font-medium uppercase tracking-wide">Overdue Amount</p>
            <p class="text-2xl font-bold text-red-800 mt-2">₦{{ number_format($overdueDebt, 2) }}</p>
            <p class="text-xs text-gray-500 mt-1">Overdue balance</p>
        </div>
    </div>
    <!-- Debt Analysis -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Debt by Payment Status -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Debt by Payment Status</h2>
            <div class="space-y-4">
                <!-- Pending (Not Started) -->
                <div class="border-l-4 border-red-500 pl-4">
                    <div class="flex justify-between items-start mb-2">
                        <div>
                            <p class="text-sm font-medium text-gray-700">Pending (Not Started)</p>
                            <p class="text-xs text-gray-500">{{ $pendingCount }} bills</p>
                        </div>
                        <span class="inline-block px-2 py-1 bg-red-100 text-red-700 text-xs font-semibold rounded">{{ $pendingCount }}</span>
                    </div>
                    <p class="text-xl font-bold text-red-600">₦{{ number_format($pendingDebt, 2) }}</p>
                </div>

                <!-- Partially Paid -->
                <div class="border-l-4 border-yellow-500 pl-4">
                    <div class="flex justify-between items-start mb-2">
                        <div>
                            <p class="text-sm font-medium text-gray-700">Partially Paid</p>
                            <p class="text-xs text-gray-500">{{ $partialCount }} bills</p>
                        </div>
                        <span class="inline-block px-2 py-1 bg-yellow-100 text-yellow-700 text-xs font-semibold rounded">{{ $partialCount }}</span>
                    </div>
                    <p class="text-xl font-bold text-yellow-600">₦{{ number_format($partialDebt, 2) }}</p>
                </div>

                <!-- Overdue Summary -->
                <div class="border-l-4 border-red-700 pl-4 mt-4 pt-4 border-t-2">
                    <div class="flex justify-between items-start mb-2">
                        <div>
                            <p class="text-sm font-medium text-gray-700">Overdue Bills</p>
                            <p class="text-xs text-gray-500">Past due date</p>
                        </div>
                        <span class="inline-block px-2 py-1 bg-red-100 text-red-800 text-xs font-semibold rounded">{{ $overdueCount }}</span>
                    </div>
                    <p class="text-xl font-bold text-red-700">₦{{ number_format($overdueDebt, 2) }}</p>
                </div>
            </div>
        </div>

        <!-- Collection Metrics -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Collection Metrics</h2>
            <div class="space-y-4">
                <!-- Total Bills -->
                <div>
                    <div class="flex justify-between items-center mb-2">
                        <span class="text-sm text-gray-600">Total Outstanding Bills</span>
                        <span class="text-2xl font-bold text-gray-900">{{ $bills->total() }}</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-blue-600 h-2 rounded-full" style="width: 100%"></div>
                    </div>
                </div>

                <!-- Pending Ratio -->
                <div>
                    <div class="flex justify-between items-center mb-2">
                        <span class="text-sm text-gray-600">Pending (Not Started)</span>
                        <span class="text-lg font-semibold text-red-600">{{ $bills->total() > 0 ? round(($pendingCount / $bills->total()) * 100) : 0 }}%</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-red-500 h-2 rounded-full" style="width: {{ $bills->total() > 0 ? round(($pendingCount / $bills->total()) * 100) : 0 }}%"></div>
                    </div>
                </div>

                <!-- Partial Ratio -->
                <div>
                    <div class="flex justify-between items-center mb-2">
                        <span class="text-sm text-gray-600">Partially Paid</span>
                        <span class="text-lg font-semibold text-yellow-600">{{ $bills->total() > 0 ? round(($partialCount / $bills->total()) * 100) : 0 }}%</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-yellow-500 h-2 rounded-full" style="width: {{ $bills->total() > 0 ? round(($partialCount / $bills->total()) * 100) : 0 }}%"></div>
                    </div>
                </div>

                <!-- Overdue Ratio -->
                <div>
                    <div class="flex justify-between items-center mb-2">
                        <span class="text-sm text-gray-600">Overdue (Past Due)</span>
                        <span class="text-lg font-semibold text-red-700">{{ $bills->total() > 0 ? round(($overdueCount / $bills->total()) * 100) : 0 }}%</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-red-700 h-2 rounded-full" style="width: {{ $bills->total() > 0 ? round(($overdueCount / $bills->total()) * 100) : 0 }}%"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Payment Reminders -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Top Debtors</h2>
            <div class="space-y-3">
                @forelse($topDebtors as $bill)
                <div class="flex justify-between items-center p-3 bg-gray-50 rounded">
                    <div>
                        <p class="text-sm font-medium text-gray-900">{{ $bill->student?->full_name ?? 'N/A' }}</p>
                        <p class="text-xs text-gray-500">{{ $bill->student?->admission_number ?? 'N/A' }}</p>
                    </div>
                    <span class="font-bold text-red-600">₦{{ number_format($bill->balance_due, 2) }}</span>
                </div>
                @empty
                <div class="text-gray-500 text-sm text-center py-4">
                    No outstanding debt
                </div>
                @endforelse
            </div>
        </div>
    </div>
    <!-- Filters -->
    <div class="bg-white rounded-lg shadow p-4">
        <form action="{{ route('payments.reports.debt-management') }}" method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <!-- Class Filter -->
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

    <!-- Debtors Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Student</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Class</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Total Due</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Paid</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Outstanding</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">% Paid</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($bills as $bill)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">
                            <div class="text-sm font-medium text-gray-900">
                                @if($bill->student)
                                    {{ $bill->student->full_name }}
                                @else
                                    N/A
                                @endif
                            </div>
                            <div class="text-xs text-gray-500">
                                @if($bill->student)
                                    {{ $bill->student->admission_number ?? 'N/A' }}
                                @else
                                    N/A
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">
                            @if($bill->student && $bill->student->schoolClass)
                                {{ $bill->student->schoolClass->name ?? 'N/A' }}
                            @else
                                N/A
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm font-medium text-gray-900">₦{{ number_format($bill->total_amount, 2) }}</td>
                        <td class="px-6 py-4 text-sm font-medium text-green-600">₦{{ number_format($bill->paid_amount, 2) }}</td>
                        <td class="px-6 py-4 text-sm font-medium text-red-600">₦{{ number_format($bill->balance_due, 2) }}</td>
                        <td class="px-6 py-4 text-sm font-medium">
                            <div class="flex items-center gap-2">
                                <div class="w-16 bg-gray-200 rounded-full h-2">
                                    <div class="bg-primary-600 h-2 rounded-full" style="width: {{ $bill->total_amount > 0 ? round(($bill->paid_amount / $bill->total_amount) * 100) : 0 }}%"></div>
                                </div>
                                <span>{{ $bill->total_amount > 0 ? round(($bill->paid_amount / $bill->total_amount) * 100) : 0 }}%</span>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            @switch($bill->status)
                                @case('partial')
                                    <span class="px-3 py-1 text-xs font-medium rounded-full bg-blue-100 text-blue-800">Partial Payment</span>
                                    @break
                                @case('pending')
                                    <span class="px-3 py-1 text-xs font-medium rounded-full bg-yellow-100 text-yellow-800">Pending</span>
                                    @break
                                @case('overdue')
                                    <span class="px-3 py-1 text-xs font-medium rounded-full bg-red-100 text-red-800">Overdue</span>
                                    @break
                                @default
                                    <span class="px-3 py-1 text-xs font-medium rounded-full bg-gray-100 text-gray-800">{{ ucfirst($bill->status) }}</span>
                            @endswitch
                        </td>
                        <td class="px-6 py-4">
                            <a href="{{ route('payments.bills.view', $bill) }}" class="text-primary-600 hover:text-primary-900 text-sm font-medium">
                                Manage
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-6 py-4 text-center text-gray-600">
                            No outstanding balances. All students have paid or partially paid their fees.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($bills->hasPages())
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $bills->links() }}
        </div>
        @endif
    </div>


</div>
@endsection
