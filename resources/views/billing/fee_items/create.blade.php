@extends('layouts.spa')

@section('content')
<div class="bg-gray-50 min-h-screen">
    <!-- Page Header -->
    <div class="bg-white border-b border-gray-200">
        <div class="max-w-3xl mx-auto px-4 py-6">
            <div class="flex items-center gap-3">
                <a href="{{ route('billing.fee-items.index') }}" class="text-gray-600 hover:text-gray-900">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Create Fee Item</h1>
                    <p class="mt-1 text-sm text-gray-600">Add a new fee item to your billing system</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-3xl mx-auto px-4 py-8">
        <div class="bg-white rounded-lg shadow p-8">
            <form action="{{ route('billing.fee-items.store') }}" method="POST" class="space-y-6">
                @csrf

                <!-- Name Field -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-900 mb-2">
                        Fee Item Name <span class="text-red-600">*</span>
                    </label>
                    <input type="text" id="name" name="name" 
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent @error('name') border-red-500 @enderror"
                           placeholder="e.g., Tuition, ICT Fee, Exam Fee"
                           value="{{ old('name') }}" required>
                    @error('name')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Description Field -->
                <div>
                    <label for="description" class="block text-sm font-medium text-gray-900 mb-2">
                        Description
                    </label>
                    <textarea id="description" name="description" rows="4"
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent @error('description') border-red-500 @enderror"
                              placeholder="Describe what this fee covers">{{ old('description') }}</textarea>
                    @error('description')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Type Field -->
                <div>
                    <label for="is_optional" class="block text-sm font-medium text-gray-900 mb-2">
                        Type <span class="text-red-600">*</span>
                    </label>
                    <select id="is_optional" name="is_optional"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent @error('is_optional') border-red-500 @enderror">
                        <option value="0" {{ old('is_optional') === '0' ? 'selected' : '' }}>Mandatory</option>
                        <option value="1" {{ old('is_optional') === '1' ? 'selected' : '' }}>Optional</option>
                    </select>
                    @error('is_optional')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Default Amount Field -->
                <div>
                    <label for="default_amount" class="block text-sm font-medium text-gray-900 mb-2">
                        Default Amount (Optional)
                    </label>
                    <div class="relative">
                        <span class="absolute left-4 top-2 text-gray-600">₦</span>
                        <input type="number" id="default_amount" name="default_amount" 
                               class="w-full pl-8 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent @error('default_amount') border-red-500 @enderror"
                               placeholder="0.00" step="0.01" min="0" 
                               value="{{ old('default_amount') }}">
                    </div>
                    <p class="mt-1 text-xs text-gray-600">This is just a reference amount. Actual fees are set in fee structures per class/session.</p>
                    @error('default_amount')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Status Field -->
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-900 mb-2">
                        Status <span class="text-red-600">*</span>
                    </label>
                    <select id="status" name="status"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent @error('status') border-red-500 @enderror">
                        <option value="active" {{ old('status') === 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ old('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                    @error('status')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Form Actions -->
                <div class="flex items-center gap-4 pt-6 border-t border-gray-200">
                    <button type="submit" 
                            class="px-6 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg font-medium transition">
                        <i class="fas fa-save mr-2"></i>Create Fee Item
                    </button>
                    <a href="{{ route('billing.fee-items.index') }}"
                       class="px-6 py-2 bg-gray-200 hover:bg-gray-300 text-gray-900 rounded-lg font-medium transition">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
