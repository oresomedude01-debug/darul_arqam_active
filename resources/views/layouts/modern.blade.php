<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" 
      dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') - Darul Arqam</title>

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Alpine.js CDN -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- Modern Design System -->
    <link rel="stylesheet" href="{{ asset('css/modern-design.css') }}">
    
    <!-- RTL Support CSS -->
    <link rel="stylesheet" href="{{ asset('css/rtl.css') }}">

    <!-- Custom Tailwind Config -->
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#f0f9ff',
                            100: '#e0f2fe',
                            200: '#bae6fd',
                            300: '#7dd3fc',
                            400: '#38bdf8',
                            500: '#0ea5e9',
                            600: '#0284c7',
                            700: '#0369a1',
                            800: '#075985',
                            900: '#0c4a6e',
                        }
                    }
                }
            }
        }
    </script>

    <style>
        [x-cloak] { display: none !important; }
    </style>

    @stack('styles')
</head>
<body class="bg-gray-50 font-sans antialiased" x-data="{
    sidebarOpen: window.innerWidth >= 1024,
    sidebarCollapsed: false,
    mobileMenuOpen: false
}" x-init="
    window.addEventListener('resize', () => {
        if (window.innerWidth >= 1024) {
            sidebarOpen = true;
            mobileMenuOpen = false;
        } else {
            sidebarOpen = false;
        }
    })
">
    <!-- Mobile Overlay -->
    <div x-show="mobileMenuOpen"
         @click="mobileMenuOpen = false"
         x-transition:enter="transition-opacity ease-linear duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity ease-linear duration-300"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm z-40 lg:hidden"
         x-cloak>
    </div>

    <!-- Sidebar -->
    <aside x-show="sidebarOpen || mobileMenuOpen"
           x-transition:enter="transition ease-out duration-300"
           x-transition:enter-start="-translate-x-full"
           x-transition:enter-end="translate-x-0"
           x-transition:leave="transition ease-in duration-300"
           x-transition:leave-start="translate-x-0"
           x-transition:leave-end="-translate-x-full"
           :class="sidebarCollapsed && !mobileMenuOpen ? 'lg:w-20' : 'lg:w-64'"
           class="fixed inset-y-0 left-0 z-50 w-64 bg-gradient-to-br from-primary-900 via-primary-800 to-primary-900 text-white shadow-2xl transition-all duration-300 lg:translate-x-0"
           x-cloak>

        <!-- Sidebar Header -->
        <div class="h-16 flex items-center justify-between px-4 border-b border-primary-700/50">
            <div class="flex items-center space-x-3 overflow-hidden">
                <div class="flex-shrink-0 w-10 h-10 bg-white rounded-lg flex items-center justify-center shadow-lg">
                    <svg class="w-6 h-6 text-primary-600" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M10.394 2.08a1 1 0 00-.788 0l-7 3a1 1 0 000 1.84L5.25 8.051a.999.999 0 01.356-.257l4-1.714a1 1 0 11.788 1.838L7.667 9.088l1.94.831a1 1 0 00.787 0l7-3a1 1 0 000-1.838l-7-3zM3.31 9.397L5 10.12v4.102a8.969 8.969 0 00-1.05-.174 1 1 0 01-.89-.89 11.115 11.115 0 01.25-3.762zM9.3 16.573A9.026 9.026 0 007 14.935v-3.957l1.818.78a3 3 0 002.364 0l5.508-2.361a11.026 11.026 0 01.25 3.762 1 1 0 01-.89.89 8.968 8.968 0 00-5.35 2.524 1 1 0 01-1.4 0zM6 18a1 1 0 001-1v-2.065a8.935 8.935 0 00-2-.712V17a1 1 0 001 1z"/>
                    </svg>
                </div>
                <div x-show="!sidebarCollapsed || mobileMenuOpen" class="transition-opacity duration-200">
                    <h1 class="text-base font-bold leading-tight">Darul Arqam</h1>
                    <p class="text-xs text-primary-200">School System</p>
                </div>
            </div>

            <!-- Mobile Close Button -->
            <button @click="mobileMenuOpen = false"
                    class="lg:hidden text-white/80 hover:text-white p-2 rounded-lg hover:bg-primary-700/30 transition">
                <i class="fas fa-times text-lg"></i>
            </button>

            <!-- Desktop Collapse Button -->
            <button @click="sidebarCollapsed = !sidebarCollapsed"
                    class="hidden lg:block text-white/80 hover:text-white p-2 rounded-lg hover:bg-primary-700/30 transition">
                <i :class="sidebarCollapsed ? 'fa-angles-right' : 'fa-angles-left'" class="fas text-sm"></i>
            </button>
        </div>

        <!-- Navigation Menu -->
        <nav class="flex-1 px-3 py-4 space-y-1 overflow-y-auto">
            <!-- Dashboard -->
            <a href="{{ route('dashboard') }}"
               class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-all
                      {{ request()->routeIs('dashboard')
                         ? 'bg-primary-700/50 text-white shadow-lg'
                         : 'text-primary-100 hover:bg-primary-700/30 hover:text-white' }}">
                <i class="fas fa-home text-base w-5"></i>
                <span x-show="!sidebarCollapsed || mobileMenuOpen" class="transition-opacity">Dashboard</span>
            </a>

            <!-- Students -->
            <div x-data="{ open: {{ request()->is('students*') ? 'true' : 'false' }} }">
                <button @click="open = !open"
                        class="w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-all
                               {{ request()->is('students*')
                                  ? 'bg-primary-700/50 text-white'
                                  : 'text-primary-100 hover:bg-primary-700/30 hover:text-white' }}">
                    <i class="fas fa-user-graduate text-base w-5"></i>
                    <span x-show="!sidebarCollapsed || mobileMenuOpen" class="flex-1 text-left transition-opacity">Students</span>
                    <i x-show="!sidebarCollapsed || mobileMenuOpen" :class="open ? 'fa-chevron-down' : 'fa-chevron-right'" class="fas text-xs transition-transform"></i>
                </button>
                <div x-show="open && (!sidebarCollapsed || mobileMenuOpen)"
                     x-transition
                     class="ml-8 mt-1 space-y-1"
                     x-cloak>
                    <a href="{{ route('students.index') }}"
                       class="block px-3 py-2 text-sm rounded-lg {{ request()->routeIs('students.index') ? 'text-white bg-primary-700/30' : 'text-primary-200 hover:text-white hover:bg-primary-700/20' }}">
                        All Students
                    </a>
                    <a href="{{ route('students.create') }}"
                       class="block px-3 py-2 text-sm rounded-lg {{ request()->routeIs('students.create') ? 'text-white bg-primary-700/30' : 'text-primary-200 hover:text-white hover:bg-primary-700/20' }}">
                        Add Student
                    </a>
                    <a href="{{ route('students.import-form') }}"
                       class="block px-3 py-2 text-sm rounded-lg {{ request()->routeIs('students.import-form') ? 'text-white bg-primary-700/30' : 'text-primary-200 hover:text-white hover:bg-primary-700/20' }}">
                        Import Students
                    </a>
                </div>
            </div>

            <!-- Teachers -->
            <a href="{{ route('teachers.index') }}"
               class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-all
                      {{ request()->is('teachers*')
                         ? 'bg-primary-700/50 text-white shadow-lg'
                         : 'text-primary-100 hover:bg-primary-700/30 hover:text-white' }}">
                <i class="fas fa-chalkboard-teacher text-base w-5"></i>
                <span x-show="!sidebarCollapsed || mobileMenuOpen" class="transition-opacity">Teachers</span>
            </a>

            <!-- Classes -->
            <a href="{{ route('classes.index') }}"
               class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-all
                      {{ request()->is('classes*')
                         ? 'bg-primary-700/50 text-white shadow-lg'
                         : 'text-primary-100 hover:bg-primary-700/30 hover:text-white' }}">
                <i class="fas fa-door-open text-base w-5"></i>
                <span x-show="!sidebarCollapsed || mobileMenuOpen" class="transition-opacity">Classes</span>
            </a>

            <!-- Subjects -->
            <a href="{{ route('subjects.index') }}"
               class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-all
                      {{ request()->is('subjects*')
                         ? 'bg-primary-700/50 text-white shadow-lg'
                         : 'text-primary-100 hover:bg-primary-700/30 hover:text-white' }}">
                <i class="fas fa-book text-base w-5"></i>
                <span x-show="!sidebarCollapsed || mobileMenuOpen" class="transition-opacity">Subjects</span>
            </a>

            <!-- Attendance -->
            <a href="{{ route('attendance.index') }}"
               class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-all
                      {{ request()->is('attendance*')
                         ? 'bg-primary-700/50 text-white shadow-lg'
                         : 'text-primary-100 hover:bg-primary-700/30 hover:text-white' }}">
                <i class="fas fa-calendar-check text-base w-5"></i>
                <span x-show="!sidebarCollapsed || mobileMenuOpen" class="transition-opacity">Attendance</span>
            </a>

            <!-- Grades -->
            <a href="{{ route('grades.index') }}"
               class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-all
                      {{ request()->is('grades*')
                         ? 'bg-primary-700/50 text-white shadow-lg'
                         : 'text-primary-100 hover:bg-primary-700/30 hover:text-white' }}">
                <i class="fas fa-chart-line text-base w-5"></i>
                <span x-show="!sidebarCollapsed || mobileMenuOpen" class="transition-opacity">Grades & Results</span>
            </a>

            <!-- Calendar & Events -->
            <a href="{{ route('calendar.index') }}"
               class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-all
                      {{ request()->is('calendar*')
                         ? 'bg-primary-700/50 text-white shadow-lg'
                         : 'text-primary-100 hover:bg-primary-700/30 hover:text-white' }}">
                <i class="fas fa-calendar-alt text-base w-5"></i>
                <span x-show="!sidebarCollapsed || mobileMenuOpen" class="transition-opacity">Calendar & Events</span>
            </a>

            <!-- Tokens -->
            <a href="{{ route('tokens.index') }}"
               class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-all
                      {{ request()->is('tokens*')
                         ? 'bg-primary-700/50 text-white shadow-lg'
                         : 'text-primary-100 hover:bg-primary-700/30 hover:text-white' }}">
                <i class="fas fa-ticket-alt text-base w-5"></i>
                <span x-show="!sidebarCollapsed || mobileMenuOpen" class="transition-opacity">Registration Tokens</span>
            </a>

            <!-- Divider -->
            <div class="my-4 border-t border-primary-700/50"></div>

            <!-- Settings -->
            <a href="#"
               class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium text-primary-100 hover:bg-primary-700/30 hover:text-white transition-all">
                <i class="fas fa-cog text-base w-5"></i>
                <span x-show="!sidebarCollapsed || mobileMenuOpen" class="transition-opacity">Settings</span>
            </a>
        </nav>

        <!-- User Profile (Bottom) -->
        <div class="p-4 border-t border-primary-700/50">
            <div x-data="{ open: false }" class="relative">
                <button @click="open = !open"
                        class="w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-primary-100 hover:bg-primary-700/30 transition-all">
                    <div class="w-8 h-8 rounded-full bg-gradient-to-br from-primary-400 to-primary-600 flex items-center justify-center text-white font-semibold text-sm flex-shrink-0">
                        {{ substr(Auth::user()->name, 0, 2) }}
                    </div>
                    <div x-show="!sidebarCollapsed || mobileMenuOpen" class="flex-1 text-left transition-opacity">
                        <p class="text-sm font-medium text-white leading-tight">{{ Auth::user()->name }}</p>
                        <p class="text-xs text-primary-200 truncate">{{ Auth::user()->email }}</p>
                    </div>
                    <i x-show="!sidebarCollapsed || mobileMenuOpen" class="fas fa-chevron-up text-xs"></i>
                </button>

                <div x-show="open"
                     @click.away="open = false"
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 scale-95"
                     x-transition:enter-end="opacity-100 scale-100"
                     x-transition:leave="transition ease-in duration-150"
                     x-transition:leave-start="opacity-100 scale-100"
                     x-transition:leave-end="opacity-0 scale-95"
                     class="absolute bottom-full left-0 right-0 mb-2 bg-white rounded-lg shadow-xl border border-gray-200 overflow-hidden"
                     x-cloak>
                    <a href="#" class="block px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 transition">
                        <i class="fas fa-user-circle mr-2 text-gray-400"></i>
                        My Profile
                    </a>
                    <a href="#" class="block px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 transition">
                        <i class="fas fa-cog mr-2 text-gray-400"></i>
                        Settings
                    </a>
                    <div class="border-t border-gray-200"></div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-full text-left px-4 py-2.5 text-sm text-red-600 hover:bg-red-50 transition">
                            <i class="fas fa-sign-out-alt mr-2"></i>
                            Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </aside>

    <!-- Main Content Area -->
    <div :class="sidebarOpen && !mobileMenuOpen && !sidebarCollapsed ? 'lg:ml-64' : (sidebarOpen && !mobileMenuOpen && sidebarCollapsed ? 'lg:ml-20' : '')"
         class="flex-1 flex flex-col min-h-screen transition-all duration-300">

        <!-- Top Header -->
        <header class="sticky top-0 z-30 bg-white border-b border-gray-200 shadow-sm">
            <div class="px-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between h-16">
                    <!-- Left: Menu Button + Breadcrumb -->
                    <div class="flex items-center gap-4">
                        <button @click="mobileMenuOpen = !mobileMenuOpen"
                                class="lg:hidden p-2 text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-lg transition">
                            <i class="fas fa-bars text-xl"></i>
                        </button>

                        <div class="hidden sm:block">
                            <nav class="flex items-center space-x-2 text-sm">
                                @yield('breadcrumb')
                            </nav>
                        </div>
                    </div>

                    <!-- Right: Search, Language, Notifications, User -->
                    <div class="flex items-center gap-2 sm:gap-3">
                        <!-- Search (Hidden on mobile) -->
                        <div class="hidden md:block relative">
                            <input type="text"
                                   placeholder="Search..."
                                   class="w-64 pl-10 pr-4 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent transition">
                            <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                        </div>

                        <!-- Language Switcher -->
                        <div x-data="{ open: false }" class="relative">
                            <button @click="open = !open"
                                    class="flex items-center gap-2 px-3 py-2 text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-lg transition">
                                <i class="fas fa-language text-lg"></i>
                                <span class="hidden sm:inline text-sm font-medium">{{ app()->getLocale() === 'ar' ? 'العربية' : 'English' }}</span>
                                <i class="fas fa-chevron-down text-xs"></i>
                            </button>

                            <div x-show="open"
                                 @click.away="open = false"
                                 x-transition
                                 class="dropdown-menu"
                                 x-cloak>
                                <a href="{{ route('locale.switch', 'en') }}"
                                   class="dropdown-item {{ app()->getLocale() === 'en' ? 'bg-primary-50 text-primary-700' : '' }}">
                                    <span>🇬🇧</span>
                                    <span>English</span>
                                    @if(app()->getLocale() === 'en')
                                        <i class="fas fa-check ml-auto text-primary-600"></i>
                                    @endif
                                </a>
                                <a href="{{ route('locale.switch', 'ar') }}"
                                   class="dropdown-item {{ app()->getLocale() === 'ar' ? 'bg-primary-50 text-primary-700' : '' }}">
                                    <span>🇸🇦</span>
                                    <span>العربية</span>
                                    @if(app()->getLocale() === 'ar')
                                        <i class="fas fa-check ml-auto text-primary-600"></i>
                                    @endif
                                </a>
                            </div>
                        </div>

                        <!-- Notifications -->
                        <div x-data="{ open: false }" class="relative">
                            <button @click="open = !open"
                                    class="relative p-2 text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-lg transition">
                                <i class="fas fa-bell text-lg"></i>
                                <span class="absolute top-1 right-1 w-2 h-2 bg-red-500 rounded-full"></span>
                            </button>

                            <div x-show="open"
                                 @click.away="open = false"
                                 x-transition
                                 class="absolute right-0 mt-2 w-80 bg-white rounded-lg shadow-xl border border-gray-200 overflow-hidden"
                                 x-cloak>
                                <div class="px-4 py-3 border-b border-gray-200 bg-gray-50">
                                    <h3 class="font-semibold text-gray-900">Notifications</h3>
                                </div>
                                <div class="max-h-96 overflow-y-auto">
                                    <a href="#" class="block px-4 py-3 hover:bg-gray-50 border-b border-gray-100 transition">
                                        <p class="text-sm font-medium text-gray-900">New student registered</p>
                                        <p class="text-xs text-gray-500 mt-1">John Doe added to class 10-A</p>
                                        <p class="text-xs text-gray-400 mt-1">2 hours ago</p>
                                    </a>
                                </div>
                                <div class="px-4 py-2 border-t border-gray-200 bg-gray-50">
                                    <a href="#" class="text-sm text-primary-600 hover:text-primary-700 font-medium">
                                        View all notifications
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main class="flex-1 overflow-y-auto">
            <div class="px-4 sm:px-6 lg:px-8 py-6">
                <!-- Success/Error Messages -->
                @if(session('success'))
                    <div class="alert alert-success mb-6 fade-in">
                        <i class="fas fa-check-circle text-xl"></i>
                        <div>
                            <p class="font-medium">Success!</p>
                            <p class="text-sm">{{ session('success') }}</p>
                        </div>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-error mb-6 fade-in">
                        <i class="fas fa-exclamation-circle text-xl"></i>
                        <div>
                            <p class="font-medium">Error!</p>
                            <p class="text-sm">{{ session('error') }}</p>
                        </div>
                    </div>
                @endif

                @yield('content')
            </div>
        </main>

        <!-- Footer -->
        <footer class="bg-white border-t border-gray-200 py-4 px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col sm:flex-row items-center justify-between gap-4 text-sm text-gray-600">
                <p>&copy; {{ date('Y') }} Darul Arqam School Management System. All rights reserved.</p>
                <div class="flex items-center gap-4">
                    <a href="#" class="hover:text-primary-600 transition">Privacy</a>
                    <a href="#" class="hover:text-primary-600 transition">Terms</a>
                    <a href="#" class="hover:text-primary-600 transition">Support</a>
                </div>
            </div>
        </footer>
    </div>

    <!-- Toast Notification Container -->
    <div id="toast-container"
         x-data="toastManager()"
         @toast.window="addToast($event.detail)"
         class="fixed top-4 right-4 z-50 flex flex-col gap-3 max-w-sm">
        <template x-for="toast in toasts" :key="toast.id">
            <div x-show="toast.visible"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="transform translate-x-full opacity-0"
                 x-transition:enter-end="transform translate-x-0 opacity-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="transform translate-x-0 opacity-100"
                 x-transition:leave-end="transform translate-x-full opacity-0"
                 :class="{
                     'bg-green-50 border-green-200 text-green-900': toast.type === 'success',
                     'bg-red-50 border-red-200 text-red-900': toast.type === 'error',
                     'bg-yellow-50 border-yellow-200 text-yellow-900': toast.type === 'warning',
                     'bg-blue-50 border-blue-200 text-blue-900': toast.type === 'info'
                 }"
                 class="flex items-start gap-3 p-4 rounded-lg border shadow-lg">
                <i :class="{
                    'fas fa-check-circle text-green-600': toast.type === 'success',
                    'fas fa-exclamation-circle text-red-600': toast.type === 'error',
                    'fas fa-exclamation-triangle text-yellow-600': toast.type === 'warning',
                    'fas fa-info-circle text-blue-600': toast.type === 'info'
                }" class="text-xl mt-0.5"></i>
                <div class="flex-1">
                    <p class="font-semibold text-sm" x-text="toast.title"></p>
                    <p class="text-xs mt-1" x-text="toast.message" x-show="toast.message"></p>
                </div>
                <button @click="removeToast(toast.id)" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </template>
    </div>

    @stack('scripts')

    <script>
        // Toast Notification System
        function toastManager() {
            return {
                toasts: [],
                nextId: 1,

                addToast(data) {
                    const toast = {
                        id: this.nextId++,
                        type: data.type || 'info',
                        title: data.title || 'Notification',
                        message: data.message || '',
                        visible: true
                    };

                    this.toasts.push(toast);

                    // Auto-remove after 5 seconds
                    setTimeout(() => {
                        this.removeToast(toast.id);
                    }, data.duration || 5000);
                },

                removeToast(id) {
                    const index = this.toasts.findIndex(t => t.id === id);
                    if (index !== -1) {
                        this.toasts[index].visible = false;
                        setTimeout(() => {
                            this.toasts.splice(index, 1);
                        }, 200);
                    }
                }
            };
        }

        // Global toast function
        window.showToast = function(type, title, message, duration = 5000) {
            window.dispatchEvent(new CustomEvent('toast', {
                detail: { type, title, message, duration }
            }));
        };

        // Auto-hide alerts after 5 seconds
        setTimeout(() => {
            document.querySelectorAll('.alert').forEach(alert => {
                alert.style.opacity = '0';
                alert.style.transform = 'translateY(-10px)';
                setTimeout(() => alert.remove(), 300);
            });
        }, 5000);
    </script>
</body>
</html>
