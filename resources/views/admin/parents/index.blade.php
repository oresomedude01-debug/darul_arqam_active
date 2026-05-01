@extends('layouts.spa')

@section('title', 'Parent Management')

@section('content')
<div class="p-4 sm:p-6">

    {{-- Header --}}
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">Parent Management</h1>
            <p class="mt-1 text-gray-500 text-sm">Manage all parents registered in the system</p>
        </div>
        <a href="{{ route('admin.parents.create') }}"
           class="inline-flex items-center gap-2 bg-purple-600 hover:bg-purple-700 text-white px-5 py-2.5 rounded-xl font-medium text-sm transition shadow-sm self-start sm:self-auto">
            <i class="fas fa-plus"></i> Add Parent
        </a>
    </div>

    {{-- Alerts --}}
    @if($message = session('success'))
    <div class="mb-5 flex items-center gap-3 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-xl text-sm">
        <i class="fas fa-check-circle text-green-500"></i>{{ $message }}
    </div>
    @endif
    @if($message = session('error'))
    <div class="mb-5 flex items-center gap-3 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-xl text-sm">
        <i class="fas fa-exclamation-circle text-red-500"></i>{{ $message }}
    </div>
    @endif

    {{-- Search --}}
    <div class="mb-5">
        <form action="{{ route('admin.parents.index') }}" method="GET" class="flex gap-2">
            <div class="relative flex-1">
                <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Search by name or email…"
                       class="w-full pl-9 pr-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-purple-400 focus:border-purple-400 transition">
            </div>
            <button type="submit" class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2.5 rounded-xl text-sm font-medium transition">
                Search
            </button>
            @if(request('search'))
            <a href="{{ route('admin.parents.index') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2.5 rounded-xl text-sm font-medium transition">
                Clear
            </a>
            @endif
        </form>
    </div>

    {{-- ====== MOBILE / TABLET: Card grid (hidden on lg+) ====== --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 lg:hidden">
        @forelse($parents as $parent)
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4">
            <div class="flex items-start justify-between mb-3">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-purple-100 flex items-center justify-center flex-shrink-0">
                        <span class="text-purple-700 font-bold text-sm">{{ strtoupper(substr($parent->name, 0, 2)) }}</span>
                    </div>
                    <div>
                        <p class="font-semibold text-gray-900 text-sm leading-tight">{{ $parent->name }}</p>
                        <p class="text-xs text-gray-500 mt-0.5">{{ $parent->email }}</p>
                    </div>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-2 text-xs text-gray-600 mb-4">
                <div>
                    <span class="text-gray-400 block mb-0.5">Phone</span>
                    <span class="font-medium">{{ $parent->profile?->phone ?? '—' }}</span>
                </div>
                <div>
                    <span class="text-gray-400 block mb-0.5">Occupation</span>
                    <span class="font-medium">{{ $parent->profile?->occupation ?? '—' }}</span>
                </div>
            </div>
            <div class="flex items-center gap-2 pt-3 border-t border-gray-100">
                <a href="{{ route('admin.parents.show', $parent) }}"
                   class="flex-1 text-center bg-purple-50 hover:bg-purple-100 text-purple-700 py-1.5 rounded-lg text-xs font-medium transition">
                    <i class="fas fa-eye mr-1"></i>View
                </a>
                <a href="{{ route('admin.parents.edit', $parent) }}"
                   class="flex-1 text-center bg-blue-50 hover:bg-blue-100 text-blue-700 py-1.5 rounded-lg text-xs font-medium transition">
                    <i class="fas fa-edit mr-1"></i>Edit
                </a>
                <form action="{{ route('admin.parents.destroy', $parent) }}" method="POST"
                      class="flex-1" onsubmit="return confirm('Delete this parent?');">
                    @csrf @method('DELETE')
                    <button type="submit"
                            class="w-full bg-red-50 hover:bg-red-100 text-red-700 py-1.5 rounded-lg text-xs font-medium transition">
                        <i class="fas fa-trash mr-1"></i>Delete
                    </button>
                </form>
            </div>
        </div>
        @empty
        <div class="col-span-full bg-white rounded-2xl border border-gray-100 px-6 py-12 text-center text-gray-400">
            <i class="fas fa-users text-4xl mb-3 block opacity-30"></i>
            <p class="font-medium">No parents found</p>
        </div>
        @endforelse
    </div>

    {{-- ====== DESKTOP: Table (hidden below lg) ====== --}}
    <div class="hidden lg:block bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <table class="w-full">
            <thead class="bg-gray-50 border-b border-gray-100">
                <tr>
                    <th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Parent</th>
                    <th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Email</th>
                    <th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Phone</th>
                    <th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Occupation</th>
                    <th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($parents as $parent)
                <tr class="hover:bg-gray-50/70 transition">
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-purple-100 flex items-center justify-center flex-shrink-0">
                                <span class="text-purple-700 font-bold text-xs">{{ strtoupper(substr($parent->name, 0, 2)) }}</span>
                            </div>
                            <span class="font-medium text-gray-900 text-sm">{{ $parent->name }}</span>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-600">{{ $parent->email }}</td>
                    <td class="px-6 py-4 text-sm text-gray-600">{{ $parent->profile?->phone ?? '—' }}</td>
                    <td class="px-6 py-4 text-sm text-gray-600">{{ $parent->profile?->occupation ?? '—' }}</td>
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-2">
                            <a href="{{ route('admin.parents.show', $parent) }}"
                               class="p-2 bg-purple-50 hover:bg-purple-100 text-purple-700 rounded-lg text-xs transition" title="View">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('admin.parents.edit', $parent) }}"
                               class="p-2 bg-blue-50 hover:bg-blue-100 text-blue-700 rounded-lg text-xs transition" title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('admin.parents.destroy', $parent) }}" method="POST"
                                  class="inline" onsubmit="return confirm('Delete this parent?');">
                                @csrf @method('DELETE')
                                <button type="submit"
                                        class="p-2 bg-red-50 hover:bg-red-100 text-red-700 rounded-lg text-xs transition" title="Delete">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-12 text-center text-gray-400">
                        <i class="fas fa-users text-4xl mb-3 block opacity-30"></i>
                        <p class="font-medium">No parents found</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    @if($parents->hasPages())
    <div class="mt-5">
        {{ $parents->links() }}
    </div>
    @endif

</div>
@endsection
