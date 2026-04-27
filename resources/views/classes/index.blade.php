@extends('layouts.modern')
@section('title', 'Classes Directory')

@section('breadcrumb')
    <span class="text-gray-400">Classes</span>
    <span class="text-gray-400">/</span>
    <span class="font-semibold text-gray-900">All Classes</span>
@endsection

@section('content')
<div x-data="classesPage()" class="space-y-6">
    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Classes Directory</h1>
            <p class="text-sm text-gray-600 mt-1">Manage your school classes</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('classes.export', request()->query()) }}" class="btn btn-outline">
                <i class="fas fa-download mr-2"></i>Export CSV
            </a>
            <a href="{{ route('classes.create') }}" class="btn btn-primary">
                <i class="fas fa-plus mr-2"></i>Add Class
            </a>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-2 md:grid-cols-2 lg:grid-cols-4 gap-2 md:gap-4">
        <!-- Total Classes -->
        <div class="card hover:shadow-lg transition-all duration-200">
            <div class="card-body">
                <div class="flex flex-col items-start justify-between gap-2">
                    <div>
                        <p class="text-xs font-semibold text-gray-600 uppercase tracking-wide">Total Classes</p>
                        <p class="text-2xl md:text-3xl font-bold text-gray-900 mt-1">{{ number_format($stats['total']) }}</p>
                    </div>
                    <div class="w-10 h-10 md:w-14 md:h-14 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center shadow-lg">
                        <i class="fas fa-school text-white text-lg md:text-2xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Active Classes -->
        <div class="card hover:shadow-lg transition-all duration-200">
            <div class="card-body">
                <div class="flex flex-col items-start justify-between gap-2">
                    <div>
                        <p class="text-xs font-semibold text-gray-600 uppercase tracking-wide">Active</p>
                        <p class="text-2xl md:text-3xl font-bold text-green-600 mt-1">{{ number_format($stats['active']) }}</p>
                    </div>
                    <div class="w-10 h-10 md:w-14 md:h-14 bg-gradient-to-br from-green-500 to-green-600 rounded-xl flex items-center justify-center shadow-lg">
                        <i class="fas fa-check-circle text-white text-lg md:text-2xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Inactive Classes -->
        <div class="card hover:shadow-lg transition-all duration-200">
            <div class="card-body">
                <div class="flex flex-col items-start justify-between gap-2">
                    <div>
                        <p class="text-xs font-semibold text-gray-600 uppercase tracking-wide">Inactive</p>
                        <p class="text-2xl md:text-3xl font-bold text-gray-600 mt-1">{{ number_format($stats['inactive']) }}</p>
                    </div>
                    <div class="w-10 h-10 md:w-14 md:h-14 bg-gradient-to-br from-gray-500 to-gray-600 rounded-xl flex items-center justify-center shadow-lg">
                        <i class="fas fa-pause-circle text-white text-lg md:text-2xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Full Classes -->
        <div class="card hover:shadow-lg transition-all duration-200">
            <div class="card-body">
                <div class="flex flex-col items-start justify-between gap-2">
                    <div>
                        <p class="text-xs font-semibold text-gray-600 uppercase tracking-wide">Full Classes</p>
                        <p class="text-2xl md:text-3xl font-bold text-red-600 mt-1">{{ number_format($stats['full']) }}</p>
                    </div>
                    <div class="w-10 h-10 md:w-14 md:h-14 bg-gradient-to-br from-red-500 to-red-600 rounded-xl flex items-center justify-center shadow-lg">
                        <i class="fas fa-user-slash text-white text-lg md:text-2xl"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Search & Filters -->
    <div class="card">
        <div class="card-body">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900">Search & Filters</h3>
                <button
                    @click="showFilters = !showFilters"
                    class="btn btn-sm btn-outline lg:hidden"
                >
                    <i class="fas fa-filter mr-2"></i>
                    <span x-text="showFilters ? 'Hide Filters' : 'Show Filters'"></span>
                </button>
            </div>

            <form method="GET" action="{{ route('classes.index') }}" class="space-y-4">
                <!-- Search Bar -->
                <div class="form-group">
                    <div class="relative">
                        <input
                            type="text"
                            name="search"
                            value="{{ request('search') }}"
                            placeholder="Search by class name, code, section, or room number..."
                            class="form-input pl-10"
                        >
                        <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                    </div>
                </div>

                <!-- Filter Options -->
                <div
                    x-show="showFilters"
                    x-transition
                    class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-3 gap-4"
                >
                    <div class="form-group">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            <option value="">All Status</option>
                            <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                            <option value="archived" {{ request('status') === 'archived' ? 'selected' : '' }}>Archived</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Class Teacher</label>
                        <select name="teacher_id" class="form-select">
                            <option value="">All Teachers</option>
                            @foreach($teachers as $teacher)
                                <option value="{{ $teacher->id }}" {{ request('teacher_id') == $teacher->id ? 'selected' : '' }}>
                                    {{ $teacher->full_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex items-center gap-3">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search mr-2"></i>Search
                    </button>
                    <a href="{{ route('classes.index') }}" class="btn btn-outline">
                        <i class="fas fa-times mr-2"></i>Clear Filters
                    </a>
                    <button
                        type="button"
                        @click="toggleView()"
                        class="btn btn-outline ml-auto hidden md:inline-flex"
                    >
                        <i :class="viewMode === 'table' ? 'fas fa-th' : 'fas fa-table'" class="mr-2"></i>
                        <span x-text="viewMode === 'table' ? 'Grid View' : 'Table View'"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Classes List -->
    <div class="card pb-16 md:pb-0">
        <div class="card-header flex items-center justify-between">
            <h3 class="text-lg font-semibold text-gray-900">
                Classes ({{ $classes->total() }})
            </h3>
            <div class="text-sm text-gray-600">
                Showing {{ $classes->firstItem() ?? 0 }} to {{ $classes->lastItem() ?? 0 }} of {{ $classes->total() }}
            </div>
        </div>

        <!-- Desktop Table View -->
        <div x-show="viewMode === 'table'" class="hidden md:block overflow-x-auto">
            <table class="table">
                <thead>
                    <tr>
                        <th>
                            <a href="{{ route('classes.index', array_merge(request()->query(), ['sort' => 'class_code', 'direction' => request('direction') === 'asc' ? 'desc' : 'asc'])) }}" class="sortable">
                                Class Code
                                @if(request('sort') === 'class_code')
                                    <i class="fas fa-sort-{{ request('direction') === 'asc' ? 'up' : 'down' }} ml-1"></i>
                                @endif
                            </a>
                        </th>
                        <th>
                            <a href="{{ route('classes.index', array_merge(request()->query(), ['sort' => 'name', 'direction' => request('direction') === 'asc' ? 'desc' : 'asc'])) }}" class="sortable">
                                Class Name
                                @if(request('sort') === 'name')
                                    <i class="fas fa-sort-{{ request('direction') === 'asc' ? 'up' : 'down' }} ml-1"></i>
                                @endif
                            </a>
                        </th>
                        <th>Class Teacher</th>
                        <th>
                            <a href="{{ route('classes.index', array_merge(request()->query(), ['sort' => 'capacity', 'direction' => request('direction') === 'asc' ? 'desc' : 'asc'])) }}" class="sortable">
                                Capacity
                                @if(request('sort') === 'capacity')
                                    <i class="fas fa-sort-{{ request('direction') === 'asc' ? 'up' : 'down' }} ml-1"></i>
                                @endif
                            </a>
                        </th>
                        <th>
                            <a href="{{ route('classes.index', array_merge(request()->query(), ['sort' => 'status', 'direction' => request('direction') === 'asc' ? 'desc' : 'asc'])) }}" class="sortable">
                                Status
                                @if(request('sort') === 'status')
                                    <i class="fas fa-sort-{{ request('direction') === 'asc' ? 'up' : 'down' }} ml-1"></i>
                                @endif
                            </a>
                        </th>
                        <th class="text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($classes as $class)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="font-mono text-sm font-semibold">{{ $class->class_code }}</td>
                        <td>
                            <div>
                                <div class="font-semibold text-gray-900">{{ $class->full_name }}</div>
                                @if($class->room_number)
                                    <div class="text-sm text-gray-600">
                                        <i class="fas fa-door-open mr-1"></i>Room {{ $class->room_number }}
                                    </div>
                                @endif
                            </div>
                        </td>
                        <td>
                            @if($class->teacher && $class->teacher->profile)
                                <div class="flex items-center space-x-2">
                                    <div class="w-8 h-8 rounded-full bg-gradient-to-br from-primary-500 to-primary-600 flex items-center justify-center text-white text-xs font-semibold">
                                        {{ substr($class->teacher->profile->first_name, 0, 1) }}{{ substr($class->teacher->profile->last_name, 0, 1) }}
                                    </div>
                                    <span class="text-sm">{{ $class->teacher->profile->first_name }} {{ $class->teacher->profile->last_name }}</span>
                                </div>
                            @else
                                <span class="text-gray-400 text-sm italic">Not assigned</span>
                            @endif
                        </td>
                        <td>
                            <div class="flex items-center gap-2">
                                <div class="flex-1">
                                    <div class="flex items-center justify-between text-sm mb-1">
                                        <span class="font-semibold">{{ $class->current_enrollment }}/{{ $class->capacity }}</span>
                                        <span class="text-gray-600">{{ $class->enrollment_percentage }}%</span>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-2">
                                        <div
                                            class="h-2 rounded-full transition-all duration-300 {{ $class->is_full ? 'bg-red-500' : ($class->enrollment_percentage >= 80 ? 'bg-yellow-500' : 'bg-green-500') }}"
                                            style="width: {{ min($class->enrollment_percentage, 100) }}%"
                                        ></div>
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td>
                            @if($class->status === 'active')
                                <span class="badge badge-success">Active</span>
                            @elseif($class->status === 'archived')
                                <span class="badge badge-secondary">Archived</span>
                            @else
                                <span class="badge badge-warning">Inactive</span>
                            @endif
                        </td>
                        <td class="text-right">
                            <div class="inline-flex items-center gap-2">
                                <a href="{{ route('classes.show', $class) }}" class="btn btn-sm btn-outline" title="View">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('classes.edit', $class) }}" class="btn btn-sm btn-primary" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('classes.destroy', $class) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this class?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-12">
                            <div class="flex flex-col items-center justify-center text-gray-500">
                                <i class="fas fa-school text-6xl mb-4 text-gray-300"></i>
                                <p class="text-lg font-semibold">No classes found</p>
                                <p class="text-sm mt-1">Try adjusting your search or filters</p>
                                <a href="{{ route('classes.create') }}" class="btn btn-primary mt-4">
                                    <i class="fas fa-plus mr-2"></i>Add First Class
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Mobile Card View -->
        <div x-show="viewMode === 'grid' || window.innerWidth < 768" class="md:hidden p-4 space-y-4">
            @forelse($classes as $class)
            <div class="table-card hover:shadow-lg transition-all duration-200">
                <div class="flex items-start justify-between mb-3">
                    <div>
                        <h4 class="font-bold text-gray-900 text-lg">{{ $class->full_name }}</h4>
                        <p class="text-sm text-gray-600 font-mono">{{ $class->class_code }}</p>
                    </div>
                    @if($class->status === 'active')
                        <span class="badge badge-success">Active</span>
                    @elseif($class->status === 'archived')
                        <span class="badge badge-secondary">Archived</span>
                    @else
                        <span class="badge badge-warning">Inactive</span>
                    @endif
                </div>

                <div class="space-y-3">
                    @if($class->teacher && $class->teacher->profile)
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 rounded-full bg-gradient-to-br from-primary-500 to-primary-600 flex items-center justify-center text-white font-semibold">
                            {{ substr($class->teacher->profile->first_name, 0, 1) }}{{ substr($class->teacher->profile->last_name, 0, 1) }}
                        </div>
                        <div>
                            <p class="text-xs text-gray-500">Class Teacher</p>
                            <p class="font-medium text-gray-900">{{ $class->teacher->profile->first_name }} {{ $class->teacher->profile->last_name }}</p>
                        </div>
                    </div>
                    @endif

                    @if($class->room_number)
                    <p class="text-sm text-gray-600">
                        <i class="fas fa-door-open mr-2 text-gray-400"></i>Room {{ $class->room_number }}
                    </p>
                    @endif

                    <!-- Capacity Progress -->
                    <div>
                        <div class="flex items-center justify-between text-sm mb-1">
                            <span class="text-gray-600">Enrollment</span>
                            <span class="font-semibold">{{ $class->current_enrollment }}/{{ $class->capacity }} ({{ $class->enrollment_percentage }}%)</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-3">
                            <div
                                class="h-3 rounded-full transition-all duration-300 {{ $class->is_full ? 'bg-red-500' : ($class->enrollment_percentage >= 80 ? 'bg-yellow-500' : 'bg-green-500') }}"
                                style="width: {{ min($class->enrollment_percentage, 100) }}%"
                            ></div>
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="mt-4 flex flex-wrap gap-2">
                    <a href="{{ route('classes.show', $class) }}" class="btn btn-sm btn-outline flex-1">
                        <i class="fas fa-eye mr-1"></i>View
                    </a>
                    <a href="{{ route('classes.edit', $class) }}" class="btn btn-sm btn-primary flex-1">
                        <i class="fas fa-edit mr-1"></i>Edit
                    </a>
                </div>
            </div>
            @empty
            <div class="text-center py-12">
                <div class="flex flex-col items-center justify-center text-gray-500">
                    <i class="fas fa-school text-6xl mb-4 text-gray-300"></i>
                    <p class="text-lg font-semibold">No classes found</p>
                    <p class="text-sm mt-1">Try adjusting your search or filters</p>
                    <a href="{{ route('classes.create') }}" class="btn btn-primary mt-4">
                        <i class="fas fa-plus mr-2"></i>Add First Class
                    </a>
                </div>
            </div>
            @endforelse
        </div>

        <!-- Pagination -->
        @if($classes->hasPages())
        <div class="card-footer">
            {{ $classes->links() }}
        </div>
        @endif
    </div>
</div>

@endsection

@push('scripts')
<script>
    function classesPage() {
        return {
            viewMode: window.innerWidth >= 768 ? 'table' : 'grid',
            showFilters: window.innerWidth >= 1024,

            toggleView() {
                this.viewMode = this.viewMode === 'table' ? 'grid' : 'table';
            },

            init() {
                // Adjust view mode on resize
                window.addEventListener('resize', () => {
                    if (window.innerWidth < 768) {
                        this.viewMode = 'grid';
                    }
                });
            },

            navigate(url) {
                // Get the root Alpine app (the SPA app)
                const spaApp = document.body.__x;
                if (spaApp && spaApp.$data && typeof spaApp.$data.navigate === 'function') {
                    spaApp.$data.navigate(url);
                } else {
                    window.location.href = url;
                }
            }
        };
    }

    // Initialize on page load
    function initializeClassesPage() {
        // This function is called by the SPA when content is loaded
        // Alpine will auto-initialize x-data components
    }

    // Initial setup on DOMContentLoaded
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initializeClassesPage);
    } else {
        initializeClassesPage();
    }
    
    // Listen for SPA content loaded events
    document.addEventListener('spaContentLoaded', initializeClassesPage);
</script>
@endpush
