@extends('layouts.spa')

@section('title', 'Parent Management')

@section('content')
<div class="p-6">
    <!-- Header -->
    <div class="mb-8 flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Parent Management</h1>
            <p class="mt-2 text-gray-600">Manage all parents in the system</p>
        </div>
        <a href="{{ route('admin.parents.create') }}" class="bg-purple-600 hover:bg-purple-700 text-white px-6 py-3 rounded-lg transition">
            <i class="fas fa-plus mr-2"></i>Add Parent
        </a>
    </div>

    <!-- Alerts -->
    @if($message = session('success'))
    <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-4 rounded-lg">
        <i class="fas fa-check-circle mr-2"></i>{{ $message }}
    </div>
    @endif

    @if($message = session('error'))
    <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-4 rounded-lg">
        <i class="fas fa-exclamation-circle mr-2"></i>{{ $message }}
    </div>
    @endif

    <!-- Search Bar -->
    <div class="mb-6">
        <form action="{{ route('admin.parents.index') }}" method="GET" class="flex gap-3">
            <input type="text" 
                   name="search" 
                   value="{{ request('search') }}" 
                   placeholder="Search by name or email..."
                   class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500">
            <button type="submit" class="bg-purple-600 hover:bg-purple-700 text-white px-6 py-2 rounded-lg transition">
                <i class="fas fa-search mr-2"></i>Search
            </button>
            @if(request('search'))
            <a href="{{ route('admin.parents.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-900 px-6 py-2 rounded-lg transition">
                <i class="fas fa-times mr-2"></i>Clear
            </a>
            @endif
        </form>
    </div>

    <!-- Parents Table -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <table class="w-full">
            <thead class="bg-gray-100 border-b">
                <tr>
                    <th class="px-6 py-4 text-left text-sm font-semibold text-gray-900">Parent Name</th>
                    <th class="px-6 py-4 text-left text-sm font-semibold text-gray-900">Email</th>
                    <th class="px-6 py-4 text-left text-sm font-semibold text-gray-900">Phone</th>
                    <th class="px-6 py-4 text-left text-sm font-semibold text-gray-900">Occupation</th>
                    <th class="px-6 py-4 text-left text-sm font-semibold text-gray-900">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($parents as $parent)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-6 py-4">
                        <div class="font-medium text-gray-900">{{ $parent->name }}</div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="text-gray-600 text-sm">{{ $parent->email }}</div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="text-gray-600 text-sm">
                            {{ $parent->profile?->phone ?? '-' }}
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="text-gray-600 text-sm">
                            {{ $parent->profile?->occupation ?? '-' }}
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex gap-2">
                            <a href="{{ route('admin.parents.show', $parent) }}" class="bg-purple-500 hover:bg-purple-600 text-white px-3 py-2 rounded text-sm transition">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('admin.parents.edit', $parent) }}" class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-2 rounded text-sm transition">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('admin.parents.destroy', $parent) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="bg-red-500 hover:bg-red-600 text-white px-3 py-2 rounded text-sm transition">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                        <i class="fas fa-inbox text-4xl mb-2 block opacity-50"></i>
                        No parents found
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    @if($parents->hasPages())
    <div class="mt-6">
        {{ $parents->links() }}
    </div>
    @endif
</div>
@endsection
