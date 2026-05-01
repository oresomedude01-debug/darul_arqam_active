<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" 
      dir="ltr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="Darul Arqam School Management System - A comprehensive platform for managing students, teachers, and school operations">
    <meta name="theme-color" content="#0284c7">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="Darul Arqam">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="msapplication-TileColor" content="#0284c7">
    <meta name="msapplication-config" content="{{ asset('browserconfig.xml') }}">
    
    <!-- PWA Manifest -->
    <link rel="manifest" href="{{ asset('manifest.json') }}">
    
    <!-- Apple Touch Icons -->
    <link rel="apple-touch-icon" href="{{ asset('images/icon-192x192.png') }}">
    <link rel="icon" type="image/png" sizes="192x192" href="{{ asset('images/icon-192x192.png') }}">
    <link rel="icon" type="image/png" sizes="512x512" href="{{ asset('images/icon-512x512.png') }}">
    
    <title id="page-title">@yield('title', 'Dashboard') - Darul Arqam</title>

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Alpine.js CDN -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- Bootstrap CSS for calendar views -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- RTL Support CSS -->
    <link rel="stylesheet" href="{{ asset('css/rtl.css') }}">
    
    <!-- Optional Modern Design System CSS (from second layout) -->
    <link rel="stylesheet" href="{{ asset('css/modern-design.css') }}">
    
    <!-- AOS CSS & Lenis (Smooth Scroll) CSS -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    
    <!-- GSAP Animation Library -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/ScrollTrigger.min.js"></script>
    
    <!-- Quill Rich Text Editor (CDN) -->
    <link href="https://cdn.jsdelivr.net/npm/quill@2.0.0/dist/quill.snow.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/quill@2.0.0/dist/quill.js"></script>
    
    <!-- Quill Custom Styling -->
    <style>
        /* Quill Editor Styling */
        .ql-container { font-family: 'Inter', system-ui, sans-serif; font-size: 1rem; }
        .ql-editor { padding: 12px; min-height: 350px; }
        .ql-toolbar { border-top: 1px solid #e5e7eb; border-bottom: 1px solid #e5e7eb; border-radius: 0; background-color: #f9fafb; }
        .ql-toolbar.ql-snow .ql-picker-label { color: #374151; }
        .ql-toolbar.ql-snow button:hover, .ql-toolbar.ql-snow button:focus, .ql-toolbar.ql-snow button.ql-active, .ql-toolbar.ql-snow .ql-picker-label:hover, .ql-toolbar.ql-snow .ql-picker-item:hover, .ql-toolbar.ql-snow .ql-picker-item.ql-selected { color: #0284c7; }
        .ql-toolbar.ql-snow .ql-stroke { stroke: #d1d5db; }
        .ql-toolbar.ql-snow .ql-stroke.ql-fill, .ql-toolbar.ql-snow .ql-fill { fill: #d1d5db; }
        .ql-toolbar.ql-snow button:hover .ql-stroke, .ql-toolbar.ql-snow button:focus .ql-stroke, .ql-toolbar.ql-snow button.ql-active .ql-stroke, .ql-toolbar.ql-snow .ql-picker-label:hover .ql-stroke, .ql-toolbar.ql-snow .ql-picker-item:hover .ql-stroke, .ql-toolbar.ql-snow .ql-picker-item.ql-selected .ql-stroke { stroke: #0284c7; }
        .ql-container.ql-snow { border: 1px solid #e5e7eb; border-radius: 0 0 0.5rem 0.5rem; }
        .ql-editor.ql-blank::before { color: #9ca3af; }
        .ql-editor { color: #374151; }
        .ql-editor h1, .ql-editor h2, .ql-editor h3 { color: #111827; }
    </style>
    
    <style> .lenis.lenis-smooth { scroll-behavior: auto !important; } .lenis.lenis-smooth [data-lenis-prevent] { overscroll-behavior: contain; } .lenis.lenis-stopped { overflow: hidden; } .lenis.lenis-smooth iframe { pointer-events: none; } </style>

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

        /* ===== LOADING STATES ===== */
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

        /* Skeleton Loading Effect */
        @keyframes shimmer {
            0% { background-position: -1000px 0; }
            100% { background-position: 1000px 0; }
        }

        .skeleton-loading {
            background: linear-gradient(90deg, #f3f4f6 25%, #e9ebee 50%, #f3f4f6 75%);
            background-size: 1000px 100%;
            animation: shimmer 1.6s ease-in-out infinite;
            border-radius: 6px;
        }

        /* Skeleton block shorthand */
        .sk { display: block; }
        .sk-rounded { border-radius: 9999px; }

        /* ===== PAGE TRANSITIONS ===== */
        .page-transition {
            animation: slideInRight 0.5s cubic-bezier(0.25, 1, 0.5, 1) forwards;
        }

        .page-transition-back {
            animation: slideInLeft 0.5s cubic-bezier(0.25, 1, 0.5, 1) forwards;
        }

        .fade-in {
            animation: fadeIn 0.4s ease-in-out forwards;
        }

        .fade-in-up {
            animation: fadeInUp 0.6s ease-out forwards;
        }

        .fade-in-down {
            animation: fadeInDown 0.6s ease-out forwards;
        }

        @keyframes slideInRight {
            from { 
                opacity: 0;
                transform: translateX(40px) rotateY(-10deg);
            }
            to { 
                opacity: 1;
                transform: translateX(0) rotateY(0deg);
            }
        }

        @keyframes slideInLeft {
            from { 
                opacity: 0;
                transform: translateX(-40px) rotateY(10deg);
            }
            to { 
                opacity: 1;
                transform: translateX(0) rotateY(0deg);
            }
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        @keyframes fadeInUp {
            from { 
                opacity: 0;
                transform: translateY(20px);
            }
            to { 
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes fadeInDown {
            from { 
                opacity: 0;
                transform: translateY(-20px);
            }
            to { 
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* ===== FORM INTERACTIONS ===== */
        .form-input, .form-select, .form-textarea {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            border-color: #e5e7eb;
        }

        .form-input:focus, .form-select:focus, .form-textarea:focus {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px -5px rgba(2, 132, 199, 0.15);
            border-color: #0284c7;
        }

        .form-input::placeholder, .form-textarea::placeholder {
            transition: color 0.3s ease;
        }

        .form-input:focus::placeholder, .form-textarea:focus::placeholder {
            color: #9ca3af;
        }

        /* ===== BUTTONS & INTERACTIVE ELEMENTS ===== */
        .btn {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
        }

        .btn::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.3);
            transform: translate(-50%, -50%);
            pointer-events: none;
        }

        .btn:active::before {
            animation: ripple 0.6s ease-out;
        }

        @keyframes ripple {
            0% {
                width: 0;
                height: 0;
                opacity: 1;
            }
            100% {
                width: 300px;
                height: 300px;
                opacity: 0;
            }
        }

        .btn:hover {
            transform: translateY(-2px);
        }

        .btn-primary:hover {
            box-shadow: 0 15px 30px -5px rgba(2, 132, 199, 0.3);
        }

        .btn-outline:hover {
            background-color: rgba(2, 132, 199, 0.05);
        }

        /* ===== CARDS & CONTAINERS ===== */
        .card {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            animation: fadeInUp 0.6s ease-out forwards;
        }

        .card:hover {
            transform: translateY(-4px);
            box-shadow: 0 20px 40px -10px rgba(0, 0, 0, 0.1);
        }

        /* Stagger animation for cards */
        .card:nth-child(1) { animation-delay: 0.1s; }
        .card:nth-child(2) { animation-delay: 0.2s; }
        .card:nth-child(3) { animation-delay: 0.3s; }
        .card:nth-child(4) { animation-delay: 0.4s; }
        .card:nth-child(5) { animation-delay: 0.5s; }

        /* ===== BADGES & LABELS ===== */
        .badge, .label {
            animation: slideInDown 0.3s ease-out;
        }

        /* ===== ALERTS & NOTIFICATIONS ===== */
        .alert {
            animation: slideInDown 0.4s ease-out;
            backdrop-filter: blur(10px);
        }

        .alert:hover {
            transform: translateX(5px);
        }

        /* ===== ACTIVE NAV LINK ===== */
        .nav-link-active {
            background: rgba(255, 255, 255, 0.1) !important;
            border-left: 4px solid #fbbf24;
            transition: all 0.3s ease;
        }

        /* ===== SCROLLABLE ELEMENTS ===== */
        .scrollbar-hide {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
        .scrollbar-hide::-webkit-scrollbar {
            display: none;
        }

        /* Smooth scroll behavior */
        html {
            scroll-behavior: smooth;
        }

        /* ===== CUSTOM SMOOTH SCROLLBARS ===== */
        /* Sidebar scrollbar – thin, subtle on dark bg */
        #sidebar-nav {
            scroll-behavior: smooth;
            overflow-y: auto;
        }
        #sidebar-nav::-webkit-scrollbar { width: 4px; }
        #sidebar-nav::-webkit-scrollbar-track { background: transparent; }
        #sidebar-nav::-webkit-scrollbar-thumb {
            background: rgba(255,255,255,0.18);
            border-radius: 99px;
        }
        #sidebar-nav::-webkit-scrollbar-thumb:hover {
            background: rgba(255,255,255,0.35);
        }
        #sidebar-nav { scrollbar-width: thin; scrollbar-color: rgba(255,255,255,0.18) transparent; }

        /* Main content scrollbar – thin, on light bg */
        #main-scroll-container {
            scroll-behavior: smooth;
            overflow-y: auto;
        }
        #main-scroll-container::-webkit-scrollbar { width: 6px; }
        #main-scroll-container::-webkit-scrollbar-track { background: #f1f5f9; }
        #main-scroll-container::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 99px;
        }
        #main-scroll-container::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }
        #main-scroll-container { scrollbar-width: thin; scrollbar-color: #cbd5e1 #f1f5f9; }

        /* ===== CHECKBOXES & RADIOS ===== */
        .form-checkbox, .form-radio {
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .form-checkbox:checked, .form-radio:checked {
            animation: scaleIn 0.2s ease-out;
        }

        @keyframes scaleIn {
            from { transform: scale(0.8); }
            to { transform: scale(1); }
        }

        /* ===== DROPDOWNS & EXPANDABLES ===== */
        .dropdown-content {
            animation: slideInDown 0.3s ease-out;
            transform-origin: top;
        }

        .accordion-item {
            transition: all 0.3s ease;
        }

        .accordion-item.open {
            background-color: rgba(2, 132, 199, 0.05);
        }

        /* ===== SUCCESS/ERROR STATES ===== */
        .success-feedback {
            animation: slideInRight 0.4s ease-out;
            background: linear-gradient(135deg, #ecfdf5 0%, #d1fae5 100%);
        }

        .error-feedback {
            animation: slideInRight 0.4s ease-out;
            background: linear-gradient(135deg, #fef2f2 0%, #fee2e2 100%);
        }

        .warning-feedback {
            animation: slideInRight 0.4s ease-out;
            background: linear-gradient(135deg, #fffbeb 0%, #fef3c7 100%);
        }

        /* ===== PROGRESS INDICATORS ===== */
        .progress-bar {
            animation: slideInLeft 0.6s ease-out;
        }

        .progress-bar::after {
            content: '';
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.7; }
        }

        /* ===== LIST ITEM ANIMATIONS ===== */
        .list-item {
            animation: fadeInUp 0.5s ease-out;
        }

        .list-item:nth-child(1) { animation-delay: 0.05s; }
        .list-item:nth-child(2) { animation-delay: 0.1s; }
        .list-item:nth-child(3) { animation-delay: 0.15s; }
        .list-item:nth-child(4) { animation-delay: 0.2s; }
        .list-item:nth-child(5) { animation-delay: 0.25s; }

        /* ===== MODAL ANIMATIONS ===== */
        .modal-backdrop {
            animation: fadeIn 0.3s ease-out;
        }

        .modal-content {
            animation: zoomIn 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        @keyframes zoomIn {
            from {
                opacity: 0;
                transform: scale(0.95) rotateX(-10deg);
            }
            to {
                opacity: 1;
                transform: scale(1) rotateX(0deg);
            }
        }

        /* ===== TOOLTIP ANIMATIONS ===== */
        .tooltip {
            animation: fadeInDown 0.2s ease-out;
        }

        /* ===== TABLE ANIMATIONS ===== */
        .table-row {
            animation: fadeInLeft 0.4s ease-out;
        }

        .table-row:nth-child(1) { animation-delay: 0.05s; }
        .table-row:nth-child(2) { animation-delay: 0.1s; }
        .table-row:nth-child(3) { animation-delay: 0.15s; }
        .table-row:nth-child(4) { animation-delay: 0.2s; }
        .table-row:nth-child(5) { animation-delay: 0.25s; }

        .table-row:hover {
            background-color: rgba(2, 132, 199, 0.05) !important;
            transform: scale(1.01);
        }

        /* ===== PWA STYLES ===== */
        #pwa-offline-indicator {
            animation: slideInDown 0.3s ease-out;
        }

    </style>

    @stack('styles')
</head>
<body class="bg-gray-50 font-sans antialiased flex h-screen overflow-hidden w-full"
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
           class="fixed top-0 bottom-16 lg:bottom-0 left-0 z-50 bg-gradient-to-br from-primary-900 via-primary-800 to-primary-900 text-white shadow-2xl transition-all duration-300 lg:translate-x-0 lg:static lg:h-screen lg:flex-shrink-0 flex flex-col"
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
                    <h1 class="text-base font-bold leading-tight">{{ $schoolSettings->school_name ?? __('common.app_name') }}</h1>
                    <p class="text-xs text-primary-200">{{ __('common.app_tagline') }}</p>
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
        <nav id="sidebar-nav" class="flex-1 px-3 py-4 pb-24 space-y-1 overscroll-contain">
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
                    <span x-show="!sidebarCollapsed || mobileMenuOpen" class="transition-opacity">{{ __('nav.dashboard') }}</span>
                </a>

                <!-- Students - Visible to: Admin, Teachers, Students (own), Parents (own child) -->
                @hasPermission('view-students')
            <div x-data="{ open: currentPath.includes('/students') }">
                <button @click="open = !open"
                        :class="currentPath.includes('/students') ? 'bg-primary-700/50 text-white' : 'text-primary-100 hover:bg-primary-700/30 hover:text-white'"
                        class="w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-all">
                    <i class="fas fa-user-graduate text-base w-5"></i>
                    <span x-show="!sidebarCollapsed || mobileMenuOpen" class="flex-1 text-left transition-opacity">{{ __('nav.students') }}</span>
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
                        {{ __('nav.all_students') }}
                    </a>
                    @hasPermission('create-student')
                    <a href="/students/create"
                       @click.prevent="navigate('/students/create')"
                       :class="currentPath === '/students/create' ? 'text-white bg-primary-700/30' : 'text-primary-200 hover:text-white hover:bg-primary-700/20'"
                       class="block px-3 py-2 text-sm rounded-lg">
                        {{ __('nav.add_student') }}
                    </a>
                                                <a href="{{ route('tokens.index') }}"
                               @click.prevent="navigate('{{ route('tokens.index') }}')"
                               :class="currentPath === '{{ route('students.import-form') }}'
                                        ? 'text-white bg-primary-700/30'
                                        : 'text-primary-200 hover:text-white hover:bg-primary-700/20'"
                               class="block px-3 py-2 text-sm rounded-lg">
                                {{ __('nav.token_mgnt') }}
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
                <span x-show="!sidebarCollapsed || mobileMenuOpen" class="transition-opacity">{{ __('nav.teachers') }}</span>
            </a>
            @endhasPermission

            <!-- Classes - Visible to: Admin, Teachers -->
            @hasPermission('view-classes')
            <a href="/classes"
               @click.prevent="navigate('/classes')"
               :class="currentPath.includes('/classes') ? 'bg-primary-700/50 text-white shadow-lg' : 'text-primary-100 hover:bg-primary-700/30 hover:text-white'"
               class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-all">
                <i class="fas fa-door-open text-base w-5"></i>
                <span x-show="!sidebarCollapsed || mobileMenuOpen" class="transition-opacity">{{ __('nav.classes') }}</span>
            </a>
            @endhasPermission

            <!-- Subjects - Visible to: Admin, Teachers -->
            @hasPermission('view-subjects')
            <a href="/subjects"
               @click.prevent="navigate('/subjects')"
               :class="currentPath.includes('/subjects') ? 'bg-primary-700/50 text-white shadow-lg' : 'text-primary-100 hover:bg-primary-700/30 hover:text-white'"
               class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-all">
                <i class="fas fa-book text-base w-5"></i>
                <span x-show="!sidebarCollapsed || mobileMenuOpen" class="transition-opacity">{{ __('nav.subjects') }}</span>
            </a>
            @endhasPermission

            <!-- Attendance - Visible to: Admin, Teachers -->
            @hasAnyPermission('manage-attendance', 'view-attendance')
            <a href="/attendance"
               @click.prevent="navigate('/attendance')"
               :class="currentPath.includes('/attendance') ? 'bg-primary-700/50 text-white shadow-lg' : 'text-primary-100 hover:bg-primary-700/30 hover:text-white'"
               class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-all">
                <i class="fas fa-calendar-check text-base w-5"></i>
                <span x-show="!sidebarCollapsed || mobileMenuOpen" class="transition-opacity">{{ __('nav.attendance') }}</span>
            </a>
            @endhasAnyPermission

            <!-- My Class(es) - Consolidated menu for teachers - Visible by permission -->
            @hasAnyPermission('view-classes-timetable', 'view-class-students', 'view-personal-timetable')
            <a href="/teacher/my-classes"
               @click.prevent="navigate('/teacher/my-classes')"
               :class="currentPath.includes('/teacher/my-classes') || currentPath.includes('/teacher/class/') ? 'bg-primary-700/50 text-white shadow-lg' : 'text-primary-100 hover:bg-primary-700/30 hover:text-white'"
               class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-all">
                <i class="fas fa-layer-group text-base w-5"></i>
                <span x-show="!sidebarCollapsed || mobileMenuOpen" class="transition-opacity">{{ __('nav.my_classes') }}</span>
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
                            <span x-show="!sidebarCollapsed || mobileMenuOpen" class="flex-1 text-left transition-opacity">{{ __('nav.parent_management') }}</span>
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
                                <i class="fas fa-list mr-2 text-xs w-4"></i>{{ __('nav.view_all_parents') }}
                            </a>
                            <a href="/admin/parents/create"
                               @click.prevent="navigate('/admin/parents/create')"
                               :class="currentPath === '/admin/parents/create' ? 'text-white bg-primary-700/30' : 'text-primary-200 hover:text-white hover:bg-primary-700/20'"
                               class="block px-3 py-2 text-sm rounded-lg transition-all">
                                <i class="fas fa-plus mr-2 text-xs w-4"></i>{{ __('nav.add_new_parent') }}
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
                        <span x-show="!sidebarCollapsed || mobileMenuOpen" class="transition-opacity">{{ __('nav.timetable') }}</span>
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
                        <span x-show="!sidebarCollapsed || mobileMenuOpen" class="transition-opacity">{{ __('nav.class_results') }}</span>
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
                                  class="flex-1 text-left transition-opacity">{{ __('nav.results') }}</span>
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
                                <i class="fas fa-home mr-2 text-xs w-4"></i>{{ __('nav.main_dashboard') }}
                            </a>
                            <a href="{{ route('results.print.selection') }}"
                               @click.prevent="navigate('{{ route('results.print.selection') }}')"
                               :class="currentPath.startsWith('/results/print-selection')
                                        ? 'text-white bg-primary-700/30'
                                        : 'text-primary-200 hover:text-white hover:bg-primary-700/20'"
                               class="block px-3 py-2 text-sm rounded-lg transition-all">
                                <i class="fas fa-print mr-2 text-xs w-4"></i>{{ __('nav.print_result_card') }}
                            </a>
                            <a href="{{ route('results.class.report.selection') }}"
                               @click.prevent="navigate('{{ route('results.class.report.selection') }}')"
                               :class="currentPath.startsWith('/results/class-report')
                                        ? 'text-white bg-primary-700/30'
                                        : 'text-primary-200 hover:text-white hover:bg-primary-700/20'"
                               class="block px-3 py-2 text-sm rounded-lg transition-all">
                                <i class="fas fa-file-pdf mr-2 text-xs w-4"></i>{{ __('nav.class_report_card') }}
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
                                  class="flex-1 text-left transition-opacity">{{ __('nav.payments') }}</span>
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
                                <i class="fas fa-home mr-2 text-xs w-4"></i>{{ __('nav.dashboard') }}
                            </a>
                            <a href="{{ route('billing.fee-structures.index') }}"
                               @click.prevent="navigate('{{ route('billing.fee-structures.index') }}')"
                               :class="currentPath.startsWith('/billing/fee-structures')
                                        ? 'text-white bg-primary-700/30'
                                        : 'text-primary-200 hover:text-white hover:bg-primary-700/20'"
                               class="block px-3 py-2 text-sm rounded-lg transition-all">
                                <i class="fas fa-layer-group mr-2 text-xs w-4"></i>{{ __('nav.fee_structures') }}
                            </a>
                            <a href="{{ route('billing.fee-items.index') }}"
                               @click.prevent="navigate('{{ route('billing.fee-items.index') }}')"
                               :class="currentPath.startsWith('/billing/fee-items')
                                        ? 'text-white bg-primary-700/30'
                                        : 'text-primary-200 hover:text-white hover:bg-primary-700/20'"
                               class="block px-3 py-2 text-sm rounded-lg transition-all">
                                <i class="fas fa-receipt mr-2 text-xs w-4"></i>{{ __('nav.fee_items') }}
                            </a>
                            <a href="{{ route('payments.bills.index') }}"
                               @click.prevent="navigate('{{ route('payments.bills.index') }}')"
                               :class="currentPath.startsWith('/payments/bills')
                                        ? 'text-white bg-primary-700/30'
                                        : 'text-primary-200 hover:text-white hover:bg-primary-700/20'"
                               class="block px-3 py-2 text-sm rounded-lg transition-all">
                                <i class="fas fa-file-invoice mr-2 text-xs w-4"></i>{{ __('nav.student_bills') }}
                            </a>
                            <a href="{{ route('payments.payment-history') }}"
                               @click.prevent="navigate('{{ route('payments.payment-history') }}')"
                               :class="currentPath.startsWith('/payments/payment-history')
                                        ? 'text-white bg-primary-700/30'
                                        : 'text-primary-200 hover:text-white hover:bg-primary-700/20'"
                               class="block px-3 py-2 text-sm rounded-lg transition-all">
                                <i class="fas fa-history mr-2 text-xs w-4"></i>{{ __('nav.payment_history') }}
                            </a>
                            <a href="{{ route('payments.debt-management') }}"
                               @click.prevent="navigate('{{ route('payments.debt-management') }}')"
                               :class="currentPath.startsWith('/payments/debt-management')
                                        ? 'text-white bg-primary-700/30'
                                        : 'text-primary-200 hover:text-white hover:bg-primary-700/20'"
                               class="block px-3 py-2 text-sm rounded-lg transition-all">
                                <i class="fas fa-chart-pie mr-2 text-xs w-4"></i>{{ __('nav.debt_management') }}
                            </a>
                            <a href="{{ route('payments.receipts.list') }}"
                               @click.prevent="navigate('{{ route('payments.receipts.list') }}')"
                               :class="currentPath.startsWith('/payments/receipts')
                                        ? 'text-white bg-primary-700/30'
                                        : 'text-primary-200 hover:text-white hover:bg-primary-700/20'"
                               class="block px-3 py-2 text-sm rounded-lg transition-all">
                                <i class="fas fa-receipt mr-2 text-xs w-4"></i>{{ __('nav.receipts') }}
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
                            <span x-show="!sidebarCollapsed || mobileMenuOpen" class="flex-1 text-left transition-opacity">{{ __('nav.access_control') }}</span>
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
                                <i class="fas fa-home mr-2 text-xs w-4"></i>{{ __('nav.rbac_dashboard') }}
                            </a>
                            <a href="/admin/roles"
                               @click.prevent="navigate('/admin/roles')"
                               :class="currentPath === '/admin/roles' || currentPath.includes('/admin/roles/') ? 'text-white bg-primary-700/30' : 'text-primary-200 hover:text-white hover:bg-primary-700/20'"
                               class="block px-3 py-2 text-sm rounded-lg transition-all">
                                <i class="fas fa-user-tag mr-2 text-xs w-4"></i>{{ __('nav.manage_roles') }}
                            </a>
                            <a href="/admin/permissions"
                               @click.prevent="navigate('/admin/permissions')"
                               :class="currentPath === '/admin/permissions' || currentPath.includes('/admin/permissions/') ? 'text-white bg-primary-700/30' : 'text-primary-200 hover:text-white hover:bg-primary-700/20'"
                               class="block px-3 py-2 text-sm rounded-lg transition-all">
                                <i class="fas fa-key mr-2 text-xs w-4"></i>{{ __('nav.manage_permissions') }}
                            </a>
                            <a href="/admin/user-roles"
                               @click.prevent="navigate('/admin/user-roles')"
                               :class="currentPath === '/admin/user-roles' ? 'text-white bg-primary-700/30' : 'text-primary-200 hover:text-white hover:bg-primary-700/20'"
                               class="block px-3 py-2 text-sm rounded-lg transition-all">
                                <i class="fas fa-users mr-2 text-xs w-4"></i>{{ __('nav.assign_user_roles') }}
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
                        <span x-show="!sidebarCollapsed || mobileMenuOpen" class="transition-opacity">{{ __('nav.settings') }}</span>
                    </a>
                @endhasPermission

                @hasPermission('manage-school-settings')
                    <a href="{{ route('admin.send-mail.index') }}"
                    @click.prevent="navigate('{{ route('admin.send-mail.index') }}')"
                    :class="currentPath.includes('/admin/send-mail') ? 'bg-primary-700/50 text-white shadow-lg' : 'text-primary-100 hover:bg-primary-700/30 hover:text-white'"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-all">
                        <i class="fas fa-paper-plane text-base w-5"></i>
                        <span x-show="!sidebarCollapsed || mobileMenuOpen" class="transition-opacity">{{ __('nav.send_mail') }}</span>
                    </a>
                @endhasPermission

                <!-- Blog Management -->
                @if(auth()->check() && (auth()->user()->hasRole('admin') || auth()->user()->hasPermission('manage-blog')))
                    <a href="{{ route('admin.blog.index') }}"
                    @click.prevent="navigate('{{ route('admin.blog.index') }}')"
                    :class="currentPath.includes('/admin/blog') ? 'bg-primary-700/50 text-white shadow-lg' : 'text-primary-100 hover:bg-primary-700/30 hover:text-white'"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-all">
                        <i class="fas fa-newspaper text-base w-5"></i>
                        <span x-show="!sidebarCollapsed || mobileMenuOpen" class="transition-opacity">{{ __('nav.blog_management') }}</span>
                    </a>
                @endif

            <!-- Divider -->
            <div class="my-4 border-t border-primary-700/50"></div>

            <!-- Notifications Link in Sidebar -->
            <a href="{{ route('notifications.index') }}"
               @click.prevent="navigate('{{ route('notifications.index') }}')"
               :class="currentPath === '{{ route('notifications.index') }}' ? 'bg-primary-700/50 text-white shadow-lg' : 'text-primary-100 hover:bg-primary-700/30 hover:text-white'"
               class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-all relative">
                <i class="fas fa-bell text-base w-5"></i>
                <span x-show="!sidebarCollapsed || mobileMenuOpen" class="transition-opacity">{{ __('nav.notifications') }}</span>
                @if(auth()->check() && auth()->user()->unreadNotifications->count() > 0)
                <span x-show="!sidebarCollapsed || mobileMenuOpen" class="ml-auto bg-red-500 text-white text-[10px] font-bold px-2 py-0.5 rounded-full">
                    {{ auth()->user()->unreadNotifications->count() }}
                </span>
                <span x-show="sidebarCollapsed && !mobileMenuOpen" class="absolute top-2 right-2 w-2 h-2 bg-red-500 rounded-full"></span>
                @endif
            </a>


        </nav>

        <!-- User Profile (Bottom) -->
        <div class="p-4 border-t border-primary-700/50 mt-auto">
            <div x-data="{ open: false }" class="relative">
                <button @click="open = !open"
                        class="w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-primary-100 hover:bg-primary-700/30 transition-all">
                    <div class="w-8 h-8 rounded-full bg-gradient-to-br from-primary-400 to-primary-600 flex items-center justify-center text-white font-semibold text-sm flex-shrink-0">
                        {{ substr(Auth::user()->name, 0, 2) }}
                    </div>
                    <div x-show="!sidebarCollapsed || mobileMenuOpen" class="flex-1 text-left transition-opacity">
                        <p class="text-sm font-medium text-white leading-tight truncate">{{ Auth::user()->name }}</p>
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
                    <a href="{{ route('profile.show') }}"
                       @click.prevent="navigate('{{ route('profile.show') }}'); open = false;"
                       class="block px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 transition">
                        <i class="fas fa-user-circle mr-2 text-gray-400"></i>
                        {{ __('nav.my_profile') }}
                    </a>
                    <a href="{{ route('profile.edit') }}"
                       @click.prevent="navigate('{{ route('profile.edit') }}'); open = false;"
                       class="block px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 transition">
                        <i class="fas fa-sliders mr-2 text-gray-400"></i>
                        {{ __('nav.account_settings') }}
                    </a>
                    <a href="{{ route('profile.change-password') }}"
                       @click.prevent="navigate('{{ route('profile.change-password') }}'); open = false;"
                       class="block px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 transition">
                        <i class="fas fa-key mr-2 text-gray-400"></i>
                        {{ __('nav.change_password') }}
                    </a>
                    <div class="border-t border-gray-200"></div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-full text-left px-4 py-2.5 text-sm text-red-600 hover:bg-red-50 transition">
                            <i class="fas fa-sign-out-alt mr-2"></i>
                            {{ __('nav.logout') }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </aside>

    <!-- Main Content Area -->
    <div class="flex-1 flex flex-col h-full overflow-hidden w-0 relative transition-all duration-300">

        <!-- Top Header -->
        <header class="flex-shrink-0 z-30 bg-white border-b border-gray-200 shadow-sm">
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

                    <!-- Right: Install, Language, Notifications -->
                    <div class="flex items-center gap-2 sm:gap-3">

                        <!-- Language Switcher -->
                        <div x-data="{ open: false }" class="relative">
                            <button @click="open = !open"
                                    class="flex items-center gap-2 px-3 py-2 text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-lg transition">
                                <i class="fas fa-language text-lg"></i>
                                <span class="hidden sm:inline text-sm font-medium">{{ app()->getLocale() === 'ar' ? 'العربية' : 'English' }}</span>
                                <i class="fas fa-chevron-down text-xs hidden sm:inline"></i>
                            </button>
                            <div x-show="open" @click.away="open = false" x-transition
                                 class="absolute right-0 mt-2 w-44 bg-white rounded-lg shadow-xl border border-gray-100 z-50 overflow-hidden py-1" x-cloak>
                                <a href="{{ route('locale.switch', 'en') }}"
                                   class="flex items-center gap-3 px-4 py-2.5 text-sm hover:bg-gray-50 transition {{ app()->getLocale() === 'en' ? 'text-primary-600 font-semibold bg-primary-50/50' : 'text-gray-700' }}">
                                    <span class="text-lg">🇬🇧</span>
                                    <span>English</span>
                                    @if(app()->getLocale() === 'en')
                                    <i class="fas fa-check ml-auto text-primary-500 text-xs"></i>
                                    @endif
                                </a>
                                <a href="{{ route('locale.switch', 'ar') }}"
                                   class="flex items-center gap-3 px-4 py-2.5 text-sm hover:bg-gray-50 transition {{ app()->getLocale() === 'ar' ? 'text-primary-600 font-semibold bg-primary-50/50' : 'text-gray-700' }}">
                                    <span class="text-lg">🇸🇦</span>
                                    <span>العربية</span>
                                    @if(app()->getLocale() === 'ar')
                                    <i class="fas fa-check ml-auto text-primary-500 text-xs"></i>
                                    @endif
                                </a>
                            </div>
                        </div>

                        <!-- Notifications -->
                        <div x-data="{ open: false }" class="relative">
                            <button @click="open = !open"
                                    class="relative p-2 text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-lg transition">
                                <i class="fas fa-bell text-lg"></i>
                                @if(auth()->check() && auth()->user()->unreadNotifications->count() > 0)
                                <span class="absolute top-1 right-1 w-2 h-2 bg-red-500 rounded-full"></span>
                                @endif
                            </button>

                            <div x-show="open" @click.away="open = false" x-transition class="absolute right-0 mt-2 w-80 bg-white rounded-lg shadow-xl border border-gray-100 z-50 overflow-hidden" x-cloak>
                                <div class="px-4 py-3 border-b border-gray-100 flex justify-between items-center bg-gray-50">
                                    <h3 class="font-semibold text-gray-800 text-sm">Notifications</h3>
                                    @if(auth()->check() && auth()->user()->unreadNotifications->count() > 0)
                                    <form action="{{ route('notifications.mark-all-read') }}" method="POST">
                                        @csrf
                                        <button type="submit" class="text-xs text-primary-600 hover:text-primary-800 font-medium">Mark all as read</button>
                                    </form>
                                    @endif
                                </div>
                                <div class="max-h-80 overflow-y-auto">
                                    @if(auth()->check())
                                        @forelse(auth()->user()->unreadNotifications as $notification)
                                            <div class="px-4 py-3 border-b border-gray-50 hover:bg-gray-50 transition {{ $notification->read_at ? 'opacity-75' : 'bg-blue-50/30' }}">
                                                <p class="text-sm text-gray-800">{{ $notification->data['message'] ?? 'New notification' }}</p>
                                                <span class="text-xs text-gray-500 mt-1 block">{{ $notification->created_at->diffForHumans() }}</span>
                                            </div>
                                        @empty
                                            <div class="px-4 py-8 text-center flex flex-col items-center justify-center">
                                                <div class="w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center mb-2">
                                                    <i class="fas fa-bell-slash text-gray-400"></i>
                                                </div>
                                                <p class="text-gray-500 text-sm">No new notifications</p>
                                            </div>
                                        @endforelse
                                    @endif
                                </div>
                                <a href="{{ route('notifications.index') }}" @click="open = false; navigate('{{ route('notifications.index') }}')" class="block px-4 py-2 text-center text-xs font-medium text-primary-600 bg-gray-50 hover:bg-gray-100 transition">
                                    View All Notifications
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <!-- Main Content (SPA Container) -->
        <main class="flex-1 overflow-y-auto overscroll-contain relative w-full" id="main-scroll-container">
            <!-- Skeleton Screen - Page Transition Placeholder -->
            <div x-show="loading"
                 x-transition:enter="transition ease-out duration-150"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition ease-in duration-100"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 class="px-4 sm:px-6 lg:px-8 py-6 space-y-6"
                 x-cloak>

                <!-- Page Title Skeleton -->
                <div class="flex items-center justify-between">
                    <div class="space-y-2">
                        <span class="sk skeleton-loading block h-7 w-48 rounded-md"></span>
                        <span class="sk skeleton-loading block h-4 w-72 rounded-md"></span>
                    </div>
                    <span class="sk skeleton-loading block h-9 w-28 rounded-lg"></span>
                </div>

                <!-- Stats Cards Skeleton -->
                <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                    <template x-for="i in 4" :key="i">
                        <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100 space-y-3">
                            <div class="flex items-center justify-between">
                                <span class="sk skeleton-loading block h-4 w-24 rounded"></span>
                                <span class="sk skeleton-loading block w-9 h-9 rounded-full"></span>
                            </div>
                            <span class="sk skeleton-loading block h-8 w-16 rounded"></span>
                            <span class="sk skeleton-loading block h-3 w-32 rounded"></span>
                        </div>
                    </template>
                </div>

                <!-- Content Panels Skeleton -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

                    <!-- Main Panel -->
                    <div class="lg:col-span-2 bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                        <!-- Panel header -->
                        <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
                            <span class="sk skeleton-loading block h-5 w-36 rounded"></span>
                            <span class="sk skeleton-loading block h-4 w-20 rounded"></span>
                        </div>
                        <!-- Table rows -->
                        <div class="divide-y divide-gray-50">
                            <template x-for="i in 5" :key="i">
                                <div class="px-5 py-3.5 flex items-center gap-4">
                                    <span class="sk skeleton-loading block w-8 h-8 rounded-full flex-shrink-0"></span>
                                    <div class="flex-1 space-y-1.5">
                                        <span class="sk skeleton-loading block h-3.5 rounded" :style="'width:' + (55 + i * 6) + '%'"></span>
                                        <span class="sk skeleton-loading block h-3 w-2/5 rounded"></span>
                                    </div>
                                    <span class="sk skeleton-loading block h-6 w-16 rounded-full"></span>
                                </div>
                            </template>
                        </div>
                    </div>

                    <!-- Side Panel -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                        <div class="px-5 py-4 border-b border-gray-100">
                            <span class="sk skeleton-loading block h-5 w-32 rounded"></span>
                        </div>
                        <div class="p-5 space-y-4">
                            <template x-for="i in 4" :key="i">
                                <div class="flex items-center gap-3">
                                    <span class="sk skeleton-loading block w-9 h-9 rounded-lg flex-shrink-0"></span>
                                    <div class="flex-1 space-y-1.5">
                                        <span class="sk skeleton-loading block h-3.5 w-3/4 rounded"></span>
                                        <span class="sk skeleton-loading block h-3 w-1/2 rounded"></span>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>

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
        <footer class="hidden md:block bg-white border-t border-gray-200 py-4 px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col sm:flex-row items-center justify-between gap-4 text-sm text-gray-600">
                <p>&copy; {{ date('Y') }} Darul Arqam School Management System. All rights reserved.</p>
                <div class="flex items-center gap-4">
                    <a href="#" class="hover:text-primary-600 transition">{{ __('nav.privacy') }}</a>
                    <a href="#" class="hover:text-primary-600 transition">{{ __('nav.terms') }}</a>
                    <a href="#" class="hover:text-primary-600 transition">{{ __('nav.support') }}</a>
                </div>
            </div>
        </footer>
        
        <!-- Push content up slightly so it doesn't hide behind tab bar on mobile -->
        <div class="h-16 lg:hidden"></div>

        <!-- Mobile Bottom Tab Bar -->
        <nav class="lg:hidden fixed bottom-0 w-full bg-white border-t border-gray-200 z-50 px-2 pt-2 pb-safe bg-opacity-95 backdrop-blur-md pb-1 touch-none shadow-[0_-4px_6px_-1px_rgba(0,0,0,0.05)]">
            <div class="flex justify-around items-center">
                <a href="{{ route('dashboard') }}" @click.prevent="navigate('{{ route('dashboard') }}')" 
                   :class="currentPath.includes('/dashboard') ? 'text-primary-600' : 'text-gray-500 hover:text-gray-900'"
                   class="flex flex-col items-center p-2 transition-colors flex-1 text-center">
                    <i class="fas fa-home text-lg mb-1" :class="currentPath.includes('/dashboard') ? 'scale-110 shadow-sm' : ''" style="transition: transform 0.2s"></i>
                    <span class="text-[10px] font-medium">{{ __('nav.home') }}</span>
                </a>
                
                @hasPermission('view-students')
                <a href="{{ url('students') }}" @click.prevent="navigate('{{ url('students') }}')"
                   :class="currentPath.includes('/students') ? 'text-primary-600' : 'text-gray-500 hover:text-gray-900'"
                   class="flex flex-col items-center p-2 transition-colors flex-1 text-center">
                    <i class="fas fa-user-graduate text-lg mb-1" :class="currentPath.includes('/students') ? 'scale-110 shadow-sm' : ''" style="transition: transform 0.2s"></i>
                    <span class="text-[10px] font-medium">{{ __('nav.students') }}</span>
                </a>
                @endhasPermission
                
                <button @click="mobileMenuOpen = !mobileMenuOpen" 
                        class="flex flex-col items-center p-2 text-primary-500 relative -top-5 flex-1 text-center">
                    <div class="w-12 h-12 rounded-full bg-gradient-to-r from-primary-500 to-primary-700 text-white flex items-center justify-center shadow-lg shadow-primary-500/40">
                        <i class="fas fa-bars text-xl"></i>
                    </div>
                </button>
                
                @hasPermission('view-classes')
                <a href="{{ url('classes') }}" @click.prevent="navigate('{{ url('classes') }}')"
                   :class="currentPath.includes('/classes') ? 'text-primary-600' : 'text-gray-500 hover:text-gray-900'"
                   class="flex flex-col items-center p-2 transition-colors flex-1 text-center">
                    <i class="fas fa-door-open text-lg mb-1" :class="currentPath.includes('/classes') ? 'scale-110 shadow-sm' : ''" style="transition: transform 0.2s"></i>
                    <span class="text-[10px] font-medium">{{ __('nav.classes') }}</span>
                </a>
                @endhasPermission
                
                <a href="{{ route('profile.show') }}" @click.prevent="navigate('{{ route('profile.show') }}')"
                   :class="currentPath.includes('/profile') ? 'text-primary-600' : 'text-gray-500 hover:text-gray-900'"
                   class="flex flex-col items-center p-2 transition-colors flex-1 text-center">
                    <i class="fas fa-user text-lg mb-1" :class="currentPath.includes('/profile') ? 'scale-110 shadow-sm' : ''" style="transition: transform 0.2s"></i>
                    <span class="text-[10px] font-medium">{{ __('nav.profile') }}</span>
                </a>
            </div>
        </nav>
        
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
    
    <!-- AOS Library -->
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>

    <!-- Lenis – sidebar only (works with overflow:auto natively) -->
    <script src="https://cdn.jsdelivr.net/gh/studio-freight/lenis@1.0.19/bundled/lenis.min.js"></script>

    <script>
        // ── Smooth scroll for SIDEBAR (Lenis works fine here) ────────────────
        function initSidebarLenis() {
            const sidebarNav = document.getElementById('sidebar-nav');
            if (sidebarNav && typeof Lenis !== 'undefined') {
                const sidebarLenis = new Lenis({
                    wrapper: sidebarNav,
                    content: sidebarNav,
                    duration: 0.9,
                    easing: (t) => Math.min(1, 1.001 - Math.pow(2, -10 * t)),
                    smoothWheel: true,
                });
                function raf(time) {
                    sidebarLenis.raf(time);
                    requestAnimationFrame(raf);
                }
                requestAnimationFrame(raf);
            }
        }

        // ── Smooth scroll for MAIN CONTENT (custom lerp – works with overflow-y:auto) ──
        // Lenis v1 element-mode requires overflow:hidden on the wrapper which
        // conflicts with the flex layout. A wheel-event interceptor + lerp
        // gives the exact same feel without any structural changes.
        function initMainSmoothScroll() {
            const el = document.getElementById('main-scroll-container');
            if (!el) return;

            let targetY   = el.scrollTop;
            let currentY  = el.scrollTop;
            let rafId     = null;
            const LERP    = 0.11;   // lower = smoother / slower
            const SPEED   = 0.85;   // wheel delta multiplier

            el.addEventListener('wheel', function(e) {
                e.preventDefault();
                targetY += e.deltaY * SPEED;
                // Clamp to scrollable range
                targetY = Math.max(0, Math.min(targetY, el.scrollHeight - el.clientHeight));

                if (!rafId) {
                    function step() {
                        const dist = targetY - currentY;
                        if (Math.abs(dist) < 0.5) {
                            currentY = targetY;
                            el.scrollTop = currentY;
                            rafId = null;
                            return;
                        }
                        currentY += dist * LERP;
                        el.scrollTop = currentY;
                        rafId = requestAnimationFrame(step);
                    }
                    rafId = requestAnimationFrame(step);
                }
            }, { passive: false });

            // Keep targetY in sync when user drags the scrollbar
            el.addEventListener('scroll', function() {
                if (!rafId) {
                    targetY  = el.scrollTop;
                    currentY = el.scrollTop;
                }
            }, { passive: true });
        }

        document.addEventListener('DOMContentLoaded', function() {
            initSidebarLenis();
            initMainSmoothScroll();
        });

        // Init Scroll Animations
        AOS.init({ duration: 800, once: true, offset: 50 });

        // Re-init AOS on SPA page load
        document.addEventListener('spaContentLoaded', () => {
            AOS.refresh();
            if (typeof reinitializeAnimations === 'function') {
                reinitializeAnimations();
            }
        });
    </script>

    <!-- Enhanced UX Animations with GSAP -->
    <script src="{{ asset('js/animations.js') }}"></script>

    <!-- PWA Registration & Installation Handler -->
    <!-- PWA Scripts -->
    <script src="{{ asset('js/unified-pwa-manager.js') }}"></script>
    <script src="{{ asset('js/pwa.js') }}"></script>

    <!-- Quill Global Initialization -->
    <script>
        function initQuill() {
            // Initialize Quill if editor element is present
            var editorContainer = document.getElementById('blog-editor-container');
            if (editorContainer && typeof Quill !== 'undefined' && !editorContainer.classList.contains('ql-container')) {
                var quill = new Quill(editorContainer, {
                    theme: 'snow',
                    modules: {
                        toolbar: [
                            ['bold', 'italic', 'underline', 'strike'],
                            ['blockquote', 'code-block'],
                            [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                            [{ 'color': [] }, { 'background': [] }],
                            ['link', 'image'],
                            ['clean']
                        ]
                    },
                    placeholder: 'Write your article here...'
                });
                
                // Sync Quill content to hidden textarea on form submission
                var hiddenTextarea = document.getElementById('blog-body-content');
                if (hiddenTextarea) {
                    // Populate initial content if editing
                    if (hiddenTextarea.value) {
                        quill.clipboard.dangerouslyPasteHTML(hiddenTextarea.value);
                    }

                    var form = hiddenTextarea.closest('form');
                    if (form) {
                        form.addEventListener('submit', function(e) {
                            hiddenTextarea.value = quill.root.innerHTML;
                        });
                    }
                }
                
                console.log('Quill editor initialized successfully');
            }
        }
        document.addEventListener('DOMContentLoaded', initQuill);
        document.addEventListener('spaContentLoaded', initQuill);
    </script>

    @stack('scripts')
</body>
</html>
