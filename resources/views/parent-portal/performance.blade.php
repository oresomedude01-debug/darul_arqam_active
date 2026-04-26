@extends('layouts.spa')

@section('title', 'Children Performance')

@section('content')
<div class="min-h-screen bg-gray-50">
    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-6">
            <h1 class="text-3xl font-bold text-gray-900">Children's Performance Analytics</h1>
            <p class="mt-2 text-sm text-gray-600">Track academic progress and performance trends</p>
        </div>

        <!-- Child Selection -->
        <div class="bg-white shadow-md rounded-lg p-6 mb-6">
            <form method="GET" class="flex gap-4">
                <div class="flex-1">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Select Child</label>
                    <select name="student" onchange="this.form.submit()" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">-- Select a child --</option>
                        @foreach($children as $child)
                            <option value="{{ $child->id }}" @if($selectedChild && $selectedChild->id == $child->id) selected @endif>
                                {{ $child->first_name }} {{ $child->last_name }} ({{ $child->schoolClass->name ?? 'N/A' }})
                            </option>
                        @endforeach
                    </select>
                </div>
            </form>
        </div>

        @if($selectedChild && $performanceData && !$performanceData['no_data'])
            <!-- Performance Summary Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
                <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg shadow-md p-6 text-white">
                    <div class="text-sm opacity-90">Overall Average</div>
                    <div class="text-3xl font-bold mt-2">{{ number_format($performanceData['overall_average'], 1) }}%</div>
                    <div class="text-xs mt-2 opacity-75">Rating: <strong>{{ $performanceData['overall_rating'] }}</strong></div>
                </div>

                <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-lg shadow-md p-6 text-white">
                    <div class="text-sm opacity-90">Best Subject</div>
                    <div class="text-3xl font-bold mt-2">{{ $performanceData['best_subject'] }}</div>
                    <div class="text-xs mt-2 opacity-75">{{ $performanceData['best_score'] }}%</div>
                </div>

                <div class="bg-gradient-to-br from-yellow-500 to-yellow-600 rounded-lg shadow-md p-6 text-white">
                    <div class="text-sm opacity-90">Needs Improvement</div>
                    <div class="text-3xl font-bold mt-2">{{ $performanceData['worst_subject'] }}</div>
                    <div class="text-xs mt-2 opacity-75">{{ $performanceData['worst_score'] }}%</div>
                </div>

                <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-lg shadow-md p-6 text-white">
                    <div class="text-sm opacity-90">Attendance Rate</div>
                    <div class="text-3xl font-bold mt-2">{{ $performanceData['attendance_rate'] }}%</div>
                    <div class="text-xs mt-2 opacity-75">{{ $performanceData['present_days'] }}/{{ $performanceData['total_days'] }} days</div>
                </div>
            </div>

            <!-- Status Alert -->
            <div class="mb-6 p-4 rounded-lg 
                @if(strpos($performanceData['status_message'], 'Excellent') !== false || strpos($performanceData['status_message'], 'Good Progress') !== false) 
                    bg-green-50 border-l-4 border-green-500 text-green-800
                @elseif(strpos($performanceData['status_message'], 'Needs Improvement') !== false) 
                    bg-yellow-50 border-l-4 border-yellow-500 text-yellow-800
                @else 
                    bg-red-50 border-l-4 border-red-500 text-red-800
                @endif">
                <h3 class="font-bold text-lg">{{ $performanceData['status_message'] }}</h3>
            </div>

            <!-- Performance Details -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Subject Performance Table -->
                <div class="bg-white shadow-md rounded-lg overflow-hidden">
                    <div class="px-6 py-4 bg-gradient-to-r from-blue-600 to-blue-700">
                        <h2 class="text-lg font-semibold text-white">Subject Performance</h2>
                    </div>

                    <div class="divide-y">
                        @foreach($performanceData['subjects'] as $subject)
                            <div class="px-6 py-4 hover:bg-gray-50">
                                <div class="flex items-center justify-between">
                                    <div class="flex-1">
                                        <h3 class="font-semibold text-gray-900">{{ $subject['name'] }}</h3>
                                        <div class="w-full bg-gray-200 rounded-full h-2 mt-2">
                                            <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $subject['score'] }}%"></div>
                                        </div>
                                    </div>
                                    <span class="ml-4 text-lg font-bold text-gray-900">{{ $subject['score'] }}%</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Performance Trend -->
                <div class="bg-white shadow-md rounded-lg p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Performance Summary</h2>
                    
                    <div class="space-y-4">
                        <div class="flex items-center justify-between">
                            <span class="text-gray-700">Total Tests Taken</span>
                            <span class="text-2xl font-bold text-blue-600">{{ $performanceData['total_tests'] }}</span>
                        </div>
                        
                        <div class="flex items-center justify-between">
                            <span class="text-gray-700">Passed Tests</span>
                            <span class="text-2xl font-bold text-green-600">{{ $performanceData['passed_tests'] }}</span>
                        </div>
                        
                        <div class="flex items-center justify-between">
                            <span class="text-gray-700">Failed Tests</span>
                            <span class="text-2xl font-bold text-red-600">{{ $performanceData['failed_tests'] }}</span>
                        </div>

                        <div class="border-t pt-4 mt-4">
                            <div class="flex items-center justify-between">
                                <span class="text-gray-700 font-semibold">Pass Rate</span>
                                <span class="text-2xl font-bold text-blue-600">{{ $performanceData['pass_rate'] }}%</span>
                            </div>
                        </div>

                        <div class="bg-blue-50 p-4 rounded-lg mt-4">
                            <p class="text-sm text-blue-800">
                                <strong>Recommendation:</strong> {{ $performanceData['recommendation'] }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Strengths and Areas for Improvement -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-6">
                <div class="bg-white shadow-md rounded-lg p-6 border-l-4 border-green-500">
                    <h3 class="text-lg font-semibold text-green-900 mb-4">✓ Strengths</h3>
                    <ul class="space-y-2">
                        @foreach($performanceData['strengths'] as $strength)
                            <li class="flex items-start">
                                <span class="text-green-600 mr-3">✓</span>
                                <span class="text-gray-700">{{ $strength }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>

                <div class="bg-white shadow-md rounded-lg p-6 border-l-4 border-orange-500">
                    <h3 class="text-lg font-semibold text-orange-900 mb-4">↑ Areas for Improvement</h3>
                    <ul class="space-y-2">
                        @foreach($performanceData['improvements'] as $improvement)
                            <li class="flex items-start">
                                <span class="text-orange-600 mr-3">→</span>
                                <span class="text-gray-700">{{ $improvement }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @elseif($selectedChild && $performanceData && $performanceData['no_data'])
            <div class="bg-blue-50 border-l-4 border-blue-500 rounded-lg p-8 text-center">
                <svg class="mx-auto h-12 w-12 text-blue-500 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <h3 class="text-lg font-semibold text-blue-900 mb-2">No Data Available</h3>
                <p class="text-blue-700">{{ $performanceData['message'] }}</p>
            </div>
        @else
            <div class="bg-white shadow-md rounded-lg p-12 text-center">
                <p class="text-gray-500 text-lg">Select a child to view their performance analytics</p>
            </div>
        @endif
    </div>
</div>
@endsection
