@extends('layouts.spa')

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Manage Fee Items</h1>
            <p class="text-gray-600 mt-1">{{ $feeStructure->name }}</p>
        </div>
        <a href="{{ route('billing.fee-structures.index') }}" class="bg-gray-200 text-gray-900 px-4 py-2 rounded-lg hover:bg-gray-300 transition">
            Back to Structures
        </a>
    </div>

    @if(session('success'))
    <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg">
        {{ session('success') }}
    </div>
    @endif

    <!-- Add Fee Item Form -->
    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Add New Fee Item</h2>
        <form action="{{ route('billing.fee-items.store') }}" method="POST" class="grid grid-cols-1 md:grid-cols-5 gap-4">
            @csrf

            <!-- Class -->
            <div>
                <label class="block text-sm font-medium text-gray-900 mb-1">Class</label>
                <select name="school_class_id" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                    <option value="">Select Class</option>
                    @foreach($classes as $class)
                    <option value="{{ $class->id }}">{{ $class->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Fee Name -->
            <div>
                <label class="block text-sm font-medium text-gray-900 mb-1">Fee Name</label>
                <input type="text" name="name" placeholder="e.g., Tuition Fee" required 
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent">
            </div>

            <!-- Amount -->
            <div>
                <label class="block text-sm font-medium text-gray-900 mb-1">Amount</label>
                <input type="number" name="amount" step="0.01" placeholder="0.00" required 
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent">
            </div>

            <!-- Description -->
            <div>
                <label class="block text-sm font-medium text-gray-900 mb-1">Description</label>
                <input type="text" name="description" placeholder="Optional description" 
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent">
            </div>

            <!-- Add Button -->
            <div class="flex items-end">
                <button type="submit" class="w-full bg-primary-600 text-white px-4 py-2 rounded-lg hover:bg-primary-700 transition font-medium">
                    <i class="fas fa-plus mr-2"></i>Add Item
                </button>
            </div>
        </form>
    </div>

    <!-- Fee Items Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Class</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Fee Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Amount</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Description</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($feeStructure->feeItems as $item)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $item->schoolClass->name }}</td>
                        <td class="px-6 py-4 text-sm text-gray-900">{{ $item->name }}</td>
                        <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ number_format($item->amount, 2) }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $item->description ?? 'N/A' }}</td>
                        <td class="px-6 py-4">
                            @if($item->is_active)
                            <span class="px-3 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800">Active</span>
                            @else
                            <span class="px-3 py-1 text-xs font-medium rounded-full bg-gray-100 text-gray-800">Inactive</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2">
                                <button class="text-blue-600 hover:text-blue-900 text-sm font-medium edit-btn" onclick="editItem({{ $item->id }}, '{{ $item->name }}', {{ $item->amount }}, '{{ $item->description }}')">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <form action="{{ route('billing.fee-items.destroy', $item) }}" method="POST" class="inline" onsubmit="return confirm('Delete this fee item?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900 text-sm">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-4 text-center text-gray-600">
                            No fee items added yet
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Generate Bills Button -->
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-6">
        <h3 class="text-lg font-semibold text-blue-900 mb-2">Generate Student Bills</h3>
        <p class="text-blue-800 mb-4">Once you've set up all fee items, generate bills for all students in their respective classes.</p>
        <form action="{{ route('payments.bills.generate') }}" method="POST" class="space-y-4">
            @csrf
            <input type="hidden" name="fee_structure_id" value="{{ $feeStructure->id }}">
            
            <div>
                <label class="block text-sm font-medium text-gray-900 mb-2">Select Class to Generate Bills For</label>
                <select name="school_class_id" required class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                    <option value="">Select Class</option>
                    @foreach($classes as $class)
                    <option value="{{ $class->id }}">{{ $class->name }}</option>
                    @endforeach
                </select>
            </div>
            
            <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition">
                <i class="fas fa-file-invoice-dollar mr-2"></i>Generate Bills for Selected Class
            </button>
        </form>
    </div>
</div>
@endsection
