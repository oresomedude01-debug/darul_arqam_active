@extends('layouts.spa')

@section('title', 'Parent Portal - Dashboard')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 via-purple-50 to-slate-100 py-4 md:py-6 px-3 md:px-4 lg:px-6">
    <div class="max-w-7xl mx-auto space-y-4 md:space-y-5 lg:space-y-6">
        <!-- Header Section -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 md:gap-6">
            <div class="flex-1">
                <h1 class="text-3xl md:text-4xl lg:text-5xl font-bold text-gray-900 leading-tight mb-2 md:mb-3">Welcome Back, {{ auth()->user()->name }}!</h1>
                <p class="text-gray-600 text-base md:text-lg">Manage your children's school activities and payments</p>
            </div>
            <a href="{{ route('parent-portal.bills') }}" class="inline-flex items-center justify-center md:justify-start px-6 md:px-8 py-3 md:py-4 bg-gradient-to-r from-purple-600 to-purple-700 text-white font-semibold rounded-xl hover:from-purple-700 hover:to-purple-800 transition-all shadow-lg hover:shadow-xl hover:-translate-y-1 text-sm md:text-base whitespace-nowrap">
                <i class="fas fa-file-invoice-dollar mr-2"></i>Pay Bills
            </a>
        </div>

        <!-- Summary Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 md:gap-4">
            <!-- Total Children -->
            <div class="group relative bg-white rounded-2xl shadow-md hover:shadow-2xl transition-all duration-300 overflow-hidden border border-blue-50 hover:border-blue-200">
                <div class="absolute inset-0 bg-gradient-to-br from-blue-50 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                <div class="bg-gradient-to-br from-blue-600 via-blue-700 to-blue-800 px-4 py-4 md:py-5 relative">
                    <div class="flex items-center justify-between gap-4">
                        <div class="flex-1">
                            <p class="text-blue-100 text-xs font-semibold uppercase tracking-wide">Total Children</p>
                            <p class="text-3xl md:text-4xl font-bold text-white mt-2">{{ $children->count() }}</p>
                        </div>
                        <div class="w-14 h-14 md:w-16 md:h-16 rounded-full bg-white/10 flex items-center justify-center group-hover:scale-110 transition-transform duration-300 backdrop-blur-sm flex-shrink-0">
                            <i class="fas fa-children text-2xl md:text-3xl text-blue-100"></i>
                        </div>
                    </div>
                </div>
                <div class="px-4 py-2 bg-gradient-to-r from-blue-50 to-transparent border-t border-blue-100">
                    <a href="{{ route('parent-portal.children') }}" class="text-xs md:text-sm text-blue-700 font-semibold hover:text-blue-900 flex items-center gap-1 group/link">
                        View all <i class="fas fa-arrow-right group-hover/link:translate-x-1 transition-transform text-xs"></i>
                    </a>
                </div>
            </div>

            <!-- Outstanding Bills -->
            <div class="group relative bg-white rounded-2xl shadow-md hover:shadow-2xl transition-all duration-300 overflow-hidden border border-red-50 hover:border-red-200">
                <div class="absolute inset-0 bg-gradient-to-br from-red-50 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                <div class="bg-gradient-to-br from-red-600 via-red-700 to-red-800 px-4 py-4 md:py-5 relative">
                    <div class="flex items-center justify-between gap-4">
                        <div class="flex-1">
                            <p class="text-red-100 text-xs font-semibold uppercase tracking-wide">Unpaid Bills</p>
                            @php
                                $formattedAmount = number_format($totalOutstanding, 0);
                                $amountLength = strlen($formattedAmount);
                                $textSize = match(true) {
                                    $amountLength > 12 => 'text-xl md:text-2xl lg:text-3xl',
                                    $amountLength > 10 => 'text-2xl md:text-3xl lg:text-4xl',
                                    $amountLength > 8 => 'text-2xl md:text-3xl lg:text-4xl',
                                    default => 'text-3xl md:text-4xl lg:text-5xl'
                                };
                            @endphp
                            <p class="{{ $textSize }} font-bold text-white mt-3">₦{{ $formattedAmount }}</p>
                            <p class="text-red-100 text-xs md:text-sm mt-2 font-medium">{{ $pendingBills }} item{{ $pendingBills !== 1 ? 's' : '' }}</p>
                        </div>
                        <div class="w-14 h-14 md:w-16 md:h-16 rounded-full bg-white/10 flex items-center justify-center group-hover:scale-110 transition-transform duration-300 backdrop-blur-sm flex-shrink-0">
                            <i class="fas fa-exclamation-circle text-2xl md:text-3xl text-red-100"></i>
                        </div>
                    </div>
                </div>
                <div class="px-4 py-2 bg-gradient-to-r from-red-50 to-transparent border-t border-red-100">
                    <a href="{{ route('parent-portal.bills') }}" class="text-xs md:text-sm text-red-700 font-semibold hover:text-red-900 flex items-center gap-1 group/link">
                        Pay now <i class="fas fa-arrow-right group-hover/link:translate-x-1 transition-transform text-xs"></i>
                    </a>
                </div>
            </div>

            <!-- Total Paid -->
            <div class="group relative bg-white rounded-2xl shadow-md hover:shadow-2xl transition-all duration-300 overflow-hidden border border-green-50 hover:border-green-200">
                <div class="absolute inset-0 bg-gradient-to-br from-green-50 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                <div class="bg-gradient-to-br from-emerald-600 via-emerald-700 to-emerald-800 px-4 py-4 md:py-5 relative">
                    <div class="flex items-center justify-between gap-4">
                        <div class="flex-1">
                            <p class="text-emerald-100 text-xs font-semibold uppercase tracking-wide">Total Paid</p>
                            <p class="text-2xl md:text-4xl font-bold text-white mt-2">₦{{ number_format($totalPaid / 1000000, 1) }}M</p>
                            <p class="text-emerald-100 text-xs mt-2"><i class="fas fa-check-circle mr-1"></i>All-time</p>
                        </div>
                        <div class="w-14 h-14 md:w-16 md:h-16 rounded-full bg-white/10 flex items-center justify-center group-hover:scale-110 transition-transform duration-300 backdrop-blur-sm flex-shrink-0">
                            <i class="fas fa-credit-card text-2xl md:text-3xl text-emerald-100"></i>
                        </div>
                    </div>
                </div>
                <div class="px-4 py-2 bg-gradient-to-r from-emerald-50 to-transparent border-t border-emerald-100">
                    <a href="{{ route('parent-portal.payment-history') }}" class="text-xs md:text-sm text-emerald-700 font-semibold hover:text-emerald-900 flex items-center gap-1 group/link">
                        History <i class="fas fa-arrow-right group-hover/link:translate-x-1 transition-transform text-xs"></i>
                    </a>
                </div>
            </div>

            <!-- Account Status -->
            <div class="group relative bg-white rounded-2xl shadow-md hover:shadow-2xl transition-all duration-300 overflow-hidden border border-indigo-50 hover:border-indigo-200">
                <div class="absolute inset-0 bg-gradient-to-br from-indigo-50 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                <div class="bg-gradient-to-br from-indigo-600 via-indigo-700 to-indigo-800 px-4 py-4 md:py-5 relative">
                    <div class="flex items-center justify-between gap-4">
                        <div class="flex-1">
                            <p class="text-indigo-100 text-xs font-semibold uppercase tracking-wide">Account Status</p>
                            <p class="text-xl md:text-2xl font-bold text-white mt-2">{{ $totalOutstanding > 0 ? 'Active' : 'Good Standing' }}</p>
                            <div class="mt-2 flex items-center gap-2">
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-white/20 text-white text-xs font-semibold rounded-full backdrop-blur-sm">
                                    <span class="w-2 h-2 bg-white rounded-full animate-pulse"></span>Verified
                                </span>
                            </div>
                        </div>
                        <div class="w-14 h-14 md:w-16 md:h-16 rounded-full bg-white/10 flex items-center justify-center group-hover:scale-110 transition-transform duration-300 backdrop-blur-sm flex-shrink-0">
                            <i class="fas fa-shield-check text-2xl md:text-3xl text-indigo-100"></i>
                        </div>
                    </div>
                </div>
                <div class="px-4 py-2 bg-gradient-to-r from-indigo-50 to-transparent border-t border-indigo-100">
                    <p class="text-xs text-indigo-700 font-medium">
                        <i class="fas fa-check text-green-600 mr-1"></i>Account active and verified
                    </p>
                </div>
            </div>
        </div>

        <!-- Children Overview Section -->
        <div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden hover:shadow-xl transition-all duration-300">
            <div class="bg-gradient-to-r from-indigo-600 via-purple-600 to-pink-600 px-6 py-6 md:py-8 relative overflow-hidden">
                <div class="absolute right-0 top-0 w-40 h-40 bg-white/5 rounded-full -mr-20 -mt-20 blur-2xl"></div>
                <h2 class="text-xl md:text-2xl font-bold text-white flex items-center gap-3 relative">
                    <i class="fas fa-graduation-cap text-pink-200"></i>Your Children
                </h2>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gradient-to-r from-indigo-50 via-purple-50 to-pink-50 border-b-2 border-indigo-100">
                        <tr>
                            <th class="px-4 md:px-6 py-4 text-left text-xs md:text-sm font-bold text-indigo-900">Name</th>
                            <th class="px-4 md:px-6 py-4 text-left text-xs md:text-sm font-bold text-indigo-900 hidden sm:table-cell">Class</th>
                            <th class="px-4 md:px-6 py-4 text-left text-xs md:text-sm font-bold text-indigo-900 hidden md:table-cell">Admission No.</th>
                            <th class="px-4 md:px-6 py-4 text-center text-xs md:text-sm font-bold text-indigo-900">Balance</th>
                            <th class="px-4 md:px-6 py-4 text-center text-xs md:text-sm font-bold text-indigo-900">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($children as $child)
                            @php
                                $outstanding = \App\Models\StudentBill::where('student_id', $child->id)
                                    ->where('status', '!=', 'paid')
                                    ->sum('total_amount');
                            @endphp
                        <tr class="hover:bg-indigo-50/40 transition-colors duration-200 group">
                            <td class="px-4 md:px-6 py-4 md:py-5">
                                <div class="flex items-center gap-3 md:gap-4">
                                    <div class="w-10 h-10 md:w-12 md:h-12 rounded-full bg-gradient-to-br from-indigo-400 to-purple-500 flex items-center justify-center flex-shrink-0 shadow-md group-hover:shadow-lg transition-shadow">
                                        <span class="text-white font-bold text-xs md:text-sm">{{ substr($child->first_name ?? '', 0, 1) }}</span>
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <p class="font-semibold text-gray-900 text-sm md:text-base truncate">{{ $child->first_name ?? '' }} {{ $child->last_name ?? '' }}</p>
                                        <p class="text-xs text-gray-500 mt-0.5">ID: {{ $child->id }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 md:px-6 py-4 md:py-5 hidden sm:table-cell">
                                <span class="inline-flex items-center gap-2 px-2 md:px-3 py-2 bg-gradient-to-r from-blue-100 to-blue-50 text-blue-800 text-xs md:text-sm rounded-lg font-medium border border-blue-200 whitespace-nowrap">
                                    <i class="fas fa-book-open text-xs hidden md:inline"></i>{{ $child->schoolClass?->name ?? 'N/A' }}
                                </span>
                            </td>
                            <td class="px-4 md:px-6 py-4 md:py-5 text-xs md:text-sm text-gray-600 font-mono hidden md:table-cell">
                                {{ $child->admission_number }}
                            </td>
                            <td class="px-4 md:px-6 py-4 md:py-5">
                                @if($outstanding > 0)
                                    <div class="inline-flex flex-col items-center">
                                        <span class="inline-flex items-center gap-1 px-2 md:px-3 py-1.5 md:py-2 bg-red-50 text-red-700 text-xs md:text-sm rounded-lg font-semibold border border-red-200 mb-1 whitespace-nowrap">
                                            <span class="w-2 h-2 bg-red-500 rounded-full"></span>
                                            ₦{{ number_format($outstanding, 0) }}
                                        </span>
                                        <span class="text-xs text-red-600 font-medium hidden md:block">Outstanding</span>
                                    </div>
                                @else
                                    <div class="inline-flex flex-col items-center">
                                        <span class="inline-flex items-center gap-1 px-2 md:px-3 py-1.5 md:py-2 bg-green-50 text-green-700 text-xs md:text-sm rounded-lg font-semibold border border-green-200 mb-1">
                                            <span class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></span>
                                            Paid
                                        </span>
                                        <span class="text-xs text-green-600 font-medium hidden md:block">Up-to-date</span>
                                    </div>
                                @endif
                            </td>
                            <td class="px-4 md:px-6 py-4 md:py-5">
                                <div class="flex items-center justify-center gap-2">
                                    <a href="{{ route('parent-portal.bills', ['student' => $child->id]) }}" 
                                       class="inline-flex items-center justify-center w-9 h-9 md:w-10 md:h-10 text-indigo-600 hover:text-white hover:bg-indigo-600 rounded-lg transition-all duration-200 border border-indigo-200 hover:border-indigo-600 group/btn text-sm">
                                        <i class="fas fa-file-invoice group-hover/btn:scale-110 transition-transform"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-4 md:px-6 py-6 md:py-8 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <div class="w-14 h-14 md:w-16 md:h-16 rounded-full bg-gray-100 flex items-center justify-center mb-4">
                                        <i class="fas fa-inbox text-2xl md:text-3xl text-gray-400"></i>
                                    </div>
                                    <p class="text-gray-600 font-medium text-sm md:text-base">No children registered</p>
                                    <p class="text-gray-400 text-xs md:text-sm mt-1">Please register your children in the system</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Quick Links -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <a href="{{ route('parent-portal.bills') }}" class="group relative bg-white rounded-2xl shadow-md hover:shadow-2xl transition-all duration-300 overflow-hidden border border-purple-100 hover:border-purple-300">
                <div class="absolute inset-0 bg-gradient-to-br from-purple-50 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                <div class="relative p-4 md:p-5 min-h-32 md:min-h-36 flex flex-col justify-between">
                    <div class="flex items-start justify-between mb-3 gap-3">
                        <div class="flex-1">
                            <h3 class="text-base md:text-lg font-bold text-gray-900 mb-1">View Bills</h3>
                            <p class="text-gray-600 text-xs md:text-sm">Check and manage all school bills</p>
                        </div>
                        <div class="w-11 h-11 md:w-12 md:h-12 rounded-full bg-gradient-to-br from-purple-100 to-purple-50 flex items-center justify-center group-hover:scale-110 transition-transform duration-300 shadow-md flex-shrink-0">
                            <i class="fas fa-file-invoice-dollar text-lg md:text-xl text-purple-600"></i>
                        </div>
                    </div>
                    <div class="inline-flex items-center text-purple-700 font-semibold text-xs md:text-sm group-hover:gap-2 transition-all">
                        View all bills <i class="fas fa-arrow-right ml-2 group-hover:translate-x-1 transition-transform"></i>
                    </div>
                </div>
            </a>

            <a href="{{ route('parent-portal.payment-history') }}" class="group relative bg-white rounded-2xl shadow-md hover:shadow-2xl transition-all duration-300 overflow-hidden border border-emerald-100 hover:border-emerald-300">
                <div class="absolute inset-0 bg-gradient-to-br from-emerald-50 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                <div class="relative p-4 md:p-5 min-h-32 md:min-h-36 flex flex-col justify-between">
                    <div class="flex items-start justify-between mb-3 gap-3">
                        <div class="flex-1">
                            <h3 class="text-base md:text-lg font-bold text-gray-900 mb-1">Payment History</h3>
                            <p class="text-gray-600 text-xs md:text-sm">Track all your transactions</p>
                        </div>
                        <div class="w-11 h-11 md:w-12 md:h-12 rounded-full bg-gradient-to-br from-emerald-100 to-emerald-50 flex items-center justify-center group-hover:scale-110 transition-transform duration-300 shadow-md flex-shrink-0">
                            <i class="fas fa-history text-lg md:text-xl text-emerald-600"></i>
                        </div>
                    </div>
                    <div class="inline-flex items-center text-emerald-700 font-semibold text-xs md:text-sm group-hover:gap-2 transition-all">
                        View history <i class="fas fa-arrow-right ml-2 group-hover:translate-x-1 transition-transform"></i>
                    </div>
                </div>
            </a>

            <a href="{{ route('parent-portal.children') }}" class="group relative bg-white rounded-2xl shadow-md hover:shadow-2xl transition-all duration-300 overflow-hidden border border-cyan-100 hover:border-cyan-300">
                <div class="absolute inset-0 bg-gradient-to-br from-cyan-50 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                <div class="relative p-4 md:p-5 min-h-32 md:min-h-36 flex flex-col justify-between">
                    <div class="flex items-start justify-between mb-3 gap-3">
                        <div class="flex-1">
                            <h3 class="text-base md:text-lg font-bold text-gray-900 mb-1">Children Info</h3>
                            <p class="text-gray-600 text-xs md:text-sm">View all your children profiles</p>
                        </div>
                        <div class="w-11 h-11 md:w-12 md:h-12 rounded-full bg-gradient-to-br from-cyan-100 to-cyan-50 flex items-center justify-center group-hover:scale-110 transition-transform duration-300 shadow-md flex-shrink-0">
                            <i class="fas fa-children text-lg md:text-xl text-cyan-600"></i>
                        </div>
                    </div>
                    <div class="inline-flex items-center text-cyan-700 font-semibold text-xs md:text-sm group-hover:gap-2 transition-all">
                        View children <i class="fas fa-arrow-right ml-2 group-hover:translate-x-1 transition-transform"></i>
                    </div>
                </div>
            </a>
        </div>

        <!-- Info Box -->
        <div class="bg-gradient-to-r from-amber-50 via-orange-50 to-red-50 rounded-2xl border-2 border-amber-200 p-4 md:p-5 shadow-md hover:shadow-lg transition-shadow duration-300">
            <div class="flex items-start gap-3 md:gap-4">
                <div class="flex-shrink-0">
                    <div class="flex items-center justify-center h-11 w-11 md:h-12 md:w-12 rounded-xl bg-gradient-to-br from-amber-400 to-orange-500 shadow-lg">
                        <i class="fas fa-lightbulb text-lg md:text-xl text-white"></i>
                    </div>
                </div>
                <div class="flex-1 min-w-0">
                    <h3 class="text-base font-bold text-amber-900 mb-1">Need Help?</h3>
                    <p class="text-amber-800 mb-3 leading-relaxed text-xs md:text-sm">If you have any questions about bills, payments, or your children's academic progress, please reach out to our support team. We're here to assist you.</p>
                    <div class="flex gap-3 flex-wrap">
                        @if($schoolSettings && $schoolSettings->school_email)
                            <a href="mailto:{{ $schoolSettings->school_email }}" class="inline-flex items-center px-4 md:px-5 py-2 md:py-2.5 bg-gradient-to-r from-amber-100 to-amber-50 text-amber-800 rounded-lg hover:from-amber-200 hover:to-amber-100 transition-all font-semibold text-xs md:text-sm border border-amber-300 hover:border-amber-400 shadow-sm hover:shadow-md">
                                <i class="fas fa-envelope mr-2"></i>Email Support
                            </a>
                        @endif
                        @if($schoolSettings && $schoolSettings->school_phone)
                            <a href="tel:{{ str_replace([' ', '-', '(', ')'], '', $schoolSettings->school_phone) }}" class="inline-flex items-center px-4 md:px-5 py-2 md:py-2.5 bg-gradient-to-r from-orange-100 to-orange-50 text-orange-800 rounded-lg hover:from-orange-200 hover:to-orange-100 transition-all font-semibold text-xs md:text-sm border border-orange-300 hover:border-orange-400 shadow-sm hover:shadow-md">
                                <i class="fas fa-phone mr-2"></i>Call Us
                            </a>
                        @endif
                        @if($schoolSettings && $schoolSettings->school_phone)
                            <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $schoolSettings->school_phone) }}?text=Hello%20I%20need%20help%20with%20my%20child's%20account" target="_blank" class="inline-flex items-center px-4 md:px-5 py-2 md:py-2.5 bg-gradient-to-r from-green-100 to-green-50 text-green-800 rounded-lg hover:from-green-200 hover:to-green-100 transition-all font-semibold text-xs md:text-sm border border-green-300 hover:border-green-400 shadow-sm hover:shadow-md">
                                <i class="fab fa-whatsapp mr-2"></i>WhatsApp
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
