<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Darul Arqam School - Excellence in Islamic Education')</title>
    <meta name="description" content="Darul Arqam School provides world-class Islamic education with modern teaching methods. Enroll your child today for a brighter future.">

    <!-- Tailwind CSS via CDN -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- Modern Design System -->
    <link rel="stylesheet" href="{{ asset('css/modern-design.css') }}">

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Tailwind Config -->
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Poppins', 'sans-serif'],
                    },
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
                            50: '#fdf4ff',
                            100: '#fae8ff',
                            200: '#f5d0fe',
                            300: '#f0abfc',
                            400: '#e879f9',
                            500: '#d946ef',
                            600: '#c026d3',
                            700: '#a21caf',
                            800: '#86198f',
                            900: '#701a75',
                        }
                    },
                    animation: {
                        'fade-in': 'fadeIn 0.6s ease-in-out',
                        'fade-in-up': 'fadeInUp 0.8s ease-out',
                        'slide-in-right': 'slideInRight 0.6s ease-out',
                        'slide-in-left': 'slideInLeft 0.6s ease-out',
                        'bounce-slow': 'bounce 3s infinite',
                        'float': 'float 3s ease-in-out infinite',
                        'pulse-slow': 'pulse 4s cubic-bezier(0.4, 0, 0.6, 1) infinite',
                    },
                    keyframes: {
                        fadeIn: {
                            '0%': { opacity: '0' },
                            '100%': { opacity: '1' },
                        },
                        fadeInUp: {
                            '0%': { opacity: '0', transform: 'translateY(20px)' },
                            '100%': { opacity: '1', transform: 'translateY(0)' },
                        },
                        slideInRight: {
                            '0%': { opacity: '0', transform: 'translateX(100px)' },
                            '100%': { opacity: '1', transform: 'translateX(0)' },
                        },
                        slideInLeft: {
                            '0%': { opacity: '0', transform: 'translateX(-100px)' },
                            '100%': { opacity: '1', transform: 'translateX(0)' },
                        },
                        float: {
                            '0%, 100%': { transform: 'translateY(0)' },
                            '50%': { transform: 'translateY(-20px)' },
                        }
                    }
                }
            }
        }
    </script>

    <style>
        [x-cloak] { display: none !important; }

        /* Smooth scroll behavior */
        html {
            scroll-behavior: smooth;
        }

        /* Gradient background */
        .gradient-bg {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .gradient-primary {
            background: linear-gradient(135deg, #0ea5e9 0%, #0369a1 100%);
        }

        /* Glassmorphism effect */
        .glass {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        /* Hover effects */
        .hover-lift {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .hover-lift:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
        }

        /* Section animations */
        .section-enter {
            opacity: 0;
            transform: translateY(50px);
            transition: opacity 0.6s ease, transform 0.6s ease;
        }

        .section-enter.active {
            opacity: 1;
            transform: translateY(0);
        }

        /* Hero background pattern */
        .hero-pattern {
            background-color: #0369a1;
            background-image:
                radial-gradient(circle at 25px 25px, rgba(255, 255, 255, 0.1) 2%, transparent 0%),
                radial-gradient(circle at 75px 75px, rgba(255, 255, 255, 0.1) 2%, transparent 0%);
            background-size: 100px 100px;
        }
    </style>

    @stack('styles')
</head>
<body class="font-sans antialiased">
    <!-- Navigation -->
    <nav x-data="{ open: false, scrolled: false }"
         @scroll.window="scrolled = window.pageYOffset > 50"
         :class="scrolled ? 'bg-white shadow-lg' : 'bg-transparent'"
         class="fixed top-0 left-0 right-0 z-50 transition-all duration-300">
        <div class="container mx-auto px-4">
            <div class="flex items-center justify-between h-20">
                <!-- Logo -->
                <a href="{{ route('landing') }}" class="flex items-center space-x-3">
                    <div class="w-12 h-12 bg-gradient-primary rounded-lg flex items-center justify-center">
                        <i class="fas fa-graduation-cap text-2xl text-white"></i>
                    </div>
                    <div>
                        <h1 :class="scrolled ? 'text-gray-900' : 'text-white'"
                            class="text-xl font-bold transition-colors">Darul Arqam</h1>
                        <p :class="scrolled ? 'text-gray-600' : 'text-white/80'"
                           class="text-xs transition-colors">Excellence in Education</p>
                    </div>
                </a>

                <!-- Desktop Navigation -->
                <div class="hidden md:flex items-center space-x-8">
                    <a href="{{ route('landing') }}"
                       :class="scrolled ? 'text-gray-700 hover:text-primary-600' : 'text-white hover:text-white/80'"
                       class="font-medium transition-colors">Home</a>
                    <a href="#about"
                       :class="scrolled ? 'text-gray-700 hover:text-primary-600' : 'text-white hover:text-white/80'"
                       class="font-medium transition-colors">About</a>
                    <a href="#programs"
                       :class="scrolled ? 'text-gray-700 hover:text-primary-600' : 'text-white hover:text-white/80'"
                       class="font-medium transition-colors">Programs</a>
                    <a href="#gallery"
                       :class="scrolled ? 'text-gray-700 hover:text-primary-600' : 'text-white hover:text-white/80'"
                       class="font-medium transition-colors">Gallery</a>
                    <a href="#contact"
                       :class="scrolled ? 'text-gray-700 hover:text-primary-600' : 'text-white hover:text-white/80'"
                       class="font-medium transition-colors">Contact</a>
                    <a href="{{ route('enrollment.token') }}"
                       class="bg-gradient-primary text-white px-6 py-2.5 rounded-full font-semibold hover:shadow-lg transition-all duration-300 hover:scale-105">
                        <i class="fas fa-edit mr-2"></i>Enroll Now
                    </a>
                    <a href="{{ route('dashboard') }}"
                       :class="scrolled ? 'text-gray-700 hover:text-primary-600' : 'text-white hover:text-white/80'"
                       class="font-medium transition-colors">
                        <i class="fas fa-user-circle mr-1"></i>Portal
                    </a>
                </div>

                <!-- Mobile Menu Button -->
                <button @click="open = !open"
                        :class="scrolled ? 'text-gray-900' : 'text-white'"
                        class="md:hidden">
                    <i class="fas text-2xl" :class="open ? 'fa-times' : 'fa-bars'"></i>
                </button>
            </div>

            <!-- Mobile Navigation -->
            <div x-show="open"
                 x-transition
                 x-cloak
                 class="md:hidden bg-white shadow-lg rounded-lg my-2 p-4">
                <a href="{{ route('landing') }}" class="block py-2 text-gray-700 hover:text-primary-600 font-medium">Home</a>
                <a href="#about" class="block py-2 text-gray-700 hover:text-primary-600 font-medium">About</a>
                <a href="#programs" class="block py-2 text-gray-700 hover:text-primary-600 font-medium">Programs</a>
                <a href="#gallery" class="block py-2 text-gray-700 hover:text-primary-600 font-medium">Gallery</a>
                <a href="#contact" class="block py-2 text-gray-700 hover:text-primary-600 font-medium">Contact</a>
                <a href="{{ route('enrollment.token') }}" class="block py-2 text-white bg-gradient-primary rounded-lg text-center mt-3 font-semibold">
                    <i class="fas fa-edit mr-2"></i>Enroll Now
                </a>
                <a href="{{ route('dashboard') }}" class="block py-2 text-gray-700 hover:text-primary-600 font-medium">
                    <i class="fas fa-user-circle mr-1"></i>Portal
                </a>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main>
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-gray-900 text-gray-300">
        <div class="container mx-auto px-4 py-16">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8 mb-8">
                <!-- About -->
                <div>
                    <div class="flex items-center space-x-3 mb-4">
                        <div class="w-10 h-10 bg-gradient-primary rounded-lg flex items-center justify-center">
                            <i class="fas fa-graduation-cap text-xl text-white"></i>
                        </div>
                        <h3 class="text-xl font-bold text-white">Darul Arqam</h3>
                    </div>
                    <p class="text-sm leading-relaxed">
                        Providing excellence in Islamic education with modern teaching methods for over 20 years.
                    </p>
                </div>

                <!-- Quick Links -->
                <div>
                    <h4 class="text-lg font-semibold text-white mb-4">Quick Links</h4>
                    <ul class="space-y-2 text-sm">
                        <li><a href="#about" class="hover:text-primary-400 transition-colors"><i class="fas fa-angle-right mr-2"></i>About Us</a></li>
                        <li><a href="#programs" class="hover:text-primary-400 transition-colors"><i class="fas fa-angle-right mr-2"></i>Programs</a></li>
                        <li><a href="#gallery" class="hover:text-primary-400 transition-colors"><i class="fas fa-angle-right mr-2"></i>Gallery</a></li>
                        <li><a href="{{ route('enrollment.token') }}" class="hover:text-primary-400 transition-colors"><i class="fas fa-angle-right mr-2"></i>Admissions</a></li>
                    </ul>
                </div>

                <!-- Contact Info -->
                <div>
                    <h4 class="text-lg font-semibold text-white mb-4">Contact Us</h4>
                    <ul class="space-y-3 text-sm">
                        <li class="flex items-start">
                            <i class="fas fa-map-marker-alt text-primary-400 mt-1 mr-3"></i>
                            <span>123 Education Street<br>Lagos, Nigeria</span>
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-phone text-primary-400 mr-3"></i>
                            <span>+234 XXX XXX XXXX</span>
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-envelope text-primary-400 mr-3"></i>
                            <span>info@darularqam.edu</span>
                        </li>
                    </ul>
                </div>

                <!-- Social Media -->
                <div>
                    <h4 class="text-lg font-semibold text-white mb-4">Follow Us</h4>
                    <div class="flex space-x-3">
                        <a href="#" class="w-10 h-10 bg-gray-800 rounded-full flex items-center justify-center hover:bg-primary-600 transition-colors">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        <a href="#" class="w-10 h-10 bg-gray-800 rounded-full flex items-center justify-center hover:bg-primary-600 transition-colors">
                            <i class="fab fa-twitter"></i>
                        </a>
                        <a href="#" class="w-10 h-10 bg-gray-800 rounded-full flex items-center justify-center hover:bg-primary-600 transition-colors">
                            <i class="fab fa-instagram"></i>
                        </a>
                        <a href="#" class="w-10 h-10 bg-gray-800 rounded-full flex items-center justify-center hover:bg-primary-600 transition-colors">
                            <i class="fab fa-youtube"></i>
                        </a>
                    </div>
                    <div class="mt-6">
                        <p class="text-sm mb-2">Subscribe to our newsletter</p>
                        <div class="flex">
                            <input type="email" placeholder="Your email" class="flex-1 px-3 py-2 bg-gray-800 rounded-l-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">
                            <button class="bg-gradient-primary px-4 py-2 rounded-r-lg hover:shadow-lg transition-all">
                                <i class="fas fa-paper-plane"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Copyright -->
            <div class="border-t border-gray-800 pt-8 text-center text-sm">
                <p>&copy; {{ date('Y') }} Darul Arqam School. All rights reserved. | Designed with <i class="fas fa-heart text-red-500"></i> for Education</p>
            </div>
        </div>
    </footer>

    <!-- Scroll to Top Button -->
    <button x-data="{ show: false }"
            @scroll.window="show = window.pageYOffset > 300"
            x-show="show"
            x-transition
            @click="window.scrollTo({ top: 0, behavior: 'smooth' })"
            class="fixed bottom-8 right-8 w-12 h-12 bg-gradient-primary text-white rounded-full shadow-lg hover:shadow-xl transition-all duration-300 z-40 flex items-center justify-center hover:scale-110">
        <i class="fas fa-arrow-up"></i>
    </button>

    @stack('scripts')

    <script>
        // Intersection Observer for scroll animations
        document.addEventListener('DOMContentLoaded', function() {
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('active');
                    }
                });
            }, {
                threshold: 0.1
            });

            document.querySelectorAll('.section-enter').forEach((el) => {
                observer.observe(el);
            });
        });
    </script>
</body>
</html>
