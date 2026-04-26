@extends('layouts.spa')

@section('title', 'Paystack Settings')

@section('content')
<div class="max-w-4xl mx-auto">
    <!-- Header -->
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Paystack Payment Gateway</h1>
        <p class="text-gray-600 mt-2">Configure Paystack for online payments</p>
    </div>

    @if(session('success'))
        <div class="mb-6 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg">
            <i class="fas fa-check-circle mr-2"></i>
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="mb-6 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg">
            <i class="fas fa-exclamation-circle mr-2"></i>
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Main Form -->
    <form action="{{ route('settings.school.update-paystack') }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')

        <!-- Getting Started Section -->
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-6">
            <h2 class="text-lg font-semibold text-blue-900 mb-2">
                <i class="fas fa-info-circle mr-2"></i>Getting Started with Paystack
            </h2>
            <p class="text-blue-800 text-sm mb-3">
                To get your API keys, follow these steps:
            </p>
            <ol class="text-blue-800 text-sm space-y-2 list-decimal list-inside">
                <li>Go to <a href="https://dashboard.paystack.com" target="_blank" class="underline font-semibold">https://dashboard.paystack.com</a></li>
                <li>Log in or create an account</li>
                <li>Navigate to Settings → Developer</li>
                <li>Copy your Test Keys (for testing) or Live Keys (for production)</li>
                <li>Paste them below</li>
            </ol>
        </div>

        <!-- API Keys Section -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center gap-3 mb-4">
                <i class="fas fa-key text-2xl text-primary-600"></i>
                <h2 class="text-2xl font-bold text-gray-900">API Keys</h2>
            </div>
            <p class="text-sm text-gray-600 mb-6">Enter your Paystack API credentials</p>

            <div class="grid grid-cols-1 gap-6">
                <!-- Public Key -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Public Key <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           name="paystack_public_key" 
                           value="{{ old('paystack_public_key', $settings->paystack_public_key) }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent font-mono text-sm"
                           placeholder="pk_test_xxxxxxxxxxxxxxxx or pk_live_xxxxxxxxxxxxxxxx"
                           @error('paystack_public_key') style="border-color: #ef4444" @enderror>
                    <p class="text-xs text-gray-500 mt-2">
                        <i class="fas fa-lightbulb mr-1"></i>
                        Starts with <code class="bg-gray-100 px-1 rounded">pk_</code>
                    </p>
                    @error('paystack_public_key')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Secret Key -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Secret Key <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <input type="password" 
                               id="secret-key"
                               name="paystack_secret_key" 
                               value="{{ old('paystack_secret_key', $settings->paystack_secret_key) }}"
                               class="w-full px-4 py-2 pr-10 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent font-mono text-sm"
                               placeholder="sk_test_xxxxxxxxxxxxxxxx or sk_live_xxxxxxxxxxxxxxxx"
                               @error('paystack_secret_key') style="border-color: #ef4444" @enderror>
                        <button type="button" 
                                onclick="togglePasswordVisibility('secret-key')"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-600 hover:text-gray-900">
                            <i class="fas fa-eye text-sm"></i>
                        </button>
                    </div>
                    <p class="text-xs text-gray-500 mt-2">
                        <i class="fas fa-lightbulb mr-1"></i>
                        Starts with <code class="bg-gray-100 px-1 rounded">sk_</code> (keep this secret!)
                    </p>
                    @error('paystack_secret_key')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Callback URL -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Callback URL</label>
                    <input type="url" 
                           name="paystack_callback_url" 
                           value="{{ old('paystack_callback_url', $settings->paystack_callback_url ?? route('paystack.callback')) }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                           placeholder="https://your-school.com/payments/paystack/callback"
                           @error('paystack_callback_url') style="border-color: #ef4444" @enderror>
                    <p class="text-xs text-gray-500 mt-2">
                        <i class="fas fa-info-circle mr-1"></i>
                        Your system will automatically generate this URL. Update it in Paystack Dashboard Settings if needed.
                    </p>
                    @error('paystack_callback_url')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Status Section -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center gap-3 mb-4">
                <i class="fas fa-toggle-on text-2xl text-primary-600"></i>
                <h2 class="text-2xl font-bold text-gray-900">Payment Status</h2>
            </div>

            <div class="space-y-4">
                <!-- Enable Online Payments -->
                <div class="flex items-center justify-between p-4 border border-gray-200 rounded-lg hover:bg-gray-50">
                    <div>
                        <p class="font-semibold text-gray-900">Enable Online Payments</p>
                        <p class="text-sm text-gray-600">Allow students to pay online via Paystack</p>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" 
                               id="enable_online_payment"
                               name="enable_online_payment" 
                               value="1" 
                               {{ $settings->enable_online_payment ? 'checked' : '' }}
                               class="sr-only peer">
                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-primary-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary-600"></div>
                    </label>
                </div>

                <!-- Current Status -->
                <div class="p-4 bg-gray-50 rounded-lg">
                    <p class="text-sm text-gray-600 mb-2">Configuration Status:</p>
                    @php
                        $isConfigured = $settings->paystack_public_key && $settings->paystack_secret_key && $settings->enable_online_payment;
                    @endphp
                    <div class="flex items-center gap-2">
                        @if($isConfigured)
                            <i class="fas fa-check-circle text-green-600 text-lg"></i>
                            <span class="font-semibold text-green-700">✓ Paystack is configured and ready to use</span>
                        @else
                            <i class="fas fa-times-circle text-red-600 text-lg"></i>
                            <span class="font-semibold text-red-700">✗ Paystack is not fully configured</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Environment Selection Info -->
        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-6">
            <h3 class="font-semibold text-yellow-900 mb-2">
                <i class="fas fa-exclamation-triangle mr-2"></i>Test vs Live Mode
            </h3>
            <p class="text-yellow-800 text-sm mb-3">
                Paystack provides two sets of keys:
            </p>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="p-3 bg-white rounded border border-yellow-200">
                    <p class="font-semibold text-sm text-gray-900">Test Keys (Development)</p>
                    <p class="text-xs text-gray-600 mt-1">Start with <code class="bg-gray-100 px-1 rounded">pk_test_</code> and <code class="bg-gray-100 px-1 rounded">sk_test_</code></p>
                    <p class="text-xs text-yellow-700 mt-2">Use for testing without real transactions</p>
                </div>
                <div class="p-3 bg-white rounded border border-yellow-200">
                    <p class="font-semibold text-sm text-gray-900">Live Keys (Production)</p>
                    <p class="text-xs text-gray-600 mt-1">Start with <code class="bg-gray-100 px-1 rounded">pk_live_</code> and <code class="bg-gray-100 px-1 rounded">sk_live_</code></p>
                    <p class="text-xs text-yellow-700 mt-2">Use for real transactions only</p>
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
                <i class="fas fa-save mr-2"></i>Save Paystack Settings
            </button>
        </div>
    </form>
</div>

<script>
function togglePasswordVisibility(fieldId) {
    const field = document.getElementById(fieldId);
    if (field.type === 'password') {
        field.type = 'text';
    } else {
        field.type = 'password';
    }
}
</script>
@endsection
