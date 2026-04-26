@extends('layouts.spa')

@section('title', 'Dashboard')

@section('breadcrumb')
    <span class="font-semibold text-gray-900">Dashboard</span>
@endsection

@section('content')
<div class="space-y-6">
    <!-- Welcome Banner -->
    <div class="bg-gradient-to-r from-primary-600 to-primary-800 rounded-xl p-8 text-white">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold mb-2">Welcome back, {{ $userName }}!</h1>
                <p class="text-primary-100">{{ $userRole }} Dashboard - Here's your analysis and KPIs</p>
            </div>
            <div class="hidden md:block">
                <i class="fas fa-chart-line text-6xl opacity-20"></i>
            </div>
        </div>
    </div>

    <!-- Admin Dashboard -->
    @if($user->hasRole('Administrator') && isset($dashboardData['admin']))
        @include('dashboard.sections.admin', ['data' => $dashboardData['admin']])
    @endif

    <!-- Teacher Dashboard -->
    @if($user->hasRole('teacher') && isset($dashboardData['teacher']))
        @include('dashboard.sections.teacher', ['data' => $dashboardData['teacher']])
    @endif
</div>

@push('scripts')
<script>
    document.addEventListener('alpine:initialized', () => {
        console.log('Dashboard initialized for role: {{ $userRole }}');
    });
</script>
@endpush
            <div class="card-header">
                <h2 class="text-lg font-semibold text-gray-900">Quick Actions</h2>
            </div>
            <div class="card-body space-y-3">
                <a href="{{ route('students.create') }}"
                   class="flex items-center space-x-3 p-3 rounded-lg border-2 border-gray-200 hover:border-primary-500 hover:bg-primary-50 transition-all group">
                    <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center group-hover:bg-blue-500 transition-colors">
                        <i class="fas fa-user-plus text-blue-600 group-hover:text-white"></i>
                    </div>
                    <div>
                        <p class="font-medium text-gray-900">Add Student</p>
                        <p class="text-xs text-gray-500">Register new student</p>
                    </div>
                </a>

                <a href="{{ route('teachers.create') }}"
                   class="flex items-center space-x-3 p-3 rounded-lg border-2 border-gray-200 hover:border-green-500 hover:bg-green-50 transition-all group">
                    <div class="w-10 h-10 rounded-full bg-green-100 flex items-center justify-center group-hover:bg-green-500 transition-colors">
                        <i class="fas fa-chalkboard-teacher text-green-600 group-hover:text-white"></i>
                    </div>
                    <div>
                        <p class="font-medium text-gray-900">Add Teacher</p>
                        <p class="text-xs text-gray-500">Register new teacher</p>
                    </div>
                </a>

                <a href="{{ route('attendance.index') }}"
                   class="flex items-center space-x-3 p-3 rounded-lg border-2 border-gray-200 hover:border-orange-500 hover:bg-orange-50 transition-all group">
                    <div class="w-10 h-10 rounded-full bg-orange-100 flex items-center justify-center group-hover:bg-orange-500 transition-colors">
                        <i class="fas fa-clipboard-check text-orange-600 group-hover:text-white"></i>
                    </div>
                    <div>
                        <p class="font-medium text-gray-900">Mark Attendance</p>
                        <p class="text-xs text-gray-500">Take today's attendance</p>
                    </div>
                </a>

                <a href="{{ route('grades.index') }}"
                   class="flex items-center space-x-3 p-3 rounded-lg border-2 border-gray-200 hover:border-purple-500 hover:bg-purple-50 transition-all group">
                    <div class="w-10 h-10 rounded-full bg-purple-100 flex items-center justify-center group-hover:bg-purple-500 transition-colors">
                        <i class="fas fa-chart-line text-purple-600 group-hover:text-white"></i>
                    </div>
                    <div>
                        <p class="font-medium text-gray-900">Enter Grades</p>
                        <p class="text-xs text-gray-500">Record exam results</p>
                    </div>
                </a>

                <form action="{{ route('payments.send-payment-reminders') }}" method="POST">
                    @csrf
                    <button type="submit"
                       class="w-full flex items-center space-x-3 p-3 rounded-lg border-2 border-gray-200 hover:border-blue-500 hover:bg-blue-50 transition-all group">
                        <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center group-hover:bg-blue-500 transition-colors">
                            <i class="fas fa-envelope text-blue-600 group-hover:text-white"></i>
                        </div>
                        <div class="text-left">
                            <p class="font-medium text-gray-900">Send Reminders</p>
                            <p class="text-xs text-gray-500">Email payment reminders</p>
                        </div>
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Recent Activity & Upcoming Events -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Recent Activity -->
        <div class="card">
            <div class="card-header">
                <h2 class="text-lg font-semibold text-gray-900">Recent Activity</h2>
                <a href="#" class="text-sm text-primary-600 hover:text-primary-700">View All</a>
            </div>
            <div class="card-body">
                <div class="space-y-4">
                    <!-- Activity Item -->
                    <div class="flex items-start space-x-3">
                        <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-user-plus text-blue-600"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm text-gray-900">
                                <span class="font-medium">John Doe</span> was registered in Class 10-A
                            </p>
                            <p class="text-xs text-gray-500 mt-1">2 hours ago</p>
                        </div>
                    </div>

                    <!-- Activity Item -->
                    <div class="flex items-start space-x-3">
                        <div class="w-10 h-10 rounded-full bg-green-100 flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-clipboard-check text-green-600"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm text-gray-900">
                                Attendance marked for <span class="font-medium">Class 9-B</span>
                            </p>
                            <p class="text-xs text-gray-500 mt-1">4 hours ago</p>
                        </div>
                    </div>

                    <!-- Activity Item -->
                    <div class="flex items-start space-x-3">
                        <div class="w-10 h-10 rounded-full bg-purple-100 flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-file-alt text-purple-600"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm text-gray-900">
                                <span class="font-medium">Math Exam</span> results uploaded for Class 11
                            </p>
                            <p class="text-xs text-gray-500 mt-1">6 hours ago</p>
                        </div>
                    </div>

                    <!-- Activity Item -->
                    <div class="flex items-start space-x-3">
                        <div class="w-10 h-10 rounded-full bg-orange-100 flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-chalkboard-teacher text-orange-600"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm text-gray-900">
                                <span class="font-medium">Mrs. Sarah Wilson</span> joined as Science teacher
                            </p>
                            <p class="text-xs text-gray-500 mt-1">Yesterday</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Upcoming Events -->
        <div class="card">
            <div class="card-header">
                <h2 class="text-lg font-semibold text-gray-900">Upcoming Events</h2>
                <a href="#" class="text-sm text-primary-600 hover:text-primary-700">View Calendar</a>
            </div>
            <div class="card-body">
                <div class="space-y-4">
                    <!-- Event Item -->
                    <div class="flex items-start space-x-4 p-3 bg-blue-50 rounded-lg border border-blue-200">
                        <div class="text-center flex-shrink-0">
                            <div class="w-12 h-12 bg-blue-600 text-white rounded-lg flex flex-col items-center justify-center">
                                <span class="text-xs font-medium">JAN</span>
                                <span class="text-lg font-bold">25</span>
                            </div>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="font-medium text-gray-900">Parent-Teacher Meeting</p>
                            <p class="text-sm text-gray-600 mt-1">9:00 AM - 3:00 PM</p>
                            <span class="badge badge-primary mt-2">School Event</span>
                        </div>
                    </div>

                    <!-- Event Item -->
                    <div class="flex items-start space-x-4 p-3 bg-green-50 rounded-lg border border-green-200">
                        <div class="text-center flex-shrink-0">
                            <div class="w-12 h-12 bg-green-600 text-white rounded-lg flex flex-col items-center justify-center">
                                <span class="text-xs font-medium">JAN</span>
                                <span class="text-lg font-bold">28</span>
                            </div>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="font-medium text-gray-900">Mid-Term Examinations Begin</p>
                            <p class="text-sm text-gray-600 mt-1">All Classes</p>
                            <span class="badge badge-success mt-2">Examination</span>
                        </div>
                    </div>

                    <!-- Event Item -->
                    <div class="flex items-start space-x-4 p-3 bg-purple-50 rounded-lg border border-purple-200">
                        <div class="text-center flex-shrink-0">
                            <div class="w-12 h-12 bg-purple-600 text-white rounded-lg flex flex-col items-center justify-center">
                                <span class="text-xs font-medium">FEB</span>
                                <span class="text-lg font-bold">05</span>
                            </div>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="font-medium text-gray-900">Science Fair</p>
                            <p class="text-sm text-gray-600 mt-1">10:00 AM - 4:00 PM</p>
                            <span class="badge badge-info mt-2">Competition</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Performance Overview -->
    <div class="card">
        <div class="card-header">
            <h2 class="text-lg font-semibold text-gray-900">Class Performance Overview</h2>
            <select class="form-select text-sm py-1">
                <option>All Classes</option>
                <option>Class 10</option>
                <option>Class 11</option>
                <option>Class 12</option>
            </select>
        </div>
        <div class="card-body">
            <div class="overflow-x-auto">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Class</th>
                            <th>Total Students</th>
                            <th>Average Grade</th>
                            <th>Attendance Rate</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="font-medium">Class 10-A</td>
                            <td>42</td>
                            <td>
                                <div class="flex items-center">
                                    <span class="font-medium text-green-600">85.5%</span>
                                    <div class="ml-2 flex-1 progress">
                                        <div class="progress-bar bg-green-600" style="width: 85.5%"></div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge badge-success">94.2%</span>
                            </td>
                            <td>
                                <span class="badge badge-success">Excellent</span>
                            </td>
                            <td>
                                <button class="text-primary-600 hover:text-primary-700 text-sm font-medium">
                                    View Details
                                </button>
                            </td>
                        </tr>
                        <tr>
                            <td class="font-medium">Class 10-B</td>
                            <td>38</td>
                            <td>
                                <div class="flex items-center">
                                    <span class="font-medium text-blue-600">78.3%</span>
                                    <div class="ml-2 flex-1 progress">
                                        <div class="progress-bar" style="width: 78.3%"></div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge badge-success">91.5%</span>
                            </td>
                            <td>
                                <span class="badge badge-primary">Good</span>
                            </td>
                            <td>
                                <button class="text-primary-600 hover:text-primary-700 text-sm font-medium">
                                    View Details
                                </button>
                            </td>
                        </tr>
                        <tr>
                            <td class="font-medium">Class 11-A</td>
                            <td>35</td>
                            <td>
                                <div class="flex items-center">
                                    <span class="font-medium text-yellow-600">72.8%</span>
                                    <div class="ml-2 flex-1 progress">
                                        <div class="progress-bar bg-yellow-600" style="width: 72.8%"></div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge badge-warning">88.3%</span>
                            </td>
                            <td>
                                <span class="badge badge-warning">Average</span>
                            </td>
                            <td>
                                <button class="text-primary-600 hover:text-primary-700 text-sm font-medium">
                                    View Details
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Dashboard specific scripts
    console.log('Dashboard loaded successfully!');

    // Initialize tooltips for chart bars
    document.addEventListener('alpine:initialized', () => {
        console.log('Alpine.js initialized on dashboard');
    });
</script>
@endpush
@endsection
