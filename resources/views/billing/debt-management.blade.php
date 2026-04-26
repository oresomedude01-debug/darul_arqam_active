@extends('layouts.spa')

@section('content')
<div class="max-w-6xl mx-auto space-y-6">
    <!-- Page Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Debt Management</h1>
            <p class="text-gray-600 mt-1">Monitor outstanding bills and manage student debt</p>
        </div>
        <form action="{{ route('billing.send-payment-reminders') }}" method="POST" class="inline">
            @csrf
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition flex items-center gap-2">
                <i class="fas fa-envelope"></i>
                Send Payment Reminders
            </button>
        </form>
    </div>

    <!-- Flash Messages -->
    @if(session('success'))
    <div class="bg-green-50 border border-green-200 rounded-lg p-4 text-green-800 shadow-md" x-data="{ show: true }" x-show="show" x-transition:leave="transition ease-in duration-300" @click.outside="show = false">
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <i class="fas fa-check-circle mr-3 text-lg"></i>
                <span class="font-medium">{{ session('success') }}</span>
            </div>
            <button @click="show = false" class="text-green-600 hover:text-green-800">
                <i class="fas fa-times"></i>
            </button>
        </div>
    </div>
    @endif

    @if(session('error'))
    <div class="bg-red-50 border border-red-200 rounded-lg p-4 text-red-800 shadow-md" x-data="{ show: true }" x-show="show" x-transition:leave="transition ease-in duration-300" @click.outside="show = false">
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <i class="fas fa-exclamation-circle mr-3 text-lg"></i>
                <span class="font-medium">{{ session('error') }}</span>
            </div>
            <button @click="show = false" class="text-red-600 hover:text-red-800">
                <i class="fas fa-times"></i>
            </button>
        </div>
    </div>
    @endif

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Total Outstanding -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm font-medium">Total Outstanding</p>
                    <p class="text-3xl font-bold text-red-600 mt-2">₦{{ number_format($totalOutstanding, 2) }}</p>
                </div>
                <div class="bg-red-100 rounded-full p-4">
                    <i class="fas fa-money-bill-wave text-red-600 text-2xl"></i>
                </div>
            </div>
        </div>

        <!-- Total Bills -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm font-medium">Outstanding Bills</p>
                    <p class="text-3xl font-bold text-orange-600 mt-2">{{ $bills->total() }}</p>
                </div>
                <div class="bg-orange-100 rounded-full p-4">
                    <i class="fas fa-receipt text-orange-600 text-2xl"></i>
                </div>
            </div>
        </div>

        <!-- Overdue Bills -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm font-medium">Overdue Bills</p>
                    <p class="text-3xl font-bold text-red-700 mt-2">{{ $bills->where('status', 'overdue')->count() }}</p>
                </div>
                <div class="bg-red-100 rounded-full p-4">
                    <i class="fas fa-exclamation-circle text-red-700 text-2xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Filters</h2>
        <form method="GET" action="{{ route('billing.debt-management') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <!-- Class Filter -->
            <div>
                <label class="block text-sm font-medium text-gray-900 mb-2">School Class</label>
                <select name="school_class_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                    <option value="">-- All Classes --</option>
                    @foreach(\App\Models\SchoolClass::orderBy('name')->get() as $class)
                    <option value="{{ $class->id }}" @selected(request('school_class_id') == $class->id)>
                        {{ $class->name }}
                    </option>
                    @endforeach
                </select>
            </div>

            <!-- Session Filter -->
            <div>
                <label class="block text-sm font-medium text-gray-900 mb-2">Academic Session</label>
                <select name="academic_session_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                    <option value="">-- All Sessions --</option>
                    @foreach(\App\Models\AcademicSession::orderBy('session', 'desc')->get() as $session)
                    <option value="{{ $session->id }}" @selected(request('academic_session_id') == $session->id)>
                        {{ $session->session }}
                    </option>
                    @endforeach
                </select>
            </div>

            <!-- Submit -->
            <div class="flex items-end gap-2">
                <button type="submit" class="flex-1 bg-primary-600 text-white px-4 py-2 rounded-lg hover:bg-primary-700 transition">
                    <i class="fas fa-filter mr-2"></i>Apply Filter
                </button>
                <a href="{{ route('billing.debt-management') }}" class="bg-gray-300 text-gray-900 px-4 py-2 rounded-lg hover:bg-gray-400 transition">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Outstanding Bills Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900">Outstanding Bills</h2>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Student Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Admission No.</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Class</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Session</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-700 uppercase tracking-wider">Total Amount</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-700 uppercase tracking-wider">Balance Due</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Due Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($bills as $bill)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">
                                {{ $bill->student->first_name ?? '' }} {{ $bill->student->last_name ?? '' }}
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                            {{ $bill->student->admission_number ?? 'N/A' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                            {{ $bill->schoolClass->name ?? 'N/A' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                            {{ $bill->academicSession->session ?? 'N/A' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-right font-medium text-gray-900">
                            ₦{{ number_format($bill->total_amount, 2) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-right font-medium text-red-600">
                            ₦{{ number_format($bill->balance_due, 2) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($bill->status === 'pending')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                    Pending
                                </span>
                            @elseif($bill->status === 'partial')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    Partial
                                </span>
                            @elseif($bill->status === 'overdue')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                    Overdue
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                            @if($bill->due_date)
                                {{ $bill->due_date->format('M d, Y') }}
                                @if($bill->due_date->isPast() && $bill->balance_due > 0)
                                    <span class="text-red-600 text-xs">(Overdue)</span>
                                @endif
                            @else
                                <span class="text-gray-400">Not Set</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            <a href="{{ route('billing.bill-view', $bill) }}" class="text-primary-600 hover:text-primary-900 font-medium">
                                View
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="px-6 py-4 text-center text-gray-500">
                            No outstanding bills found.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($bills->hasPages())
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $bills->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
