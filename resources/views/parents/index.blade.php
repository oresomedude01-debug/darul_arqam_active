@extends('layouts.spa')

@section('content')
<div class="min-h-screen bg-gray-50 px-6 py-8">
    <!-- Breadcrumb -->
    <div class="flex items-center gap-2 text-sm text-gray-600 mb-6">
        <a href="{{ route('dashboard') }}" class="text-primary-600 hover:text-primary-700">Home</a>
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        <span class="text-gray-900 font-medium">Parents Management</span>
    </div>

    <!-- Header -->
    <div class="mb-8 flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Parents & Guardians</h1>
            <p class="text-gray-600 mt-2">Manage parent and guardian accounts</p>
        </div>
        <div>
            <a href="{{ route('admin.parents.create') }}" class="inline-flex items-center px-6 py-3 bg-primary-600 text-white font-medium rounded-lg hover:bg-primary-700 transition-colors gap-2">
                <i class="fas fa-plus"></i>Add Parent
            </a>
        </div>
    </div>

    <!-- Alert Messages -->
    @if ($errors->any())
        <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-lg">
            <p class="text-sm text-red-700 font-medium">{{ $errors->first() }}</p>
        </div>
    @endif

    @if (session('success'))
        <div class="mb-4 p-4 bg-green-50 border border-green-200 rounded-lg">
            <p class="text-sm text-green-700">{{ session('success') }}</p>
        </div>
    @endif

    <!-- Search & Filter Section -->
    <div class="bg-white rounded-lg shadow mb-6 p-4 border-l-4 border-primary-600">
        <div class="flex items-center gap-4 flex-wrap">
            <div class="flex-1 min-w-64">
                <label class="text-sm font-medium text-gray-700 block mb-1">Search Parents</label>
                <form method="GET" action="{{ route('admin.parents.index') }}" class="flex gap-2">
                    <input type="text" name="search" placeholder="Search by name, email, or phone..." 
                        value="{{ request('search') }}"
                        class="flex-1 px-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-primary-500 focus:border-primary-500">
                    <button type="submit" class="px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition-colors text-sm font-medium">
                        <i class="fas fa-search mr-2"></i>Search
                    </button>
                </form>
            </div>
            <div>
                <label class="text-sm font-medium text-gray-700 block mb-1">Filter by Status</label>
                <form method="GET" action="{{ route('admin.parents.index') }}" class="flex gap-2">
                    <input type="hidden" name="search" value="{{ request('search') }}">
                    <select name="status" onchange="this.form.submit()" class="px-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-primary-500 focus:border-primary-500">
                        <option value="">All Status</option>
                        <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </form>
            </div>
        </div>
    </div>

    <!-- Parents Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="w-full">
            <thead class="bg-gray-100 border-b border-gray-200">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-900 uppercase tracking-wider">Name</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-900 uppercase tracking-wider">Email</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-900 uppercase tracking-wider">Phone</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-900 uppercase tracking-wider">Occupation</th>
                    <th class="px-6 py-3 text-center text-xs font-semibold text-gray-900 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-center text-xs font-semibold text-gray-900 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse ($parents as $parent)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">
                            <p class="font-semibold text-gray-900">{{ $parent->first_name }} {{ $parent->last_name }}</p>
                        </td>
                        <td class="px-6 py-4">
                            <p class="text-sm text-gray-600">{{ $parent->user->email }}</p>
                        </td>
                        <td class="px-6 py-4">
                            <p class="text-sm text-gray-600">{{ $parent->phone ?? 'N/A' }}</p>
                        </td>
                        <td class="px-6 py-4">
                            <p class="text-sm text-gray-600">{{ $parent->occupation ?? 'N/A' }}</p>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold {{ $parent->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                {{ ucfirst($parent->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            <div class="flex items-center justify-center gap-2">
                                <a href="{{ route('admin.parents.show', $parent->id) }}" class="text-blue-600 hover:text-blue-900 transition-colors" title="View">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('admin.parents.edit', $parent->id) }}" class="text-primary-600 hover:text-primary-900 transition-colors" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="{{ route('admin.parents.children', $parent->id) }}" class="text-green-600 hover:text-green-900 transition-colors" title="Manage Children">
                                    <i class="fas fa-users"></i>
                                </a>
                                <form action="{{ route('admin.parents.destroy', $parent->id) }}" method="POST" class="inline" onclick="return confirm('Are you sure?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900 transition-colors" title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-8 text-center">
                            <div class="text-gray-500">
                                <i class="fas fa-inbox text-4xl mb-2"></i>
                                <p class="mt-2">No parents found. <a href="{{ route('admin.parents.create') }}" class="text-primary-600 hover:text-primary-700 font-medium">Add a parent</a></p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="mt-6">
        {{ $parents->links() }}
    </div>
</div>
@endsection
