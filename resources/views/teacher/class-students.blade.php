@extends('layouts.spa')

@section('title', 'My Class Students')

@section('breadcrumb')
    <span class="text-gray-400">Teaching</span>
    <span class="text-gray-400">/</span>
    <span class="text-gray-400">
        <a href="{{ route('teacher.my-classes') }}" class="hover:text-gray-600">My Classes</a>
    </span>
    <span class="text-gray-400">/</span>
    <span class="font-semibold text-gray-900">{{ $selectedClass ? $selectedClass->name : 'Students' }}</span>
@endsection

@section('content')
<div class="space-y-8">
    <!-- Hero Header Section -->
    <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-xl shadow-sm border border-blue-100 p-8">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-6">
            <div>
                <h1 class="text-4xl font-bold text-gray-900 mb-2">
                    @if($selectedClass)
                        {{ $selectedClass->name }} - Students
                    @else
                        Class Students
                    @endif
                </h1>
                <p class="text-gray-600 text-lg flex items-center gap-2">
                    <i class="fas fa-book text-blue-600"></i>
                    @if($selectedClass)
                        Manage {{ $selectedClass->name }} students
                    @else
                        View and manage students
                    @endif
                </p>
            </div>
            <div class="flex items-center gap-3">
                @if($selectedClass)
                    <a href="{{ route('teacher.mark-attendance', ['class' => $selectedClass->id]) }}" 
                       class="inline-flex items-center gap-2 px-6 py-3 bg-green-600 text-white rounded-lg shadow-md hover:bg-green-700 transition font-semibold">
                        <i class="fas fa-check-square"></i>
                        Mark Attendance
                    </a>
                    <a href="{{ route('teacher.class.export', ['class' => $selectedClass->id]) }}" 
                       class="inline-flex items-center gap-2 px-6 py-3 bg-white text-gray-700 rounded-lg shadow-md border border-gray-200 hover:shadow-lg hover:border-gray-300 transition font-semibold">
                        <i class="fas fa-download"></i>
                        Export CSV
                    </a>
                    <a href="{{ route('teacher.my-classes') }}" 
                       class="inline-flex items-center gap-2 px-6 py-3 bg-gray-200 text-gray-700 rounded-lg shadow-md hover:bg-gray-300 transition font-semibold">
                        <i class="fas fa-arrow-left"></i>
                        Back
                    </a>
                @endif
            </div>
        </div>
    </div>

    <!-- Success Message -->
    @if(session('success'))
    <div class="bg-green-50 border-l-4 border-green-500 p-6 rounded-lg">
        <div class="flex gap-4">
            <div class="flex-shrink-0 text-2xl text-green-600">
                <i class="fas fa-check-circle"></i>
            </div>
            <div>
                <h3 class="font-semibold text-green-900">Success</h3>
                <p class="text-green-800 mt-1">{{ session('success') }}</p>
            </div>
        </div>
    </div>
    @endif

    <!-- Class Selection (if teacher has multiple classes) -->
    @if($teacherClasses->count() > 1 && !request('class'))
    <div class="card shadow-sm border border-gray-200">
        <div class="card-body">
            <form method="GET" action="{{ route('teacher.class.students') }}" class="space-y-4">
                <label class="block">
                    <span class="text-sm font-semibold text-gray-900 mb-2 flex items-center gap-2">
                        <i class="fas fa-list text-blue-600"></i>
                        Select  Class
                    </span>
                    <select name="class" onchange="this.form.submit()" class="form-select w-full md:w-96">
                        @foreach($teacherClasses as $class)
                            <option value="{{ $class->id }}" {{ $selectedClass && $selectedClass->id === $class->id ? 'selected' : '' }}>
                                {{ $class->name }}
                                @if($class->section)
                                    - Section {{ $class->section }}
                                @endif
                                ({{ $class->students()->count() }} students)
                            </option>
                        @endforeach
                    </select>
                </label>
            </form>
        </div>
    </div>
    @endif

    <!-- No Classes Message -->
    @if($teacherClasses->isEmpty())
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
        <!-- Stats Overview -->
        <div class="grid grid-cols-1 md:grid-cols-5 gap-6">
            <!-- Total Students -->
            <div class="card shadow-sm hover:shadow-md transition border border-gray-200">
                <div class="card-body">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600 font-medium">Total Students</p>
                            <p class="text-3xl font-bold text-gray-900 mt-2">{{ number_format($stats['total']) }}</p>
                        </div>
                        <div class="w-14 h-14 bg-gradient-to-br from-blue-100 to-blue-50 rounded-full flex items-center justify-center shadow-sm">
                            <i class="fas fa-users text-blue-600 text-2xl"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Active Students -->
            <div class="card shadow-sm hover:shadow-md transition border border-gray-200">
                <div class="card-body">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600 font-medium">Active</p>
                            <p class="text-3xl font-bold text-green-600 mt-2">{{ number_format($stats['active']) }}</p>
                        </div>
                        <div class="w-14 h-14 bg-gradient-to-br from-green-100 to-green-50 rounded-full flex items-center justify-center shadow-sm">
                            <i class="fas fa-check-circle text-green-600 text-2xl"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pending Students -->
            <div class="card shadow-sm hover:shadow-md transition border border-gray-200">
                <div class="card-body">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600 font-medium">Pending</p>
                            <p class="text-3xl font-bold text-yellow-600 mt-2">{{ number_format($stats['pending']) }}</p>
                        </div>
                        <div class="w-14 h-14 bg-gradient-to-br from-yellow-100 to-yellow-50 rounded-full flex items-center justify-center shadow-sm">
                            <i class="fas fa-hourglass-half text-yellow-600 text-2xl"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Male Students -->
            <div class="card shadow-sm hover:shadow-md transition border border-gray-200">
                <div class="card-body">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600 font-medium">Male</p>
                            <p class="text-3xl font-bold text-blue-600 mt-2">{{ number_format($stats['male']) }}</p>
                        </div>
                        <div class="w-14 h-14 bg-gradient-to-br from-blue-100 to-blue-50 rounded-full flex items-center justify-center shadow-sm">
                            <i class="fas fa-mars text-blue-600 text-2xl"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Female Students -->
            <div class="card shadow-sm hover:shadow-md transition border border-gray-200">
                <div class="card-body">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600 font-medium">Female</p>
                            <p class="text-3xl font-bold text-pink-600 mt-2">{{ number_format($stats['female']) }}</p>
                        </div>
                        <div class="w-14 h-14 bg-gradient-to-br from-pink-100 to-pink-50 rounded-full flex items-center justify-center shadow-sm">
                            <i class="fas fa-venus text-pink-600 text-2xl"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters & Search -->
        <div class="card shadow-sm border border-gray-200">
            <div class="card-header bg-gradient-to-r from-blue-50 to-blue-25 border-b border-blue-100">
                <h3 class="text-lg font-semibold text-gray-900">
                    <i class="fas fa-filter text-blue-600 mr-2"></i>
                    Search & Filter
                </h3>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('teacher.class.students') }}" class="space-y-4">
                    @if($selectedClass)
                        <input type="hidden" name="class" value="{{ $selectedClass->id }}">
                    @endif
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <!-- Search -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Search Students</label>
                            <input type="text"
                                   name="search"
                                   value="{{ request('search') }}"
                                   placeholder="Name, ID, or email..."
                                   class="form-input w-full">
                        </div>

                        <!-- Gender Filter -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Gender</label>
                            <select name="gender" class="form-select w-full">
                                <option value="">All Genders</option>
                                <option value="male" {{ request('gender') === 'male' ? 'selected' : '' }}>Male</option>
                                <option value="female" {{ request('gender') === 'female' ? 'selected' : '' }}>Female</option>
                            </select>
                        </div>

                        <!-- Status Filter -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                            <select name="status" class="form-select w-full">
                                <option value="">All Status</option>
                                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                                <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                            </select>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex gap-2 pt-2">
                        <button type="submit" class="inline-flex items-center gap-2 px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-semibold">
                            <i class="fas fa-search"></i>
                            Search
                        </button>
                        <a href="{{ route('teacher.class.students') }}" class="inline-flex items-center gap-2 px-6 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition font-semibold">
                            <i class="fas fa-redo"></i>
                            Reset
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Students Table -->
        <div class="card shadow-sm border border-gray-200">
            <div class="card-header bg-gradient-to-r from-indigo-50 to-indigo-25 border-b border-indigo-100">
                <div class="flex items-center justify-between">
                    <h2 class="text-xl font-semibold text-gray-900">
                        <span class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-indigo-100 text-indigo-600 mr-3">
                            <i class="fas fa-list"></i>
                        </span>
                        @if($selectedClass)
                            {{ $selectedClass->name }} Students
                        @else
                            Students
                        @endif
                    </h2>
                    @if($students->total() > 0)
                        <span class="text-sm font-semibold px-4 py-2 bg-white rounded-full text-indigo-600 border border-indigo-200">
                            {{ number_format($students->total()) }} student{{ $students->total() !== 1 ? 's' : '' }}
                        </span>
                    @endif
                </div>
            </div>
            <div class="card-body p-0">
                <div class="overflow-x-auto">
                    <table class="table">
                        <thead class="bg-gray-50 border-b border-gray-200">
                            <tr>
                                <th class="text-gray-700 font-semibold">Student ID</th>
                                <th class="text-gray-700 font-semibold">Name</th>
                                <th class="text-gray-700 font-semibold">Gender</th>
                                <th class="text-gray-700 font-semibold">Admission Date</th>
                                <th class="text-gray-700 font-semibold">Status</th>
                                <th class="text-gray-700 font-semibold text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($students as $student)
                            <tr class="border-b border-gray-100 hover:bg-gray-50 transition">
                                <td>
                                    <span class="font-semibold text-gray-900 px-3 py-1 rounded-full bg-gray-100">
                                        {{ $student->admission_number }}
                                    </span>
                                </td>
                                <td>
                                    <div class="flex items-center gap-3">
                                        <div class="flex-shrink-0 w-10 h-10 rounded-full {{ $student->gender === 'male' ? 'bg-blue-100' : 'bg-pink-100' }} flex items-center justify-center overflow-hidden shadow-sm">
                                            @if($student->photo)
                                                <img src="{{ Storage::url($student->photo) }}"
                                                     alt="{{ $student->full_name }}"
                                                     class="w-full h-full object-cover">
                                            @else
                                                <span class="text-sm font-bold {{ $student->gender === 'male' ? 'text-blue-600' : 'text-pink-600' }}">
                                                    {{ substr($student->first_name, 0, 1) }}{{ substr($student->last_name, 0, 1) }}
                                                </span>
                                            @endif
                                        </div>
                                        <div>
                                            <p class="font-semibold text-gray-900">{{ $student->full_name }}</p>
                                            @if($student->user && $student->user->email)
                                                <p class="text-xs text-gray-500">{{ $student->user->email }}</p>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="flex items-center gap-2">
                                        <i class="fas fa-{{ $student->gender === 'male' ? 'mars text-blue-600' : 'venus text-pink-600' }}"></i>
                                        <span class="capitalize text-sm font-medium">{{ $student->gender }}</span>
                                    </div>
                                </td>
                                <td>
                                    <div class="text-sm">
                                        @if($student->admission_date)
                                            <div class="font-semibold text-gray-900">{{ $student->admission_date->format('M d, Y') }}</div>
                                            <div class="text-gray-500 text-xs">{{ $student->admission_date->diffForHumans() }}</div>
                                        @else
                                            <span class="text-gray-400">-</span>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    @switch($student->status)
                                        @case('active')
                                            <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-green-50 text-green-700 font-semibold border border-green-200">
                                                <i class="fas fa-check-circle"></i>
                                                Active
                                            </span>
                                            @break
                                        @case('pending')
                                            <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-yellow-50 text-yellow-700 font-semibold border border-yellow-200">
                                                <i class="fas fa-hourglass-half"></i>
                                                Pending
                                            </span>
                                            @break
                                        @case('inactive')
                                            <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-red-50 text-red-700 font-semibold border border-red-200">
                                                <i class="fas fa-ban"></i>
                                                Inactive
                                            </span>
                                            @break
                                        @case('graduated')
                                            <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-purple-50 text-purple-700 font-semibold border border-purple-200">
                                                <i class="fas fa-graduation-cap"></i>
                                                Graduated
                                            </span>
                                            @break
                                        @case('withdrawn')
                                            <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-gray-50 text-gray-700 font-semibold border border-gray-200">
                                                <i class="fas fa-times-circle"></i>
                                                Withdrawn
                                            </span>
                                            @break
                                        @default
                                            <span class="text-gray-400">-</span>
                                    @endswitch
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('teacher.student-detail', $student->id) }}" 
                                       class="inline-flex items-center gap-2 px-4 py-2 bg-blue-50 text-blue-700 rounded-lg hover:bg-blue-100 transition font-semibold text-sm border border-blue-200">
                                        <i class="fas fa-eye"></i>
                                        View
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center py-12">
                                    <div class="flex flex-col items-center justify-center">
                                        <i class="fas fa-inbox text-gray-300 text-5xl mb-4"></i>
                                        <p class="text-gray-500 text-lg font-medium">No students found</p>
                                        <p class="text-gray-400 text-sm mt-1">Try adjusting your search or filters</p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Pagination -->
        @if($students->hasPages())
        <div class="flex justify-center">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
                {{ $students->links() }}
            </div>
        </div>
        @endif
    @endif
</div>

@push('scripts')
<script>
    // Any additional Alpine.js data if needed
</script>
@endpush

@endsection
