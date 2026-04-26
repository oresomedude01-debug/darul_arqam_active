@extends('layouts.spa')

@section('title', 'My Children')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-slate-100 py-4 md:py-6 px-3 md:px-4 lg:px-6">
    <div class="max-w-6xl mx-auto space-y-4 md:space-y-5">
        <!-- Header -->
        <div class="flex items-center justify-between">
            <div>
            <h1 class="text-3xl md:text-4xl font-bold text-gray-900">My Children</h1>
                <p class="text-gray-600 mt-1 md:mt-2">Manage information for all your children</p>
            </div>
        </div>

        <!-- Children Table -->
        <div class="bg-white rounded-2xl shadow-lg border border-blue-100 min-h-96">
            <div class="bg-gradient-to-r from-blue-600 to-indigo-600 px-4 md:px-6 py-4 md:py-5">
                <h2 class="text-lg md:text-xl font-bold text-white flex items-center gap-2">
                    <i class="fas fa-users"></i>Children List
                </h2>
            </div>

            <!-- Grid View (Mobile) -->
            <div class="md:hidden p-3 space-y-3">
                @if($children->count() > 0)
                    @foreach($children as $item)
                        @php
                            $child = $item['student'];
                            $outstanding = $item['outstanding'];
                        @endphp
                        <div class="bg-white rounded-lg shadow border border-blue-100 p-4 hover:shadow-md transition-all">
                            <div class="space-y-3">
                                <!-- Name and Info -->
                                <div class="border-b border-gray-100 pb-3">
                                    <p class="font-bold text-gray-900 text-base">{{ $child->first_name }} {{ $child->last_name }}</p>
                                    @if($child->date_of_birth)
                                        <p class="text-xs text-gray-600 mt-0.5">DOB: {{ $child->date_of_birth->format('M d, Y') }}</p>
                                    @endif
                                    <p class="text-xs text-gray-500 font-mono mt-1">Admission Number: {{ $child->admission_number }}</p>
                                </div>

                                <!-- Class and Status -->
                                <div class="flex gap-2">
                                    <span class="inline-block px-2.5 py-1 bg-blue-100 text-blue-800 rounded-lg font-semibold text-xs">
                                        {{ $child->schoolClass?->name ?? 'N/A' }}
                                    </span>
                                    <span class="inline-block px-2.5 py-1 {{ $child->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }} rounded-lg font-semibold text-xs">
                                        {{ ucfirst($child->status) }}
                                    </span>
                                </div>

                                <!-- Outstanding Balance -->
                                <div class="bg-gradient-to-r {{ $outstanding > 0 ? 'from-red-50 to-red-100' : 'from-green-50 to-green-100' }} rounded-lg p-2.5">
                                    <p class="text-xs text-gray-600">Outstanding Balance</p>
                                    <p class="text-lg font-bold {{ $outstanding > 0 ? 'text-red-600' : 'text-green-600' }}">
                                        ₦{{ number_format($outstanding, 2) }}
                                    </p>
                                </div>

                                <!-- Actions -->
                                <div class="space-y-2 pt-2">
                                    <div class="grid grid-cols-2 gap-2">
                                        <a href="{{ route('parent-portal.children.show', $child->id) }}" 
                                           class="inline-flex items-center justify-center px-3 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium text-xs">
                                            <i class="fas fa-eye mr-1"></i>View
                                        </a>
                                        <a href="{{ route('parent-portal.attendance', ['student' => $child->id]) }}" 
                                           class="inline-flex items-center justify-center px-3 py-2 bg-teal-600 text-white rounded-lg hover:bg-teal-700 transition-colors font-medium text-xs">
                                            <i class="fas fa-calendar-check mr-1"></i>Attendance
                                        </a>
                                        <a href="{{ route('parent-portal.performance', ['student' => $child->id]) }}" 
                                           class="inline-flex items-center justify-center px-3 py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-700 transition-colors font-medium text-xs">
                                            <i class="fas fa-chart-line mr-1"></i>Performance
                                        </a>
                                        <a href="{{ route('parent-portal.bills', ['student' => $child->id]) }}" 
                                           class="inline-flex items-center justify-center px-3 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors font-medium text-xs">
                                            <i class="fas fa-file-invoice-dollar mr-1"></i>Bills
                                        </a>
                                    </div>
                                    @if($outstanding > 0)
                                        <a href="{{ route('parent-portal.bills', ['student' => $child->id, 'status' => 'pending']) }}" 
                                           class="w-full inline-flex items-center justify-center px-3 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors font-medium text-xs">
                                            <i class="fas fa-money-bill-wave mr-1"></i>Pay Outstanding
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="p-8 text-center bg-white rounded-lg border border-gray-200">
                        <i class="fas fa-inbox text-4xl text-gray-300 mb-3 block"></i>
                        <h3 class="text-lg font-bold text-gray-900 mb-1">No Children Found</h3>
                        <p class="text-sm text-gray-600">Contact the school office to register your children.</p>
                    </div>
                @endif
            </div>

            <!-- Table View (Desktop) -->
            <div class="hidden md:block overflow-visible relative z-0">
                @if($children->count() > 0)
                <div class="overflow-x-auto overflow-y-visible">
                <table class="w-full relative">
                    <thead class="bg-gradient-to-r from-blue-50 to-indigo-50 border-b-2 border-blue-200">
                        <tr>
                            <th class="px-4 md:px-6 py-3 md:py-4 text-left text-xs md:text-sm font-bold text-blue-900">Name</th>
                            <th class="px-4 md:px-6 py-3 md:py-4 text-left text-xs md:text-sm font-bold text-blue-900">Admission Number</th>
                            <th class="px-4 md:px-6 py-3 md:py-4 text-center text-xs md:text-sm font-bold text-blue-900">Class</th>
                            <th class="px-4 md:px-6 py-3 md:py-4 text-center text-xs md:text-sm font-bold text-blue-900">Status</th>
                            <th class="px-4 md:px-6 py-3 md:py-4 text-center text-xs md:text-sm font-bold text-blue-900">Outstanding</th>
                            <th class="px-4 md:px-6 py-3 md:py-4 text-center text-xs md:text-sm font-bold text-blue-900">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 relative overflow-visible">
                        @foreach($children as $item)
                            @php
                                $child = $item['student'];
                                $outstanding = $item['outstanding'];
                            @endphp
                        <tr class="hover:bg-blue-50/50 transition-colors">
                            <td class="px-4 md:px-6 py-3 md:py-4">
                                <div>
                                    <p class="font-semibold text-gray-900 text-sm md:text-base">{{ $child->first_name }} {{ $child->last_name }}</p>
                                    @if($child->date_of_birth)
                                    <p class="text-xs text-gray-600 mt-0.5">DOB: {{ $child->date_of_birth->format('M d, Y') }}</p>
                                    @endif
                                </div>
                            </td>
                            <td class="px-4 md:px-6 py-3 md:py-4">
                                <span class="text-xs md:text-sm font-mono text-gray-700">{{ $child->admission_number }}</span>
                            </td>
                            <td class="px-4 md:px-6 py-3 md:py-4 text-center">
                                <span class="inline-block px-2 md:px-3 py-1 bg-blue-100 text-blue-800 rounded-lg font-semibold text-xs md:text-sm">
                                    {{ $child->schoolClass?->name ?? 'N/A' }}
                                </span>
                            </td>
                            <td class="px-4 md:px-6 py-3 md:py-4 text-center">
                                <span class="inline-block px-2 md:px-3 py-1 {{ $child->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }} rounded-lg font-semibold text-xs md:text-sm">
                                    {{ ucfirst($child->status) }}
                                </span>
                            </td>
                            <td class="px-4 md:px-6 py-3 md:py-4 text-center">
                                <span class="text-sm md:text-lg font-bold {{ $outstanding > 0 ? 'text-red-600' : 'text-green-600' }}">
                                    ₦{{ number_format($outstanding, 2) }}
                                </span>
                            </td>
                            <td class="px-4 md:px-6 py-3 md:py-4 text-center relative z-0">
                                <div x-data="{ open: false }" @click.outside="open = false" class="relative">
                                    <button @click="open = !open" class="inline-flex items-center px-3 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors font-medium text-xs md:text-sm">
                                        <i class="fas fa-ellipsis-v mr-1"></i>Actions
                                    </button>
                                    <div x-show="open" x-transition class="absolute right-0 top-full mt-2 w-48 bg-white border border-gray-200 rounded-lg shadow-2xl z-50">
                                        <a href="{{ route('parent-portal.children.show', $child->id) }}" 
                                           class="flex items-center px-4 py-2.5 text-gray-700 hover:bg-blue-50 hover:text-blue-600 text-sm border-b border-gray-100">
                                            <i class="fas fa-eye w-4 mr-2"></i>View Profile
                                        </a>
                                        <a href="{{ route('parent-portal.attendance', ['student' => $child->id]) }}" 
                                           class="flex items-center px-4 py-2.5 text-gray-700 hover:bg-teal-50 hover:text-teal-600 text-sm border-b border-gray-100">
                                            <i class="fas fa-calendar-check w-4 mr-2"></i>Attendance
                                        </a>
                                        <a href="{{ route('parent-portal.performance', ['student' => $child->id]) }}" 
                                           class="flex items-center px-4 py-2.5 text-gray-700 hover:bg-orange-50 hover:text-orange-600 text-sm border-b border-gray-100">
                                            <i class="fas fa-chart-line w-4 mr-2"></i>Performance
                                        </a>
                                        
                                        <a href="{{ route('parent-portal.bills', ['student' => $child->id]) }}" 
                                           class="flex items-center px-4 py-2.5 text-gray-700 hover:bg-purple-50 hover:text-purple-600 text-sm border-b border-gray-100">
                                            <i class="fas fa-file-invoice-dollar w-4 mr-2"></i>Payment
                                        </a>
                                        @if($outstanding > 0)
                                        <a href="{{ route('parent-portal.bills', ['student' => $child->id, 'status' => 'pending']) }}" 
                                           class="flex items-center px-4 py-2.5 text-gray-700 hover:bg-red-50 hover:text-red-600 text-sm">
                                            <i class="fas fa-money-bill-wave w-4 mr-2"></i>Pay Outstanding
                                        </a>
                                        @endif
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                </div>
                @else
                <div class="p-12 text-center">
                    <i class="fas fa-inbox text-5xl text-gray-300 mb-4 block"></i>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">No Children Found</h3>
                    <p class="text-gray-600">You don't have any children registered in the system. Please contact the school office.</p>
                </div>
                @endif
            </div>
        </div>

        <!-- Back Button -->
        <div class="flex gap-2 px-3 md:px-0">
            <a href="{{ route('parent-portal.dashboard') }}" class="inline-flex items-center px-3 md:px-4 py-1.5 md:py-2 text-gray-600 hover:text-gray-900 font-medium text-sm md:text-base">
                <i class="fas fa-arrow-left mr-2"></i>Back to Dashboard
            </a>
        </div>
    </div>
</div>
@endsection
