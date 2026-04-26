@extends('layouts.spa')

@section('content')
<div class="bg-gray-50 min-h-screen">
    <!-- Page Header -->
    <div class="bg-white border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 py-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Fee Items</h1>
                    <p class="mt-1 text-sm text-gray-600"></p>
                </div>
                <a href="{{ route('billing.fee-items.create') }}" 
                   class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg font-medium transition">
                    <i class="fas fa-plus mr-2"></i>Add Fee Item
                </a>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-4 py-8">
        @if($feeItems->count() > 0)
        <div class="grid grid-cols-1 gap-6">
            <!-- Fee Items Table -->
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">
                                Name
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">
                                Description
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">
                                Type
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">
                                Default Amount
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">
                                Status
                            </th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-700 uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($feeItems as $item)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="text-sm font-medium text-gray-900">{{ $item->name }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-sm text-gray-600">{{ $item->description ?? 'N/A' }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                                    {{ $item->is_optional ? 'bg-blue-100 text-blue-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $item->is_optional ? 'Optional' : 'Mandatory' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="text-sm text-gray-900">
                                    {{ $item->default_amount ? '₦' . number_format($item->default_amount, 2) : '-' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                                    {{ $item->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                    {{ ucfirst($item->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('billing.fee-items.edit', $item) }}" 
                                       class="text-indigo-600 hover:text-indigo-900 transition">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    @if($item->status === 'active')
                                    <form action="{{ route('billing.fee-items.deactivate', $item) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="text-amber-600 hover:text-amber-900 transition" 
                                                title="Deactivate">
                                            <i class="fas fa-ban"></i>
                                        </button>
                                    </form>
                                    @else
                                    <form action="{{ route('billing.fee-items.activate', $item) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="text-green-600 hover:text-green-900 transition" 
                                                title="Activate">
                                            <i class="fas fa-check-circle"></i>
                                        </button>
                                    </form>
                                    @endif
                                    <form action="{{ route('billing.fee-items.destroy', $item) }}" method="POST" class="inline"
                                          onsubmit="return confirm('Are you sure?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900 transition">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($feeItems->hasPages())
            <div class="flex justify-center">
                {{ $feeItems->links() }}
            </div>
            @endif
        </div>
        @else
        <!-- Empty State -->
        <div class="bg-white rounded-lg shadow p-12 text-center">
            <div class="mb-4">
                <i class="fas fa-inbox text-6xl text-gray-300"></i>
            </div>
            <h3 class="text-lg font-medium text-gray-900 mb-2">No Fee Items Yet</h3>
            <p class="text-gray-600 mb-6">Create your first fee item to get started.</p>
            <a href="{{ route('billing.fee-items.create') }}" 
               class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg font-medium transition">
                <i class="fas fa-plus mr-2"></i>Create Fee Item
            </a>
        </div>
        @endif
    </div>
</div>
@endsection
