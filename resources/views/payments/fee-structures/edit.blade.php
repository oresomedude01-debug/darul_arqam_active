@extends('layouts.spa')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Page Header -->
    <div>
        <h1 class="text-3xl font-bold text-gray-900">Edit Fee Structure Template</h1>
        <p class="text-gray-600 mt-1">Update template name, description, and associated fee items</p>
    </div>

    <!-- Form -->
    <form action="{{ route('billing.fee-structures.update', $feeStructure) }}" method="POST" class="bg-white rounded-lg shadow p-6 space-y-6">
        @csrf
        @method('PUT')

        <!-- Template Name -->
        <div>
            <label class="block text-sm font-medium text-gray-900 mb-2">Template Name <span class="text-red-600">*</span></label>
            <input type="text" name="name" placeholder="e.g., Standard Tuition 2024/2025" 
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent @error('name') border-red-500 @enderror"
                   value="{{ old('name', $feeStructure->name) }}">
            @error('name')
            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Description -->
        <div>
            <label class="block text-sm font-medium text-gray-900 mb-2">Description</label>
            <textarea name="description" rows="3" placeholder="Enter description..." 
                      class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent @error('description') border-red-500 @enderror">{{ old('description', $feeStructure->description) }}</textarea>
            @error('description')
            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Academic Session -->
        <div>
            <label class="block text-sm font-medium text-gray-900 mb-2">Academic Session</label>
            <select name="academic_session_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent @error('academic_session_id') border-red-500 @enderror">
                <option value="">-- Select Session (Optional) --</option>
                @foreach($sessions as $session)
                <option value="{{ $session->id }}" @selected($feeStructure->academic_session_id == $session->id)>
                    {{ $session->session }}
                </option>
                @endforeach
            </select>
            @error('academic_session_id')
            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Active Status -->
        <div class="flex items-center gap-2">
            <input type="checkbox" name="is_active" id="is_active" value="1" 
                   @checked(old('is_active', $feeStructure->is_active))
                   class="rounded border-gray-300 text-primary-600 focus:ring-primary-500">
            <label for="is_active" class="text-sm font-medium text-gray-900">Active</label>
        </div>

        <!-- Fee Items Section -->
        <div class="border-t pt-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Fee Items in This Template</h2>
            
            @if($feeStructure->items->isNotEmpty())
            <div class="space-y-3">
                @foreach($feeStructure->items as $item)
                <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg">
                    <span class="flex-1">
                        <span class="font-medium text-gray-900">{{ $item->feeItem->name }}</span>
                        <span class="text-gray-600 text-sm ml-2">₦{{ number_format($item->amount, 2) }}</span>
                    </span>
                </div>
                @endforeach
                <div class="text-sm text-gray-600 mt-2">
                    Total Amount: <span class="font-semibold">₦{{ number_format($feeStructure->total_amount, 2) }}</span>
                </div>
            </div>
            @else
            <p class="text-gray-600 italic">No fee items added yet.</p>
            @endif
        </div>

        <!-- Buttons -->
        <div class="flex gap-3 border-t pt-6">
            <button type="submit" class="bg-primary-600 text-white px-6 py-2 rounded-lg hover:bg-primary-700 transition">
                Update Template
            </button>
            <a href="{{ route('billing.fee-structures.index') }}" class="bg-gray-200 text-gray-900 px-6 py-2 rounded-lg hover:bg-gray-300 transition">
                Cancel
            </a>
        </div>
    </form>
</div>
@endsection

