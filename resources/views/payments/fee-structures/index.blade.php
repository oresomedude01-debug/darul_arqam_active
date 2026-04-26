@extends('layouts.spa')

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Fee Structures</h1>
            <p class="text-gray-600 mt-1">Create and manage fee structures for academic sessions</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('billing.fee-structures.create') }}" class="bg-primary-600 text-white px-4 py-2 rounded-lg hover:bg-primary-700 transition flex items-center gap-2">
                <i class="fas fa-plus"></i> New Fee Structure
            </a>
        </div>
    </div>

    @if(session('success'))
    <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg">
        {{ session('success') }}
    </div>
    @endif

    <!-- Fee Structures Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Session</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Description</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($feeStructures as $structure)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">
                            <div class="text-sm font-medium text-gray-900">{{ $structure->name }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-600">{{ $structure->academicSession->session }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-600">{{ Str::limit($structure->description, 50) }}</div>
                        </td>
                        <td class="px-6 py-4">
                            @if($structure->is_active)
                            <span class="px-3 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800">Active</span>
                            @else
                            <span class="px-3 py-1 text-xs font-medium rounded-full bg-gray-100 text-gray-800">Inactive</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2">
                                <a href="{{ route('billing.fee-structures.edit', $structure) }}" class="text-primary-600 hover:text-primary-900 text-sm font-medium" title="Edit Fee Items">
                                    <i class="fas fa-cog"></i> Edit Items
                                </a>
                                <a href="{{ route('billing.fee-structures.edit', $structure) }}" class="text-blue-600 hover:text-blue-900 text-sm" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('billing.fee-structures.destroy', $structure) }}" method="POST" class="inline" onsubmit="return confirm('Delete this fee structure?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900 text-sm" title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-4 text-center text-gray-600">
                            No fee structures created yet. <a href="{{ route('billing.fee-structures.create') }}" class="text-primary-600 hover:text-primary-900">Create one now</a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($feeStructures->hasPages())
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $feeStructures->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
