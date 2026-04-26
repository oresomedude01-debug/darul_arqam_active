@extends('layouts.spa')

@section('content')
<div class="min-h-screen bg-gray-50 px-2 sm:px-3 md:px-4 lg:px-6 py-4 sm:py-6 md:py-8">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Mail Configuration Tester</h1>
        <p class="text-gray-600 mt-2">Test and verify your email sending configuration</p>
    </div>

    <!-- Success/Error Messages -->
    @if (session('success'))
        <div class="mb-4 p-4 bg-green-50 border border-green-200 rounded-lg flex items-start justify-between">
            <div class="flex gap-3">
                <i class="fas fa-check-circle text-green-600 mt-0.5"></i>
                <p class="text-green-700">{{ session('success') }}</p>
            </div>
            <button type="button" onclick="this.parentElement.style.display='none'" class="text-green-700 hover:text-green-900">
                <i class="fas fa-times"></i>
            </button>
        </div>
    @endif

    @if (session('error'))
        <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-lg flex items-start justify-between">
            <div class="flex gap-3">
                <i class="fas fa-exclamation-circle text-red-600 mt-0.5"></i>
                <p class="text-red-700">{{ session('error') }}</p>
            </div>
            <button type="button" onclick="this.parentElement.style.display='none'" class="text-red-700 hover:text-red-900">
                <i class="fas fa-times"></i>
            </button>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Connection Test Card -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-bold text-gray-900 mb-4 flex items-center gap-2">
                    <i class="fas fa-network-wired text-blue-600"></i>
                    Connection Test
                </h2>
                <p class="text-gray-600 text-sm mb-4">Test SMTP connection to verify basic connectivity</p>
                <button onclick="testConnection()" class="px-6 py-3 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition-colors flex items-center gap-2">
                    <i class="fas fa-plug"></i>Test Connection
                </button>
                <div id="connectionResult" class="mt-4 hidden p-4 rounded-lg border"></div>
            </div>

            <!-- Send Test Email Card -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-bold text-gray-900 mb-4 flex items-center gap-2">
                    <i class="fas fa-envelope text-green-600"></i>
                    Send Test Email
                </h2>
                <p class="text-gray-600 text-sm mb-6">Send a test email to verify your mail configuration is working</p>

                <form action="{{ route('admin.mail-test.send') }}" method="POST" class="space-y-4">
                    @csrf

                    <!-- Recipient Email -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Recipient Email Address</label>
                        <input type="email" name="recipient_email" required placeholder="test@example.com"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500"
                            value="{{ old('recipient_email') }}">
                        @error('recipient_email')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Email Type -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Email Type</label>
                        <div class="space-y-2">
                            <label class="flex items-center gap-3 cursor-pointer">
                                <input type="radio" name="email_type" value="simple" checked class="w-4 h-4">
                                <span class="text-gray-700">Simple Text Email</span>
                            </label>
                            <label class="flex items-center gap-3 cursor-pointer">
                                <input type="radio" name="email_type" value="html" class="w-4 h-4">
                                <span class="text-gray-700">HTML Formatted Email</span>
                            </label>
                        </div>
                        @error('email_type')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Submit -->
                    <button type="submit" class="w-full px-6 py-3 bg-green-600 text-white font-medium rounded-lg hover:bg-green-700 transition-colors flex items-center justify-center gap-2">
                        <i class="fas fa-paper-plane"></i>Send Test Email
                    </button>
                </form>
            </div>
        </div>

        <!-- Sidebar - Configuration -->
        <div class="lg:col-span-1">
            <!-- Config Card -->
            <div class="bg-white rounded-lg shadow p-6 sticky top-6">
                <h2 class="text-xl font-bold text-gray-900 mb-4 flex items-center gap-2">
                    <i class="fas fa-cog text-purple-600"></i>
                    Current Config
                </h2>

                <div class="space-y-4 text-sm">
                    <!-- Driver -->
                    <div>
                        <p class="text-gray-600 font-medium">Mail Driver</p>
                        <p class="text-gray-900 font-bold text-lg mt-1">
                            @php
                                $driver = config('mail.driver');
                                $driverIcon = match($driver) {
                                    'smtp' => '📧',
                                    'mailgun' => '🔫',
                                    'mailtrap' => '🪤',
                                    'ses' => '☁️',
                                    'log' => '📝',
                                    'array' => '🔄',
                                    default => '❓'
                                };
                            @endphp
                            {{ $driverIcon }} {{ strtoupper($driver) }}
                        </p>
                    </div>

                    <!-- From Address -->
                    <div>
                        <p class="text-gray-600 font-medium">From Address</p>
                        <p class="text-gray-900 font-mono text-xs bg-gray-50 p-2 rounded mt-1 break-all">
                            {{ config('mail.from.address') ?? 'Not set' }}
                        </p>
                    </div>

                    <!-- From Name -->
                    <div>
                        <p class="text-gray-600 font-medium">From Name</p>
                        <p class="text-gray-900 font-mono text-xs bg-gray-50 p-2 rounded mt-1">
                            {{ config('mail.from.name') ?? 'Not set' }}
                        </p>
                    </div>

                    @if(config('mail.driver') === 'smtp')
                        <!-- SMTP Host -->
                        <div>
                            <p class="text-gray-600 font-medium">SMTP Host</p>
                            <p class="text-gray-900 font-mono text-xs bg-gray-50 p-2 rounded mt-1">
                                {{ config('mail.host') ?? 'Not set' }}
                            </p>
                        </div>

                        <!-- SMTP Port -->
                        <div>
                            <p class="text-gray-600 font-medium">SMTP Port</p>
                            <p class="text-gray-900 font-mono text-xs bg-gray-50 p-2 rounded mt-1">
                                {{ config('mail.port') ?? 'Not set' }}
                            </p>
                        </div>

                        <!-- Encryption -->
                        <div>
                            <p class="text-gray-600 font-medium">Encryption</p>
                            <p class="text-gray-900 font-mono text-xs bg-gray-50 p-2 rounded mt-1">
                                {{ config('mail.encryption') ?? 'None' }}
                            </p>
                        </div>
                    @endif

                    <!-- Info Box -->
                    <div class="bg-blue-50 border border-blue-200 rounded p-3 mt-4">
                        <p class="text-blue-800 text-xs">
                            <i class="fas fa-info-circle mr-2"></i>
                            <strong>Tip:</strong> Check your .env file for mail configuration settings.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function testConnection() {
    const button = event.target;
    const resultDiv = document.getElementById('connectionResult');
    
    button.disabled = true;
    button.innerHTML = '<i class="fas fa-spinner fa-spin"></i>Testing...';

    fetch('{{ route("admin.mail-test.connection") }}')
        .then(response => response.json())
        .then(data => {
            resultDiv.classList.remove('hidden');
            
            if (data.status === 'success') {
                resultDiv.className = 'mt-4 p-4 rounded-lg border bg-green-50 border-green-200';
                resultDiv.innerHTML = `
                    <div class="flex items-start gap-3">
                        <i class="fas fa-check-circle text-green-600 mt-1"></i>
                        <div>
                            <p class="font-bold text-green-800">Connection Successful</p>
                            <p class="text-green-700 text-sm mt-1">${data.message}</p>
                        </div>
                    </div>
                `;
            } else if (data.status === 'info') {
                resultDiv.className = 'mt-4 p-4 rounded-lg border bg-blue-50 border-blue-200';
                resultDiv.innerHTML = `
                    <div class="flex items-start gap-3">
                        <i class="fas fa-info-circle text-blue-600 mt-1"></i>
                        <div>
                            <p class="font-bold text-blue-800">Info</p>
                            <p class="text-blue-700 text-sm mt-1">${data.message}</p>
                        </div>
                    </div>
                `;
            } else {
                resultDiv.className = 'mt-4 p-4 rounded-lg border bg-red-50 border-red-200';
                resultDiv.innerHTML = `
                    <div class="flex items-start gap-3">
                        <i class="fas fa-times-circle text-red-600 mt-1"></i>
                        <div>
                            <p class="font-bold text-red-800">Connection Failed</p>
                            <p class="text-red-700 text-sm mt-1">${data.message}</p>
                        </div>
                    </div>
                `;
            }
        })
        .catch(error => {
            resultDiv.classList.remove('hidden');
            resultDiv.className = 'mt-4 p-4 rounded-lg border bg-red-50 border-red-200';
            resultDiv.innerHTML = `
                <div class="flex items-start gap-3">
                    <i class="fas fa-times-circle text-red-600 mt-1"></i>
                    <div>
                        <p class="font-bold text-red-800">Error</p>
                        <p class="text-red-700 text-sm mt-1">${error.message}</p>
                    </div>
                </div>
            `;
        })
        .finally(() => {
            button.disabled = false;
            button.innerHTML = '<i class="fas fa-plug"></i>Test Connection';
        });
}
</script>
@endsection
