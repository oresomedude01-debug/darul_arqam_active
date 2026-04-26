<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title id="page-title">@yield('title', 'Dashboard') - Darul Arqam</title>

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Alpine.js CDN -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- Bootstrap CSS for calendar views -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Optional Modern Design System CSS (from second layout) -->
    <link rel="stylesheet" href="{{ asset('css/modern-design.css') }}">

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

        /* Loading spinner */
        .spinner {
            border: 3px solid #f3f4f6;
            border-top: 3px solid #0284c7;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        /* Page transition */
        .page-transition {
            animation: fadeIn 0.3s ease-in-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Active nav link indicator */
        .nav-link-active {
            background: rgba(255, 255, 255, 0.1) !important;
            border-left: 4px solid #fbbf24;
        }

        /* Scrollable without visible scrollbar */
        .scrollbar-hide {
            -ms-overflow-style: none;  /* IE and Edge */
            scrollbar-width: none;      /* Firefox */
        }
        .scrollbar-hide::-webkit-scrollbar {
            display: none;  /* Chrome, Safari and Opera */
        }
    </style>

    @stack('styles')
</head>
<body class="bg-gray-50 font-sans antialiased"
      x-data="spaApp()"
      x-init="initSPA()"
      @popstate.window="handlePopState($event)">

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
           class="fixed inset-y-0 left-0 z-50 w-64 bg-gradient-to-br from-primary-900 via-primary-800 to-primary-900 text-white shadow-2xl transition-all duration-300 lg:translate-x-0 flex flex-col"
           x-cloak>

        <!-- Sidebar Header -->
        <div class="h-16 flex items-center justify-between px-4 border-b border-primary-700/50">
            <div class="flex items-center space-x-3 overflow-hidden">
                <div class="flex-shrink-0 w-10 h-10 bg-white rounded-lg flex items-center justify-center shadow-lg overflow-hidden">
                    @php
                        $schoolSettings = \App\Models\SchoolSetting::first();
                        $schoolLogo = $schoolSettings && $schoolSettings->school_logo ? asset('storage/' . $schoolSettings->school_logo) : null;
                    @endphp
                    @if($schoolLogo)
                        <img src="{{ $schoolLogo }}" alt="School Logo" class="w-full h-full object-cover">
                    @else
                        <svg class="w-6 h-6 text-primary-600" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M10.394 2.08a1 1 0 00-.788 0l-7 3a1 1 0 000 1.84L5.25 8.051a.999.999 0 01.356-.257l4-1.714a1 1 0 11.788 1.838L7.667 9.088l1.94.831a1 1 0 00.787 0l7-3a1 1 0 000-1.838l-7-3zM3.31 9.397L5 10.12v4.102a8.969 8.969 0 00-1.05-.174 1 1 0 01-.89-.89 11.115 11.115 0 01.25-3.762zM9.3 16.573A9.026 9.026 0 007 14.935v-3.957l1.818.78a3 3 0 002.364 0l5.508-2.361a11.026 11.026 0 01.25 3.762 1 1 0 01-.89.89 8.968 8.968 0 00-5.35 2.524 1 1 0 01-1.4 0zM6 18a1 1 0 001-1v-2.065a8.935 8.935 0 00-2-.712V17a1 1 0 001 1z"/>
                        </svg>
                    @endif
                </div>
                <div x-show="!sidebarCollapsed || mobileMenuOpen" class="transition-opacity duration-200">
                    <h1 class="text-base font-bold leading-tight">{{ $schoolSettings->school_name ?? 'Darul Arqam' }}</h1>
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
        <nav class="flex-1 px-3 py-4 space-y-1 overflow-y-auto scrollbar-hide">
            <!-- Hide all admin/teacher navigation for students - Show only Student Portal -->
            @if(auth()->check() && auth()->user()->hasRole('student'))
                <!-- Student Portal Navigation Only - Visible to students only -->
                <div x-data="{ open: currentPath.includes('/student-portal') }">
                    <button @click="open = !open"
                            :class="currentPath.includes('/student-portal') ? 'bg-primary-700/50 text-white' : 'text-primary-100 hover:bg-primary-700/30 hover:text-white'"
                            class="w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-all">
                        <i class="fas fa-graduation-cap text-base w-5"></i>
                        <span x-show="!sidebarCollapsed || mobileMenuOpen" class="flex-1 text-left transition-opacity">Student Portal</span>
                        <i x-show="!sidebarCollapsed || mobileMenuOpen" :class="open ? 'fa-chevron-down' : 'fa-chevron-right'" class="fas text-xs transition-transform"></i>
                    </button>
                    <div x-show="open && (!sidebarCollapsed || mobileMenuOpen)"
                         x-transition
                         class="ml-8 mt-1 space-y-1"
                         x-cloak>
                        <a href="{{ route('student-portal.dashboard') }}"
                           @click.prevent="navigate('{{ route('student-portal.dashboard') }}')"
                           :class="currentPath === '{{ route('student-portal.dashboard') }}' ? 'text-white bg-primary-700/30' : 'text-primary-200 hover:text-white hover:bg-primary-700/20'"
                           class="block px-3 py-2 text-sm rounded-lg transition-all">
                            <i class="fas fa-home mr-2 text-xs w-4"></i>Dashboard
                        </a>
                        <a href="{{ route('student-portal.timetable') }}"
                           @click.prevent="navigate('{{ route('student-portal.timetable') }}')"
                           :class="currentPath === '{{ route('student-portal.timetable') }}' ? 'text-white bg-primary-700/30' : 'text-primary-200 hover:text-white hover:bg-primary-700/20'"
                           class="block px-3 py-2 text-sm rounded-lg transition-all">
                            <i class="fas fa-clock mr-2 text-xs w-4"></i>Timetable
                        </a>
                        <a href="{{ route('student-portal.calendar') }}"
                           @click.prevent="navigate('{{ route('student-portal.calendar') }}')"
                           :class="currentPath === '{{ route('student-portal.calendar') }}' ? 'text-white bg-primary-700/30' : 'text-primary-200 hover:text-white hover:bg-primary-700/20'"
                           class="block px-3 py-2 text-sm rounded-lg transition-all">
                            <i class="fas fa-calendar-alt mr-2 text-xs w-4"></i>Calendar
                        </a>
                        <a href="{{ route('student-portal.attendance') }}"
                           @click.prevent="navigate('{{ route('student-portal.attendance') }}')"
                           :class="currentPath === '{{ route('student-portal.attendance') }}' ? 'text-white bg-primary-700/30' : 'text-primary-200 hover:text-white hover:bg-primary-700/20'"
                           class="block px-3 py-2 text-sm rounded-lg transition-all">
                            <i class="fas fa-clipboard-list mr-2 text-xs w-4"></i>Attendance
                        </a>
                        <a href="{{ route('student-portal.results') }}"
                           @click.prevent="navigate('{{ route('student-portal.results') }}')"
                           :class="currentPath === '{{ route('student-portal.results') }}' ? 'text-white bg-primary-700/30' : 'text-primary-200 hover:text-white hover:bg-primary-700/20'"
                           class="block px-3 py-2 text-sm rounded-lg transition-all">
                            <i class="fas fa-chart-bar mr-2 text-xs w-4"></i>Results
                        </a>
                        <a href="{{ route('student-portal.profile') }}"
                           @click.prevent="navigate('{{ route('student-portal.profile') }}')"
                           :class="currentPath === '{{ route('student-portal.profile') }}' ? 'text-white bg-primary-700/30' : 'text-primary-200 hover:text-white hover:bg-primary-700/20'"
                           class="block px-3 py-2 text-sm rounded-lg transition-all">
                            <i class="fas fa-user mr-2 text-xs w-4"></i>Profile
                        </a>
                        <a href="{{ route('student-portal.attendance.complaint') }}"
                           @click.prevent="navigate('{{ route('student-portal.attendance.complaint') }}')"
                           :class="currentPath === '{{ route('student-portal.attendance.complaint') }}' ? 'text-white bg-primary-700/30' : 'text-primary-200 hover:text-white hover:bg-primary-700/20'"
                           class="block px-3 py-2 text-sm rounded-lg transition-all">
                            <i class="fas fa-exclamation-circle mr-2 text-xs w-4"></i>Report Attendance
                        </a>
                        <a href="{{ route('student-portal.notifications') }}"
                           @click.prevent="navigate('{{ route('student-portal.notifications') }}')"
                           :class="currentPath === '{{ route('student-portal.notifications') }}' ? 'text-white bg-primary-700/30' : 'text-primary-200 hover:text-white hover:bg-primary-700/20'"
                           class="block px-3 py-2 text-sm rounded-lg transition-all relative">
                            <i class="fas fa-bell mr-2 text-xs w-4"></i>Notifications
                            <span class="inline-block w-2 h-2 bg-red-500 rounded-full ml-2"></span>
                        </a>
                    </div>
                </div>
            @else
                <!-- Dashboard -->
                <a href="/dashboard"
                   @click.prevent="navigate('/dashboard')"
                   :class="currentPath === '/dashboard' ? 'bg-primary-700/50 text-white shadow-lg' : 'text-primary-100 hover:bg-primary-700/30 hover:text-white'"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-all">
                    <i class="fas fa-home text-base w-5"></i>
                    <span x-show="!sidebarCollapsed || mobileMenuOpen" class="transition-opacity">Dashboard</span>
                </a>

                <!-- Students - Visible to: Admin, Teachers, Students (own), Parents (own child) -->
                @hasPermission('view-students')
            <div x-data="{ open: currentPath.includes('/students') }">
                <button @click="open = !open"
                        :class="currentPath.includes('/students') ? 'bg-primary-700/50 text-white' : 'text-primary-100 hover:bg-primary-700/30 hover:text-white'"
                        class="w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-all">
                    <i class="fas fa-user-graduate text-base w-5"></i>
                    <span x-show="!sidebarCollapsed || mobileMenuOpen" class="flex-1 text-left transition-opacity">Students</span>
                    <i x-show="!sidebarCollapsed || mobileMenuOpen" :class="open ? 'fa-chevron-down' : 'fa-chevron-right'" class="fas text-xs transition-transform"></i>
                </button>
                <div x-show="open && (!sidebarCollapsed || mobileMenuOpen)"
                     x-transition
                     class="ml-8 mt-1 space-y-1"
                     x-cloak>
                    <a href="/students"
                       @click.prevent="navigate('/students')"
                       :class="currentPath === '/students' ? 'text-white bg-primary-700/30' : 'text-primary-200 hover:text-white hover:bg-primary-700/20'"
                       class="block px-3 py-2 text-sm rounded-lg">
                        All Students
                    </a>
                    @hasPermission('create-student')
                    <a href="/students/create"
                       @click.prevent="navigate('/students/create')"
                       :class="currentPath === '/students/create' ? 'text-white bg-primary-700/30' : 'text-primary-200 hover:text-white hover:bg-primary-700/20'"
                       class="block px-3 py-2 text-sm rounded-lg">
                        Add Student
                    </a>
                                                <a href="{{ route('tokens.index') }}"
                               @click.prevent="navigate('{{ route('tokens.index') }}')"
                               :class="currentPath === '{{ route('students.import-form') }}'
                                        ? 'text-white bg-primary-700/30'
                                        : 'text-primary-200 hover:text-white hover:bg-primary-700/20'"
                               class="block px-3 py-2 text-sm rounded-lg">
                                Token Mgnt.
                            </a>
                    @endhasPermission
                </div>
            </div>
            @endhasPermission

            <!-- Teachers - Visible to: Admin, Teachers -->
            @hasPermission('view-teachers')
            <a href="/teachers"
               @click.prevent="navigate('/teachers')"
               :class="currentPath.includes('/teachers') ? 'bg-primary-700/50 text-white shadow-lg' : 'text-primary-100 hover:bg-primary-700/30 hover:text-white'"
               class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-all">
                <i class="fas fa-chalkboard-teacher text-base w-5"></i>
                <span x-show="!sidebarCollapsed || mobileMenuOpen" class="transition-opacity">Teachers</span>
            </a>
            @endhasPermission

            <!-- Classes - Visible to: Admin, Teachers -->
            @hasPermission('view-classes')
            <a href="/classes"
               @click.prevent="navigate('/classes')"
               :class="currentPath.includes('/classes') ? 'bg-primary-700/50 text-white shadow-lg' : 'text-primary-100 hover:bg-primary-700/30 hover:text-white'"
               class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-all">
                <i class="fas fa-door-open text-base w-5"></i>
                <span x-show="!sidebarCollapsed || mobileMenuOpen" class="transition-opacity">Classes</span>
            </a>
            @endhasPermission

            <!-- Subjects - Visible to: Admin, Teachers -->
            @hasPermission('view-subjects')
            <a href="/subjects"
               @click.prevent="navigate('/subjects')"
               :class="currentPath.includes('/subjects') ? 'bg-primary-700/50 text-white shadow-lg' : 'text-primary-100 hover:bg-primary-700/30 hover:text-white'"
               class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-all">
                <i class="fas fa-book text-base w-5"></i>
                <span x-show="!sidebarCollapsed || mobileMenuOpen" class="transition-opacity">Subjects</span>
            </a>
            @endhasPermission

            <!-- Attendance - Visible to: Admin, Teachers -->
            @hasAnyPermission('manage-attendance', 'view-attendance')
            <a href="/attendance"
               @click.prevent="navigate('/attendance')"
               :class="currentPath.includes('/attendance') ? 'bg-primary-700/50 text-white shadow-lg' : 'text-primary-100 hover:bg-primary-700/30 hover:text-white'"
               class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-all">
                <i class="fas fa-calendar-check text-base w-5"></i>
                <span x-show="!sidebarCollapsed || mobileMenuOpen" class="transition-opacity">Attendance</span>
            </a>
            @endhasAnyPermission

            <!-- My Class(es) - Consolidated menu for teachers - Visible by permission -->
            @hasAnyPermission('view-classes-timetable', 'view-class-students', 'view-personal-timetable')
            <a href="/teacher/my-classes"
               @click.prevent="navigate('/teacher/my-classes')"
               :class="currentPath.includes('/teacher/my-classes') || currentPath.includes('/teacher/class/') ? 'bg-primary-700/50 text-white shadow-lg' : 'text-primary-100 hover:bg-primary-700/30 hover:text-white'"
               class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-all">
                <i class="fas fa-layer-group text-base w-5"></i>
                <span x-show="!sidebarCollapsed || mobileMenuOpen" class="transition-opacity">My Class(es)</span>
            </a>
            @endhasAnyPermission

            @hasPermission('manage-parents')
                    <!-- Parent Management -->
                    <div x-data="{ open: currentPath.includes('/admin/parents') }">
                        <button @click="open = !open"
                                :class="currentPath.includes('/admin/parents')
                                        ? 'bg-primary-700/50 text-white'
                                        : 'text-primary-100 hover:bg-primary-700/30 hover:text-white'"
                                class="w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-all">
                            <i class="fas fa-user-friends text-base w-5"></i>
                            <span x-show="!sidebarCollapsed || mobileMenuOpen" class="flex-1 text-left transition-opacity">Parent Management</span>
                            <i x-show="!sidebarCollapsed || mobileMenuOpen" :class="open ? 'fa-chevron-down' : 'fa-chevron-right'" class="fas text-xs transition-transform"></i>
                        </button>
                        <div x-show="open && (!sidebarCollapsed || mobileMenuOpen)"
                             x-transition
                             class="ml-8 mt-1 space-y-1"
                             x-cloak>
                            <a href="/admin/parents"
                               @click.prevent="navigate('/admin/parents')"
                               :class="currentPath === '/admin/parents' || currentPath.includes('/admin/parents/') ? 'text-white bg-primary-700/30' : 'text-primary-200 hover:text-white hover:bg-primary-700/20'"
                               class="block px-3 py-2 text-sm rounded-lg transition-all">
                                <i class="fas fa-list mr-2 text-xs w-4"></i>View All Parents
                            </a>
                            <a href="/admin/parents/create"
                               @click.prevent="navigate('/admin/parents/create')"
                               :class="currentPath === '/admin/parents/create' ? 'text-white bg-primary-700/30' : 'text-primary-200 hover:text-white hover:bg-primary-700/20'"
                               class="block px-3 py-2 text-sm rounded-lg transition-all">
                                <i class="fas fa-plus mr-2 text-xs w-4"></i>Add New Parent
                            </a>
                        </div>
                    </div>
            @endhasPermission
                                <!-- Timetable - Visible to: Admin, Teachers, Students -->
            @hasPermission('view-timetable')
                    <a href="/timetable"
                       @click.prevent="navigate('/timetable')"
                       :class="currentPath.includes('/timetable') ? 'bg-primary-700/50 text-white shadow-lg' : 'text-primary-100 hover:bg-primary-700/30 hover:text-white'"
                       class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-all">
                        <i class="fas fa-calendar-week text-base w-5"></i>
                        <span x-show="!sidebarCollapsed || mobileMenuOpen" class="transition-opacity">Timetable</span>
                    </a>
            @endhasPermission
            <!-- Grades - Visible to: Admin, Teachers, Students (own), Parents (own child) -->




            <!-- Parent Portal - Visible to parents with any parent portal permission -->
            @hasAnyPermission('view-own-children', 'view-own-bills', 'view-own-payment-history', 'make-paystack-payment', 'make-bank-transfer-payment', 'make-cash-payment', 'make-cheque-payment')

                @hasPermission('view-own-children')
                <a href="{{ route('parent-portal.children') }}"
                   @click.prevent="navigate('{{ route('parent-portal.children') }}')"
                   :class="currentPath === '{{ route('parent-portal.children') }}' ? 'bg-primary-700/50 text-white shadow-lg' : 'text-primary-100 hover:bg-primary-700/30 hover:text-white'"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-all">
                    <i class="fas fa-child text-base w-5"></i>
                    <span x-show="!sidebarCollapsed || mobileMenuOpen" class="transition-opacity">My Children</span>
                </a>
                <a href="{{ route('parent-portal.bills') }}"
                   @click.prevent="navigate('{{ route('parent-portal.bills') }}')"
                   :class="currentPath === '{{ route('parent-portal.bills') }}' ? 'bg-primary-700/50 text-white shadow-lg' : 'text-primary-100 hover:bg-primary-700/30 hover:text-white'"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-all">
                    <i class="fas fa-receipt text-base w-5"></i>
                    <span x-show="!sidebarCollapsed || mobileMenuOpen" class="transition-opacity">Payment</span>
                </a>
                <a href="{{ route('parent-portal.results') }}"
                   @click.prevent="navigate('{{ route('parent-portal.results') }}')"
                   :class="currentPath === '{{ route('parent-portal.results') }}' ? 'bg-primary-700/50 text-white shadow-lg' : 'text-primary-100 hover:bg-primary-700/30 hover:text-white'"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-all">
                    <i class="fas fa-chart-bar text-base w-5"></i>
                    <span x-show="!sidebarCollapsed || mobileMenuOpen" class="transition-opacity">Results & Grades</span>
                </a>
                <a href="{{ route('parent-portal.announcements') }}"
                   @click.prevent="navigate('{{ route('parent-portal.announcements') }}')"
                   :class="currentPath === '{{ route('parent-portal.announcements') }}' ? 'bg-primary-700/50 text-white shadow-lg' : 'text-primary-100 hover:bg-primary-700/30 hover:text-white'"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-all">
                    <i class="fas fa-bell text-base w-5"></i>
                    <span x-show="!sidebarCollapsed || mobileMenuOpen" class="transition-opacity">Announcements</span>
                </a>
                <a href="{{ route('parent-portal.calendar') }}"
                   @click.prevent="navigate('{{ route('parent-portal.calendar') }}')"
                   :class="currentPath === '{{ route('parent-portal.calendar') }}' ? 'bg-primary-700/50 text-white shadow-lg' : 'text-primary-100 hover:bg-primary-700/30 hover:text-white'"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-all">
                    <i class="fas fa-calendar-alt text-base w-5"></i>
                    <span x-show="!sidebarCollapsed || mobileMenuOpen" class="transition-opacity">School Calendar</span>
                </a>
                @endhasPermission
            @endhasAnyPermission

            @endif
<!-- Class Results - Teacher result management by permission -->
             @hasAnyPermission('view-class-results', 'edit-class-results')
                    <a href="{{ route('teacher.results.classes') }}"
                       @click.prevent="navigate('{{ route('teacher.results.classes') }}')"
                       :class="currentPath.includes('/teacher/results') ? 'bg-primary-700/50 text-white shadow-lg' : 'text-primary-100 hover:bg-primary-700/30 hover:text-white'"
                       class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-all">
                        <i class="fas fa-chart-bar text-base w-5"></i>
                        <span x-show="!sidebarCollapsed || mobileMenuOpen" class="transition-opacity">Class Results</span>
                    </a>
                 @endhasAnyPermission

            @hasPermission('manage-school-results')
                    <div x-data="{ open: currentPath.startsWith('/results') }">
                        <button @click="open = !open"
                                :class="currentPath.startsWith('/results')
                                        ? 'bg-primary-700/50 text-white'
                                        : 'text-primary-100 hover:bg-primary-700/30 hover:text-white'"
                                class="w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-all">
                            <i class="fas fa-chart-line text-base w-5"></i>
                            <span x-show="!sidebarCollapsed || mobileMenuOpen"
                                  class="flex-1 text-left transition-opacity">Results</span>
                            <i x-show="!sidebarCollapsed || mobileMenuOpen"
                               :class="open ? 'fa-chevron-down' : 'fa-chevron-right'"
                               class="fas text-xs transition-transform"></i>
                        </button>
                        <div x-show="open && (!sidebarCollapsed || mobileMenuOpen)"
                             x-transition
                             class="ml-8 mt-1 space-y-1"
                             x-cloak>
                            <a href="{{ route('results.index') }}"
                               @click.prevent="navigate('{{ route('results.index') }}')"
                               :class="currentPath === '{{ route('results.index') }}'
                                        ? 'text-white bg-primary-700/30'
                                        : 'text-primary-200 hover:text-white hover:bg-primary-700/20'"
                               class="block px-3 py-2 text-sm rounded-lg transition-all">
                                <i class="fas fa-home mr-2 text-xs w-4"></i>Main Dashboard
                            </a>
                            <a href="{{ route('results.print.selection') }}"
                               @click.prevent="navigate('{{ route('results.print.selection') }}')"
                               :class="currentPath.startsWith('/results/print-selection')
                                        ? 'text-white bg-primary-700/30'
                                        : 'text-primary-200 hover:text-white hover:bg-primary-700/20'"
                               class="block px-3 py-2 text-sm rounded-lg transition-all">
                                <i class="fas fa-print mr-2 text-xs w-4"></i>Print Result Card
                            </a>
                            <a href="{{ route('results.class.report.selection') }}"
                               @click.prevent="navigate('{{ route('results.class.report.selection') }}')"
                               :class="currentPath.startsWith('/results/class-report')
                                        ? 'text-white bg-primary-700/30'
                                        : 'text-primary-200 hover:text-white hover:bg-primary-700/20'"
                               class="block px-3 py-2 text-sm rounded-lg transition-all">
                                <i class="fas fa-file-pdf mr-2 text-xs w-4"></i>Class Report Card
                            </a>
                        </div>
                    </div>
                @endhasPermission


            <!-- Divider -->

                            <!-- Payments -->
                @hasPermission('view-payments')
                    <div x-data="{ open: currentPath.startsWith('/payments') }">
                        <button @click="open = !open"
                                :class="currentPath.startsWith('/payments')
                                        ? 'bg-primary-700/50 text-white'
                                        : 'text-primary-100 hover:bg-primary-700/30 hover:text-white'"
                                class="w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-all">
                            <i class="fas fa-money-bill text-base w-5"></i>
                            <span x-show="!sidebarCollapsed || mobileMenuOpen"
                                  class="flex-1 text-left transition-opacity">Payments</span>
                            <i x-show="!sidebarCollapsed || mobileMenuOpen"
                               :class="open ? 'fa-chevron-down' : 'fa-chevron-right'"
                               class="fas text-xs transition-transform"></i>
                        </button>
                        <div x-show="open && (!sidebarCollapsed || mobileMenuOpen)"
                             x-transition
                             class="ml-8 mt-1 space-y-1"
                             x-cloak>
                            <a href="{{ route('payments.index') }}"
                               @click.prevent="navigate('{{ route('payments.index') }}')"
                               :class="currentPath === '{{ route('payments.index') }}'
                                        ? 'text-white bg-primary-700/30'
                                        : 'text-primary-200 hover:text-white hover:bg-primary-700/20'"
                               class="block px-3 py-2 text-sm rounded-lg transition-all">
                                <i class="fas fa-home mr-2 text-xs w-4"></i>Dashboard
                            </a>
                            <a href="{{ route('billing.fee-structures.index') }}"
                               @click.prevent="navigate('{{ route('billing.fee-structures.index') }}')"
                               :class="currentPath.startsWith('/billing/fee-structures')
                                        ? 'text-white bg-primary-700/30'
                                        : 'text-primary-200 hover:text-white hover:bg-primary-700/20'"
                               class="block px-3 py-2 text-sm rounded-lg transition-all">
                                <i class="fas fa-layer-group mr-2 text-xs w-4"></i>Fee Structures
                            </a>
                            <a href="{{ route('billing.fee-items.index') }}"
                               @click.prevent="navigate('{{ route('billing.fee-items.index') }}')"
                               :class="currentPath.startsWith('/billing/fee-items')
                                        ? 'text-white bg-primary-700/30'
                                        : 'text-primary-200 hover:text-white hover:bg-primary-700/20'"
                               class="block px-3 py-2 text-sm rounded-lg transition-all">
                                <i class="fas fa-receipt mr-2 text-xs w-4"></i>Fee Items
                            </a>
                            <a href="{{ route('payments.bills.index') }}"
                               @click.prevent="navigate('{{ route('payments.bills.index') }}')"
                               :class="currentPath.startsWith('/payments/bills')
                                        ? 'text-white bg-primary-700/30'
                                        : 'text-primary-200 hover:text-white hover:bg-primary-700/20'"
                               class="block px-3 py-2 text-sm rounded-lg transition-all">
                                <i class="fas fa-file-invoice mr-2 text-xs w-4"></i>Student Bills
                            </a>
                            <a href="{{ route('payments.payment-history') }}"
                               @click.prevent="navigate('{{ route('payments.payment-history') }}')"
                               :class="currentPath.startsWith('/payments/payment-history')
                                        ? 'text-white bg-primary-700/30'
                                        : 'text-primary-200 hover:text-white hover:bg-primary-700/20'"
                               class="block px-3 py-2 text-sm rounded-lg transition-all">
                                <i class="fas fa-history mr-2 text-xs w-4"></i>Payment History
                            </a>
                            <a href="{{ route('payments.debt-management') }}"
                               @click.prevent="navigate('{{ route('payments.debt-management') }}')"
                               :class="currentPath.startsWith('/payments/debt-management')
                                        ? 'text-white bg-primary-700/30'
                                        : 'text-primary-200 hover:text-white hover:bg-primary-700/20'"
                               class="block px-3 py-2 text-sm rounded-lg transition-all">
                                <i class="fas fa-chart-pie mr-2 text-xs w-4"></i>Debt Management
                            </a>
                            <a href="{{ route('payments.receipts.list') }}"
                               @click.prevent="navigate('{{ route('payments.receipts.list') }}')"
                               :class="currentPath.startsWith('/payments/receipts')
                                        ? 'text-white bg-primary-700/30'
                                        : 'text-primary-200 hover:text-white hover:bg-primary-700/20'"
                               class="block px-3 py-2 text-sm rounded-lg transition-all">
                                <i class="fas fa-receipt mr-2 text-xs w-4"></i>Receipts
                            </a>
                        </div>
                    </div>
                @endhasPermission

                    <!-- Role & Permission Management - Admin Only -->

                    <div class="my-4 border-t border-primary-700/50"></div>
                    
                    @hasPermission('manage-permissions')
                    <div x-data="{ open: currentPath.includes('/rbac') || currentPath.includes('/roles') || currentPath.includes('/permissions') }">
                        <button @click="open = !open"
                                :class="(currentPath.includes('/rbac') || currentPath.includes('/roles') || currentPath.includes('/permissions'))
                                        ? 'bg-primary-700/50 text-white'
                                        : 'text-primary-100 hover:bg-primary-700/30 hover:text-white'"
                                class="w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-all">
                            <i class="fas fa-lock text-base w-5"></i>
                            <span x-show="!sidebarCollapsed || mobileMenuOpen" class="flex-1 text-left transition-opacity">Access Control</span>
                            <i x-show="!sidebarCollapsed || mobileMenuOpen" :class="open ? 'fa-chevron-down' : 'fa-chevron-right'" class="fas text-xs transition-transform"></i>
                        </button>
                        <div x-show="open && (!sidebarCollapsed || mobileMenuOpen)"
                             x-transition
                             class="ml-8 mt-1 space-y-1"
                             x-cloak>
                            <a href="/admin/rbac"
                               @click.prevent="navigate('/admin/rbac')"
                               :class="currentPath === '/admin/rbac' ? 'text-white bg-primary-700/30' : 'text-primary-200 hover:text-white hover:bg-primary-700/20'"
                               class="block px-3 py-2 text-sm rounded-lg transition-all">
                                <i class="fas fa-home mr-2 text-xs w-4"></i>RBAC Dashboard
                            </a>
                            <a href="/admin/roles"
                               @click.prevent="navigate('/admin/roles')"
                               :class="currentPath === '/admin/roles' || currentPath.includes('/admin/roles/') ? 'text-white bg-primary-700/30' : 'text-primary-200 hover:text-white hover:bg-primary-700/20'"
                               class="block px-3 py-2 text-sm rounded-lg transition-all">
                                <i class="fas fa-user-tag mr-2 text-xs w-4"></i>Manage Roles
                            </a>
                            <a href="/admin/permissions"
                               @click.prevent="navigate('/admin/permissions')"
                               :class="currentPath === '/admin/permissions' || currentPath.includes('/admin/permissions/') ? 'text-white bg-primary-700/30' : 'text-primary-200 hover:text-white hover:bg-primary-700/20'"
                               class="block px-3 py-2 text-sm rounded-lg transition-all">
                                <i class="fas fa-key mr-2 text-xs w-4"></i>Manage Permissions
                            </a>
                            <a href="/admin/user-roles"
                               @click.prevent="navigate('/admin/user-roles')"
                               :class="currentPath === '/admin/user-roles' ? 'text-white bg-primary-700/30' : 'text-primary-200 hover:text-white hover:bg-primary-700/20'"
                               class="block px-3 py-2 text-sm rounded-lg transition-all">
                                <i class="fas fa-users mr-2 text-xs w-4"></i>Assign User Roles
                            </a>
                        </div>
                    </div>
                    @endhasPermission

                @hasPermission('manage-school-settings')
                    <a href="/settings"
                    @click.prevent="navigate('/settings')"
                    :class="currentPath.includes('/settings') ? 'bg-primary-700/50 text-white shadow-lg' : 'text-primary-100 hover:bg-primary-700/30 hover:text-white'"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-all">
                        <i class="fas fa-cog text-base w-5"></i>
                        <span x-show="!sidebarCollapsed || mobileMenuOpen" class="transition-opacity">School Settings</span>
                    </a>
                @endhasPermission

            <!-- Divider -->
            <div class="my-4 border-t border-primary-700/50"></div>

            <!-- User Profile Collapsible Menu - For all authenticated users -->
            <div x-data="{ userMenuOpen: false }">
                <button @click="userMenuOpen = !userMenuOpen"
                        class="w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium text-primary-100 hover:bg-primary-700/30 hover:text-white transition-all">
                    <div class="w-5 h-5 rounded-full bg-gradient-to-br from-primary-400 to-primary-600 flex items-center justify-center text-white text-xs font-semibold flex-shrink-0">
                        {{ substr(Auth::user()->name, 0, 1) }}
                    </div>
                    <span x-show="!sidebarCollapsed || mobileMenuOpen" class="flex-1 text-left transition-opacity truncate">{{ Auth::user()->name }}</span>
                    <i x-show="!sidebarCollapsed || mobileMenuOpen" :class="userMenuOpen ? 'fa-chevron-down' : 'fa-chevron-right'" class="fas text-xs transition-transform"></i>
                </button>
                
                <!-- User Menu Items -->
                <div x-show="userMenuOpen && (!sidebarCollapsed || mobileMenuOpen)"
                     x-transition
                     class="ml-8 mt-1 space-y-1"
                     x-cloak>
                    <a href="{{ route('profile.show') }}"
                       @click.prevent="navigate('{{ route('profile.show') }}')"
                       class="flex items-center gap-3 px-3 py-2 text-sm text-primary-200 hover:text-white hover:bg-primary-700/20 rounded-lg transition-all">
                        <i class="fas fa-user-circle text-sm w-4"></i>
                        <span>My Profile</span>
                    </a>
                    <a href="{{ route('profile.edit') }}"
                       @click.prevent="navigate('{{ route('profile.edit') }}')"
                       class="flex items-center gap-3 px-3 py-2 text-sm text-primary-200 hover:text-white hover:bg-primary-700/20 rounded-lg transition-all">
                        <i class="fas fa-sliders text-sm w-4"></i>
                        <span>Account Settings</span>
                    </a>
                    <a href="{{ route('profile.change-password') }}"
                       @click.prevent="navigate('{{ route('profile.change-password') }}')"
                       class="flex items-center gap-3 px-3 py-2 text-sm text-primary-200 hover:text-white hover:bg-primary-700/20 rounded-lg transition-all">
                        <i class="fas fa-key text-sm w-4"></i>
                        <span>Change Password</span>
                    </a>
                    <form method="POST" action="{{ route('logout') }}" class="w-full">
                        @csrf
                        <button type="submit" class="w-full flex items-center gap-3 px-3 py-2 text-sm text-primary-200 hover:text-white hover:bg-primary-700/20 rounded-lg transition-all text-left">
                            <i class="fas fa-sign-out-alt text-sm w-4"></i>
                            <span>Logout</span>
                        </button>
                    </form>
                </div>
            </div>
        </nav>
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
                            <nav class="flex items-center space-x-2 text-sm" id="breadcrumb">
                                <!-- Breadcrumb will be dynamically updated -->
                            </nav>
                        </div>
                    </div>

                    <!-- Right: Search, Language, Notifications -->
                    <div class="flex items-center gap-2 sm:gap-3">
                        <!-- Search -->
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
                            </button>
                        </div>

                        <!-- Notifications -->
                        <div x-data="{ open: false }" class="relative">
                            <button @click="open = !open"
                                    class="relative p-2 text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-lg transition">
                                <i class="fas fa-bell text-lg"></i>
                                <span class="absolute top-1 right-1 w-2 h-2 bg-red-500 rounded-full"></span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <!-- Main Content (SPA Container) -->
        <main class="flex-1 overflow-y-auto">
            <!-- Loading Indicator -->
            <div x-show="loading"
                 x-transition
                 class="flex items-center justify-center py-20"
                 x-cloak>
                <div class="text-center">
                    <div class="spinner mx-auto mb-4"></div>
                    <p class="text-gray-600">Loading...</p>
                </div>
            </div>

            <!-- Content Area -->
            <div x-show="!loading"
                 id="spa-content"
                 class="px-4 sm:px-6 lg:px-8 py-6 page-transition">
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

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- SPA JavaScript -->
    <script>
        function spaApp() {
            return {
                sidebarOpen: window.innerWidth >= 1024,
                sidebarCollapsed: false,
                mobileMenuOpen: false,
                loading: false,
                currentPath: window.location.pathname,

                initSPA() {
                    // Handle window resize
                    window.addEventListener('resize', () => {
                        if (window.innerWidth >= 1024) {
                            this.sidebarOpen = true;
                            this.mobileMenuOpen = false;
                        } else {
                            this.sidebarOpen = false;
                        }
                    });

                    // Set initial path
                    this.currentPath = window.location.pathname;
                },

                /**
                 * Execute scripts found in HTML content
                 * Supports both inline scripts and external script references
                 */
                executeScripts(container) {
                    const scripts = container.querySelectorAll('script');
                    
                    scripts.forEach(oldScript => {
                        const newScript = document.createElement('script');
                        
                        // Copy attributes
                        Array.from(oldScript.attributes).forEach(attr => {
                            // Skip src for now, we'll handle it specially
                            if (attr.name !== 'src') {
                                newScript.setAttribute(attr.name, attr.value);
                            }
                        });
                        
                        // Handle external scripts
                        if (oldScript.src) {
                            newScript.src = oldScript.src;
                            newScript.async = false; // Ensure sequential execution
                        }
                        // Handle inline scripts
                        else if (oldScript.textContent) {
                            newScript.textContent = oldScript.textContent;
                        }
                        
                        // Append to container to execute
                        container.appendChild(newScript);
                        // Remove the old script to avoid duplication
                        oldScript.remove();
                    });
                },

                /**
                 * Main SPA navigation function
                 * @param {string} url - URL to navigate to
                 * @param {boolean} push - Whether to push state to history
                 */
                async navigate(url, push = true) {
                    // Close mobile menu
                    this.mobileMenuOpen = false;

                    // Show loading state
                    this.loading = true;

                    try {
                        // 1. Fetch page content with AJAX headers
                        const response = await fetch(url, {
                            method: 'GET',
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'text/html',
                                'Cache-Control': 'no-cache'
                            },
                            credentials: 'same-origin'
                        });

                        // Check for redirect or error
                        if (!response.ok) {
                            if (response.status === 302 || response.status === 301) {
                                // Server redirected, do full page load
                                window.location.href = response.url;
                                return;
                            }
                            throw new Error(`HTTP ${response.status}`);
                        }

                        const html = await response.text();

                        // 2. Parse the HTML response
                        const parser = new DOMParser();
                        const doc = parser.parseFromString(html, 'text/html');

                        // Check for parse errors
                        if (doc.body.innerHTML.includes('parsererror')) {
                            throw new Error('Failed to parse response HTML');
                        }

                        // 3. Extract page title
                        const title = doc.querySelector('title');
                        if (title) {
                            document.title = title.textContent;
                        }

                        // 4. Extract new content from #spa-content
                        const newContent = doc.querySelector('#spa-content');
                        if (!newContent) {
                            throw new Error('Response does not contain #spa-content element');
                        }

                        // 5. Get current SPA content container
                        const spaContent = document.getElementById('spa-content');
                        if (!spaContent) {
                            throw new Error('Current page missing #spa-content element');
                        }

                        // 6. Replace content
                        spaContent.innerHTML = newContent.innerHTML;

                        // 7. Update browser history (only if push is true)
                        if (push) {
                            history.pushState({ url: url }, '', url);
                        }

                        // 8. Update current path
                        this.currentPath = url;

                        // 9. Wait briefly for DOM to be fully settled
                        await new Promise(resolve => setTimeout(resolve, 50));

                        // 10. Execute all scripts in the new content (inline and external)
                        this.executeScripts(spaContent);

                        // 11. Wait for external scripts to load if any
                        await new Promise(resolve => setTimeout(resolve, 100));

                        // 12. Reinitialize Alpine.js on new content
                        if (window.Alpine) {
                            try {
                                window.Alpine.initTree(spaContent);
                            } catch (e) {
                                console.warn('Alpine re-initialization warning:', e.message);
                            }
                        }

                        // 13. Reinitialize Bootstrap components
                        if (typeof bootstrap !== 'undefined') {
                            try {
                                // Tooltips
                                const tooltips = spaContent.querySelectorAll('[data-bs-toggle="tooltip"]');
                                tooltips.forEach(el => {
                                    try {
                                        bootstrap.Tooltip.getOrCreateInstance(el);
                                    } catch (e) {
                                        // Silent fail for individual tooltips
                                    }
                                });

                                // Modals
                                const modals = spaContent.querySelectorAll('.modal');
                                modals.forEach(el => {
                                    try {
                                        bootstrap.Modal.getOrCreateInstance(el);
                                    } catch (e) {
                                        // Silent fail for individual modals
                                    }
                                });

                                // Popovers
                                const popovers = spaContent.querySelectorAll('[data-bs-toggle="popover"]');
                                popovers.forEach(el => {
                                    try {
                                        bootstrap.Popover.getOrCreateInstance(el);
                                    } catch (e) {
                                        // Silent fail for individual popovers
                                    }
                                });
                            } catch (e) {
                                console.warn('Bootstrap re-initialization warning:', e.message);
                            }
                        }

                        // 14. Dispatch custom event for page-specific initialization
                        const event = new CustomEvent('spaContentLoaded', {
                            detail: { content: spaContent, url: url }
                        });
                        document.dispatchEvent(event);

                        // 15. Scroll to top
                        spaContent.scrollIntoView({ behavior: 'smooth' });

                    } catch (error) {
                        console.error('SPA Navigation Error:', error);
                        
                        // Fallback to full page reload on any error
                        window.location.href = url;
                    } finally {
                        // Always hide loading state
                        this.loading = false;
                    }
                },

                /**
                 * Handle browser back/forward navigation
                 */
                handlePopState(event) {
                    if (event.state && event.state.url) {
                        // Don't push to history again since popstate already updated it
                        this.navigate(event.state.url, false);
                    }
                }
            }
        }

        // Handle form submissions via AJAX
        document.addEventListener('submit', async function(e) {
            const form = e.target;

            // Only intercept forms with data-ajax attribute
            if (!form.hasAttribute('data-ajax')) {
                return;
            }

            e.preventDefault();

            const formData = new FormData(form);
            const method = form.method || 'POST';
            const action = form.action;

            try {
                const response = await fetch(action, {
                    method: method,
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });

                const data = await response.json();

                if (data.success && data.redirect) {
                    // Show success message and then redirect
                    alert(data.message || 'Success!');
                    window.Alpine.evaluate(document.body, '$dispatch("navigate", { url: "' + data.redirect + '" })');
                } else if (data.success) {
                    // Show success message without redirect
                    alert(data.message || 'Success!');
                } else if (data.message) {
                    // Show error message
                    alert(data.message);
                } else {
                    // Show generic error
                    alert('An error occurred');
                }
            } catch (error) {
                console.error('Form submission error:', error);
                alert('An error occurred while submitting the form');
            }
        });
    </script>

    @stack('scripts')
</body>
</html>
