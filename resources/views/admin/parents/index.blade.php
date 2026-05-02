@extends('layouts.spa')
@section('title', 'Parent Management')

@section('breadcrumb')
    <span class="text-gray-400">Admin</span>
    <span class="text-gray-400">/</span>
    <span class="font-semibold text-gray-900">Parents</span>
@endsection

@section('content')
<div x-data="parentsPage()" class="space-y-6">

    {{-- Page Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Parent Management</h1>
            <p class="text-sm text-gray-600 mt-1">Manage all registered parents</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.parents.create') }}" class="btn btn-primary">
                <i class="fas fa-plus mr-2"></i>Add Parent
            </a>
        </div>
    </div>

    {{-- Alerts --}}
    @if($message = session('success'))
    <div class="flex items-center gap-3 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-xl text-sm">
        <i class="fas fa-check-circle text-green-500 flex-shrink-0"></i>{{ $message }}
    </div>
    @endif
    @if($message = session('error'))
    <div class="flex items-center gap-3 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-xl text-sm">
        <i class="fas fa-exclamation-circle text-red-500 flex-shrink-0"></i>{{ $message }}
    </div>
    @endif

    {{-- Stats Cards --}}
    <div class="grid grid-cols-2 md:grid-cols-2 lg:grid-cols-4 gap-2 md:gap-4">
        <div class="card hover:shadow-lg transition-all duration-200">
            <div class="card-body">
                <div class="flex flex-col items-start justify-between gap-2">
                    <div>
                        <p class="text-xs font-semibold text-gray-600 uppercase tracking-wide">Total Parents</p>
                        <p class="text-2xl md:text-3xl font-bold text-gray-900 mt-1">{{ number_format($stats['total']) }}</p>
                    </div>
                    <div class="w-10 h-10 md:w-14 md:h-14 bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl flex items-center justify-center shadow-lg">
                        <i class="fas fa-user-friends text-white text-lg md:text-2xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="card hover:shadow-lg transition-all duration-200">
            <div class="card-body">
                <div class="flex flex-col items-start justify-between gap-2">
                    <div>
                        <p class="text-xs font-semibold text-gray-600 uppercase tracking-wide">With Children</p>
                        <p class="text-2xl md:text-3xl font-bold text-green-600 mt-1">{{ number_format($stats['with_children']) }}</p>
                    </div>
                    <div class="w-10 h-10 md:w-14 md:h-14 bg-gradient-to-br from-green-500 to-green-600 rounded-xl flex items-center justify-center shadow-lg">
                        <i class="fas fa-child text-white text-lg md:text-2xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="card hover:shadow-lg transition-all duration-200">
            <div class="card-body">
                <div class="flex flex-col items-start justify-between gap-2">
                    <div>
                        <p class="text-xs font-semibold text-gray-600 uppercase tracking-wide">No Children</p>
                        <p class="text-2xl md:text-3xl font-bold text-amber-600 mt-1">{{ number_format($stats['without_children']) }}</p>
                    </div>
                    <div class="w-10 h-10 md:w-14 md:h-14 bg-gradient-to-br from-amber-400 to-amber-500 rounded-xl flex items-center justify-center shadow-lg">
                        <i class="fas fa-user-slash text-white text-lg md:text-2xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="card hover:shadow-lg transition-all duration-200">
            <div class="card-body">
                <div class="flex flex-col items-start justify-between gap-2">
                    <div>
                        <p class="text-xs font-semibold text-gray-600 uppercase tracking-wide">With Occupation</p>
                        <p class="text-2xl md:text-3xl font-bold text-blue-600 mt-1">{{ number_format($stats['with_occupation']) }}</p>
                    </div>
                    <div class="w-10 h-10 md:w-14 md:h-14 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center shadow-lg">
                        <i class="fas fa-briefcase text-white text-lg md:text-2xl"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Search & Filters --}}
    <div class="card">
        <div class="card-body">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900">Search & Filters</h3>
                <button @click="showFilters = !showFilters" class="btn btn-sm btn-outline lg:hidden">
                    <i class="fas fa-filter mr-2"></i>
                    <span x-text="showFilters ? 'Hide Filters' : 'Show Filters'"></span>
                </button>
            </div>

            <form method="GET" action="{{ route('admin.parents.index') }}" class="space-y-4">
                <div class="form-group">
                    <div class="relative">
                        <input type="text" name="search" value="{{ request('search') }}"
                               placeholder="Search by name or email…"
                               class="form-input pl-10">
                        <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search mr-2"></i>Search
                    </button>
                    <a href="{{ route('admin.parents.index') }}" class="btn btn-outline">
                        <i class="fas fa-times mr-2"></i>Clear
                    </a>
                    <button type="button" @click="toggleView()"
                            class="btn btn-outline ml-auto hidden md:inline-flex">
                        <i :class="viewMode === 'table' ? 'fas fa-th' : 'fas fa-table'" class="mr-2"></i>
                        <span x-text="viewMode === 'table' ? 'Grid View' : 'Table View'"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Parents List --}}
    <div class="card pb-16 md:pb-0">
        <div class="card-header flex items-center justify-between">
            <h3 class="text-lg font-semibold text-gray-900">
                Parents ({{ $parents->total() }})
            </h3>
            <div class="text-sm text-gray-600">
                Showing {{ $parents->firstItem() ?? 0 }} – {{ $parents->lastItem() ?? 0 }} of {{ $parents->total() }}
            </div>
        </div>

        {{-- Desktop Table View --}}
        <div x-show="viewMode === 'table'" class="hidden md:block overflow-x-auto">
            <table class="table">
                <thead>
                    <tr>
                        <th>Parent</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Occupation</th>
                        <th>Children</th>
                        <th class="text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($parents as $parent)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td>
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-full bg-gradient-to-br from-purple-500 to-purple-600 flex items-center justify-center text-white text-xs font-bold flex-shrink-0">
                                    {{ strtoupper(substr($parent->name, 0, 2)) }}
                                </div>
                                <span class="font-semibold text-gray-900">{{ $parent->name }}</span>
                            </div>
                        </td>
                        <td class="text-sm text-gray-600">{{ $parent->email }}</td>
                        <td class="text-sm text-gray-600">{{ $parent->profile?->phone ?? '—' }}</td>
                        <td class="text-sm text-gray-600">{{ $parent->profile?->occupation ?? '—' }}</td>
                        <td>
                            @php $childCount = $parent->children()->count(); @endphp
                            @if($childCount > 0)
                                <span class="badge badge-success">{{ $childCount }} {{ Str::plural('child', $childCount) }}</span>
                            @else
                                <span class="badge badge-warning">None</span>
                            @endif
                        </td>
                        <td class="text-right">
                            <div class="inline-flex items-center gap-2">
                                <a href="{{ route('admin.parents.show', $parent) }}" class="btn btn-sm btn-outline" title="View">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('admin.parents.edit', $parent) }}" class="btn btn-sm btn-primary" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('admin.parents.destroy', $parent) }}" method="POST"
                                      class="inline" onsubmit="return confirm('Delete this parent?')">
                                    @csrf @method('DELETE')
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
                                <i class="fas fa-user-friends text-6xl mb-4 text-gray-300"></i>
                                <p class="text-lg font-semibold">No parents found</p>
                                <p class="text-sm mt-1">Try adjusting your search</p>
                                <a href="{{ route('admin.parents.create') }}" class="btn btn-primary mt-4">
                                    <i class="fas fa-plus mr-2"></i>Add First Parent
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Mobile / Grid Card View --}}
        <div x-show="viewMode === 'grid' || window.innerWidth < 768" class="md:hidden p-4 space-y-4">
            @forelse($parents as $parent)
            <div class="table-card hover:shadow-lg transition-all duration-200">
                <div class="flex items-start justify-between mb-3">
                    <div class="flex items-center gap-3">
                        <div class="w-11 h-11 rounded-full bg-gradient-to-br from-purple-500 to-purple-600 flex items-center justify-center text-white font-bold flex-shrink-0">
                            {{ strtoupper(substr($parent->name, 0, 2)) }}
                        </div>
                        <div>
                            <h4 class="font-bold text-gray-900">{{ $parent->name }}</h4>
                            <p class="text-sm text-gray-500">{{ $parent->email }}</p>
                        </div>
                    </div>
                    @php $childCount = $parent->children()->count(); @endphp
                    @if($childCount > 0)
                        <span class="badge badge-success">{{ $childCount }} {{ Str::plural('child', $childCount) }}</span>
                    @else
                        <span class="badge badge-warning">No children</span>
                    @endif
                </div>

                <div class="space-y-2 text-sm text-gray-600">
                    @if($parent->profile?->phone)
                    <p><i class="fas fa-phone mr-2 text-gray-400 w-4"></i>{{ $parent->profile->phone }}</p>
                    @endif
                    @if($parent->profile?->occupation)
                    <p><i class="fas fa-briefcase mr-2 text-gray-400 w-4"></i>{{ $parent->profile->occupation }}</p>
                    @endif
                </div>

                <div class="mt-4 flex flex-wrap gap-2">
                    <a href="{{ route('admin.parents.show', $parent) }}" class="btn btn-sm btn-outline flex-1">
                        <i class="fas fa-eye mr-1"></i>View
                    </a>
                    <a href="{{ route('admin.parents.edit', $parent) }}" class="btn btn-sm btn-primary flex-1">
                        <i class="fas fa-edit mr-1"></i>Edit
                    </a>
                </div>
            </div>
            @empty
            <div class="text-center py-12">
                <div class="flex flex-col items-center justify-center text-gray-500">
                    <i class="fas fa-user-friends text-6xl mb-4 text-gray-300"></i>
                    <p class="text-lg font-semibold">No parents found</p>
                    <a href="{{ route('admin.parents.create') }}" class="btn btn-primary mt-4">
                        <i class="fas fa-plus mr-2"></i>Add First Parent
                    </a>
                </div>
            </div>
            @endforelse
        </div>

        {{-- Pagination --}}
        @if($parents->hasPages())
        <div class="card-footer">
            {{ $parents->links() }}
        </div>
        @endif
    </div>

</div>
@endsection

@push('scripts')
<script>
    function parentsPage() {
        return {
            viewMode: window.innerWidth >= 768 ? 'table' : 'grid',
            showFilters: window.innerWidth >= 1024,

            toggleView() {
                this.viewMode = this.viewMode === 'table' ? 'grid' : 'table';
            },

            init() {
                window.addEventListener('resize', () => {
                    if (window.innerWidth < 768) this.viewMode = 'grid';
                });
            },
        };
    }
</script>
@endpush
