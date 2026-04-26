@extends('student-portal.layout')

@section('portal-title', 'Dashboard')

@section('student-content')
<div class="space-y-6">
    <!-- Welcome Banner -->
    <div class="bg-gradient-to-r from-indigo-600 via-purple-600 to-pink-600 rounded-2xl p-8 text-white shadow-lg">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-4xl font-bold mb-2">Welcome, {{ auth()->user()->name }}!</h1>
                <p class="text-indigo-100 text-lg">Let's check your academic progress</p>
            </div>
            <div class="hidden lg:block text-6xl opacity-20">
                <i class="fas fa-book-open"></i>
            </div>
        </div>
    </div>

    <!-- Key Statistics -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        @foreach($dashboardData['stats'] as $stat)
            <div class="group relative bg-white rounded-2xl shadow-md hover:shadow-2xl transition-all duration-300 overflow-hidden border border-{{ $stat['color'] }}-50 hover:border-{{ $stat['color'] }}-200">
                <div class="absolute inset-0 bg-gradient-to-br from-{{ $stat['color'] }}-50 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                <div class="bg-gradient-to-br from-{{ $stat['color'] }}-600 via-{{ $stat['color'] }}-700 to-{{ $stat['color'] }}-800 px-6 py-5 relative">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-{{ $stat['color'] }}-100 text-xs font-semibold uppercase tracking-wide">{{ $stat['label'] }}</p>
                            <p class="text-3xl font-bold text-white mt-2">{{ $stat['value'] }}</p>
                        </div>
                        <div class="w-16 h-16 rounded-full bg-white/10 flex items-center justify-center group-hover:scale-110 transition-transform duration-300 backdrop-blur-sm">
                            <i class="{{ $stat['icon'] }} text-3xl text-{{ $stat['color'] }}-100"></i>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Two Column Layout -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Recent Results -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 border border-gray-200 overflow-hidden">
                <div class="bg-gradient-to-r from-purple-600 to-pink-600 px-6 py-5 relative overflow-hidden">
                    <div class="absolute right-0 top-0 w-40 h-40 bg-white/5 rounded-full -mr-20 -mt-20 blur-2xl"></div>
                    <h2 class="text-xl font-bold text-white flex items-center gap-3 relative">
                        <i class="fas fa-chart-line text-pink-200"></i>Recent Results
                    </h2>
                </div>

                <div class="divide-y divide-gray-100">
                    @forelse($dashboardData['recentResults'] as $result)
                        <div class="p-6 hover:bg-gray-50 transition-colors group">
                            <div class="flex items-center justify-between">
                                <div class="flex-1">
                                    <p class="font-semibold text-gray-900">{{ $result['subject'] }}</p>
                                    <p class="text-sm text-gray-500 mt-1">{{ $result['term'] }} • {{ $result['date'] }}</p>
                                </div>
                                <div class="text-right">
                                    <div class="text-3xl font-bold text-indigo-600">{{ $result['score'] }}</div>
                                    <div class="text-xs text-gray-600 font-semibold mt-1">{{ $result['grade'] }}</div>
                                </div>
                            </div>
                            <div class="mt-3 w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-gradient-to-r from-indigo-500 to-purple-600 h-2 rounded-full" style="width: {{ $result['score'] }}%"></div>
                            </div>
                        </div>
                    @empty
                        <div class="p-8 text-center">
                            <i class="fas fa-inbox text-4xl text-gray-300 mb-3 block"></i>
                            <p class="text-gray-600 font-medium">No results available yet</p>
                        </div>
                    @endforelse
                </div>

                <div class="px-6 py-4 bg-gray-50 border-t border-gray-100">
                    <p class="text-gray-600 font-semibold text-sm">Latest results shown</p>
                </div>
            </div>
        </div>

        <!-- Performance & Attendance Sidebar -->
        <div class="space-y-6">
            <!-- Performance Card -->
            <div class="bg-gradient-to-br from-emerald-50 to-teal-50 rounded-2xl shadow-md border border-emerald-200 p-6 hover:shadow-lg transition-all">
                <h3 class="text-lg font-bold text-emerald-900 mb-4 flex items-center gap-2">
                    <i class="fas fa-star text-emerald-600"></i>Performance
                </h3>
                <div class="space-y-4">
                    <div>
                        <div class="flex justify-between items-center mb-2">
                            <p class="text-sm font-semibold text-emerald-800">Passed</p>
                            <span class="text-2xl font-bold text-emerald-600">{{ $dashboardData['passedCount'] }}</span>
                        </div>
                        <div class="w-full bg-emerald-200 rounded-full h-2">
                            <div class="bg-emerald-600 h-2 rounded-full" style="width: 70%"></div>
                        </div>
                    </div>
                    <div>
                        <div class="flex justify-between items-center mb-2">
                            <p class="text-sm font-semibold text-emerald-800">Failed</p>
                            <span class="text-2xl font-bold text-red-600">{{ $dashboardData['failedCount'] }}</span>
                        </div>
                        <div class="w-full bg-red-200 rounded-full h-2">
                            <div class="bg-red-600 h-2 rounded-full" style="width: 30%"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Attendance Card -->
            <div class="bg-gradient-to-br from-blue-50 to-cyan-50 rounded-2xl shadow-md border border-blue-200 p-6 hover:shadow-lg transition-all">
                <h3 class="text-lg font-bold text-blue-900 mb-4 flex items-center gap-2">
                    <i class="fas fa-calendar-check text-blue-600"></i>Attendance
                </h3>
                <div class="flex items-center justify-center mb-6">
                    <div class="relative w-40 h-40">
                        <svg class="w-full h-full transform -rotate-90" viewBox="0 0 120 120">
                            <circle cx="60" cy="60" r="54" fill="none" stroke="#e0f2fe" stroke-width="8"/>
                            <circle cx="60" cy="60" r="54" fill="none" stroke="#0369a1" stroke-width="8" 
                                    stroke-dasharray="{{$dashboardData['attendancePercentage'] * 3.39}}" stroke-dashoffset="0"
                                    stroke-linecap="round" style="transition: stroke-dasharray 1s ease"/>
                        </svg>
                        <div class="absolute inset-0 flex items-center justify-center flex-col">
                            <span class="text-3xl font-bold text-blue-600">{{ $dashboardData['attendancePercentage'] }}%</span>
                            <span class="text-xs text-blue-600 font-medium">Attendance</span>
                        </div>
                    </div>
                </div>
                <p class="text-center text-sm text-blue-700 font-semibold">Keep up good attendance!</p>
            </div>

            <!-- Quick Links -->
            <div class="space-y-2">
                <a href="{{ route('student-portal.timetable') }}" class="block w-full px-4 py-3 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 font-semibold rounded-lg border border-indigo-200 transition-all flex items-center gap-2">
                    <i class="fas fa-clock"></i>View Timetable
                </a>
                <a href="{{ route('student-portal.calendar') }}" class="block w-full px-4 py-3 bg-purple-50 hover:bg-purple-100 text-purple-700 font-semibold rounded-lg border border-purple-200 transition-all flex items-center gap-2">
                    <i class="fas fa-calendar-alt"></i>Academic Calendar
                </a>
            </div>
        </div>
    </div>

    <!-- Motivational Message -->
    <div class="bg-gradient-to-r from-amber-50 to-orange-50 rounded-2xl border-2 border-amber-200 p-6 shadow-md">
        <div class="flex items-start gap-4">
            <div class="w-12 h-12 rounded-full bg-gradient-to-br from-amber-400 to-orange-500 flex items-center justify-center flex-shrink-0">
                <i class="fas fa-lightbulb text-lg text-white"></i>
            </div>
            <div>
                <h3 class="font-bold text-amber-900 mb-1">Keep Learning! 📚</h3>
                <p class="text-amber-800 text-sm">Your academic journey is important. Review your results, maintain good attendance, and stay focused on your goals.</p>
            </div>
        </div>
    </div>
</div>

<style>
    /* Custom color utilities for dynamic backgrounds */
    .bg-blue-600 { background-color: rgb(37, 99, 235); }
    .bg-green-600 { background-color: rgb(22, 163, 74); }
    .bg-purple-600 { background-color: rgb(147, 51, 234); }
    .bg-amber-600 { background-color: rgb(217, 119, 6); }
    
    .from-blue-600 { --tw-gradient-from: rgb(37, 99, 235); }
    .from-green-600 { --tw-gradient-from: rgb(22, 163, 74); }
    .from-purple-600 { --tw-gradient-from: rgb(147, 51, 234); }
    .from-amber-600 { --tw-gradient-from: rgb(217, 119, 6); }
</style>
@endsection
