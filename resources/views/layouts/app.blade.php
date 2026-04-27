<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" 
      dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}"
      class="h-full bg-gray-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Darul Arqam') - School Management System</title>

    <!-- Tailwind CSS via CDN -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Alpine.js via CDN for React-like interactivity -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('css/custom.css') }}">
    
    <!-- RTL Support CSS -->
    <link rel="stylesheet" href="{{ asset('css/rtl.css') }}">
    
    <!-- Scrollbar CSS -->
    <link rel="stylesheet" href="{{ asset('css/scrollbar.css') }}">

    <!-- Tailwind Config -->
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
                        },
                        secondary: {
                            50: '#faf5ff',
                            100: '#f3e8ff',
                            200: '#e9d5ff',
                            300: '#d8b4fe',
                            400: '#c084fc',
                            500: '#a855f7',
                            600: '#9333ea',
                            700: '#7e22ce',
                            800: '#6b21a8',
                            900: '#581c87',
                        }
                    }
                }
            }
        }
    </script>

    @stack('styles')
</head>
<body class="h-full" x-data="{ sidebarOpen: false, mobileMenuOpen: false }">

    <!-- Mobile Sidebar Backdrop -->
    <div x-show="sidebarOpen"
         @click="sidebarOpen = false"
         x-transition:enter="transition-opacity ease-linear duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity ease-linear duration-300"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-gray-900/80 lg:hidden z-40"
         style="display: none;">
    </div>

    <div class="flex h-full">
        <!-- Sidebar -->
        <aside class="fixed inset-y-0 left-0 z-50 w-64 bg-gradient-to-b from-primary-800 to-primary-900 text-white transform transition-transform duration-300 ease-in-out lg:translate-x-0 lg:static lg:inset-auto flex flex-col"
               :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
               x-cloak>

            <!-- Logo -->
            <div class="flex items-center justify-between h-16 px-6 border-b border-primary-700">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-white rounded-lg flex items-center justify-center">
                        <i class="fas fa-graduation-cap text-primary-600 text-xl"></i>
                    </div>
                    <div>
                        <h1 class="text-lg font-bold">Darul Arqam</h1>
                        <p class="text-xs text-primary-200">School Management</p>
                    </div>
                </div>
                <button @click="sidebarOpen = false" class="lg:hidden text-white hover:text-primary-200">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>

            <!-- Navigation -->
            <nav class="flex-1 px-4 py-6 space-y-2 overflow-y-auto overflow-x-hidden scrollbar-thin scrollbar-thumb-primary-600 scrollbar-track-primary-800">
                <!-- Dashboard -->
                <a href="{{ route('dashboard') }}"
                   class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <i class="fas fa-home w-5"></i>
                    <span>{{ __('nav.dashboard') }}</span>
                </a>

                <!-- Students Section - Admin, Teachers -->
                @if(auth()->user()->hasRole(['admin', 'teacher']))
                <div x-data="{ open: {{ request()->is('students*') ? 'true' : 'false' }} }">
                    <button @click="open = !open"
                            class="nav-item w-full justify-between">
                        <div class="flex items-center space-x-3">
                            <i class="fas fa-user-graduate w-5"></i>
                            <span>{{ __('nav.students') }}</span>
                        </div>
                        <i class="fas fa-chevron-down text-xs transform transition-transform"
                           :class="open ? 'rotate-180' : ''"></i>
                    </button>
                    <div x-show="open"
                         x-collapse
                         class="ml-8 mt-2 space-y-1">
                        @if(auth()->user()->hasPermission('view-students'))
                        <a href="{{ route('students.index') }}" class="nav-sub-item">{{ __('nav.all_students') }}</a>
                        @endif
                        @if(auth()->user()->hasPermission('create-student'))
                        <a href="{{ route('students.create') }}" class="nav-sub-item">{{ __('nav.add_new') }}</a>
                        @endif
                        <a href="#" class="nav-sub-item">{{ __('nav.import_students') }}</a>
                    </div>
                </div>
                @endif

                <!-- Student Profile - Students/Parents viewing own profile -->
                @if(auth()->user()->hasRole(['student', 'parent']))
                <a href="{{ route('students.show', auth()->user()->profile->id ?? '') }}"
                   class="nav-item">
                    <i class="fas fa-id-card w-5"></i>
                    <span>{{ __('nav.profile') }}</span>
                </a>
                @endif

                <!-- Registration Tokens - Admin -->
                @if(auth()->user()->hasRole('admin') && auth()->user()->hasPermission('manage-tokens'))
                <div x-data="{ open: {{ request()->is('tokens*') ? 'true' : 'false' }} }">
                    <button @click="open = !open"
                            class="nav-item w-full justify-between">
                        <div class="flex items-center space-x-3">
                            <i class="fas fa-ticket-alt w-5"></i>
                            <span>{{ __('nav.tokens') }}</span>
                        </div>
                        <i class="fas fa-chevron-down text-xs transform transition-transform"
                           :class="open ? 'rotate-180' : ''"></i>
                    </button>
                    <div x-show="open"
                         x-collapse
                         class="ml-8 mt-2 space-y-1">
                        <a href="{{ route('tokens.index') }}" class="nav-sub-item">{{ __('nav.all_tokens') }}</a>
                        <a href="{{ route('tokens.create') }}" class="nav-sub-item">{{ __('nav.generate_tokens') }}</a>
                    </div>
                </div>
                @endif

                <!-- Teachers Section - Admin -->
                @if(auth()->user()->hasRole('admin') && auth()->user()->hasPermission('view-teachers'))
                <div x-data="{ open: {{ request()->is('teachers*') ? 'true' : 'false' }} }">
                    <button @click="open = !open"
                            class="nav-item w-full justify-between">
                        <div class="flex items-center space-x-3">
                            <i class="fas fa-chalkboard-teacher w-5"></i>
                            <span>{{ __('nav.teachers') }}</span>
                        </div>
                        <i class="fas fa-chevron-down text-xs transform transition-transform"
                           :class="open ? 'rotate-180' : ''"></i>
                    </button>
                    <div x-show="open"
                         x-collapse
                         class="ml-8 mt-2 space-y-1">
                        <a href="{{ route('teachers.index') }}" class="nav-sub-item">{{ __('nav.all_teachers') }}</a>
                        @if(auth()->user()->hasPermission('create-teacher'))
                        <a href="{{ route('teachers.create') }}" class="nav-sub-item">{{ __('nav.add_new') }}</a>
                        @endif
                    </div>
                </div>
                @endif

                <!-- Classes Section - Admin, Teachers -->
                @if(auth()->user()->hasRole(['admin', 'teacher']) && auth()->user()->hasPermission('view-classes'))
                <div x-data="{ open: {{ request()->is('classes*') ? 'true' : 'false' }} }">
                    <button @click="open = !open"
                            class="nav-item w-full justify-between">
                        <div class="flex items-center space-x-3">
                            <i class="fas fa-book-open w-5"></i>
                            <span>{{ __('nav.classes') }}</span>
                        </div>
                        <i class="fas fa-chevron-down text-xs transform transition-transform"
                           :class="open ? 'rotate-180' : ''"></i>
                    </button>
                    <div x-show="open"
                         x-collapse
                         class="ml-8 mt-2 space-y-1">
                        <a href="{{ route('classes.index') }}" class="nav-sub-item">{{ __('nav.all_classes') }}</a>
                        @if(auth()->user()->hasPermission('create-class'))
                        <a href="{{ route('classes.create') }}" class="nav-sub-item">{{ __('nav.add_new') }}</a>
                        @endif
                        <a href="#" class="nav-sub-item">{{ __('nav.class_schedule') }}</a>
                    </div>
                </div>
                @endif

                <!-- Attendance - Admin, Teachers -->
                @if(auth()->user()->hasRole(['admin', 'teacher']) && auth()->user()->hasPermission('view-attendance'))
                <a href="{{ route('attendance.index') }}"
                   class="nav-item {{ request()->routeIs('attendance.*') ? 'active' : '' }}">
                    <i class="fas fa-clipboard-check w-5"></i>
                    <span>{{ __('nav.attendance') }}</span>
                </a>
                @endif

                <!-- Grades - Admin, Teachers -->
                @if(auth()->user()->hasRole(['admin', 'teacher']) && auth()->user()->hasPermission('view-grades'))
                <a href="{{ route('grades.index') }}"
                   class="nav-item {{ request()->routeIs('grades.*') ? 'active' : '' }}">
                    <i class="fas fa-chart-line w-5"></i>
                    <span>{{ __('nav.grades') }}</span>
                </a>
                @endif

                <!-- Exams - Admin, Teachers -->
                @if(auth()->user()->hasRole(['admin', 'teacher']) && auth()->user()->hasPermission('view-exams'))
                <a href="#" class="nav-item">
                    <i class="fas fa-file-alt w-5"></i>
                    <span>{{ __('nav.examinations') }}</span>
                </a>
                @endif

                <!-- Library - All authenticated users -->
                @if(auth()->user()->hasPermission('view-library'))
                <a href="#" class="nav-item">
                    <i class="fas fa-book w-5"></i>
                    <span>{{ __('nav.library') }}</span>
                </a>
                @endif

                <!-- Finance - Admin -->
                @if(auth()->user()->hasRole('admin') && auth()->user()->hasPermission('view-finance'))
                <div x-data="{ open: {{ request()->is('billing*', 'payment*') ? 'true' : 'false' }} }">
                    <button @click="open = !open"
                            class="nav-item w-full justify-between">
                        <div class="flex items-center space-x-3">
                            <i class="fas fa-dollar-sign w-5"></i>
                            <span>{{ __('nav.finance') }}</span>
                        </div>
                        <i class="fas fa-chevron-down text-xs transform transition-transform"
                           :class="open ? 'rotate-180' : ''"></i>
                    </button>
                    <div x-show="open"
                         x-collapse
                         class="ml-8 mt-2 space-y-1">
                        <!-- Billing System -->
                        <div x-data="{ billingOpen: {{ request()->is('billing*') ? 'true' : 'false' }} }">
                            <button @click="billingOpen = !billingOpen"
                                    class="nav-sub-item w-full justify-between text-left">
                                <div class="flex items-center">
                                    <i class="fas fa-file-invoice-dollar mr-2 w-4"></i>
                                    <span>Billing</span>
                                </div>
                                <i class="fas fa-chevron-right text-xs transform transition-transform"
                                   :class="billingOpen ? 'rotate-90' : ''"></i>
                            </button>
                            <div x-show="billingOpen"
                                 x-collapse
                                 class="ml-6 mt-1 space-y-1">
                                <a href="{{ route('billing.generate-bills.form') }}" class="nav-sub-item text-sm">
                                    <i class="fas fa-plus-circle mr-2 w-4"></i>{{ __('common.create') }}
                                </a>
                                <a href="{{ route('billing.debt-management') }}" class="nav-sub-item text-sm">
                                    <i class="fas fa-exclamation-circle mr-2 w-4"></i>{{ __('common.pending') }}
                                </a>
                                <a href="{{ route('billing.payment-history') }}" class="nav-sub-item text-sm">
                                    <i class="fas fa-history mr-2 w-4"></i>{{ __('common.view') }}
                                </a>
                            </div>
                        </div>

                        <!-- Payment System -->
                        <div x-data="{ paymentOpen: {{ request()->is('payments*') ? 'true' : 'false' }} }">
                            <button @click="paymentOpen = !paymentOpen"
                                    class="nav-sub-item w-full justify-between text-left">
                                <div class="flex items-center">
                                    <i class="fas fa-credit-card mr-2 w-4"></i>
                                    <span>Payments</span>
                                </div>
                                <i class="fas fa-chevron-right text-xs transform transition-transform"
                                   :class="paymentOpen ? 'rotate-90' : ''"></i>
                            </button>
                            <div x-show="paymentOpen"
                                 x-collapse
                                 class="ml-6 mt-1 space-y-1">
                                <a href="{{ route('payments.index') }}" class="nav-sub-item text-sm">
                                    <i class="fas fa-list mr-2 w-4"></i>{{ __('common.all') }}
                                </a>
                            </div>
                        </div>

                        <!-- Fee Structures -->
                        <a href="{{ route('billing.fee-structures.index') }}" class="nav-sub-item">
                            <i class="fas fa-list-alt mr-2 w-4"></i>{{ __('common.filter') }}
                        </a>

                        <!-- Fee Items -->
                        <a href="{{ route('billing.fee-items.index') }}" class="nav-sub-item">
                            <i class="fas fa-tags mr-2 w-4"></i>{{ __('common.create') }}
                        </a>
                    </div>
                </div>
                @endif

                <!-- Reports - Admin -->
                @if(auth()->user()->hasRole('admin') && auth()->user()->hasPermission('view-reports'))
                <a href="#" class="nav-item">
                    <i class="fas fa-chart-bar w-5"></i>
                    <span>{{ __('nav.reports') }}</span>
                </a>
                @endif

                <!-- Settings - Admin only -->
                @if(auth()->user()->hasRole('admin'))
                <a href="#" class="nav-item">
                    <i class="fas fa-cog w-5"></i>
                    <span>{{ __('nav.settings') }}</span>
                </a>
                @endif
            </nav>

            <!-- User Profile -->
            <div class="border-t border-primary-700 p-4">
                <div x-data="{ open: false }" class="relative">
                    <button @click="open = !open"
                            class="flex items-center space-x-3 w-full p-2 rounded-lg hover:bg-primary-700 transition">
                        <div class="w-10 h-10 rounded-full bg-primary-600 flex items-center justify-center">
                            <span class="text-sm font-bold">{{ substr(Auth::user()->name, 0, 2) }}</span>
                        </div>
                        <div class="flex-1 text-left">
                            <p class="text-sm font-medium">{{ Auth::user()->name }}</p>
                            <p class="text-xs text-primary-300">{{ Auth::user()->email }}</p>
                        </div>
                        <i class="fas fa-chevron-up text-xs"></i>
                    </button>

                    <div x-show="open"
                         @click.away="open = false"
                         x-transition
                         class="absolute bottom-full left-0 right-0 mb-2 bg-white rounded-lg shadow-lg py-2"
                         style="display: none;">
                        <div class="px-4 py-2 border-b border-gray-100">
                            <p class="text-sm font-semibold text-gray-900">{{ Auth::user()->name }}</p>
                            <p class="text-xs text-gray-500">{{ Auth::user()->email }}</p>
                        </div>
                        <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                            <i class="fas fa-user-circle mr-2"></i> {{ __('nav.profile') }}
                        </a>
                        <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                            <i class="fas fa-cog mr-2"></i> {{ __('nav.settings') }}
                        </a>
                        <hr class="my-2">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                                <i class="fas fa-sign-out-alt mr-2"></i> {{ __('nav.logout') }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </aside>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col min-w-0">
            <!-- Top Header -->
            <header class="bg-white shadow-sm sticky top-0 z-30">
                <div class="flex items-center justify-between px-4 py-4 lg:px-8">
                    <!-- Mobile Menu Button & Breadcrumb -->
                    <div class="flex items-center space-x-4">
                        <button @click="sidebarOpen = true"
                                class="lg:hidden text-gray-600 hover:text-gray-900">
                            <i class="fas fa-bars text-xl"></i>
                        </button>

                        <div class="flex items-center space-x-2 text-sm">
                            <i class="fas fa-home text-gray-400"></i>
                            <span class="text-gray-400">/</span>
                            @yield('breadcrumb', 'Dashboard')
                        </div>
                    </div>

                    <!-- Search & Actions -->
                    <div class="flex items-center space-x-4">
                        <!-- Search -->
                        <div class="hidden md:block relative">
                            <input type="text"
                                   placeholder="Search..."
                                   class="w-64 pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                            <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                        </div>

                        <!-- Language Switcher -->
                        <div x-data="{ open: false }" class="relative">
                            <button @click="open = !open"
                                    class="flex items-center space-x-2 p-2 text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-lg">
                                <i class="fas fa-language text-xl"></i>
                                <span class="hidden md:inline text-sm font-medium">{{ app()->getLocale() === 'ar' ? 'العربية' : 'English' }}</span>
                                <i class="fas fa-chevron-down text-xs"></i>
                            </button>

                            <div x-show="open"
                                 @click.away="open = false"
                                 x-transition
                                 class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-xl border border-gray-200 py-2"
                                 style="display: none;">
                                <a href="{{ route('locale.switch', 'en') }}"
                                   class="flex items-center px-4 py-2 hover:bg-gray-50 {{ app()->getLocale() === 'en' ? 'bg-primary-50 text-primary-700' : 'text-gray-700' }}">
                                    <span class="mr-2">🇬🇧</span>
                                    <span class="font-medium">English</span>
                                    @if(app()->getLocale() === 'en')
                                        <i class="fas fa-check ml-auto text-primary-600"></i>
                                    @endif
                                </a>
                                <a href="{{ route('locale.switch', 'ar') }}"
                                   class="flex items-center px-4 py-2 hover:bg-gray-50 {{ app()->getLocale() === 'ar' ? 'bg-primary-50 text-primary-700' : 'text-gray-700' }}">
                                    <span class="mr-2">🇸🇦</span>
                                    <span class="font-medium">العربية</span>
                                    @if(app()->getLocale() === 'ar')
                                        <i class="fas fa-check ml-auto text-primary-600"></i>
                                    @endif
                                </a>
                            </div>
                        </div>

                        <!-- Notifications -->
                        <div x-data="{ open: false }" class="relative">
                            <button @click="open = !open"
                                    class="relative p-2 text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-lg">
                                <i class="fas fa-bell text-xl"></i>
                                <span class="absolute top-1 right-1 w-2 h-2 bg-red-500 rounded-full"></span>
                            </button>

                            <div x-show="open"
                                 @click.away="open = false"
                                 x-transition
                                 class="absolute right-0 mt-2 w-80 bg-white rounded-lg shadow-xl border border-gray-200 py-2"
                                 style="display: none;">
                                <div class="px-4 py-2 border-b border-gray-200">
                                    <h3 class="font-semibold text-gray-900">Notifications</h3>
                                </div>
                                <div class="max-h-96 overflow-y-auto">
                                    <a href="#" class="block px-4 py-3 hover:bg-gray-50 border-b border-gray-100">
                                        <p class="text-sm font-medium text-gray-900">New student registered</p>
                                        <p class="text-xs text-gray-500 mt-1">John Doe has been added to class 10-A</p>
                                        <p class="text-xs text-gray-400 mt-1">2 hours ago</p>
                                    </a>
                                    <a href="#" class="block px-4 py-3 hover:bg-gray-50 border-b border-gray-100">
                                        <p class="text-sm font-medium text-gray-900">Attendance reminder</p>
                                        <p class="text-xs text-gray-500 mt-1">Don't forget to mark today's attendance</p>
                                        <p class="text-xs text-gray-400 mt-1">4 hours ago</p>
                                    </a>
                                </div>
                                <div class="px-4 py-2 border-t border-gray-200">
                                    <a href="#" class="text-sm text-primary-600 hover:text-primary-700 font-medium">
                                        View all notifications
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- Quick Actions -->
                        <button class="hidden lg:flex items-center space-x-2 px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition">
                            <i class="fas fa-plus"></i>
                            <span>Quick Add</span>
                        </button>
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <main class="flex-1 overflow-y-auto bg-gray-50">
                <div class="px-4 py-6 lg:px-8">
                    @yield('content')
                </div>
            </main>

            <!-- Footer -->
            <footer class="bg-white border-t border-gray-200 py-4 px-4 lg:px-8">
                <div class="flex flex-col md:flex-row justify-between items-center text-sm text-gray-600">
                    <p>&copy; {{ date('Y') }} Darul Arqam School Management System. All rights reserved.</p>
                    <p class="mt-2 md:mt-0">Version 1.0.0</p>
                </div>
            </footer>
        </div>
    </div>

    <!-- Custom JavaScript -->
    <script src="{{ asset('js/custom.js') }}"></script>

    @stack('scripts')
</body>
</html>
