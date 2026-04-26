@extends('layouts.spa')

@section('title', 'Dashboard')

@section('breadcrumb')
    <span class="font-semibold text-gray-900">{{ $data['userRole'] }} Dashboard</span>
@endsection

@section('content')
<div class="space-y-6">
    <!-- Welcome Banner -->
    <div class="bg-gradient-to-r from-indigo-600 to-indigo-800 rounded-xl p-8 text-white">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold mb-2">Welcome, {{ auth()->user()->profile->first_name ?? 'User' }}</h1>
                <p class="text-indigo-100">{{ $data['userRole'] }} - Personalized dashboard</p>
            </div>
            <div class="hidden md:block">
                <i class="fas fa-tachometer-alt text-6xl opacity-20"></i>
            </div>
        </div>
    </div>

    <!-- KPIs Section - Permission-Based -->
    @if(!empty($data['kpis']))
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        @foreach($data['kpis'] as $kpi)
        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-{{ $kpi['color'] }}-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm font-medium">{{ $kpi['label'] }}</p>
                    <p class="text-2xl font-bold text-gray-900 mt-2">{{ $kpi['value'] }}</p>
                    <p class="text-xs text-gray-500 mt-1">{{ $kpi['description'] }}</p>
                </div>
                <div class="w-12 h-12 bg-{{ $kpi['color'] }}-100 rounded-full flex items-center justify-center">
                    <i class="{{ $kpi['icon'] }} text-{{ $kpi['color'] }}-600 text-lg"></i>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @else
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
        <p class="text-blue-800">
            <i class="fas fa-info-circle mr-2"></i>You don't have access to any system KPIs based on your current role.
        </p>
    </div>
    @endif

    <!-- System Overview Chart -->
    @if(!empty($data['systemOverview']))
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">
                <i class="fas fa-chart-pie mr-2 text-indigo-600"></i>System Overview
            </h3>
            <div style="position: relative; height: 300px;">
                <canvas id="overviewChart"></canvas>
            </div>
        </div>

        <!-- Role Information -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">
                <i class="fas fa-shield-alt mr-2 text-indigo-600"></i>Your Role & Permissions
            </h3>
            <div class="space-y-3">
                <div class="border-b pb-3">
                    <p class="text-sm text-gray-600">Current Role</p>
                    <p class="font-semibold text-gray-900">{{ $data['roleLabel'] }}</p>
                </div>
                <div class="border-b pb-3">
                    <p class="text-sm text-gray-600 mb-2">Accessible Modules</p>
                    <div class="flex flex-wrap gap-2">
                        @forelse($data['permissions'] ?? [] as $permission)
                        <span class="inline-block px-3 py-1 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                            {{ ucfirst(str_replace('-', ' ', $permission)) }}
                        </span>
                        @empty
                        <span class="text-gray-500">No specific permissions assigned</span>
                        @endforelse
                    </div>
                </div>
                <div>
                    <p class="text-sm text-gray-600 mb-2">Available Actions</p>
                    <ul class="text-sm text-gray-700 space-y-1">
                        @if(in_array('view-dashboard', $data['permissions'] ?? []))
                        <li><i class="fas fa-check-circle text-green-600 mr-2"></i>View Dashboard</li>
                        @endif
                        @if(in_array('view-reports', $data['permissions'] ?? []))
                        <li><i class="fas fa-check-circle text-green-600 mr-2"></i>View Reports</li>
                        @endif
                        @if(in_array('manage-users', $data['permissions'] ?? []))
                        <li><i class="fas fa-check-circle text-green-600 mr-2"></i>Manage Users</li>
                        @endif
                        @if(empty($data['permissions']))
                        <li class="text-gray-500"><i class="fas fa-lock mr-2"></i>No actions available</li>
                        @endif
                    </ul>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Accessible Data Section -->
    @if(!empty($data['accessibleData']))
    <div class="space-y-6">
        <h3 class="text-lg font-semibold text-gray-900">
            <i class="fas fa-database mr-2 text-indigo-600"></i>Accessible Data
        </h3>

        <!-- Permission-Based Charts -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Student Distribution Chart -->
            <div class="bg-white rounded-lg shadow p-6">
                <h4 class="text-base font-semibold text-gray-900 mb-4">
                    <i class="fas fa-users mr-2 text-indigo-600"></i>Student Distribution
                </h4>
                <div style="position: relative; height: 250px;">
                    <canvas id="studentDistChart"></canvas>
                </div>
            </div>

            <!-- Class Enrollment Chart -->
            <div class="bg-white rounded-lg shadow p-6">
                <h4 class="text-base font-semibold text-gray-900 mb-4">
                    <i class="fas fa-school mr-2 text-indigo-600"></i>Class Enrollment
                </h4>
                <div style="position: relative; height: 250px;">
                    <canvas id="classEnrollChart"></canvas>
                </div>
            </div>

            <!-- Attendance Summary Chart -->
            <div class="bg-white rounded-lg shadow p-6">
                <h4 class="text-base font-semibold text-gray-900 mb-4">
                    <i class="fas fa-check-circle mr-2 text-indigo-600"></i>Attendance Summary
                </h4>
                <div style="position: relative; height: 250px;">
                    <canvas id="attendanceSummaryChart"></canvas>
                </div>
            </div>

            <!-- Results Overview Chart -->
            <div class="bg-white rounded-lg shadow p-6">
                <h4 class="text-base font-semibold text-gray-900 mb-4">
                    <i class="fas fa-graduation-cap mr-2 text-indigo-600"></i>Results Overview
                </h4>
                <div style="position: relative; height: 250px;">
                    <canvas id="resultsOverviewChart"></canvas>
                </div>
            </div>

            <!-- Billing Overview Chart -->
            <div class="bg-white rounded-lg shadow p-6">
                <h4 class="text-base font-semibold text-gray-900 mb-4">
                    <i class="fas fa-file-invoice mr-2 text-indigo-600"></i>Billing Overview
                </h4>
                <div style="position: relative; height: 250px;">
                    <canvas id="billingChart"></canvas>
                </div>
            </div>

            <!-- User Roles Distribution Chart -->
            <div class="bg-white rounded-lg shadow p-6">
                <h4 class="text-base font-semibold text-gray-900 mb-4">
                    <i class="fas fa-users-cog mr-2 text-indigo-600"></i>System Users by Role
                </h4>
                <div style="position: relative; height: 250px;">
                    <canvas id="rolesChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Data Summary Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mt-6">
            @if(isset($data['accessibleData']['students']))
            <div class="bg-white rounded-lg shadow p-4 border-l-4 border-blue-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-sm">Total Students</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $data['accessibleData']['students']['total'] ?? 0 }}</p>
                        <p class="text-xs text-gray-500 mt-1">{{ $data['accessibleData']['students']['active'] ?? 0 }} active</p>
                    </div>
                    <i class="fas fa-user-graduate text-3xl text-blue-200"></i>
                </div>
            </div>
            @endif

            @if(isset($data['accessibleData']['teachers']))
            <div class="bg-white rounded-lg shadow p-4 border-l-4 border-green-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-sm">Total Teachers</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $data['accessibleData']['teachers']['total'] ?? 0 }}</p>
                        <p class="text-xs text-gray-500 mt-1">{{ $data['accessibleData']['teachers']['active'] ?? 0 }} active</p>
                    </div>
                    <i class="fas fa-chalkboard-user text-3xl text-green-200"></i>
                </div>
            </div>
            @endif

            @if(isset($data['accessibleData']['classes']))
            <div class="bg-white rounded-lg shadow p-4 border-l-4 border-purple-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-sm">Total Classes</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $data['accessibleData']['classes']['total'] ?? 0 }}</p>
                        <p class="text-xs text-gray-500 mt-1">{{ $data['accessibleData']['classes']['active'] ?? 0 }} active</p>
                    </div>
                    <i class="fas fa-school text-3xl text-purple-200"></i>
                </div>
            </div>
            @endif
        </div>
    </div>
    @else
    <div class="bg-amber-50 border border-amber-200 rounded-lg p-6">
        <div class="text-center">
            <i class="fas fa-lock text-3xl text-amber-600 mb-3"></i>
            <h3 class="font-semibold text-amber-900 mb-2">Limited Access</h3>
            <p class="text-amber-800">
                Your role doesn't have access to view specific data sections. Please contact an administrator for more information.
            </p>
        </div>
    </div>
    @endif

    <!-- Help & Support Section -->
    <div class="bg-gradient-to-r from-gray-50 to-gray-100 rounded-lg p-6 border border-gray-200">
        <div class="flex items-start gap-4">
            <div class="w-12 h-12 bg-indigo-100 rounded-full flex items-center justify-center flex-shrink-0">
                <i class="fas fa-question-circle text-indigo-600 text-lg"></i>
            </div>
            <div class="flex-1">
                <h3 class="font-semibold text-gray-900 mb-2">Need Help?</h3>
                <p class="text-gray-600 text-sm mb-4">
                    If you need access to additional modules or have questions about your role and permissions, please contact the system administrator.
                </p>
                <a href="mailto:{{ config('app.admin_email', 'admin@example.com') }}" class="text-indigo-600 hover:text-indigo-800 text-sm font-medium">
                    <i class="fas fa-envelope mr-2"></i>Contact Administrator
                </a>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    @if(!empty($data['systemOverview']))
    // System Overview Chart
    const overviewCtx = document.getElementById('overviewChart').getContext('2d');
    new Chart(overviewCtx, {
        type: 'doughnut',
        data: @json($data['systemOverview']),
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: true, position: 'bottom' }
            }
        }
    });
    @endif

    // Student Distribution Chart
    @if(isset($data['studentDistribution']))
    const studentDistCtx = document.getElementById('studentDistChart').getContext('2d');
    new Chart(studentDistCtx, {
        type: 'doughnut',
        data: @json($data['studentDistribution']),
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: true, position: 'bottom' }
            }
        }
    });
    @endif

    // Class Enrollment Chart
    @if(isset($data['classEnrollment']))
    const classEnrollCtx = document.getElementById('classEnrollChart').getContext('2d');
    new Chart(classEnrollCtx, {
        type: 'bar',
        data: @json($data['classEnrollment']),
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
    @endif

    // Attendance Summary Chart
    @if(isset($data['attendanceSummary']))
    const attendanceSummaryCtx = document.getElementById('attendanceSummaryChart').getContext('2d');
    new Chart(attendanceSummaryCtx, {
        type: 'doughnut',
        data: @json($data['attendanceSummary']),
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: true, position: 'bottom' }
            }
        }
    });
    @endif

    // Results Overview Chart
    @if(isset($data['resultsOverview']))
    const resultsOverviewCtx = document.getElementById('resultsOverviewChart').getContext('2d');
    new Chart(resultsOverviewCtx, {
        type: 'bar',
        data: @json($data['resultsOverview']),
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
    @endif

    // Billing Overview Chart
    @if(isset($data['billingOverview']))
    const billingCtx = document.getElementById('billingChart').getContext('2d');
    new Chart(billingCtx, {
        type: 'doughnut',
        data: @json($data['billingOverview']),
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: true, position: 'bottom' }
            }
        }
    });
    @endif

    // User Roles Distribution Chart
    @if(isset($data['userRolesDistribution']))
    const rolesCtx = document.getElementById('rolesChart').getContext('2d');
    new Chart(rolesCtx, {
        type: 'doughnut',
        data: @json($data['userRolesDistribution']),
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: true, position: 'bottom' }
            }
        }
    });
    @endif
