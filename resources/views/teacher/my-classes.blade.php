@extends('layouts.spa')

@section('title', 'My Classes')

@section('breadcrumb')
    <span class="text-gray-400">Teaching</span>
    <span class="text-gray-400">/</span>
    <span class="font-semibold text-gray-900">My Classes</span>
@endsection

@section('content')
<div class="space-y-8">
    <!-- Hero Header Section -->
    <div class="bg-gradient-to-r from-purple-50 to-indigo-50 rounded-xl shadow-sm border border-purple-100 p-8">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-6">
            <div>
                <h1 class="text-4xl font-bold text-gray-900 mb-2">My Classes</h1>
                <p class="text-gray-600 text-lg flex items-center gap-2">
                    <i class="fas fa-door-open text-purple-600"></i>
                    Select a class to manage students, attendance, and timetable
                </p>
            </div>
            <div class="flex items-center gap-3">
                <div class="text-right">
                    <p class="text-3xl font-bold text-purple-600">{{ $classes->count() }}</p>
                    <p class="text-gray-600 text-sm">Assigned Classes</p>
                </div>
                <div class="w-16 h-16 bg-gradient-to-br from-purple-100 to-purple-50 rounded-full flex items-center justify-center shadow-sm">
                    <i class="fas fa-layer-group text-purple-600 text-3xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- No Classes Message -->
    @if($classes->isEmpty())
    <div class="bg-yellow-50 border-l-4 border-yellow-500 p-6 rounded-lg">
        <div class="flex gap-4">
            <div class="flex-shrink-0 text-2xl text-yellow-600">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            <div>
                <h3 class="font-semibold text-yellow-900">No Classes Assigned</h3>
                <p class="text-yellow-800 mt-1">You are not assigned to any class yet. Contact your administrator to assign you to a class.</p>
            </div>
        </div>
    </div>
    @else
        <!-- Classes Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($classes as $class)
            <div class="group card shadow-md hover:shadow-xl transition-all duration-300 border border-gray-200 overflow-hidden">
                <!-- Class Header -->
                <div class="bg-gradient-to-r from-indigo-500 to-blue-500 p-6 text-white">
                    <div class="flex items-start justify-between mb-4">
                        <div>
                            <h2 class="text-2xl font-bold">{{ $class->name }}</h2>
                            @if($class->section)
                            <p class="text-indigo-100 text-sm mt-1">Section {{ $class->section }}</p>
                            @endif
                        </div>
                        <div class="w-12 h-12 bg-white/20 rounded-lg flex items-center justify-center">
                            <i class="fas fa-book text-lg"></i>
                        </div>
                    </div>
                    @if($class->teacher)
                    <p class="text-indigo-100 text-sm flex items-center gap-2">
                        <i class="fas fa-user-tie"></i>
                        {{ $class->teacher->full_name }}
                    </p>
                    @endif
                </div>

                <!-- Class Stats -->
                <div class="card-body">
                    <div class="grid grid-cols-3 gap-4 mb-6">
                        <!-- Total Students -->
                        <div class="text-center">
                            <p class="text-3xl font-bold text-gray-900">{{ $class->students()->count() }}</p>
                            <p class="text-xs text-gray-600 mt-1">
                                <i class="fas fa-users"></i>
                                Students
                            </p>
                        </div>

                        <!-- Male -->
                        <div class="text-center">
                            <p class="text-3xl font-bold text-blue-600">{{ $class->students()->where('gender', 'male')->count() }}</p>
                            <p class="text-xs text-gray-600 mt-1">
                                <i class="fas fa-mars"></i>
                                Male
                            </p>
                        </div>

                        <!-- Female -->
                        <div class="text-center">
                            <p class="text-3xl font-bold text-pink-600">{{ $class->students()->where('gender', 'female')->count() }}</p>
                            <p class="text-xs text-gray-600 mt-1">
                                <i class="fas fa-venus"></i>
                                Female
                            </p>
                        </div>
                    </div>

                    <!-- Quick Action Buttons -->
                    <div class="space-y-3">
                        <!-- View Students -->
                        <a href="{{ route('teacher.class.students', ['class' => $class->id]) }}"
                           class="flex items-center gap-3 w-full px-4 py-3 bg-blue-50 text-blue-700 rounded-lg hover:bg-blue-100 transition font-semibold border border-blue-200 group/btn">
                            <i class="fas fa-list text-lg w-5"></i>
                            <span>View Students</span>
                            <i class="fas fa-arrow-right text-sm ml-auto opacity-0 group-hover/btn:opacity-100 transition"></i>
                        </a>

                        <!-- Mark Attendance -->
                        <a href="{{ route('teacher.mark-attendance', ['class' => $class->id]) }}"
                           class="flex items-center gap-3 w-full px-4 py-3 bg-green-50 text-green-700 rounded-lg hover:bg-green-100 transition font-semibold border border-green-200 group/btn">
                            <i class="fas fa-check-square text-lg w-5"></i>
                            <span>Mark Attendance</span>
                            <i class="fas fa-arrow-right text-sm ml-auto opacity-0 group-hover/btn:opacity-100 transition"></i>
                        </a>

                        <!-- View Timetable -->
                        <a href="{{ route('teacher.classes-timetable') }}?class={{ $class->id }}"
                           class="flex items-center gap-3 w-full px-4 py-3 bg-purple-50 text-purple-700 rounded-lg hover:bg-purple-100 transition font-semibold border border-purple-200 group/btn">
                            <i class="fas fa-calendar-alt text-lg w-5"></i>
                            <span>View Timetable</span>
                            <i class="fas fa-arrow-right text-sm ml-auto opacity-0 group-hover/btn:opacity-100 transition"></i>
                        </a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <!-- Additional Actions -->
        @if($classes->count() > 0)
        <div class="card shadow-sm border border-gray-200">
            <div class="card-header bg-gradient-to-r from-gray-50 to-gray-25 border-b border-gray-100">
                <h3 class="text-lg font-semibold text-gray-900">
                    <i class="fas fa-star text-amber-600 mr-2"></i>
                    Quick Access
                </h3>
            </div>
            <div class="card-body">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <!-- My Personal Timetable -->
                    <a href="{{ route('teacher.my-timetable') }}"
                       class="flex items-center gap-4 p-4 bg-gradient-to-br from-purple-50 to-pink-50 rounded-lg hover:shadow-md transition border border-purple-200 group/quick">
                        <div class="w-12 h-12 bg-gradient-to-br from-purple-400 to-pink-400 rounded-lg flex items-center justify-center shadow-sm">
                            <i class="fas fa-clock text-white text-lg"></i>
                        </div>
                        <div class="flex-1">
                            <p class="font-semibold text-gray-900">My Timetable</p>
                            <p class="text-xs text-gray-600">View your complete schedule</p>
                        </div>
                        <i class="fas fa-arrow-right text-gray-400 group-hover/quick:text-gray-600 transition"></i>
                    </a>

                    <!-- All Classes Timetable -->
                    <a href="{{ route('teacher.classes-timetable') }}"
                       class="flex items-center gap-4 p-4 bg-gradient-to-br from-indigo-50 to-blue-50 rounded-lg hover:shadow-md transition border border-indigo-200 group/quick">
                        <div class="w-12 h-12 bg-gradient-to-br from-indigo-400 to-blue-400 rounded-lg flex items-center justify-center shadow-sm">
                            <i class="fas fa-door-open text-white text-lg"></i>
                        </div>
                        <div class="flex-1">
                            <p class="font-semibold text-gray-900">Classes Timetable</p>
                            <p class="text-xs text-gray-600">View all classes schedule</p>
                        </div>
                        <i class="fas fa-arrow-right text-gray-400 group-hover/quick:text-gray-600 transition"></i>
                    </a>

                    <!-- Class Attendance Records -->
                    <a href="{{ route('teacher.class.attendance') }}"
                       class="flex items-center gap-4 p-4 bg-gradient-to-br from-green-50 to-emerald-50 rounded-lg hover:shadow-md transition border border-green-200 group/quick">
                        <div class="w-12 h-12 bg-gradient-to-br from-green-400 to-emerald-400 rounded-lg flex items-center justify-center shadow-sm">
                            <i class="fas fa-history text-white text-lg"></i>
                        </div>
                        <div class="flex-1">
                            <p class="font-semibold text-gray-900">Attendance Records</p>
                            <p class="text-xs text-gray-600">View attendance history</p>
                        </div>
                        <i class="fas fa-arrow-right text-gray-400 group-hover/quick:text-gray-600 transition"></i>
                    </a>
                </div>
            </div>
        </div>
        @endif
    @endif
</div>

@push('scripts')
<script>
    // Add any Alpine.js logic if needed
</script>
@endpush

@endsection
