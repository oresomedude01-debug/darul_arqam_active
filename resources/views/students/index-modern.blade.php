@extends('layouts.spa')

@section('title', 'Students Management')

@section('breadcrumb')
    <span class="text-gray-500">
        <i class="fas fa-home text-xs"></i>
    </span>
    <span class="text-gray-300">/</span>
    <span class="text-gray-900 font-medium">Students</span>
@endsection

@section('content')
<div x-data="studentsPage()" class="space-y-6">
    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">Students Management</h1>
            <p class="text-sm text-gray-600 mt-1">Manage and monitor all registered students</p>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            <a href="{{ route('students.export') }}" class="btn btn-outline btn-sm">
                <i class="fas fa-download"></i>
                <span class="hidden sm:inline ml-2">Export</span>
            </a>
            <a href="{{ route('students.import-form') }}" class="btn btn-outline btn-sm">
                <i class="fas fa-upload"></i>
                <span class="hidden sm:inline ml-2">Import</span>
            </a>
            <a href="{{ route('students.create') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i>
                <span class="ml-2">Add Student</span>
            </a>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
        <!-- Total Students -->
        <div class="card hover:shadow-lg transition-shadow duration-300">
            <div class="card-body">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <p class="text-xs font-semibold text-gray-600 uppercase tracking-wide">Total Students</p>
                        <p class="text-2xl sm:text-3xl font-bold text-gray-900 mt-2">{{ number_format($stats['total']) }}</p>
                        <p class="text-xs text-gray-500 mt-1">
                            <i class="fas fa-users mr-1"></i>
                            All registered
                        </p>
                    </div>
                    <div class="w-14 h-14 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center shadow-lg">
                        <i class="fas fa-users text-white text-2xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Male Students -->
        <div class="card hover:shadow-lg transition-shadow duration-300">
            <div class="card-body">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <p class="text-xs font-semibold text-gray-600 uppercase tracking-wide">Male</p>
                        <p class="text-2xl sm:text-3xl font-bold text-gray-900 mt-2">{{ number_format($stats['male']) }}</p>
                        <p class="text-xs text-gray-500 mt-1">
                            <i class="fas fa-male mr-1"></i>
                            {{ $stats['total'] > 0 ? round(($stats['male']/$stats['total'])*100, 1) : 0 }}%
                        </p>
                    </div>
                    <div class="w-14 h-14 bg-gradient-to-br from-green-500 to-green-600 rounded-xl flex items-center justify-center shadow-lg">
                        <i class="fas fa-male text-white text-2xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Female Students -->
        <div class="card hover:shadow-lg transition-shadow duration-300">
            <div class="card-body">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <p class="text-xs font-semibold text-gray-600 uppercase tracking-wide">Female</p>
                        <p class="text-2xl sm:text-3xl font-bold text-gray-900 mt-2">{{ number_format($stats['female']) }}</p>
                        <p class="text-xs text-gray-500 mt-1">
                            <i class="fas fa-female mr-1"></i>
                            {{ $stats['total'] > 0 ? round(($stats['female']/$stats['total'])*100, 1) : 0 }}%
                        </p>
                    </div>
                    <div class="w-14 h-14 bg-gradient-to-br from-pink-500 to-pink-600 rounded-xl flex items-center justify-center shadow-lg">
                        <i class="fas fa-female text-white text-2xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- New This Month -->
        <div class="card hover:shadow-lg transition-shadow duration-300">
            <div class="card-body">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <p class="text-xs font-semibold text-gray-600 uppercase tracking-wide">New This Month</p>
                        <p class="text-2xl sm:text-3xl font-bold text-gray-900 mt-2">{{ number_format($stats['new_this_month']) }}</p>
                        <p class="text-xs text-gray-500 mt-1">
                            <i class="fas fa-calendar mr-1"></i>
                            {{ date('F Y') }}
                        </p>
                    </div>
                    <div class="w-14 h-14 bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl flex items-center justify-center shadow-lg">
                        <i class="fas fa-user-plus text-white text-2xl"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card">
        <div class="card-header">
            <div class="flex items-center justify-between">
                <h3 class="text-base font-semibold text-gray-900">
                    <i class="fas fa-filter mr-2 text-primary-600"></i>
                    Filter Students
                </h3>
                <button @click="showFilters = !showFilters"
                        class="lg:hidden text-sm text-primary-600 hover:text-primary-700 font-medium">
                    <span x-text="showFilters ? 'Hide' : 'Show'"></span>
                    <i :class="showFilters ? 'fa-chevron-up' : 'fa-chevron-down'" class="fas ml-1"></i>
                </button>
            </div>
        </div>
        <div class="card-body" x-show="showFilters" x-transition>
            <form method="GET" action="{{ route('students.index') }}" class="space-y-4">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
                    <!-- Search -->
                    <div class="lg:col-span-2">
                        <label class="form-label">Search</label>
                        <div class="relative">
                            <input type="text"
                                   name="search"
                                   value="{{ request('search') }}"
                                   placeholder="Name, ID, or email..."
                                   class="form-input pl-10">
                            <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                        </div>
                    </div>

                    <!-- Class Filter -->
                    <div>
                        <label class="form-label">Class</label>
                        <select name="class" class="form-select">
                            <option value="">All Classes</option>
                            @foreach(['Nursery 1', 'Nursery 2', 'Primary 1', 'Primary 2', 'Primary 3', 'Primary 4', 'Primary 5', 'Primary 6', 'JSS 1', 'JSS 2', 'JSS 3', 'SSS 1', 'SSS 2', 'SSS 3'] as $class)
                                <option value="{{ $class }}" {{ request('class') === $class ? 'selected' : '' }}>
                                    {{ $class }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Gender Filter -->
                    <div>
                        <label class="form-label">Gender</label>
                        <select name="gender" class="form-select">
                            <option value="">All</option>
                            <option value="male" {{ request('gender') === 'male' ? 'selected' : '' }}>Male</option>
                            <option value="female" {{ request('gender') === 'female' ? 'selected' : '' }}>Female</option>
                        </select>
                    </div>

                    <!-- Status Filter -->
                    <div>
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            <option value="">All</option>
                            <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                            <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                            <option value="graduated" {{ request('status') === 'graduated' ? 'selected' : '' }}>Graduated</option>
                            <option value="withdrawn" {{ request('status') === 'withdrawn' ? 'selected' : '' }}>Withdrawn</option>
                        </select>
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex flex-wrap gap-2 pt-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search mr-2"></i>
                        Apply Filters
                    </button>
                    <a href="{{ route('students.index') }}" class="btn btn-outline">
                        <i class="fas fa-redo mr-2"></i>
                        Reset
                    </a>
                    @if(request()->hasAny(['search', 'class', 'gender', 'status']))
                        <div class="flex items-center ml-auto text-sm text-gray-600">
                            <i class="fas fa-info-circle mr-2"></i>
                            <span>Filters active</span>
                        </div>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <!-- Students Table/Cards -->
    <div class="card">
        <div class="card-header">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <h3 class="text-base font-semibold text-gray-900">
                    All Students
                    @if($students->total() > 0)
                        <span class="text-sm text-gray-500 font-normal ml-2">({{ number_format($students->total()) }} total)</span>
                    @endif
                </h3>
                <div class="flex items-center gap-3">
                    <div class="flex items-center gap-2 text-sm">
                        <label class="text-gray-600">Per page:</label>
                        <select class="form-select text-sm py-1 pr-8" onchange="window.location.href='?per_page='+this.value">
                            <option value="10" {{ request('per_page') == 10 ? 'selected' : '' }}>10</option>
                            <option value="20" {{ request('per_page', 20) == 20 ? 'selected' : '' }}>20</option>
                            <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                            <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100</option>
                        </select>
                    </div>

                    <div class="hidden md:flex items-center gap-1 bg-gray-100 rounded-lg p-1">
                        <button @click="viewMode = 'table'"
                                :class="viewMode === 'table' ? 'bg-white shadow' : ''"
                                class="px-3 py-1.5 rounded text-sm transition">
                            <i class="fas fa-table"></i>
                        </button>
                        <button @click="viewMode = 'grid'"
                                :class="viewMode === 'grid' ? 'bg-white shadow' : ''"
                                class="px-3 py-1.5 rounded text-sm transition">
                            <i class="fas fa-th-large"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="card-body p-0">
            <!-- Desktop Table View -->
            <div x-show="viewMode === 'table'" class="hidden md:block table-responsive overflow-x-auto">
                <table class="table">
                    <thead>
                        <tr>
                            <th class="sortable">
                                <div class="flex items-center gap-2">
                                    Student ID
                                    <i class="fas fa-sort text-gray-400 text-xs"></i>
                                </div>
                            </th>
                            <th>Student Info</th>
                            <th>Class</th>
                            <th>Gender</th>
                            <th class="sortable">
                                <div class="flex items-center gap-2">
                                    Admission Date
                                    <i class="fas fa-sort text-gray-400 text-xs"></i>
                                </div>
                            </th>
                            <th>Status</th>
                            <th class="text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($students as $student)
                        <tr>
                            <td>
                                <a href="{{ route('students.show', $student->id) }}"
                                   class="font-medium text-primary-600 hover:text-primary-700 hover:underline">
                                    {{ $student->admission_number }}
                                </a>
                            </td>
                            <td>
                                <div class="flex items-center gap-3">
                                    <div class="flex-shrink-0 w-10 h-10 rounded-full {{ $student->gender === 'male' ? 'bg-blue-100' : 'bg-pink-100' }} flex items-center justify-center overflow-hidden">
                                        @if($student->photo_path)
                                            <img src="{{ Storage::url($student->photo_path) }}"
                                                 alt="{{ $student->full_name }}"
                                                 class="w-full h-full object-cover">
                                        @else
                                            <span class="text-sm font-semibold {{ $student->gender === 'male' ? 'text-blue-600' : 'text-pink-600' }}">
                                                {{ substr($student->first_name, 0, 1) }}{{ substr($student->last_name, 0, 1) }}
                                            </span>
                                        @endif
                                    </div>
                                    <div>
                                        <p class="font-medium text-gray-900">{{ $student->full_name }}</p>
                                        @if($student->email)
                                            <p class="text-xs text-gray-500">{{ $student->email }}</p>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="flex flex-col gap-1">
                                    <span class="badge badge-primary">{{ $student->class_level }}</span>
                                    @if($student->section)
                                        <span class="text-xs text-gray-500">Section {{ $student->section }}</span>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <span class="capitalize text-sm">
                                    <i class="fas fa-{{ $student->gender === 'male' ? 'male' : 'female' }} mr-1 {{ $student->gender === 'male' ? 'text-blue-600' : 'text-pink-600' }}"></i>
                                    {{ $student->gender }}
                                </span>
                            </td>
                            <td>
                                <div class="flex flex-col">
                                    <span class="text-sm text-gray-900">{{ $student->admission_date->format('M d, Y') }}</span>
                                    <span class="text-xs text-gray-500">{{ $student->admission_date->diffForHumans() }}</span>
                                </div>
                            </td>
                            <td>
                                @switch($student->status)
                                    @case('active')
                                        <span class="badge badge-success">
                                            <i class="fas fa-check-circle"></i>
                                            <span class="ml-1">Active</span>
                                        </span>
                                        @break
                                    @case('pending')
                                        <span class="badge badge-warning">
                                            <i class="fas fa-clock"></i>
                                            <span class="ml-1">Pending</span>
                                        </span>
                                        @break
                                    @case('graduated')
                                        <span class="badge badge-primary">
                                            <i class="fas fa-graduation-cap"></i>
                                            <span class="ml-1">Graduated</span>
                                        </span>
                                        @break
                                    @case('withdrawn')
                                        <span class="badge badge-error">
                                            <i class="fas fa-times-circle"></i>
                                            <span class="ml-1">Withdrawn</span>
                                        </span>
                                        @break
                                    @default
                                        <span class="badge badge-gray">{{ ucfirst($student->status) }}</span>
                                @endswitch
                            </td>
                            <td>
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('students.show', $student->id) }}"
                                       class="btn btn-sm btn-icon text-blue-600 hover:bg-blue-50"
                                       title="View Details">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('students.edit', $student->id) }}"
                                       class="btn btn-sm btn-icon text-green-600 hover:bg-green-50"
                                       title="Edit Student">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form method="POST" action="{{ route('students.destroy', $student->id) }}" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                onclick="return confirm('Are you sure you want to delete {{ $student->full_name }}?')"
                                                class="btn btn-sm btn-icon text-red-600 hover:bg-red-50"
                                                title="Delete Student">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7">
                                <div class="empty-state">
                                    <div class="empty-state-icon">
                                        <i class="fas fa-user-graduate"></i>
                                    </div>
                                    <h3 class="empty-state-title">No students found</h3>
                                    <p class="empty-state-text">
                                        @if(request()->hasAny(['search', 'class', 'gender', 'status']))
                                            Try adjusting your filters or search terms
                                        @else
                                            Get started by adding your first student
                                        @endif
                                    </p>
                                    @if(request()->hasAny(['search', 'class', 'gender', 'status']))
                                        <a href="{{ route('students.index') }}" class="btn btn-outline">
                                            <i class="fas fa-redo mr-2"></i>
                                            Clear All Filters
                                        </a>
                                    @else
                                        <a href="{{ route('students.create') }}" class="btn btn-primary">
                                            <i class="fas fa-plus mr-2"></i>
                                            Add Student
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Mobile/Tablet Card View -->
            <div x-show="viewMode === 'grid' || window.innerWidth < 768" class="table-cards md:hidden p-4">
                @forelse($students as $student)
                <div class="table-card hover:shadow-lg transition-shadow">
                    <div class="flex items-start justify-between mb-3">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 rounded-full {{ $student->gender === 'male' ? 'bg-blue-100' : 'bg-pink-100' }} flex items-center justify-center overflow-hidden flex-shrink-0">
                                @if($student->photo_path)
                                    <img src="{{ Storage::url($student->photo_path) }}"
                                         alt="{{ $student->full_name }}"
                                         class="w-full h-full object-cover">
                                @else
                                    <span class="text-sm font-semibold {{ $student->gender === 'male' ? 'text-blue-600' : 'text-pink-600' }}">
                                        {{ substr($student->first_name, 0, 1) }}{{ substr($student->last_name, 0, 1) }}
                                    </span>
                                @endif
                            </div>
                            <div>
                                <p class="font-semibold text-gray-900">{{ $student->full_name }}</p>
                                <p class="text-xs text-gray-500">{{ $student->admission_number }}</p>
                            </div>
                        </div>
                        @switch($student->status)
                            @case('active')
                                <span class="badge badge-success">Active</span>
                                @break
                            @case('pending')
                                <span class="badge badge-warning">Pending</span>
                                @break
                            @default
                                <span class="badge badge-gray">{{ ucfirst($student->status) }}</span>
                        @endswitch
                    </div>

                    <div class="space-y-2 text-sm">
                        <div class="table-card-row">
                            <span class="table-card-label">Class</span>
                            <span class="table-card-value">{{ $student->class_level }} {{ $student->section ? '- ' . $student->section : '' }}</span>
                        </div>
                        <div class="table-card-row">
                            <span class="table-card-label">Gender</span>
                            <span class="table-card-value capitalize">{{ $student->gender }}</span>
                        </div>
                        <div class="table-card-row">
                            <span class="table-card-label">Admitted</span>
                            <span class="table-card-value">{{ $student->admission_date->format('M d, Y') }}</span>
                        </div>
                    </div>

                    <div class="flex gap-2 mt-4 pt-3 border-t border-gray-200">
                        <a href="{{ route('students.show', $student->id) }}" class="btn btn-sm btn-outline flex-1">
                            <i class="fas fa-eye mr-1"></i>
                            View
                        </a>
                        <a href="{{ route('students.edit', $student->id) }}" class="btn btn-sm btn-primary flex-1">
                            <i class="fas fa-edit mr-1"></i>
                            Edit
                        </a>
                    </div>
                </div>
                @empty
                <div class="empty-state">
                    <div class="empty-state-icon">
                        <i class="fas fa-user-graduate"></i>
                    </div>
                    <h3 class="empty-state-title">No students found</h3>
                    <p class="empty-state-text">
                        @if(request()->hasAny(['search', 'class', 'gender', 'status']))
                            Try adjusting your filters
                        @else
                            Add your first student to get started
                        @endif
                    </p>
                    <a href="{{ request()->hasAny(['search', 'class', 'gender', 'status']) ? route('students.index') : route('students.create') }}"
                       class="btn btn-primary mt-4">
                        <i class="fas fa-{{ request()->hasAny(['search', 'class', 'gender', 'status']) ? 'redo' : 'plus' }} mr-2"></i>
                        {{ request()->hasAny(['search', 'class', 'gender', 'status']) ? 'Clear Filters' : 'Add Student' }}
                    </a>
                </div>
                @endforelse
            </div>
        </div>

        <!-- Pagination -->
        @if($students->hasPages())
        <div class="card-footer">
            <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                <div class="text-sm text-gray-700">
                    Showing <span class="font-medium">{{ $students->firstItem() }}</span> to
                    <span class="font-medium">{{ $students->lastItem() }}</span> of
                    <span class="font-medium">{{ number_format($students->total()) }}</span> results
                </div>
                <div>
                    {{ $students->links() }}
                </div>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
    function studentsPage() {
        return {
            viewMode: window.innerWidth >= 768 ? 'table' : 'grid',
            showFilters: window.innerWidth >= 1024,
            init() {
                // Responsive view mode handling
                window.addEventListener('resize', () => {
                    if (window.innerWidth < 768) {
                        this.viewMode = 'grid';
                    }
                    if (window.innerWidth >= 1024) {
                        this.showFilters = true;
                    }
                });
            }
        }
    }
</script>
@endpush
