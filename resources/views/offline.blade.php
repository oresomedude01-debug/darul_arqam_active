<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Offline - Darul Arqam School Management System</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="manifest" href="{{ asset('manifest.json') }}">
    <meta name="theme-color" content="#0284c7">
</head>
<body class="bg-gradient-to-br from-gray-50 to-gray-100">
    <div class="min-h-screen flex items-center justify-center px-4">
        <div class="max-w-md w-full">
            <!-- Icon -->
            <div class="text-center mb-8">
                <div class="inline-flex items-center justify-center w-20 h-20 bg-yellow-100 rounded-full mb-4">
                    <i class="fas fa-wifi-slash text-4xl text-yellow-600"></i>
                </div>
                <h1 class="text-3xl font-bold text-gray-900 mb-2">You're Offline</h1>
                <p class="text-gray-600">Your internet connection is currently unavailable</p>
            </div>

            <!-- Info Card -->
            <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
                <div class="space-y-4">
                    <div class="flex items-start gap-3">
                        <div class="flex-shrink-0 w-6 h-6 bg-blue-100 rounded-full flex items-center justify-center mt-0.5">
                            <i class="fas fa-check text-sm text-blue-600"></i>
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-900">What you can do</h3>
                            <p class="text-sm text-gray-600 mt-1">We've cached some important pages for you. You can still:</p>
                            <ul class="text-sm text-gray-600 mt-2 space-y-1 ml-0">
                                <li>• View your dashboard (if previously loaded)</li>
                                <li>• Check cached student/teacher data</li>
                                <li>• Review previously viewed pages</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- What's unavailable -->
            <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
                <h3 class="font-semibold text-red-900 mb-2 flex items-center gap-2">
                    <i class="fas fa-exclamation-triangle"></i>
                    What's unavailable
                </h3>
                <ul class="text-sm text-red-800 space-y-1">
                    <li>• Real-time data updates</li>
                    <li>• New content uploads</li>
                    <li>• Account changes</li>
                    <li>• API calls and server requests</li>
                </ul>
            </div>

            <!-- Tips -->
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                <h3 class="font-semibold text-blue-900 mb-2 flex items-center gap-2">
                    <i class="fas fa-lightbulb"></i>
                    Tips
                </h3>
                <ul class="text-sm text-blue-800 space-y-1">
                    <li>✓ Check your WiFi or mobile connection</li>
                    <li>✓ Try moving closer to your router</li>
                    <li>✓ Restart your device's network</li>
                    <li>✓ Contact your internet provider if problems persist</li>
                </ul>
            </div>

            <!-- Action Buttons -->
            <div class="space-y-3">
                <button onclick="location.reload()" 
                        class="w-full px-6 py-3 bg-gradient-to-r from-primary-500 to-primary-600 text-white font-semibold rounded-lg hover:shadow-lg transition-all transform hover:-translate-y-0.5 active:translate-y-0 flex items-center justify-center gap-2">
                    <i class="fas fa-sync"></i>
                    Try Again
                </button>
                <button onclick="history.back()" 
                        class="w-full px-6 py-3 bg-gray-200 text-gray-900 font-semibold rounded-lg hover:bg-gray-300 transition flex items-center justify-center gap-2">
                    <i class="fas fa-arrow-left"></i>
                    Go Back
                </button>
            </div>

            <!-- Status Info -->
            <div class="mt-8 p-4 bg-gray-200 rounded-lg text-center">
                <p class="text-sm text-gray-700" id="status-info">
                    <span id="status-indicator" class="inline-block w-2 h-2 bg-red-500 rounded-full mr-2"></span>
                    Offline
                </p>
                <p class="text-xs text-gray-600 mt-1">Last online: <span id="last-online">just now</span></p>
            </div>
        </div>
    </div>

    <script>
        // Update online status
        function updateStatus() {
            const indicator = document.getElementById('status-indicator');
            const statusInfo = document.getElementById('status-info');
            
            if (navigator.onLine) {
                indicator.classList.remove('bg-red-500');
                indicator.classList.add('bg-green-500');
                statusInfo.textContent = 'Back Online!';
                
                // Auto-reload after 2 seconds when back online
                setTimeout(() => {
                    location.reload();
                }, 2000);
            }
        }

        window.addEventListener('online', updateStatus);
        window.addEventListener('offline', () => {
            document.getElementById('last-online').textContent = 'disconnected';
        });

        // Update last online time
        setInterval(() => {
            const lastOnline = document.getElementById('last-online');
            if (lastOnline.textContent === 'disconnected') return;
            
            const now = new Date();
            const minutes = Math.floor((Date.now() - now.getTime()) / 60000);
            lastOnline.textContent = minutes > 0 ? `${minutes}m ago` : 'just now';
        }, 10000);

        // Check if back online
        updateStatus();
    </script>
</body>
</html>
