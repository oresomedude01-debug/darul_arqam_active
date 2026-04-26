@extends('layouts.spa')

@section('title', 'Subjects')

@section('breadcrumb')
    <span class="text-gray-400">Academics</span>
    <span class="text-gray-400">/</span>
    <span class="font-semibold text-gray-900">Subjects</span>
@endsection

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Subjects</h1>
            <p class="text-sm text-gray-600 mt-1">Manage academic subjects and their settings</p>
        </div>
        <a href="{{ route('subjects.create') }}" class="btn btn-primary">
            <i class="fas fa-plus mr-2"></i>Add Subject
        </a>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="card">
            <div class="card-body">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Total Subjects</p>
                        <p class="text-3xl font-bold text-gray-900 mt-2">{{ $stats['total'] }}</p>
                    </div>
                    <div class="w-12 h-12 bg-primary-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-book text-primary-600 text-xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Active</p>
                        <p class="text-3xl font-bold text-green-600 mt-2">{{ $stats['active'] }}</p>
                    </div>
                    <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-check-circle text-green-600 text-xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Inactive</p>
                        <p class="text-3xl font-bold text-gray-600 mt-2">{{ $stats['inactive'] }}</p>
                    </div>
                    <div class="w-12 h-12 bg-gray-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-times-circle text-gray-600 text-xl"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Search and Filters -->
    <div class="card">
        <div class="card-body">
            <form method="GET" action="{{ route('subjects.index') }}" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <!-- Search -->
                    <div class="form-group">
                        <input
                            type="text"
                            name="search"
                            value="{{ request('search') }}"
                            placeholder="Search subjects..."
                            class="form-input"
                        >
                    </div>

                    <!-- Category Filter -->
                    <div class="form-group">
                        <select name="category" class="form-select">
                            <option value="">All Categories</option>
                            @foreach($categories as $category)
                                <option value="{{ $category }}" {{ request('category') === $category ? 'selected' : '' }}>
                                    {{ $category }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Status Filter -->
                    <div class="form-group">
                        <select name="status" class="form-select">
                            <option value="">All Status</option>
                            <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>

                    <!-- Actions -->
                    <div class="flex items-center gap-2">
                        <button type="submit" class="btn btn-primary flex-1">
                            <i class="fas fa-search mr-2"></i>Filter
                        </button>
                        <a href="{{ route('subjects.index') }}" class="btn btn-outline">
                            <i class="fas fa-redo"></i>
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Subjects List -->
    <div class="card">
        <div class="card-header">
            <h3 class="text-lg font-semibold text-gray-900">
                All Subjects ({{ $subjects->total() }})
            </h3>
        </div>
        <div class="card-body p-0">
            <!-- Desktop Table View -->
            <div class="hidden md:block overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <a href="{{ route('subjects.index', array_merge(request()->all(), ['sort' => 'name', 'direction' => request('sort') === 'name' && request('direction') === 'asc' ? 'desc' : 'asc'])) }}" class="flex items-center gap-1 hover:text-gray-700">
                                    Subject
                                    @if(request('sort') === 'name')
                                        <i class="fas fa-sort-{{ request('direction') === 'asc' ? 'up' : 'down' }}"></i>
                                    @endif
                                </a>
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <a href="{{ route('subjects.index', array_merge(request()->all(), ['sort' => 'code', 'direction' => request('sort') === 'code' && request('direction') === 'asc' ? 'desc' : 'asc'])) }}" class="flex items-center gap-1 hover:text-gray-700">
                                    Code
                                    @if(request('sort') === 'code')
                                        <i class="fas fa-sort-{{ request('direction') === 'asc' ? 'up' : 'down' }}"></i>
                                    @endif
                                </a>
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <a href="{{ route('subjects.index', array_merge(request()->all(), ['sort' => 'category', 'direction' => request('sort') === 'category' && request('direction') === 'asc' ? 'desc' : 'asc'])) }}" class="flex items-center gap-1 hover:text-gray-700">
                                    Category
                                    @if(request('sort') === 'category')
                                        <i class="fas fa-sort-{{ request('direction') === 'asc' ? 'up' : 'down' }}"></i>
                                    @endif
                                </a>
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Classes</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($subjects as $subject)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 rounded-lg bg-{{ $subject->color }}-100 flex items-center justify-center mr-3">
                                        <i class="fas fa-book text-{{ $subject->color }}-600"></i>
                                    </div>
                                    <div>
                                        <div class="text-sm font-medium text-gray-900">{{ $subject->name }}</div>
                                        @if($subject->description)
                                            <div class="text-sm text-gray-500">{{ Str::limit($subject->description, 40) }}</div>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="font-mono text-sm font-semibold text-gray-700">{{ $subject->code }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($subject->category)
                                    <span class="badge badge-secondary">{{ $subject->category }}</span>
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="text-sm text-gray-900">{{ $subject->classes->count() }} classes</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($subject->is_active)
                                    <span class="badge badge-success">Active</span>
                                @else
                                    <span class="badge badge-secondary">Inactive</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('subjects.show', $subject) }}" class="text-primary-600 hover:text-primary-900">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('subjects.edit', $subject) }}" class="text-yellow-600 hover:text-yellow-900">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('subjects.destroy', $subject) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this subject?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                                <i class="fas fa-book text-4xl text-gray-300 mb-3"></i>
                                <p>No subjects found</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Mobile Card View -->
            <div class="md:hidden space-y-4 p-4">
                @forelse($subjects as $subject)
                <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                    <div class="flex items-start justify-between mb-3">
                        <div class="flex items-center flex-1">
                            <div class="w-10 h-10 rounded-lg bg-{{ $subject->color }}-100 flex items-center justify-center mr-3">
                                <i class="fas fa-book text-{{ $subject->color }}-600"></i>
                            </div>
                            <div>
                                <h4 class="font-semibold text-gray-900">{{ $subject->name }}</h4>
                                <p class="text-sm text-gray-600 font-mono">{{ $subject->code }}</p>
                            </div>
                        </div>
                        @if($subject->is_active)
                            <span class="badge badge-success">Active</span>
                        @else
                            <span class="badge badge-secondary">Inactive</span>
                        @endif
                    </div>

                    <div class="grid grid-cols-2 gap-2 text-sm mb-3">
                        <div>
                            <span class="text-gray-600">Category:</span>
                            <span class="font-medium text-gray-900">{{ $subject->category ?? '-' }}</span>
                        </div>
                        <div>
                            <span class="text-gray-600">Classes:</span>
                            <span class="font-medium text-gray-900">{{ $subject->classes->count() }}</span>
                        </div>
                    </div>

                    <div class="flex items-center gap-2">
                        <a href="{{ route('subjects.show', $subject) }}" class="btn btn-xs btn-outline flex-1">
                            <i class="fas fa-eye mr-1"></i>View
                        </a>
                        <a href="{{ route('subjects.edit', $subject) }}" class="btn btn-xs btn-primary">
                            <i class="fas fa-edit mr-1"></i>Edit
                        </a>
                        <form action="{{ route('subjects.destroy', $subject) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-xs btn-danger">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </div>
                </div>
                @empty
                <div class="text-center py-12 text-gray-500">
                    <i class="fas fa-book text-4xl text-gray-300 mb-3"></i>
                    <p>No subjects found</p>
                </div>
                @endforelse
            </div>
        </div>

        <!-- Pagination -->
        @if($subjects->hasPages())
        <div class="card-footer">
            {{ $subjects->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
