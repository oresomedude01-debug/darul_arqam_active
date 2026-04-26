@extends('layouts.spa')

@section('title', 'Timetables')

@section('breadcrumb')
    <span class="text-gray-400">School Management</span>
    <span class="text-gray-400">/</span>
    <span class="font-semibold text-gray-900">Timetables</span>
@endsection

@section('content')
<div class="space-y-6" x-data="{ searchQuery: '', filteredClasses: [] }">
    <!-- Hero Header Section -->
    <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-xl shadow-sm border border-blue-100 p-8">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-6">
            <div>
                <h1 class="text-4xl font-bold text-gray-900 mb-2">School Timetables</h1>
                <p class="text-gray-600 text-lg flex items-center gap-2">
                    <i class="fas fa-calendar-alt text-blue-600"></i>
                    Manage class timetables
                </p>
            </div>
        </div>
    </div>

    @if($classes->isEmpty())
        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-6 text-center">
            <i class="fas fa-info-circle text-yellow-600 text-3xl mb-3"></i>
            <p class="text-gray-700 font-semibold">No classes found</p>
            <p class="text-gray-500 mt-2">Create classes first to manage their timetables.</p>
        </div>
    @else
        <!-- Search Bar -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <div class="flex items-center gap-3">
                <i class="fas fa-search text-gray-400 text-lg"></i>
                <input type="text" 
                       x-model="searchQuery"
                       placeholder="Search classes by name or teacher..." 
                       class="flex-1 outline-none text-gray-900 placeholder-gray-500"
                       autocomplete="off">
                <button @click="searchQuery = ''" x-show="searchQuery" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>

        <!-- Classes Table -->
        <div class="card shadow-sm border border-gray-200">
            <div class="card-header">
                <h3 class="text-lg font-semibold text-gray-900">
                    <i class="fas fa-list mr-2 text-blue-600"></i>Classes Timetables
                    <span class="text-sm text-gray-500 font-normal ml-2">
                        (<span x-text="$el.parentElement.parentElement.querySelector('tbody tr:not([x-show=\"false\"])') ? Array.from($el.parentElement.parentElement.querySelector('tbody').querySelectorAll('tr')).filter(tr => !tr.hasAttribute('x-show') || tr.getAttribute('x-show') !== 'false').length : 0"></span> of {{ $classes->count() }})
                    </span>
                </h3>
            </div>
            <div class="card-body p-0">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Class Name</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Class Teacher</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Periods</th>
                                <th class="px-6 py-3 text-center text-xs font-semibold text-gray-600 uppercase">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach($classes as $class)
                            <tr class="hover:bg-gray-50 transition-colors" 
                                x-show="!searchQuery || '{{ strtolower($class->name) }}'.includes(searchQuery.toLowerCase()) || '{{ strtolower($class->full_name ?? '') }}'.includes(searchQuery.toLowerCase()) || '{{ strtolower($class->teacher?->full_name ?? '') }}'.includes(searchQuery.toLowerCase())">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                                            <i class="fas fa-door-open text-blue-600"></i>
                                        </div>
                                        <div>
                                            <p class="text-sm font-semibold text-gray-900">{{ $class->name }}</p>
                                            <p class="text-xs text-gray-500">{{ $class->full_name ?? $class->name }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-2">
                                        @if($class->teacher)
                                            <div class="w-8 h-8 bg-purple-100 rounded-full flex items-center justify-center">
                                                <i class="fas fa-user text-purple-600 text-xs"></i>
                                            </div>
                                            <div>
                                                <p class="text-sm font-medium text-gray-900">{{ $class->teacher->full_name }}</p>
                                            </div>
                                        @else
                                            <span class="inline-block px-3 py-1 bg-yellow-100 text-yellow-700 text-xs font-semibold rounded-full">
                                                Not Assigned
                                            </span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-2">
                                        <span class="inline-block px-3 py-1 bg-blue-100 text-blue-700 text-xs font-semibold rounded-full">
                                            {{ $class->timetables->count() }} periods
                                        </span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <a href="{{ route('classes.timetable.index', $class) }}" 
                                       class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition text-sm font-semibold">
                                        <i class="fas fa-pen"></i>
                                        Manage
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <!-- No Results Message -->
                    <div class="text-center py-8" x-show="searchQuery && Array.from(document.querySelectorAll('tbody tr')).every(tr => tr.style.display === 'none')">
                        <i class="fas fa-search text-gray-400 text-3xl mb-3 block"></i>
                        <p class="text-gray-600 font-medium">No classes found matching "<span x-text="searchQuery"></span>"</p>
                        <p class="text-gray-500 text-sm mt-1">Try searching with a different class name or teacher</p>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection
