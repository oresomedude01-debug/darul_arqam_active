@extends('layouts.spa')

@section('title', 'Financial Settings')

@section('content')
<div class="max-w-6xl mx-auto">
    <!-- Header -->
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Financial Settings</h1>
        <p class="text-gray-600 mt-2">Configure payment methods and financial details for the school</p>
    </div>

    <form action="{{ route('settings.school.update-financial') }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')

        <!-- Bank Account Details Section -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center gap-3 mb-4">
                <i class="fas fa-university text-2xl text-primary-600"></i>
                <h2 class="text-2xl font-bold text-gray-900">Bank Account Details</h2>
            </div>
            <p class="text-sm text-gray-600 mb-6">For students to make bank transfers</p>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Bank Name -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Bank Name</label>
                    <input type="text" 
                           name="bank_name" 
                           value="{{ $settings->bank_name }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                           placeholder="e.g., ABC Bank Ltd">
                </div>

                <!-- Account Holder Name -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Account Holder Name</label>
                    <input type="text" 
                           name="account_holder_name" 
                           value="{{ $settings->account_holder_name }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                           placeholder="School account holder name">
                </div>

                <!-- Account Number -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Account Number</label>
                    <input type="text" 
                           name="account_number" 
                           value="{{ $settings->account_number }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                           placeholder="12345678901234">
                </div>

                <!-- Account Type -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Account Type</label>
                    <select name="account_type" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                        <option value="">Select Account Type</option>
                        <option value="Savings" {{ $settings->account_type === 'Savings' ? 'selected' : '' }}>Savings Account</option>
                        <option value="Current" {{ $settings->account_type === 'Current' ? 'selected' : '' }}>Current Account</option>
                        <option value="Business" {{ $settings->account_type === 'Business' ? 'selected' : '' }}>Business Account</option>
                    </select>
                </div>

                <!-- Bank Code -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Bank Code</label>
                    <input type="text" 
                           name="bank_code" 
                           value="{{ $settings->bank_code }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                           placeholder="e.g., 050">
                </div>

                <!-- Routing Number -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Routing Number (US)</label>
                    <input type="text" 
                           name="routing_number" 
                           value="{{ $settings->routing_number }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                           placeholder="Optional for US banks">
                </div>

                <!-- SWIFT Code -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">SWIFT Code</label>
                    <input type="text" 
                           name="swift_code" 
                           value="{{ $settings->swift_code }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                           placeholder="e.g., ABCDUS33">
                </div>

                <!-- IBAN -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">IBAN</label>
                    <input type="text" 
                           name="iban" 
                           value="{{ $settings->iban }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                           placeholder="Optional for international transfers">
                </div>
            </div>
        </div>

        <!-- Mobile Money Details Section -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center gap-3 mb-4">
                <i class="fas fa-mobile text-2xl text-primary-600"></i>
                <h2 class="text-2xl font-bold text-gray-900">Mobile Money Details</h2>
            </div>
            <p class="text-sm text-gray-600 mb-6">For mobile money payments (MTN, Vodafone, etc.)</p>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Mobile Money Provider -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Provider</label>
                    <input type="text" 
                           name="mobile_money_provider" 
                           value="{{ $settings->mobile_money_provider }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                           placeholder="e.g., MTN, Vodafone, AirtelTigo">
                </div>

                <!-- Mobile Money Number -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Mobile Money Number</label>
                    <input type="text" 
                           name="mobile_money_number" 
                           value="{{ $settings->mobile_money_number }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                           placeholder="e.g., 0500123456">
                </div>
            </div>
        </div>

        <!-- Cheque Payment Details Section -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center gap-3 mb-4">
                <i class="fas fa-receipt text-2xl text-primary-600"></i>
                <h2 class="text-2xl font-bold text-gray-900">Cheque Payment Details</h2>
            </div>
            <p class="text-sm text-gray-600 mb-6">For cheque payments</p>

            <div class="grid grid-cols-1 gap-6">
                <!-- Cheque Payable To -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Cheque Payable To</label>
                    <input type="text" 
                           name="cheque_payable_to" 
                           value="{{ $settings->cheque_payable_to }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                           placeholder="School account name">
                </div>

                <!-- Cheque Instructions -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Cheque Instructions</label>
                    <textarea name="cheque_instructions" 
                              rows="3"
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                              placeholder="e.g., Cheques should be sent to: [address]">{{ $settings->cheque_instructions }}</textarea>
                </div>
            </div>
        </div>

        <!-- General Payment Instructions Section -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center gap-3 mb-4">
                <i class="fas fa-info-circle text-2xl text-primary-600"></i>
                <h2 class="text-2xl font-bold text-gray-900">General Payment Instructions</h2>
            </div>
            <p class="text-sm text-gray-600 mb-6">General payment information displayed to students and parents</p>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Payment Instructions</label>
                <textarea name="payment_instructions" 
                          rows="4"
                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                          placeholder="Enter general payment instructions here...">{{ $settings->payment_instructions }}</textarea>
                <p class="text-xs text-gray-500 mt-2">This will be displayed on student payment pages</p>
            </div>
        </div>

        <!-- Finance Contact Section -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center gap-3 mb-4">
                <i class="fas fa-user text-2xl text-primary-600"></i>
                <h2 class="text-2xl font-bold text-gray-900">Finance Contact</h2>
            </div>
            <p class="text-sm text-gray-600 mb-6">Contact details for finance inquiries</p>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Contact Name -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Contact Person Name</label>
                    <input type="text" 
                           name="finance_contact_name" 
                           value="{{ $settings->finance_contact_name }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                           placeholder="Finance officer name">
                </div>

                <!-- Contact Email -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Contact Email</label>
                    <input type="email" 
                           name="finance_contact_email" 
                           value="{{ $settings->finance_contact_email }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                           placeholder="finance@school.edu">
                </div>

                <!-- Contact Phone -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Contact Phone</label>
                    <input type="tel" 
                           name="finance_contact_phone" 
                           value="{{ $settings->finance_contact_phone }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                           placeholder="+1 (555) 000-0000">
                </div>
            </div>
        </div>

        <!-- Form Actions -->
        <div class="flex gap-3 justify-end">
            <a href="{{ route('settings.school.index') }}" 
               class="px-6 py-2.5 border border-gray-300 rounded-lg text-gray-700 font-medium hover:bg-gray-50 transition">
                Cancel
            </a>
            <button type="submit" 
                    class="px-6 py-2.5 bg-primary-600 text-white rounded-lg font-medium hover:bg-primary-700 transition">
                <i class="fas fa-save mr-2"></i>Save Financial Details
            </button>
        </div>
    </form>
</div>
@endsection
