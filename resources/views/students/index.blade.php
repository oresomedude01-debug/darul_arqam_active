@extends('layouts.spa')

@section('title', 'Students')

@section('breadcrumb')
    <span class="text-gray-400">Student Management</span>
    <span class="text-gray-400">/</span>
    <span class="font-semibold text-gray-900">All Students</span>
@endsection

@section('content')
<div class="space-y-6" x-data="studentManagement()">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Students Management</h1>
            <p class="text-gray-600 mt-1">Manage all registered students in your school</p>
        </div>
        <div class="flex items-center space-x-3">
            <button @click="showBulkActions = !showBulkActions"
                    x-show="selectedStudents.length > 0"
                    class="btn btn-outline">
                <i class="fas fa-tasks mr-2"></i>
                Bulk Actions (<span x-text="selectedStudents.length"></span>)
            </button>
            <a href="{{ route('students.export') }}" class="btn btn-outline">
                <i class="fas fa-download mr-2"></i>
                Export
            </a>
            <a href="{{ route('students.import-form') }}" class="btn btn-outline">
                <i class="fas fa-upload mr-2"></i>
                Import
            </a>
            <a href="{{ route('students.create') }}" class="btn btn-primary">
                <i class="fas fa-plus mr-2"></i>
                Add Student
            </a>
        </div>
    </div>

    <!-- Success Message -->
    @if(session('success'))
    <div class="alert alert-success fade-in">
        <i class="fas fa-check-circle text-xl"></i>
        <p>{{ session('success') }}</p>
    </div>
    @endif

    <!-- Warning Message -->
    @if(session('warning'))
    <div class="alert alert-warning fade-in">
        <i class="fas fa-exclamation-triangle text-xl"></i>
        <p>{{ session('warning') }}</p>
    </div>
    @endif

    <!-- Import Errors -->
    @if($errors->any() || session('import_errors'))
    <div class="card bg-red-50 border-l-4 border-red-500">
        <div class="card-body">
            <h3 class="text-lg font-semibold text-red-900 mb-3">Import Errors</h3>
            <div class="space-y-2 max-h-64 overflow-y-auto">
                @forelse(session('import_errors', $errors->all()) as $error)
                    <div class="text-sm text-red-700 flex items-start">
                        <i class="fas fa-times-circle mr-2 mt-0.5 flex-shrink-0"></i>
                        <span>{{ $error }}</span>
                    </div>
                @empty
                    <p class="text-sm text-red-700">No error details available</p>
                @endforelse
            </div>
        </div>
    </div>
    @endif

    <!-- Stats Overview -->
    <div class="grid grid-cols-1 md:grid-cols-5 gap-6">
        <!-- Total Students -->
        <div class="card">
            <div class="card-body">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600">Total Students</p>
                        <p class="text-2xl font-bold text-gray-900 mt-1">{{ number_format($stats['total']) }}</p>
                    </div>
                    <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-users text-blue-600 text-xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Active Students -->
        <div class="card">
            <div class="card-body">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600">Active</p>
                        <p class="text-2xl font-bold text-green-600 mt-1">{{ number_format($stats['active']) }}</p>
                    </div>
                    <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-check-circle text-green-600 text-xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pending Students -->
        <div class="card">
            <div class="card-body">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600">Pending</p>
                        <p class="text-2xl font-bold text-yellow-600 mt-1">{{ number_format($stats['pending']) }}</p>
                    </div>
                    <div class="w-12 h-12 bg-yellow-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-hourglass text-yellow-600 text-xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Male Students -->
        <div class="card">
            <div class="card-body">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600">Male</p>
                        <p class="text-2xl font-bold text-blue-600 mt-1">{{ number_format($stats['male']) }}</p>
                    </div>
                    <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-male text-blue-600 text-xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Female Students -->
        <div class="card">
            <div class="card-body">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600">Female</p>
                        <p class="text-2xl font-bold text-pink-600 mt-1">{{ number_format($stats['female']) }}</p>
                    </div>
                    <div class="w-12 h-12 bg-pink-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-female text-pink-600 text-xl"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bulk Actions Panel -->
    <div x-show="showBulkActions" x-collapse class="card">
        <div class="card-body">
            <div class="flex items-center justify-between">
                <p class="text-sm text-gray-600">
                    <span x-text="selectedStudents.length"></span> student(s) selected
                </p>
                <div class="flex space-x-3">
                    <button @click="clearSelection()" class="btn btn-sm btn-outline">
                        Clear Selection
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters & Search -->
    <div class="card">
        <div class="card-body">
            <form id="filterForm" method="GET" action="{{ route('students.index') }}" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <!-- Search -->
                    <div>
                        <input type="text"
                               name="search"
                               value="{{ request('search') }}"
                               placeholder="Search name, ID, email..."
                               class="form-input">
                    </div>

                    <!-- Class Filter - Dynamic -->
                    <div>
                        <select name="class" class="form-select">
                            <option value="">All Classes</option>
                            @foreach($schoolClasses as $class)
                                <option value="{{ $class->id }}" {{ request('class') == $class->id ? 'selected' : '' }}>
                                    {{ $class->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Gender Filter -->
                    <div>
                        <select name="gender" class="form-select">
                            <option value="">All Genders</option>
                            <option value="male" {{ request('gender') === 'male' ? 'selected' : '' }}>Male</option>
                            <option value="female" {{ request('gender') === 'female' ? 'selected' : '' }}>Female</option>
                        </select>
                    </div>

                    <!-- Status Filter -->
                    <div>
                        <select name="status" class="form-select">
                            <option value="">All Status</option>
                            <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                            <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                            <option value="graduated" {{ request('status') === 'graduated' ? 'selected' : '' }}>Graduated</option>
                            <option value="withdrawn" {{ request('status') === 'withdrawn' ? 'selected' : '' }}>Withdrawn</option>
                        </select>
                    </div>
                </div>

                <!-- Session Filter (if sessions exist) -->
                @if(isset($sessions) && $sessions->count() > 0)
                <div>
                    <select name="session" class="form-select">
                        <option value="">All Sessions</option>
                        @foreach($sessions as $session)
                            <option value="{{ $session }}" {{ request('session') === $session ? 'selected' : '' }}>
                                {{ $session }}
                            </option>
                        @endforeach
                    </select>
                </div>
                @endif

                <!-- Actions -->
                <div class="flex space-x-2">
                    <button type="submit" @click="applyFilter()" class="btn btn-primary">
                        <i class="fas fa-filter mr-2"></i>
                        Filter
                    </button>
                    <button type="button" @click="resetFilter()" class="btn btn-outline">
                        <i class="fas fa-redo"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Students Table -->
    <div class="card">
        <div class="card-header">
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-semibold text-gray-900">
                    All Students
                    @if($students->total() > 0)
                        <span class="text-sm text-gray-500 font-normal ml-2">({{ number_format($students->total()) }} total)</span>
                    @endif
                </h2>
            </div>
        </div>
        <div class="card-body p-0">
            <!-- Desktop Table -->
            <div class="overflow-x-auto hidden md:block">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Student ID</th>
                            <th>Name</th>
                            <th>Class</th>
                            <th>Gender</th>
                            <th>Admission Date</th>
                            <th>Status</th>
                            <th>Actions</th>
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
                                <div class="flex items-center space-x-3">
                                    <div class="flex-shrink-0 w-10 h-10 rounded-full {{ $student->gender === 'male' ? 'bg-blue-100' : 'bg-pink-100' }} flex items-center justify-center overflow-hidden">
                                        @if($student->photo)
                                            <img src="{{ Storage::url($student->photo) }}"
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
                                        @if($student->user && $student->user->email)
                                            <p class="text-xs text-gray-500">{{ $student->user->email }}</p>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td>
                                @if($student->schoolClass)
                                    <span class="badge badge-primary">{{ $student->schoolClass->name }}</span>
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>
                            <td>
                                <div class="flex items-center gap-2">
                                    <i class="fas fa-{{ $student->gender === 'male' ? 'male text-blue-600' : 'female text-pink-600' }}"></i>
                                    <span class="capitalize text-sm">{{ $student->gender }}</span>
                                </div>
                            </td>
                            <td>
                                <div class="text-sm">
                                    @if($student->admission_date)
                                        <div class="text-gray-900">{{ $student->admission_date->format('M d, Y') }}</div>
                                        <div class="text-gray-500 text-xs">{{ $student->admission_date->diffForHumans() }}</div>
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </div>
                            </td>
                            <td>
                                @switch($student->status)
                                    @case('active')
                                        <span class="badge badge-success">
                                            <i class="fas fa-check-circle mr-1"></i> Active
                                        </span>
                                        @break
                                    @case('pending')
                                        <span class="badge badge-warning">
                                            <i class="fas fa-hourglass mr-1"></i> Pending
                                        </span>
                                        @break
                                    @case('graduated')
                                        <span class="badge badge-info">
                                            <i class="fas fa-graduation-cap mr-1"></i> Graduated
                                        </span>
                                        @break
                                    @case('withdrawn')
                                        <span class="badge badge-danger">
                                            <i class="fas fa-times-circle mr-1"></i> Withdrawn
                                        </span>
                                        @break
                                    @case('inactive')
                                        <span class="badge badge-secondary">
                                            <i class="fas fa-ban mr-1"></i> Inactive
                                        </span>
                                        @break
                                    @default
                                        <span class="badge badge-gray">{{ ucfirst($student->status) }}</span>
                                @endswitch
                            </td>
                            <td>
                                <div class="flex items-center space-x-2">
                                    <a href="{{ route('students.show', $student->id) }}"
                                       class="text-blue-600 hover:text-blue-700"
                                       title="View Details">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('students.edit', $student->id) }}"
                                       class="text-green-600 hover:text-green-700"
                                       title="Edit Student">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="{{ route('students.print', $student->id) }}"
                                       class="text-purple-600 hover:text-purple-700"
                                       title="Print Details">
                                        <i class="fas fa-print"></i>
                                    </a>
                                    <form method="POST" action="{{ route('students.destroy', $student->id) }}" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                onclick="return confirm('Are you sure you want to delete {{ $student->full_name }}?')"
                                                class="text-red-600 hover:text-red-700"
                                                title="Delete Student">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-12">
                                <div class="text-gray-400">
                                    <i class="fas fa-user-graduate text-5xl mb-4 block"></i>
                                    <p class="text-lg font-medium">No students found</p>
                                    @if(request()->hasAny(['search', 'class', 'gender', 'status', 'session']))
                                        <p class="text-sm mt-2 text-gray-500">Try adjusting your filters or search terms</p>
                                        <a href="{{ route('students.index') }}" @click.prevent="resetFilter()" class="text-primary-600 hover:text-primary-700 mt-3 inline-block">
                                            <i class="fas fa-redo mr-2"></i>Clear all filters
                                        </a>
                                    @else
                                        <p class="text-sm mt-2 text-gray-500">Get started by adding your first student</p>
                                        <a href="{{ route('students.create') }}" class="text-primary-600 hover:text-primary-700 mt-3 inline-block">
                                            <i class="fas fa-plus mr-2"></i>Add Student
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Mobile Grid -->
            <div class="md:hidden space-y-3 p-4">
                @forelse($students as $student)
                <div class="bg-white border border-gray-200 rounded-lg shadow-sm hover:shadow-md transition-shadow">
                    <div class="p-4 space-y-3">
                        <!-- Header: Student Name and Status -->
                        <div class="flex items-start justify-between gap-3">
                            <div class="flex-1">
                                <div class="text-xs text-gray-400 font-semibold uppercase tracking-wider mb-2">Student</div>
                                <div class="flex items-center space-x-3">
                                    <div class="flex-shrink-0 w-12 h-12 rounded-full {{ $student->gender === 'male' ? 'bg-blue-100' : 'bg-pink-100' }} flex items-center justify-center overflow-hidden flex-shrink-0">
                                        @if($student->photo)
                                            <img src="{{ Storage::url($student->photo) }}"
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
                                        @if($student->user && $student->user->email)
                                            <p class="text-xs text-gray-500">{{ $student->user->email }}</p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="text-right">
                                <div class="text-xs text-gray-400 font-semibold uppercase tracking-wider mb-2">Status</div>
                                @switch($student->status)
                                    @case('active')
                                        <span class="badge badge-success inline-flex items-center">
                                            <i class="fas fa-check-circle mr-1"></i> Active
                                        </span>
                                        @break
                                    @case('pending')
                                        <span class="badge badge-warning inline-flex items-center">
                                            <i class="fas fa-hourglass mr-1"></i> Pending
                                        </span>
                                        @break
                                    @case('graduated')
                                        <span class="badge badge-info inline-flex items-center">
                                            <i class="fas fa-graduation-cap mr-1"></i> Graduated
                                        </span>
                                        @break
                                    @case('withdrawn')
                                        <span class="badge badge-danger inline-flex items-center">
                                            <i class="fas fa-times-circle mr-1"></i> Withdrawn
                                        </span>
                                        @break
                                    @case('inactive')
                                        <span class="badge badge-secondary inline-flex items-center">
                                            <i class="fas fa-ban mr-1"></i> Inactive
                                        </span>
                                        @break
                                    @default
                                        <span class="badge badge-gray">{{ ucfirst($student->status) }}</span>
                                @endswitch
                            </div>
                        </div>

                        <div class="border-t border-gray-100"></div>

                        <!-- Row 2: ID and Class -->
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <div class="text-xs text-gray-400 font-semibold uppercase tracking-wider mb-2">Student ID</div>
                                <a href="{{ route('students.show', $student->id) }}"
                                   class="font-semibold text-primary-600 hover:text-primary-700">
                                    {{ $student->admission_number }}
                                </a>
                            </div>
                            <div>
                                <div class="text-xs text-gray-400 font-semibold uppercase tracking-wider mb-2">Class</div>
                                @if($student->schoolClass)
                                    <span class="badge badge-primary">{{ $student->schoolClass->name }}</span>
                                @else
                                    <div class="text-sm text-gray-400">—</div>
                                @endif
                            </div>
                        </div>

                        <!-- Row 3: Gender and Admission Date -->
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <div class="text-xs text-gray-400 font-semibold uppercase tracking-wider mb-2">Gender</div>
                                <div class="flex items-center gap-2">
                                    <i class="fas fa-{{ $student->gender === 'male' ? 'male text-blue-600' : 'female text-pink-600' }}"></i>
                                    <span class="capitalize text-sm font-medium text-gray-900">{{ $student->gender }}</span>
                                </div>
                            </div>
                            <div>
                                <div class="text-xs text-gray-400 font-semibold uppercase tracking-wider mb-2">Admitted</div>
                                @if($student->admission_date)
                                    <div class="text-sm font-medium text-gray-900">{{ $student->admission_date->format('M d, Y') }}</div>
                                    <div class="text-xs text-gray-500">{{ $student->admission_date->diffForHumans() }}</div>
                                @else
                                    <div class="text-sm text-gray-400">—</div>
                                @endif
                            </div>
                        </div>

                        <div class="border-t border-gray-100"></div>

                        <!-- Row 4: Actions -->
                        <div class="flex items-center justify-center gap-2">
                            <a href="{{ route('students.show', $student->id) }}"
                               class="inline-flex items-center justify-center p-2.5 rounded-lg bg-blue-50 text-blue-600 hover:bg-blue-100 transition-colors"
                               title="View Details">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('students.edit', $student->id) }}"
                               class="inline-flex items-center justify-center p-2.5 rounded-lg bg-green-50 text-green-600 hover:bg-green-100 transition-colors"
                               title="Edit Student">
                                <i class="fas fa-edit"></i>
                            </a>
                            <a href="{{ route('students.print', $student->id) }}"
                               class="inline-flex items-center justify-center p-2.5 rounded-lg bg-purple-50 text-purple-600 hover:bg-purple-100 transition-colors"
                               title="Print Details">
                                <i class="fas fa-print"></i>
                            </a>
                            <form method="POST" action="{{ route('students.destroy', $student->id) }}" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                        onclick="return confirm('Are you sure you want to delete {{ $student->full_name }}?')"
                                        class="inline-flex items-center justify-center p-2.5 rounded-lg bg-red-50 text-red-600 hover:bg-red-100 transition-colors"
                                        title="Delete Student">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                @empty
                <div class="text-center py-12 bg-white rounded-lg border border-gray-200">
                    <div class="text-gray-400">
                        <i class="fas fa-user-graduate text-5xl mb-4 opacity-50"></i>
                        <p class="text-lg font-medium text-gray-600">No students found</p>
                        @if(request()->hasAny(['search', 'class', 'gender', 'status', 'session']))
                            <p class="text-sm mt-2 text-gray-500">Try adjusting your filters or search terms</p>
                            <a href="{{ route('students.index') }}" @click.prevent="resetFilter()" class="btn btn-primary mt-4 inline-block">
                                <i class="fas fa-redo mr-2"></i>Clear all filters
                            </a>
                        @else
                            <p class="text-sm mt-2 text-gray-500">Get started by adding your first student</p>
                            <a href="{{ route('students.create') }}" class="btn btn-primary mt-4 inline-block">
                                <i class="fas fa-plus mr-2"></i>Add Student
                            </a>
                        @endif
                    </div>
                </div>
                @endforelse
            </div>
        </div>

        @if($students->hasPages())
        <div class="card-footer">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <p class="text-sm text-gray-700">
                        Showing <span class="font-semibold text-gray-900">{{ $students->firstItem() }}</span> to
                        <span class="font-semibold text-gray-900">{{ $students->lastItem() }}</span> of
                        <span class="font-semibold text-gray-900">{{ number_format($students->total()) }}</span> results
                    </p>
                </div>
                <div class="flex justify-center md:justify-end">
                    {{ $students->links() }}
                </div>
            </div>
        </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
function studentManagement() {
    return {
        selectedStudents: [],
        showBulkActions: false,

        toggleStudent(studentId) {
            const index = this.selectedStudents.indexOf(studentId);
            if (index > -1) {
                this.selectedStudents.splice(index, 1);
            } else {
                this.selectedStudents.push(studentId);
            }
            this.showBulkActions = this.selectedStudents.length > 0;
        },

        toggleSelectAll(event) {
            if (event.target.checked) {
                // Select all visible students
                this.selectedStudents = @json($students->pluck('id'));
            } else {
                this.selectedStudents = [];
            }
            this.showBulkActions = this.selectedStudents.length > 0;
        },

        clearSelection() {
            this.selectedStudents = [];
            this.showBulkActions = false;
        },

        applyFilter() {
            // Simply submit the form - no special handling needed
            document.getElementById('filterForm').submit();
        },

        resetFilter() {
            // Clear all filter inputs and submit
            const form = document.getElementById('filterForm');
            form.reset();
            // Redirect to students index
            window.location.href = '{{ route('students.index') }}';
        },
    }
}

// Initialize on page load
function initializeStudentsPage() {
    // This function is called by the SPA when content is loaded
    // Alpine will auto-initialize x-data components
}

// Initial setup on DOMContentLoaded
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initializeStudentsPage);
} else {
    initializeStudentsPage();
}

// Listen for SPA content loaded events
document.addEventListener('spaContentLoaded', initializeStudentsPage);
</script>
@endpush
@endsection
