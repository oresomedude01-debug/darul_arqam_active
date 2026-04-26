@extends('layouts.spa')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    @if ($errors->any())
    <div class="bg-red-50 border border-red-200 rounded-lg p-4">
        <h3 class="font-semibold text-red-900 mb-2">Please fix the following errors:</h3>
        <ul class="list-disc list-inside text-red-700 text-sm space-y-1">
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    @if (session('success'))
    <div class="bg-green-50 border border-green-200 rounded-lg p-4">
        <p class="text-green-900 font-semibold">✓ {{ session('success') }}</p>
    </div>
    @endif

    <!-- Page Header -->
    <div>
        <h1 class="text-3xl font-bold text-gray-900">Create Fee Structure Template</h1>
        <p class="text-gray-600 mt-1">Create a new fee structure template that can be applied to different classes</p>
    </div>

    <!-- Form -->
    <form action="{{ route('billing.fee-structures.store') }}" method="POST" class="bg-white rounded-lg shadow p-6 space-y-6">
        @csrf

        <!-- Template Name -->
        <div>
            <label class="block text-sm font-medium text-gray-900 mb-2">Template Name <span class="text-red-600">*</span></label>
            <input type="text" name="name" placeholder="e.g., Standard Tuition 2024/2025" 
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent @error('name') border-red-500 @enderror"
                   value="{{ old('name') }}" required>
            @error('name')
            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Description -->
        <div>
            <label class="block text-sm font-medium text-gray-900 mb-2">Description</label>
            <textarea name="description" rows="3" placeholder="Enter description..." 
                      class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent @error('description') border-red-500 @enderror">{{ old('description') }}</textarea>
            @error('description')
            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Academic Session -->
        <div>
            <label class="block text-sm font-medium text-gray-900 mb-2">Academic Session (Optional)</label>
            <select name="academic_session_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent @error('academic_session_id') border-red-500 @enderror">
                <option value="">-- Select Session --</option>
                @foreach($sessions as $session)
                <option value="{{ $session->id }}" @selected(old('academic_session_id') == $session->id)>
                    {{ $session->session }}
                </option>
                @endforeach
            </select>
            @error('academic_session_id')
            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Fee Items Section -->
        <div class="border-t pt-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Add Fee Items <span class="text-red-600">*</span></h2>
            <p class="text-gray-600 text-sm mb-4">Add at least one fee item to this template</p>
            
            <div id="fee-items-container" class="space-y-3">
                <div class="fee-item-row flex gap-3 items-end p-4 bg-gray-50 rounded-lg">
                    <div class="flex-1">
                        <label class="block text-xs font-medium text-gray-700 mb-1">Fee Item</label>
                        <select name="fee_items[]" class="fee-select w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent" required>
                            <option value="">-- Select Fee Item --</option>
                            @foreach($feeItems as $item)
                            <option value="{{ $item->id }}" data-price="{{ $item->default_amount }}">{{ $item->name }} (₦{{ number_format($item->default_amount, 2) }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="w-40">
                        <label class="block text-xs font-medium text-gray-700 mb-1">Amount (Auto-loaded)</label>
                        <div class="flex items-center gap-2">
                            <input type="number" name="amounts[]" placeholder="0.00" step="0.01" 
                                   class="fee-amount flex-1 px-4 py-2 border border-gray-300 rounded-lg bg-white focus:ring-2 focus:ring-primary-500 focus:border-transparent" readonly required>
                            <span class="fee-status text-xl text-gray-400">◯</span>
                        </div>
                    </div>
                    <button type="button" onclick="removeFeeItem(this)" class="bg-red-600 text-white px-3 py-2 rounded-lg hover:bg-red-700 transition" title="Remove this fee item">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>

            <button type="button" onclick="addFeeItem()" class="mt-3 bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition text-sm">
                <i class="fas fa-plus mr-2"></i>Add Another Item
            </button>
            
            @error('fee_items')
            <p class="text-red-600 text-sm mt-2">{{ $message }}</p>
            @enderror
        </div>

        <!-- Buttons -->
        <div class="flex gap-3 border-t pt-6">
            <button type="submit" class="bg-primary-600 text-white px-6 py-2 rounded-lg hover:bg-primary-700 transition">
                Create Template
            </button>
            <a href="{{ route('billing.fee-structures.index') }}" class="bg-gray-200 text-gray-900 px-6 py-2 rounded-lg hover:bg-gray-300 transition">
                Cancel
            </a>
        </div>
    </form>
</div>

<script>
function addFeeItem() {
    const container = document.getElementById('fee-items-container');
    const newRow = document.querySelector('.fee-item-row').cloneNode(true);
    const selectElement = newRow.querySelector('.fee-select');
    const amountInput = newRow.querySelector('.fee-amount');
    const statusSpan = newRow.querySelector('.fee-status');
    
    selectElement.value = '';
    amountInput.value = '';
    statusSpan.innerHTML = '◯';
    statusSpan.classList.remove('text-green-600');
    statusSpan.classList.add('text-gray-400');
    
    selectElement.addEventListener('change', loadFeePrice);
    container.appendChild(newRow);
    console.log('Fee item row added');
}

function removeFeeItem(btn) {
    const rows = document.querySelectorAll('.fee-item-row');
    if (rows.length > 1) {
        btn.closest('.fee-item-row').remove();
        console.log('Fee item row removed');
    } else {
        alert('You must keep at least one fee item');
    }
}

function loadFeePrice(e) {
    const selectedOption = e.target.options[e.target.selectedIndex];
    const price = selectedOption.getAttribute('data-price');
    const row = e.target.closest('.fee-item-row');
    const amountInput = row.querySelector('.fee-amount');
    const statusSpan = row.querySelector('.fee-status');
    
    if (price) {
        const formattedPrice = parseFloat(price).toFixed(2);
        amountInput.value = formattedPrice;
        statusSpan.innerHTML = '✓';
        statusSpan.classList.remove('text-gray-400');
        statusSpan.classList.add('text-green-600');
        console.log('Price loaded: ₦' + formattedPrice);
    } else {
        amountInput.value = '';
        statusSpan.innerHTML = '◯';
        statusSpan.classList.remove('text-green-600');
        statusSpan.classList.add('text-gray-400');
    }
}

// Attach event listeners to all fee selects on page load
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.fee-select').forEach(select => {
        select.addEventListener('change', loadFeePrice);
    });
    console.log('Fee structure form initialized');
});
</script>
@endsection
