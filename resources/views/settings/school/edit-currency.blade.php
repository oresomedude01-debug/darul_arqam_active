@extends('layouts.spa')

@section('title', 'Edit Currency Settings')

@section('breadcrumb')
    <span class="text-gray-400">Settings</span>
    <span class="text-gray-400">/</span>
    <a href="{{ route('settings.school.index') }}" class="text-primary-600 hover:text-primary-700">School Settings</a>
    <span class="text-gray-400">/</span>
    <span class="font-semibold text-gray-900">Currency Settings</span>
@endsection

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Currency Settings</h1>
            <p class="text-sm text-gray-600 mt-1">Configure the currency used for fees and financial transactions</p>
        </div>
        <a href="{{ route('settings.school.index') }}" class="btn btn-outline">
            <i class="fas fa-arrow-left mr-2"></i>Back to Settings
        </a>
    </div>

    <!-- Form -->
    <form action="{{ route('settings.school.update-currency') }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')

        <!-- Currency Configuration -->
        <div class="card">
            <div class="card-header">
                <h3 class="text-lg font-semibold text-gray-900">
                    <i class="fas fa-money-bill-wave mr-2 text-primary-600"></i>Currency Configuration
                </h3>
            </div>
            <div class="card-body space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="form-group">
                        <label class="form-label">Currency Code <span class="text-red-500">*</span></label>
                        <select name="currency_code" class="form-select @error('currency_code') border-red-500 @enderror" required>
                            <option value="">-- Select Currency --</option>
                            <option value="NGN" @selected($settings->currency_code === 'NGN')>Nigerian Naira (₦)</option>
                            <option value="GHS" @selected($settings->currency_code === 'GHS')>Ghanaian Cedi (₵)</option>
                            <option value="KES" @selected($settings->currency_code === 'KES')>Kenyan Shilling (KSh)</option>
                            <option value="UGX" @selected($settings->currency_code === 'UGX')>Ugandan Shilling (USh)</option>
                            <option value="ZAR" @selected($settings->currency_code === 'ZAR')>South African Rand (R)</option>
                            <option value="TZS" @selected($settings->currency_code === 'TZS')>Tanzanian Shilling (TSh)</option>
                            <option value="USD" @selected($settings->currency_code === 'USD')>US Dollar ($)</option>
                            <option value="EUR" @selected($settings->currency_code === 'EUR')>Euro (€)</option>
                            <option value="GBP" @selected($settings->currency_code === 'GBP')>British Pound (£)</option>
                            <option value="INR" @selected($settings->currency_code === 'INR')>Indian Rupee (₹)</option>
                            <option value="PKR" @selected($settings->currency_code === 'PKR')>Pakistani Rupee (₨)</option>
                            <option value="BDT" @selected($settings->currency_code === 'BDT')>Bangladeshi Taka (৳)</option>
                            <option value="AED" @selected($settings->currency_code === 'AED')>UAE Dirham (د.إ)</option>
                            <option value="SAR" @selected($settings->currency_code === 'SAR')>Saudi Riyal (﷼)</option>
                            <option value="QAR" @selected($settings->currency_code === 'QAR')>Qatari Rial (﷼)</option>
                        </select>
                        @error('currency_code')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label">Currency Symbol <span class="text-red-500">*</span></label>
                        <input type="text" name="currency_symbol" value="{{ old('currency_symbol', $settings->currency_symbol) }}" class="form-input @error('currency_symbol') border-red-500 @enderror" maxlength="5" required placeholder="e.g., ₦, $, €">
                        <p class="text-xs text-gray-500 mt-1">The symbol displayed for this currency</p>
                        @error('currency_symbol')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="alert alert-info">
                    <i class="fas fa-info-circle mr-2"></i>
                    <strong>Tip:</strong> This currency will be used throughout the system for fees, payments, and financial reports.
                </div>
            </div>
        </div>

        <!-- Format Preview -->
        <div class="card">
            <div class="card-header">
                <h3 class="text-lg font-semibold text-gray-900">
                    <i class="fas fa-eye mr-2 text-primary-600"></i>Format Preview
                </h3>
            </div>
            <div class="card-body">
                <p class="text-sm text-gray-600 mb-4">How amounts will be displayed in the system:</p>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="bg-gray-50 p-6 rounded-lg text-center border border-gray-200">
                        <div class="text-sm text-gray-600 mb-2">School Fees</div>
                        <div class="text-3xl font-bold text-primary-600" id="preview-fees">₦50,000</div>
                    </div>
                    <div class="bg-gray-50 p-6 rounded-lg text-center border border-gray-200">
                        <div class="text-sm text-gray-600 mb-2">Monthly Fee</div>
                        <div class="text-3xl font-bold text-primary-600" id="preview-monthly">₦10,000</div>
                    </div>
                    <div class="bg-gray-50 p-6 rounded-lg text-center border border-gray-200">
                        <div class="text-sm text-gray-600 mb-2">Exam Fee</div>
                        <div class="text-3xl font-bold text-primary-600" id="preview-exam">₦5,000</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Common Currencies Reference -->
        <div class="card">
            <div class="card-header">
                <h3 class="text-lg font-semibold text-gray-900">
                    <i class="fas fa-book mr-2 text-primary-600"></i>Common Currency Symbols
                </h3>
            </div>
            <div class="card-body">
                <div class="grid grid-cols-2 md:grid-cols-4 gap-3 text-sm">
                    <div class="flex items-center space-x-2 p-2 bg-gray-50 rounded">
                        <span class="font-bold">₦</span>
                        <span class="text-gray-600">Nigerian Naira</span>
                    </div>
                    <div class="flex items-center space-x-2 p-2 bg-gray-50 rounded">
                        <span class="font-bold">$</span>
                        <span class="text-gray-600">US Dollar</span>
                    </div>
                    <div class="flex items-center space-x-2 p-2 bg-gray-50 rounded">
                        <span class="font-bold">€</span>
                        <span class="text-gray-600">Euro</span>
                    </div>
                    <div class="flex items-center space-x-2 p-2 bg-gray-50 rounded">
                        <span class="font-bold">£</span>
                        <span class="text-gray-600">British Pound</span>
                    </div>
                    <div class="flex items-center space-x-2 p-2 bg-gray-50 rounded">
                        <span class="font-bold">₵</span>
                        <span class="text-gray-600">Ghanaian Cedi</span>
                    </div>
                    <div class="flex items-center space-x-2 p-2 bg-gray-50 rounded">
                        <span class="font-bold">₹</span>
                        <span class="text-gray-600">Indian Rupee</span>
                    </div>
                    <div class="flex items-center space-x-2 p-2 bg-gray-50 rounded">
                        <span class="font-bold">¥</span>
                        <span class="text-gray-600">Chinese Yuan</span>
                    </div>
                    <div class="flex items-center space-x-2 p-2 bg-gray-50 rounded">
                        <span class="font-bold">₨</span>
                        <span class="text-gray-600">Pakistani Rupee</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex items-center justify-between">
            <a href="{{ route('settings.school.index') }}" class="btn btn-outline">
                <i class="fas fa-times mr-2"></i>Cancel
            </a>
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save mr-2"></i>Save Changes
            </button>
        </div>
    </form>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const currencySelect = document.querySelector('select[name="currency_code"]');
        const currencySymbolInput = document.querySelector('input[name="currency_symbol"]');

        // Currency symbols map
        const currencySymbols = {
            'NGN': '₦',
            'GHS': '₵',
            'KES': 'KSh',
            'UGX': 'USh',
            'ZAR': 'R',
            'TZS': 'TSh',
            'USD': '$',
            'EUR': '€',
            'GBP': '£',
            'INR': '₹',
            'PKR': '₨',
            'BDT': '৳',
            'AED': 'د.إ',
            'SAR': '﷼',
            'QAR': '﷼'
        };

        currencySelect.addEventListener('change', function() {
            const code = this.value;
            if (currencySymbols[code]) {
                currencySymbolInput.value = currencySymbols[code];
                updatePreview();
            }
        });

        currencySymbolInput.addEventListener('input', updatePreview);

        function updatePreview() {
            const symbol = currencySymbolInput.value || '₦';
            document.getElementById('preview-fees').textContent = symbol + '50,000';
            document.getElementById('preview-monthly').textContent = symbol + '10,000';
            document.getElementById('preview-exam').textContent = symbol + '5,000';
        }

        updatePreview();
    });
</script>
@endsection
