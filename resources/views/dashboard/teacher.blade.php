@extends('layouts.spa')

@section('title', 'Teacher Dashboard')

@section('breadcrumb')
    <span class="font-semibold text-gray-900">Teacher Dashboard</span>
@endsection

@section('content')
<div class="space-y-6">
    <!-- Welcome Banner -->
    <div class="bg-gradient-to-r from-emerald-600 to-emerald-800 rounded-xl p-8 text-white">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold mb-2">Welcome, {{ auth()->user()->profile->first_name ?? 'Teacher' }}</h1>
                <p class="text-emerald-100">Your classes and student performance overview</p>
            </div>
            <div class="hidden md:block">
                <i class="fas fa-chalkboard-user text-6xl opacity-20"></i>
            </div>
        </div>
    </div>

    <!-- KPIs Section -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        @foreach($data['kpis'] as $kpi)
        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-{{ $kpi['color'] }}-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm font-medium">{{ $kpi['label'] }}</p>
                    <p class="text-2xl font-bold text-gray-900 mt-2">{{ $kpi['value'] }}</p>
                    <p class="text-xs text-gray-500 mt-1">
                        <span class="text-{{ $kpi['color'] }}-600">{{ $kpi['change'] }}</span>
                    </p>
                </div>
                <div class="w-12 h-12 bg-{{ $kpi['color'] }}-100 rounded-full flex items-center justify-center">
                    <i class="{{ $kpi['icon'] }} text-{{ $kpi['color'] }}-600 text-lg"></i>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Charts Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Class Performance -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">
                <i class="fas fa-chart-line mr-2 text-emerald-600"></i>Class Performance Trend
            </h3>
            <div style="position: relative; height: 300px;">
                <canvas id="performanceChart"></canvas>
            </div>
        </div>

        <!-- Student Attendance -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">
                <i class="fas fa-users-check mr-2 text-emerald-600"></i>Student Attendance by Class
            </h3>
            <div style="position: relative; height: 300px;">
                <canvas id="attendanceChart"></canvas>
            </div>
        </div>

        <!-- Results Distribution -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">
                <i class="fas fa-graduation-cap mr-2 text-emerald-600"></i>Results Distribution
            </h3>
            <div style="position: relative; height: 300px;">
                <canvas id="resultsChart"></canvas>
            </div>
        </div>

        <!-- Class Enrollment -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">
                <i class="fas fa-chair mr-2 text-emerald-600"></i>Class Enrollment vs Capacity
            </h3>
            <div style="position: relative; height: 300px;">
                <canvas id="enrollmentChart"></canvas>
            </div>
        </div>

        <!-- Grade Distribution -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">
                <i class="fas fa-chart-bar mr-2 text-emerald-600"></i>Grade Distribution
            </h3>
            <div style="position: relative; height: 300px;">
                <canvas id="gradeChart"></canvas>
            </div>
        </div>

        <!-- Weekly Lesson Schedule -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">
                <i class="fas fa-calendar-week mr-2 text-emerald-600"></i>Weekly Lesson Schedule
            </h3>
            <div style="position: relative; height: 300px;">
                <canvas id="weeklyChart"></canvas>
            </div>
        </div>

        <!-- Subject-wise Performance -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">
                <i class="fas fa-book mr-2 text-emerald-600"></i>Subject Performance
            </h3>
            <div style="position: relative; height: 300px;">
                <canvas id="subjectChart"></canvas>
            </div>
        </div>

        <!-- Attendance Trend -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">
                <i class="fas fa-chart-line mr-2 text-emerald-600"></i>Attendance Trend
            </h3>
            <div style="position: relative; height: 300px;">
                <canvas id="attendanceTrendChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Upcoming Lessons & Student Progress -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Upcoming Lessons -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">
                <i class="fas fa-calendar-alt mr-2 text-emerald-600"></i>Upcoming Lessons
            </h3>
            <div class="space-y-3 max-h-96 overflow-y-auto">
                @forelse($data['upcomingLessons'] as $lesson)
                <div class="border-l-4 border-emerald-500 bg-emerald-50 p-4 rounded">
                    <div class="flex items-start justify-between">
                        <div>
                            <h4 class="font-semibold text-gray-900">{{ $lesson['subject'] }}</h4>
                            <p class="text-sm text-gray-600">{{ $lesson['class'] }}</p>
                            <p class="text-xs text-gray-500 mt-1">
                                <i class="fas fa-clock mr-1"></i>{{ $lesson['time'] }}
                            </p>
                        </div>
                        <span class="inline-block px-3 py-1 rounded-full text-xs font-semibold
                            @if(($lesson['status'] ?? 'upcoming') === 'upcoming')
                                bg-blue-100 text-blue-800
                            @elseif(($lesson['status'] ?? 'upcoming') === 'today')
                                bg-yellow-100 text-yellow-800
                            @else
                                bg-gray-100 text-gray-800
                            @endif
                        ">
                            {{ ucfirst($lesson['status'] ?? 'upcoming') }}
                        </span>
                    </div>
                </div>
                @empty
                <div class="text-center py-8 text-gray-500">
                    <i class="fas fa-inbox text-3xl mb-2"></i>
                    <p>No upcoming lessons</p>
                </div>
                @endforelse
            </div>
        </div>

        <!-- Student Progress -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">
                <i class="fas fa-star mr-2 text-emerald-600"></i>Top Performing Students
            </h3>
            <div class="space-y-3 max-h-96 overflow-y-auto">
                @forelse($data['studentProgress'] as $student)
                <div class="flex items-center justify-between border-b pb-3">
                    <div class="flex-1">
                        <p class="font-medium text-gray-900">{{ $student['name'] }}</p>
                        <p class="text-sm text-gray-600">{{ $student['class'] }}</p>
                    </div>
                    <div class="text-right">
                        <div class="flex items-center gap-2">
                            <div class="w-32 h-2 bg-gray-200 rounded-full overflow-hidden">
                                <div class="h-full bg-emerald-500" style="width: {{ $student['avgScore'] }}%"></div>
                            </div>
                            <span class="font-semibold text-gray-900 min-w-12">{{ $student['avgScore'] }}%</span>
                        </div>
                    </div>
                </div>
                @empty
                <div class="text-center py-8 text-gray-500">
                    <i class="fas fa-inbox text-3xl mb-2"></i>
                    <p>No student progress data available</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Store charts globally for cleanup
    window.dashboardCharts = window.dashboardCharts || {};

    function initializeTeacherDashboardCharts() {
        // Destroy existing charts
        Object.values(window.dashboardCharts).forEach(chart => {
            if (chart && typeof chart.destroy === 'function') {
                chart.destroy();
            }
        });
        window.dashboardCharts = {};

        // Class Performance Chart
        const performanceCtx = document.getElementById('performanceChart');
        if (performanceCtx) {
            window.dashboardCharts.performance = new Chart(performanceCtx.getContext('2d'), {
                type: 'line',
                data: @json($data['classPerformance']),
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: true, position: 'top' }
                    },
                    scales: {
                        y: { beginAtZero: true, max: 100 }
                    }
                }
            });
        }

        // Attendance Chart
        const attendanceCtx = document.getElementById('attendanceChart');
        if (attendanceCtx) {
            window.dashboardCharts.attendance = new Chart(attendanceCtx.getContext('2d'), {
                type: 'bar',
                data: @json($data['studentAttendance']),
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false }
                    },
                    scales: {
                        y: { beginAtZero: true, max: 100 }
                    }
                }
            });
        }

        // Results Distribution Chart
        const resultsCtx = document.getElementById('resultsChart');
        if (resultsCtx) {
            window.dashboardCharts.results = new Chart(resultsCtx.getContext('2d'), {
                type: 'doughnut',
                data: @json($data['resultDistribution']),
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: true, position: 'bottom' }
                    }
                }
            });
        }

        // Enrollment Chart
        const enrollmentCtx = document.getElementById('enrollmentChart');
        if (enrollmentCtx) {
            window.dashboardCharts.enrollment = new Chart(enrollmentCtx.getContext('2d'), {
                type: 'bar',
                data: @json($data['classEnrollment']),
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: true, position: 'top' }
                    },
                    scales: {
                        y: { beginAtZero: true }
                    }
                }
            });
        }

        // Grade Distribution Chart
        @if(isset($data['gradeDistribution']))
        const gradeCtx = document.getElementById('gradeChart');
        if (gradeCtx) {
            window.dashboardCharts.grade = new Chart(gradeCtx.getContext('2d'), {
                type: 'bar',
                data: @json($data['gradeDistribution']),
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false }
                    },
                    scales: {
                        y: { beginAtZero: true }
                    }
                }
            });
        }
        @endif

        // Weekly Lesson Schedule Chart
        @if(isset($data['weeklySchedule']))
        const weeklyCtx = document.getElementById('weeklyChart');
        if (weeklyCtx) {
            window.dashboardCharts.weekly = new Chart(weeklyCtx.getContext('2d'), {
                type: 'bar',
                data: @json($data['weeklySchedule']),
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false }
                    },
                    scales: {
                        y: { beginAtZero: true }
                    }
                }
            });
        }
        @endif

        // Subject-wise Performance Chart
        @if(isset($data['subjectPerformance']))
        const subjectCtx = document.getElementById('subjectChart');
        if (subjectCtx) {
            window.dashboardCharts.subject = new Chart(subjectCtx.getContext('2d'), {
                type: 'line',
                data: @json($data['subjectPerformance']),
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: true, position: 'top' }
                    },
                    scales: {
                        y: { beginAtZero: true, max: 100 }
                    }
                }
            });
        }
        @endif

        // Attendance Trend Chart
        @if(isset($data['attendanceTrend']))
        const attendanceTrendCtx = document.getElementById('attendanceTrendChart');
        if (attendanceTrendCtx) {
            window.dashboardCharts.attendanceTrend = new Chart(attendanceTrendCtx.getContext('2d'), {
                type: 'line',
                data: @json($data['attendanceTrend']),
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: true, position: 'top' }
                    },
                    scales: {
                        y: { beginAtZero: true, max: 100 }
                    }
                }
            });
        }
        @endif
    }

    // Initialize on page load
    document.addEventListener('DOMContentLoaded', initializeTeacherDashboardCharts);
    
    // Re-initialize when navigating via SPA
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initializeTeacherDashboardCharts);
    } else {
        initializeTeacherDashboardCharts();
    }
</script>
@endpush
