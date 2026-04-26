@extends('layouts.spa')

@section('title', 'Admin Dashboard')

@section('breadcrumb')
    <span class="font-semibold text-gray-900">Admin Dashboard</span>
@endsection

@section('content')
<div class="space-y-6">
    <!-- Welcome Banner -->
    <div class="bg-gradient-to-r from-primary-600 to-primary-800 rounded-xl p-8 text-white">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold mb-2">Admin Dashboard</h1>
                <p class="text-primary-100">System overview and key performance indicators</p>
            </div>
            <div class="hidden md:block">
                <i class="fas fa-chart-line text-6xl opacity-20"></i>
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
                        <span class="text-{{ $kpi['color'] }}-600">{{ $kpi['percentage'] }}</span> {{ $kpi['trend'] }}
                    </p>
                </div>
                <div class="w-12 h-12 bg-{{ $kpi['color'] }}-100 rounded-full flex items-center justify-center">
                    <i class="{{ $kpi['icon'] }} text-{{ $kpi['color'] }}-600 text-lg"></i>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Quick Actions -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <a href="{{ route('students.create') }}" class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition">
            <div class="flex items-center">
                <div class="bg-blue-100 p-3 rounded-lg mr-4">
                    <i class="fas fa-user-plus text-blue-600 text-2xl"></i>
                </div>
                <div>
                    <h3 class="font-semibold text-gray-900">Add Student</h3>
                    <p class="text-gray-600 text-sm">Enroll new student</p>
                </div>
                <i class="fas fa-arrow-right ml-auto text-gray-400"></i>
            </div>
        </a>
        <a href="{{ route('payments.bills.index') }}" class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition">
            <div class="flex items-center">
                <div class="bg-green-100 p-3 rounded-lg mr-4">
                    <i class="fas fa-money-bill text-green-600 text-2xl"></i>
                </div>
                <div>
                    <h3 class="font-semibold text-gray-900">Record Payment</h3>
                    <p class="text-gray-600 text-sm">Add student payment</p>
                </div>
                <i class="fas fa-arrow-right ml-auto text-gray-400"></i>
            </div>
        </a>
        <a href="{{ route('calendar.index') }}" class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition">
            <div class="flex items-center">
                <div class="bg-purple-100 p-3 rounded-lg mr-4">
                    <i class="fas fa-calendar-alt text-purple-600 text-2xl"></i>
                </div>
                <div>
                    <h3 class="font-semibold text-gray-900">Create Event</h3>
                    <p class="text-gray-600 text-sm">Add to calendar</p>
                </div>
                <i class="fas fa-arrow-right ml-auto text-gray-400"></i>
            </div>
        </a>
        <a href="{{ route('students.export') }}" class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition">
            <div class="flex items-center">
                <div class="bg-orange-100 p-3 rounded-lg mr-4">
                    <i class="fas fa-file-csv text-orange-600 text-2xl"></i>
                </div>
                <div>
                    <h3 class="font-semibold text-gray-900">Generate Report</h3>
                    <p class="text-gray-600 text-sm">Export system report</p>
                </div>
                <i class="fas fa-arrow-right ml-auto text-gray-400"></i>
            </div>
        </a>
    </div>

    <!-- Charts Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Enrollment Trend -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">
                <i class="fas fa-chart-line mr-2 text-primary-600"></i>Enrollment Trend
            </h3>
            <div style="position: relative; height: 300px;">
                <canvas id="enrollmentChart"></canvas>
            </div>
        </div>

        <!-- Class Distribution -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">
                <i class="fas fa-pie-chart mr-2 text-primary-600"></i>Class Distribution
            </h3>
            <div style="position: relative; height: 300px;">
                <canvas id="classChart"></canvas>
            </div>
        </div>

        <!-- Attendance Overview -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">
                <i class="fas fa-check-circle mr-2 text-primary-600"></i>Attendance Overview
            </h3>
            <div style="position: relative; height: 300px;">
                <canvas id="attendanceChart"></canvas>
            </div>
        </div>

        <!-- Payment Status -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">
                <i class="fas fa-money-bill-wave mr-2 text-primary-600"></i>Payment Status
            </h3>
            <div style="position: relative; height: 300px;">
                <canvas id="paymentChart"></canvas>
            </div>
        </div>

        <!-- Results Summary -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">
                <i class="fas fa-graduation-cap mr-2 text-primary-600"></i>Results Summary
            </h3>
            <div style="position: relative; height: 300px;">
                <canvas id="resultsChart"></canvas>
            </div>
        </div>

        <!-- Staff Breakdown -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">
                <i class="fas fa-users mr-2 text-primary-600"></i>Staff Breakdown
            </h3>
            <div style="position: relative; height: 300px;">
                <canvas id="staffChart"></canvas>
            </div>
        </div>

        <!-- Monthly Revenue Trend -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">
                <i class="fas fa-coins mr-2 text-primary-600"></i>Monthly Revenue
            </h3>
            <div style="position: relative; height: 300px;">
                <canvas id="revenueChart"></canvas>
            </div>
        </div>

        <!-- Student Performance Distribution -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">
                <i class="fas fa-chart-bar mr-2 text-primary-600"></i>Student Performance
            </h3>
            <div style="position: relative; height: 300px;">
                <canvas id="performanceChart"></canvas>
            </div>
        </div>

        <!-- Bill Status Summary -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">
                <i class="fas fa-file-invoice mr-2 text-primary-600"></i>Bill Status
            </h3>
            <div style="position: relative; height: 300px;">
                <canvas id="billStatusChart"></canvas>
            </div>
        </div>

        <!-- Session-wise Enrollment -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">
                <i class="fas fa-chart-line mr-2 text-primary-600"></i>Session Enrollment
            </h3>
            <div style="position: relative; height: 300px;">
                <canvas id="sessionChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Recent Activities & Events Section -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Recent Enrollments -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">
                <i class="fas fa-user-plus mr-2 text-blue-600"></i>Recent Enrollments
            </h3>
            <div class="space-y-3 max-h-96 overflow-y-auto">
                @if(isset($data['recentActivities']['newStudents']) && count($data['recentActivities']['newStudents']) > 0)
                    @foreach($data['recentActivities']['newStudents'] as $student)
                    <div class="flex items-center justify-between border-b pb-3">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                                <i class="fas fa-user text-blue-600 text-sm"></i>
                            </div>
                            <div>
                                <p class="font-medium text-gray-900 text-sm">{{ $student['name'] ?? 'Student' }}</p>
                                <p class="text-xs text-gray-600">{{ $student['class'] ?? 'Class' }}</p>
                            </div>
                        </div>
                        <p class="text-xs text-gray-500">{{ $student['date'] ?? 'Today' }}</p>
                    </div>
                    @endforeach
                @else
                    <div class="text-center py-8">
                        <i class="fas fa-inbox text-gray-400 text-3xl mb-2"></i>
                        <p class="text-gray-500 text-sm">No recent enrollments</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Recent Payments -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">
                <i class="fas fa-receipt mr-2 text-green-600"></i>Recent Payments
            </h3>
            <div class="space-y-3 max-h-96 overflow-y-auto">
                @if(isset($data['recentActivities']['recentPayments']) && count($data['recentActivities']['recentPayments']) > 0)
                    @foreach($data['recentActivities']['recentPayments'] as $payment)
                    <div class="flex items-center justify-between border-b pb-3">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center">
                                <i class="fas fa-money-bill text-green-600 text-sm"></i>
                            </div>
                            <div>
                                <p class="font-medium text-gray-900 text-sm">{{ $payment['amount'] ?? '₦0.00' }}</p>
                                <p class="text-xs text-gray-600">{{ $payment['method'] ?? 'Payment' }}</p>
                            </div>
                        </div>
                        <p class="text-xs text-gray-500">{{ $payment['date'] ?? 'Today' }}</p>
                    </div>
                    @endforeach
                @else
                    <div class="text-center py-8">
                        <i class="fas fa-inbox text-gray-400 text-3xl mb-2"></i>
                        <p class="text-gray-500 text-sm">No recent payments</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Upcoming Events -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">
                <i class="fas fa-calendar-check mr-2 text-purple-600"></i>Upcoming Events
            </h3>
            <div class="space-y-3 max-h-96 overflow-y-auto">
                @if(isset($data['upcomingEvents']) && count($data['upcomingEvents']) > 0)
                    @foreach($data['upcomingEvents'] as $event)
                    <div class="flex items-start space-x-3 border-b pb-3">
                        <div class="w-2 h-2 bg-purple-600 rounded-full mt-2"></div>
                        <div class="flex-1">
                            <p class="font-medium text-gray-900 text-sm">{{ $event['name'] }}</p>
                            <div class="flex items-center justify-between mt-1">
                                <p class="text-xs text-gray-600">
                                    <i class="fas fa-clock text-gray-400 mr-1"></i>{{ $event['date'] }}
                                </p>
                                <span class="px-2 py-1 bg-purple-100 text-purple-700 text-xs rounded-full">
                                    {{ $event['type'] }}
                                </span>
                            </div>
                        </div>
                    </div>
                    @endforeach
                @else
                    <div class="text-center py-8">
                        <i class="fas fa-calendar text-gray-400 text-3xl mb-2"></i>
                        <p class="text-gray-500 text-sm">No upcoming events</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Store charts globally for cleanup
    window.dashboardCharts = window.dashboardCharts || {};

    function initializeAdminDashboardCharts() {
        // Destroy existing charts
        Object.values(window.dashboardCharts).forEach(chart => {
            if (chart && typeof chart.destroy === 'function') {
                chart.destroy();
            }
        });
        window.dashboardCharts = {};

        // Enrollment Trend Chart
        const enrollmentCtx = document.getElementById('enrollmentChart');
        if (enrollmentCtx) {
            window.dashboardCharts.enrollment = new Chart(enrollmentCtx.getContext('2d'), {
                type: 'line',
                data: @json($data['enrollmentTrend']),
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

        // Class Distribution Chart
        const classCtx = document.getElementById('classChart');
        if (classCtx) {
            window.dashboardCharts.class = new Chart(classCtx.getContext('2d'), {
                type: 'doughnut',
                data: @json($data['classDistribution']),
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: true, position: 'bottom' }
                    }
                }
            });
        }

        // Attendance Chart
        const attendanceCtx = document.getElementById('attendanceChart');
        if (attendanceCtx) {
            window.dashboardCharts.attendance = new Chart(attendanceCtx.getContext('2d'), {
                type: 'doughnut',
                data: @json($data['attendanceOverview']),
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: true, position: 'bottom' }
                    }
                }
            });
        }

        // Payment Status Chart
        const paymentCtx = document.getElementById('paymentChart');
        if (paymentCtx) {
            window.dashboardCharts.payment = new Chart(paymentCtx.getContext('2d'), {
                type: 'bar',
                data: @json($data['paymentStatus']),
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

        // Results Chart
        const resultsCtx = document.getElementById('resultsChart');
        if (resultsCtx) {
            window.dashboardCharts.results = new Chart(resultsCtx.getContext('2d'), {
                type: 'bar',
                data: @json($data['resultsSummary']),
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

        // Staff Chart
        const staffCtx = document.getElementById('staffChart');
        if (staffCtx) {
            window.dashboardCharts.staff = new Chart(staffCtx.getContext('2d'), {
                type: 'doughnut',
                data: @json($data['staffBreakdown']),
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: true, position: 'bottom' }
                    }
                }
            });
        }

        // Monthly Revenue Chart
        @if(isset($data['monthlyRevenue']))
        const revenueCtx = document.getElementById('revenueChart');
        if (revenueCtx) {
            window.dashboardCharts.revenue = new Chart(revenueCtx.getContext('2d'), {
                type: 'line',
                data: @json($data['monthlyRevenue']),
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
        @endif

        // Grades Chart
        @if(isset($data['gradesDistribution']))
        const gradesCtx = document.getElementById('gradesChart');
        if (gradesCtx) {
            window.dashboardCharts.grades = new Chart(gradesCtx.getContext('2d'), {
                type: 'bar',
                data: @json($data['gradesDistribution']),
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
        @endif

        // Performance Distribution Chart
        @if(isset($data['performanceDistribution']))
        const performanceCtx = document.getElementById('performanceChart');
        if (performanceCtx) {
            window.dashboardCharts.performance = new Chart(performanceCtx.getContext('2d'), {
                type: 'bar',
                data: @json($data['performanceDistribution']),
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

        // Bill Status Chart
        @if(isset($data['billStatus']))
        const billStatusCtx = document.getElementById('billStatusChart');
        if (billStatusCtx) {
            window.dashboardCharts.billStatus = new Chart(billStatusCtx.getContext('2d'), {
                type: 'doughnut',
                data: @json($data['billStatus']),
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: true, position: 'bottom' }
                    }
                }
            });
        }
        @endif

        // Session-wise Enrollment Chart
        @if(isset($data['sessionEnrollment']))
        const sessionCtx = document.getElementById('sessionChart');
        if (sessionCtx) {
            window.dashboardCharts.session = new Chart(sessionCtx.getContext('2d'), {
                type: 'line',
                data: @json($data['sessionEnrollment']),
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
        @endif
    }

    // Initialize on page load
    document.addEventListener('DOMContentLoaded', initializeAdminDashboardCharts);
    
    // Re-initialize when navigating via SPA
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initializeAdminDashboardCharts);
    } else {
        initializeAdminDashboardCharts();
    }
</script>
@endpush
@endsection