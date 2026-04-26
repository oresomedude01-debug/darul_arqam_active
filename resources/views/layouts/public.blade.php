@php
    $schoolSettings = \App\Models\SchoolSetting::getInstance();
@endphp

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" 
      dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}"
      class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>@yield('title') | {{ $schoolSettings->school_name ?? 'Portal' }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Lexend:wght@400;600;700&display=swap" rel="stylesheet">

    <script src="https://cdn.tailwindcss.com"></script>

    <script src="https://kit.fontawesome.com/your-kit-id.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                        heading: ['Lexend', 'sans-serif'],
                    },
                    colors: {
                        brand: {
                            50: '#f0f7ff',
                            100: '#e0effe',
                            200: '#bae0fd',
                            600: '#2563eb', // Primary Brand Color
                            700: '#1d4ed8',
                            900: '#1e3a8a',
                        },
                        accent: '#f59e0b', // Amber for call-to-actions
                    },
                    boxShadow: {
                        'soft': '0 2px 15px -3px rgba(0, 0, 0, 0.07), 0 4px 6px -2px rgba(0, 0, 0, 0.05)',
                    }
                }
            }
        }
    </script>

    <style>
        [x-cloak] { display: none !important; }
        body { font-feature-settings: "cv11", "ss01"; }
        .glass { background: rgba(255, 255, 255, 0.8); backdrop-filter: blur(10px); }
    </style>
    
    <!-- RTL Support CSS -->
    <link rel="stylesheet" href="{{ asset('css/rtl.css') }}">
    
    @stack('styles')
</head>
<body class="antialiased bg-[#f8fafc] text-slate-900 flex flex-col min-h-screen">

    <div class="h-1.5 w-full bg-gradient-to-r from-brand-600 via-purple-500 to-accent"></div>

    <nav x-data="{ mobileMenu: false }" class="glass sticky top-0 z-50 border-b border-slate-200">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-20">
                <div class="flex items-center">
                    <a href="/" class="flex items-center gap-3 transition hover:opacity-90">
                        @if($schoolSettings->school_logo)
                            <img src="{{ asset('storage/' . $schoolSettings->school_logo) }}" alt="Logo" class="h-10 w-auto object-contain">
                        @else
                            <div class="h-10 w-10 bg-brand-600 rounded-xl flex items-center justify-center shadow-lg shadow-brand-200">
                                <i class="fas fa-graduation-cap text-white"></i>
                            </div>
                        @endif
                        <div class="hidden sm:block">
                            <span class="block text-lg font-heading font-bold leading-none text-slate-800">
                                {{ $schoolSettings->school_name ?? 'School Name' }}
                            </span>
                            <span class="text-[11px] uppercase tracking-widest font-semibold text-brand-600">
                                Enrollment Portal
                            </span>
                        </div>
                    </a>
                </div>

                <div class="hidden md:flex items-center gap-8">
                    <div class="flex flex-col items-end">
                        <span class="text-xs text-slate-500 font-medium">Support Line</span>
                        <a href="tel:{{ $schoolSettings->school_phone }}" class="text-sm font-bold text-slate-700 hover:text-brand-600 transition">
                            {{ $schoolSettings->school_phone ?? '+1 (234) 567-890' }}
                        </a>
                    </div>
                    <a href="#" class="px-5 py-2.5 bg-brand-600 text-white text-sm font-semibold rounded-full hover:bg-brand-700 transition shadow-md shadow-brand-100">
                        Check Status
                    </a>
                </div>

                <div class="md:hidden flex items-center">
                    <button @click="mobileMenu = !mobileMenu" class="text-slate-600 p-2">
                        <i class="fa-solid fa-bars-staggered text-2xl"></i>
                    </button>
                </div>
            </div>
        </div>

        <div x-show="mobileMenu" x-cloak class="md:hidden border-t border-slate-100 bg-white p-4">
            <a href="#" class="block py-2 text-slate-700 font-medium">Check Enrollment Status</a>
            <a href="tel:{{ $schoolSettings->school_phone }}" class="block py-2 text-slate-700 font-medium">Contact Support</a>
        </div>
    </nav>

    <main class="flex-grow">
        @if(View::hasSection('header_title'))
        <div class="bg-white border-b border-slate-200 py-10 mb-8">
            <div class="container mx-auto px-4">
                <h2 class="text-3xl font-heading font-bold text-slate-900">@yield('header_title')</h2>
                <p class="mt-2 text-slate-600 max-w-2xl">@yield('header_subtitle')</p>
            </div>
        </div>
        @endif

        <div class="container mx-auto px-4 py-4 lg:py-10">
            @if(session('success'))
                <div class="mb-6 p-4 bg-emerald-50 border-l-4 border-emerald-500 text-emerald-800 rounded-r-lg shadow-sm">
                    <div class="flex items-center gap-3">
                        <i class="fa-solid fa-circle-check"></i>
                        <p class="font-medium">{{ session('success') }}</p>
                    </div>
                </div>
            @endif

            @yield('content')
        </div>
    </main>

    <footer class="bg-white border-t border-slate-200 pt-12 pb-8">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-10 mb-10">
                <div class="col-span-1">
                    <h3 class="font-heading font-bold text-slate-800 mb-4">About the Portal</h3>
                    <p class="text-slate-500 text-sm leading-relaxed">
                        Our streamlined enrollment process is designed to get you started on your educational journey as quickly and efficiently as possible.
                    </p>
                </div>
                <div>
                    <h3 class="font-heading font-bold text-slate-800 mb-4">Quick Links</h3>
                    <ul class="text-slate-500 text-sm space-y-2">
                        <li><a href="#" class="hover:text-brand-600 transition">Privacy Policy</a></li>
                        <li><a href="#" class="hover:text-brand-600 transition">Requirements Guide</a></li>
                        <li><a href="#" class="hover:text-brand-600 transition">FAQs</a></li>
                    </ul>
                </div>
                <div>
                    <h3 class="font-heading font-bold text-slate-800 mb-4">Contact</h3>
                    <ul class="text-slate-500 text-sm space-y-2">
                        <li class="flex items-center gap-2">
                            <i class="fa-solid fa-envelope text-brand-600"></i>
                            {{ $schoolSettings->school_email ?? 'admissions@school.edu' }}
                        </li>
                        <li class="flex items-center gap-2">
                            <i class="fa-solid fa-location-dot text-brand-600"></i>
                            {{ $schoolSettings->school_address ?? '123 Education Way' }}
                        </li>
                    </ul>
                </div>
            </div>
            
            <div class="pt-8 border-t border-slate-100 flex flex-col md:flex-row justify-between items-center gap-4">
                <p class="text-slate-400 text-xs text-center md:text-left">
                    &copy; {{ date('Y') }} {{ $schoolSettings->school_name }}. {{ $schoolSettings->footer_text ?? 'All rights reserved.' }}
                </p>
                <div class="flex gap-4 grayscale opacity-60">
                    <i class="fa-brands fa-facebook hover:text-blue-600 cursor-pointer"></i>
                    <i class="fa-brands fa-x-twitter hover:text-black cursor-pointer"></i>
                    <i class="fa-brands fa-instagram hover:text-pink-600 cursor-pointer"></i>
                </div>
            </div>
        </div>
    </footer>

    @stack('scripts')
</body>
</html>