@php
    $schoolSettings = \App\Models\SchoolSetting::getInstance();
@endphp

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - {{ $schoolSettings->school_name ?? 'School' }}</title>

    <!-- Tailwind CSS via CDN -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- Modern Design System -->
    <link rel="stylesheet" href="{{ asset('css/modern-design.css') }}">

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Poppins', 'sans-serif'],
                    },
                }
            }
        }
    </script>

    <style>
        .gradient-bg {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .gradient-primary {
            background: linear-gradient(135deg, #0ea5e9 0%, #0369a1 100%) !important;
            color: #ffffff !important;
        }
        .gradient-primary:hover {
            opacity: 0.95 !important;
        }
        .gradient-primary:disabled {
            opacity: 0.6 !important;
            cursor: not-allowed !important;
        }
    </style>
    <script>
        function forgotPasswordData() {
            return {
                showForgotPassword: false,
                forgotPasswordEmail: '',
                forgotPasswordMessage: '',
                forgotPasswordSuccess: false,
                sendingResetLink: false,
                async submitForgotPassword() {
                    this.sendingResetLink = true;
                    this.forgotPasswordMessage = '';

                    try {
                        const response = await fetch('/api/password-reset-request', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                            },
                            body: JSON.stringify({
                                email: this.forgotPasswordEmail
                            })
                        });

                        const data = await response.json();

                        if (response.ok) {
                            this.forgotPasswordSuccess = true;
                            this.forgotPasswordMessage = '✓ Password reset link sent! Check your email.';
                            this.forgotPasswordEmail = '';
                            setTimeout(() => {
                                this.showForgotPassword = false;
                                this.forgotPasswordMessage = '';
                                this.forgotPasswordSuccess = false;
                            }, 2500);
                        } else {
                            this.forgotPasswordSuccess = false;
                            this.forgotPasswordMessage = data.message || 'Failed to send reset link. Please try again.';
                        }
                    } catch (error) {
                        this.forgotPasswordSuccess = false;
                        this.forgotPasswordMessage = 'An error occurred. Please try again.';
                    } finally {
                        this.sendingResetLink = false;
                    }
                }
            }
        }
    </script>
<body class="font-sans" x-data="forgotPasswordData()">
    <div class="min-h-screen flex">
        <!-- Left Panel - Login Form -->
        <div class="w-full lg:w-1/2 flex items-center justify-center p-8 bg-white">
            <div class="w-full max-w-md">
                <!-- Logo & Title -->
                <div class="text-center mb-8">
                    @if($schoolSettings->school_logo)
                        <img src="{{ asset('storage/' . $schoolSettings->school_logo) }}" alt="School Logo" class="h-16 w-auto mx-auto mb-4">
                    @else
                        <div class="w-20 h-20 bg-gradient-primary rounded-2xl flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-graduation-cap text-4xl text-white"></i>
                        </div>
                    @endif
                    <h1 class="text-3xl font-bold text-gray-900 mb-2">{{ $schoolSettings->school_name ?? 'Welcome' }}</h1>
                    <p class="text-gray-600">{{ $schoolSettings->school_motto ?? 'Sign in to your account to continue' }}</p>
                </div>

                <!-- Error Messages -->
                @if ($errors->any())
                    <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6 rounded">
                        <div class="flex items-start">
                            <i class="fas fa-exclamation-circle text-red-500 mr-3 mt-0.5"></i>
                            <div>
                                <p class="font-semibold text-red-900">Error</p>
                                <ul class="text-sm text-red-700 mt-1">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Login Form -->
                <form method="POST" action="{{ route('login') }}" class="space-y-6">
                    @csrf

                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-envelope mr-2 text-gray-400"></i>Email Address
                        </label>
                        <input type="email"
                               id="email"
                               name="email"
                               value="{{ old('email') }}"
                               required
                               autofocus
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                               placeholder="admin@darularqam.edu">
                    </div>

                    <!-- Password -->
                    <div x-data="{ showPassword: false }">
                        <label for="password" class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-lock mr-2 text-gray-400"></i>Password
                        </label>
                        <div class="relative">
                            <input :type="showPassword ? 'text' : 'password'"
                                   id="password"
                                   name="password"
                                   required
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 pr-12"
                                   placeholder="••••••••">
                            <button type="button"
                                    @click="showPassword = !showPassword"
                                    class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600">
                                <i class="fas" :class="showPassword ? 'fa-eye-slash' : 'fa-eye'"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Remember Me & Forgot Password -->
                    <div class="flex items-center justify-between">
                        <label class="flex items-center">
                            <input type="checkbox" name="remember" class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                            <span class="ml-2 text-sm text-gray-600">Remember me</span>
                        </label>
                        <button type="button" @click="showForgotPassword = true" class="text-sm font-semibold text-blue-600 hover:text-blue-700">
                            Forgot password?
                        </button>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" class="w-full gradient-primary py-3 rounded-lg font-semibold hover:shadow-xl transition-all duration-300 transform hover:scale-105">
                        <i class="fas fa-sign-in-alt mr-2"></i>Sign In
                    </button>
                </form>

                <!-- Divider -->
                <div class="relative my-8">
                    <div class="absolute inset-0 flex items-center">
                        <div class="w-full border-t border-gray-300"></div>
                    </div>
                    <div class="relative flex justify-center text-sm">
                        <span class="px-4 bg-white text-gray-500">Or </span>
                    </div>
                </div>

                <!-- Public Links -->
                <div class="space-y-3">
                    <a href="{{ route('landing') }}" class="w-full flex items-center justify-center px-4 py-3 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors duration-200">
                        <i class="fas fa-home mr-2 text-gray-600"></i>
                        <span class="text-gray-700 font-medium">Back to Home</span>
                    </a>
                    <a href="{{ route('enrollment.token') }}" class="w-full flex items-center justify-center px-4 py-3 bg-green-50 border border-green-200 rounded-lg hover:bg-green-100 transition-colors duration-200">
                        <i class="fas fa-edit mr-2 text-green-600"></i>
                        <span class="text-green-700 font-medium">Enroll Your Child</span>
                    </a>
                </div>

                <!-- Footer -->
                <p class="text-center text-sm text-gray-600 mt-8">
                    © {{ date('Y') }} Darul Arqam School. All rights reserved.
                </p>
            </div>
        </div>

        <!-- Right Panel - Decorative -->
        <div class="hidden lg:flex lg:w-1/2 gradient-bg items-center justify-center p-12 relative overflow-hidden">
            <!-- Decorative Elements -->
            <div class="absolute top-20 left-20 w-64 h-64 bg-white/10 rounded-full blur-3xl"></div>
            <div class="absolute bottom-20 right-20 w-96 h-96 bg-white/10 rounded-full blur-3xl"></div>

            <div class="relative z-10 text-white text-center">
                <!-- Illustration -->
                <div class="mb-8">
                    <svg viewBox="0 0 400 400" class="w-80 h-80 mx-auto">
                        <circle cx="200" cy="200" r="150" fill="#ffffff" opacity="0.1"/>
                        <circle cx="200" cy="200" r="100" fill="#ffffff" opacity="0.1"/>

                        <!-- Book -->
                        <path d="M150 180 L250 180 L250 280 L150 280 Z" fill="#ffffff" opacity="0.9"/>
                        <path d="M200 180 L200 280" stroke="#667eea" stroke-width="3"/>
                        <line x1="160" y1="200" x2="190" y2="200" stroke="#667eea" stroke-width="2"/>
                        <line x1="210" y1="200" x2="240" y2="200" stroke="#667eea" stroke-width="2"/>
                        <line x1="160" y1="220" x2="190" y2="220" stroke="#667eea" stroke-width="2"/>
                        <line x1="210" y1="220" x2="240" y2="220" stroke="#667eea" stroke-width="2"/>

                        <!-- Graduation Cap -->
                        <path d="M200 140 L150 160 L200 180 L250 160 Z" fill="#fbbf24"/>
                        <rect x="195" y="140" width="10" height="60" fill="#fbbf24"/>
                        <circle cx="200" cy="135" r="10" fill="#ffffff"/>
                    </svg>
                </div>

                <h2 class="text-4xl font-bold mb-4">{{ $schoolSettings->school_name ?? 'School' }}</h2>
                <p class="text-xl mb-6 text-white/90">{{ $schoolSettings->school_motto ?? 'Excellence in Education' }}</p>
                <p class="text-white/80 max-w-md mx-auto">
                    {{ $schoolSettings->school_vision ?? 'Empowering students with knowledge, character, and faith.' }}
                </p>

                <!-- Stats -->
                <div class="grid grid-cols-3 gap-6 mt-12 max-w-lg mx-auto">
                    <div class="text-center">
                        <div class="text-3xl font-bold text-yellow-300">{{ $schoolSettings->founded_year ?? '20' }}+</div>
                        <div class="text-sm text-white/80 mt-1">Years</div>
                    </div>
                    <div class="text-center">
                        <div class="text-3xl font-bold text-yellow-300">2K+</div>
                        <div class="text-sm text-white/80 mt-1">Students</div>
                    </div>
                    <div class="text-center">
                        <div class="text-3xl font-bold text-yellow-300">98%</div>
                        <div class="text-sm text-white/80 mt-1">Success</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Forgot Password Modal -->
    <div x-show="showForgotPassword" x-cloak @keydown.escape="showForgotPassword = false" class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4" x-transition>
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md" @click.stop>
            <!-- Modal Header -->
            <div class="bg-gradient-to-r from-blue-600 to-purple-600 px-6 py-6 flex items-center justify-between">
                <h3 class="text-xl font-bold text-white">Reset Password</h3>
                <button @click="showForgotPassword = false" class="text-white/80 hover:text-white text-2xl">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <!-- Modal Body -->
            <div class="p-6">
                <p class="text-gray-600 mb-6">Enter your email address and we'll send you a link to reset your password.</p>

                <form @submit.prevent="submitForgotPassword" id="forgotPasswordForm">
                    <div class="mb-6">
                        <label class="block text-sm font-semibold text-gray-900 mb-2">Email Address</label>
                        <input type="email" 
                               x-model="forgotPasswordEmail"
                               class="w-full px-4 py-3 border-2 border-gray-200 rounded-lg focus:border-blue-600 focus:ring-2 focus:ring-blue-100 transition" 
                               placeholder="your@email.com"
                               required>
                    </div>

                    <div x-show="forgotPasswordMessage" x-cloak class="mb-4 p-4 rounded-lg text-sm" :class="forgotPasswordSuccess ? 'bg-green-50 text-green-700 border border-green-200' : 'bg-red-50 text-red-700 border border-red-200'" x-transition>
                        <span x-text="forgotPasswordMessage"></span>
                    </div>

                    <button type="submit" :disabled="sendingResetLink" class="w-full gradient-primary py-3 rounded-lg font-semibold hover:shadow-xl transition-all disabled:opacity-50">
                        <span x-show="!sendingResetLink">Send Reset Link</span>
                        <span x-show="sendingResetLink" x-cloak><i class="fas fa-spinner fa-spin mr-2"></i>Sending...</span>
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-gray-900 text-gray-400 text-sm py-4 fixed bottom-0 w-full">
        <div class="container mx-auto px-4 text-center">
            <p>&copy; {{ date('Y') }} {{ $schoolSettings->school_name ?? 'School' }}. {{ $schoolSettings->footer_text ?? 'All rights reserved.' }}</p>
        </div>
    </footer>

</body>
</html>
