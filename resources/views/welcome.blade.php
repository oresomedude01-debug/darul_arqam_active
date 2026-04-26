<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @php
        $schoolSettings = \App\Models\SchoolSetting::getInstance();
    @endphp
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $schoolSettings->school_name ?? 'Madrasah' }} - Excellence in Islamic Education</title>
    <meta name="description" content="{{ $schoolSettings->school_motto ?? 'Excellence in Islamic Education' }}">
    
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: { 50: '#e6f0f5', 100: '#cce1eb', 200: '#99c3d7', 300: '#66a5c3', 400: '#3387af', 500: '#0B4D73', 600: '#093e5c', 700: '#072e45', 800: '#041f2e', 900: '#020f17' },
                        warm: { 50: '#fdfbf7', 100: '#f9f5eb', 200: '#f0e6d3', 300: '#e4d4b8', 400: '#d4bc94', 500: '#c4a470' }
                    },
                    fontFamily: { sans: ['Inter', 'system-ui', 'sans-serif'] }
                }
            }
        }
    </script>
    
    <style>
        * { font-family: 'Inter', system-ui, sans-serif; }
        html { scroll-behavior: smooth; }
        
        /* Animations */
        @keyframes fadeInUp { from { opacity: 0; transform: translateY(30px); } to { opacity: 1; transform: translateY(0); } }
        @keyframes fadeInLeft { from { opacity: 0; transform: translateX(-30px); } to { opacity: 1; transform: translateX(0); } }
        @keyframes fadeInRight { from { opacity: 0; transform: translateX(30px); } to { opacity: 1; transform: translateX(0); } }
        @keyframes float { 0%, 100% { transform: translateY(0px); } 50% { transform: translateY(-15px); } }
        @keyframes pulse-soft { 0%, 100% { opacity: 0.4; } 50% { opacity: 0.8; } }
        @keyframes shimmer { 0% { background-position: -200% 0; } 100% { background-position: 200% 0; } }
        
        .animate-fade-in-up { animation: fadeInUp 0.8s ease-out forwards; }
        .animate-fade-in-left { animation: fadeInLeft 0.8s ease-out forwards; }
        .animate-fade-in-right { animation: fadeInRight 0.8s ease-out forwards; }
        .animate-float { animation: float 6s ease-in-out infinite; }
        .animate-pulse-soft { animation: pulse-soft 3s ease-in-out infinite; }
        
        .delay-100 { animation-delay: 0.1s; opacity: 0; }
        .delay-200 { animation-delay: 0.2s; opacity: 0; }
        .delay-300 { animation-delay: 0.3s; opacity: 0; }
        .delay-400 { animation-delay: 0.4s; opacity: 0; }
        
        /* Islamic Pattern */
        .islamic-pattern {
            background-image: url("data:image/svg+xml,%3Csvg width='80' height='80' viewBox='0 0 80 80' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M40 0L80 40L40 80L0 40z' fill='none' stroke='%23ffffff' stroke-width='0.5' opacity='0.1'/%3E%3Ccircle cx='40' cy='40' r='15' fill='none' stroke='%23ffffff' stroke-width='0.5' opacity='0.08'/%3E%3C/svg%3E");
        }
        
        /* Card Effects */
        .card-hover { transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1); }
        .card-hover:hover { transform: translateY(-8px); box-shadow: 0 25px 50px -12px rgba(11, 77, 115, 0.2); }
        
        /* Gradient overlays */
        .hero-overlay {
            background: linear-gradient(135deg, rgba(11, 77, 115, 0.92) 0%, rgba(11, 77, 115, 0.85) 50%, rgba(11, 77, 115, 0.75) 100%);
        }
        
        /* Decorative shapes */
        .shape-blob { border-radius: 60% 40% 30% 70% / 60% 30% 70% 40%; }
        
        /* Scroll reveal */
        .reveal { opacity: 0; transform: translateY(30px); transition: all 0.8s ease; }
        .reveal.active { opacity: 1; transform: translateY(0); }
    </style>
</head>

<body class="antialiased bg-warm-50 text-gray-800">

    <!-- Navigation -->
    <header id="navbar" class="fixed top-0 left-0 right-0 z-50 bg-white/95 backdrop-blur-md shadow-sm">
        <nav class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <a href="#" class="flex items-center gap-3">
                    @if($schoolSettings->school_logo)
                        <img src="{{ asset('storage/' . $schoolSettings->school_logo) }}" alt="{{ $schoolSettings->school_name }}" class="h-10 w-10 rounded-lg object-cover">
                    @else
                        <div class="h-10 w-10 bg-brand-500 rounded-lg flex items-center justify-center">
                            <i class="fas fa-mosque text-white text-lg"></i>
                        </div>
                    @endif
                    <span class="font-bold text-brand-500 text-lg hidden sm:block">{{ $schoolSettings->school_name ?? 'Madrasah' }}</span>
                </a>

                <div class="hidden md:flex items-center gap-8">
                    <a href="#home" class="text-gray-600 hover:text-brand-500 text-sm font-medium transition-colors">Home</a>
                    <a href="{{ route('about') }}" class="text-gray-600 hover:text-brand-500 text-sm font-medium transition-colors">About</a>
                    <a href="#programs" class="text-gray-600 hover:text-brand-500 text-sm font-medium transition-colors">Programs</a>
                    <a href="{{ route('gallery') }}" class="text-gray-600 hover:text-brand-500 text-sm font-medium transition-colors">Gallery</a>
                    <a href="{{ route('blog.index') }}" class="text-gray-600 hover:text-brand-500 text-sm font-medium transition-colors">Blog</a>
                    <a href="#contact" class="text-gray-600 hover:text-brand-500 text-sm font-medium transition-colors">Contact</a>
                </div>

                <div class="flex items-center gap-3">
                    @if (Route::has('login'))
                        @auth
                            <a href="{{ url('/dashboard') }}" class="text-sm font-medium text-brand-500 hover:text-brand-600 hidden sm:block">Dashboard</a>
                        @else
                            <a href="{{ route('login') }}" class="text-sm font-medium text-gray-600 hover:text-brand-500 hidden sm:block">Log in</a>
                            <a href="{{ route('enrollment.token') }}" class="bg-brand-500 hover:bg-brand-600 text-white text-sm font-semibold px-5 py-2.5 rounded-lg transition-all hover:shadow-lg">Enroll</a>
                        @endauth
                    @endif
                    <button id="mobileMenuBtn" class="md:hidden p-2 text-gray-600" aria-label="Menu">
                        <i class="fas fa-bars text-xl"></i>
                    </button>
                </div>
            </div>
        </nav>

        <div id="mobileMenu" class="hidden md:hidden bg-white border-t">
            <div class="px-4 py-4 space-y-3">
                <a href="#home" class="block py-2 text-gray-700 font-medium">Home</a>
                <a href="{{ route('about') }}" class="block py-2 text-gray-700 font-medium">About</a>
                <a href="#programs" class="block py-2 text-gray-700 font-medium">Programs</a>
                <a href="{{ route('gallery') }}" class="block py-2 text-gray-700 font-medium">Gallery</a>
                <a href="{{ route('blog.index') }}" class="block py-2 text-gray-700 font-medium">Blog</a>
                <a href="#contact" class="block py-2 text-gray-700 font-medium">Contact</a>
                @guest
                    <a href="{{ route('login') }}" class="block py-2 text-gray-700 font-medium">Log in</a>
                @endguest
            </div>
        </div>
    </header>

    <!-- Hero Section with Image -->
    <section id="home" class="relative min-h-screen flex items-center overflow-hidden">
        <!-- Background Image -->
        <div class="absolute inset-0">
            <img src="{{ asset('images/hero-students.png') }}" alt="Students Learning Quran" class="w-full h-full object-cover">
            <div class="hero-overlay absolute inset-0"></div>
        </div>
        
        <!-- Decorative Elements -->
        <div class="absolute top-20 left-10 w-64 h-64 bg-white/5 shape-blob animate-float"></div>
        <div class="absolute bottom-20 right-10 w-48 h-48 bg-warm-500/10 shape-blob animate-float" style="animation-delay: 2s;"></div>
        <div class="absolute top-1/2 right-1/4 w-32 h-32 border border-white/10 rounded-full animate-pulse-soft"></div>
        
        <!-- Islamic Pattern Overlay -->
        <div class="absolute inset-0 islamic-pattern opacity-30"></div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 py-32">
            <div class="max-w-3xl">
                <!-- Badge -->
                <div class="animate-fade-in-up inline-flex items-center gap-2 bg-white/15 backdrop-blur-sm px-4 py-2 rounded-full mb-6">
                    <span class="w-2 h-2 bg-green-400 rounded-full animate-pulse"></span>
                    <span class="text-white/90 text-sm font-medium">Admissions Open</span>
                </div>

                <!-- Headline -->
                <h1 class="animate-fade-in-up delay-100 text-4xl sm:text-5xl lg:text-6xl font-extrabold text-white leading-tight mb-6">
                    {{ $schoolSettings->school_name ?? 'Excellence in Islamic Education' }}
                </h1>

                <!-- Subtext -->
                <p class="animate-fade-in-up delay-200 text-lg lg:text-xl text-white/85 leading-relaxed mb-8 max-w-2xl">
                    {{ $schoolSettings->school_mission ?? 'Nurturing hearts and minds through authentic Quranic and Arabic education in the tradition of the righteous scholars.' }}
                </p>

                <!-- CTA Buttons -->
                <div class="animate-fade-in-up delay-300 flex flex-col sm:flex-row gap-4">
                    <a href="{{ route('enrollment.token') }}" class="group inline-flex items-center justify-center gap-2 bg-white text-brand-500 font-semibold px-8 py-4 rounded-xl hover:bg-warm-100 transition-all hover:shadow-xl text-center">
                        <i class="fas fa-user-plus group-hover:scale-110 transition-transform"></i>
                        <span>Start Enrollment</span>
                    </a>
                    <a href="#programs" class="inline-flex items-center justify-center gap-2 border-2 border-white/40 text-white font-semibold px-8 py-4 rounded-xl hover:bg-white/10 backdrop-blur-sm transition-all text-center">
                        <i class="fas fa-book-quran"></i>
                        <span>View Programs</span>
                    </a>
                </div>

                <!-- Trust Stats -->
                <div class="animate-fade-in-up delay-400 grid grid-cols-3 gap-8 mt-16 pt-8 border-t border-white/20">
                    <div class="text-center sm:text-left">
                        <div class="text-3xl lg:text-4xl font-bold text-white">20+</div>
                        <div class="text-sm text-white/70 mt-1">Years Teaching</div>
                    </div>
                    <div class="text-center sm:text-left">
                        <div class="text-3xl lg:text-4xl font-bold text-white">100+</div>
                        <div class="text-sm text-white/70 mt-1">Huffaz Produced</div>
                    </div>
                    <div class="text-center sm:text-left">
                        <div class="text-3xl lg:text-4xl font-bold text-white">30+</div>
                        <div class="text-sm text-white/70 mt-1">Qualified Scholars</div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Scroll Indicator -->
        <div class="absolute bottom-8 left-1/2 -translate-x-1/2 text-white/60 animate-bounce">
            <i class="fas fa-chevron-down text-2xl"></i>
        </div>
    </section>

    <!-- About Preview -->
    <section class="py-20 lg:py-28 bg-white reveal">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid lg:grid-cols-2 gap-16 items-center">
                <div>
                    <span class="text-brand-500 font-semibold text-sm uppercase tracking-wide">About Us</span>
                    <h2 class="text-3xl lg:text-4xl font-bold text-gray-900 mt-2 mb-6">About {{ $schoolSettings->school_name ?? 'Our Madrasah' }}</h2>
                    <p class="text-gray-600 leading-relaxed mb-6">
                        {{ $schoolSettings->school_vision ?? 'We are a premier Islamic educational institution dedicated to nurturing brilliant minds and noble characters through authentic Quranic and Arabic education.' }}
                    </p>
                    
                    <div class="grid grid-cols-2 gap-4 mb-8">
                        <div class="flex items-start gap-3 p-4 bg-brand-50 rounded-xl">
                            <div class="w-10 h-10 bg-brand-500 rounded-lg flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-quran text-white"></i>
                            </div>
                            <div>
                                <h4 class="font-semibold text-gray-900 text-sm">Quran & Tajweed</h4>
                                <p class="text-xs text-gray-600">Complete Hifz program</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-3 p-4 bg-green-50 rounded-xl">
                            <div class="w-10 h-10 bg-green-600 rounded-lg flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-language text-white"></i>
                            </div>
                            <div>
                                <h4 class="font-semibold text-gray-900 text-sm">Arabic Language</h4>
                                <p class="text-xs text-gray-600">Classical Arabic studies</p>
                            </div>
                        </div>
                    </div>
                    
                    <a href="{{ route('about') }}" class="inline-flex items-center gap-2 text-brand-500 font-semibold hover:gap-3 transition-all">
                        Learn more about us <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
                
                <div class="relative">
                    <div class="bg-brand-500 text-white p-8 rounded-2xl shadow-2xl">
                        <div class="flex items-center gap-3 mb-4">
                            <i class="fas fa-bullseye text-2xl text-white/80"></i>
                            <h3 class="text-xl font-bold">Our Mission</h3>
                        </div>
                        <p class="text-white/90 leading-relaxed">
                            {{ $schoolSettings->school_mission ?? 'To provide authentic Islamic education rooted in the Quran and Sunnah, nurturing scholars who will benefit the Ummah.' }}
                        </p>
                    </div>
                    <!-- Decorative -->
                    <div class="absolute -bottom-4 -right-4 w-24 h-24 bg-warm-200 rounded-2xl -z-10"></div>
                    <div class="absolute -top-4 -left-4 w-16 h-16 border-4 border-brand-200 rounded-xl -z-10"></div>
                </div>
            </div>
        </div>
    </section>

    <!-- Programs Section -->
    <section id="programs" class="py-20 lg:py-28 bg-gray-50 reveal">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-2xl mx-auto mb-16">
                <span class="text-brand-500 font-semibold text-sm uppercase tracking-wide">Our Programs</span>
                <h2 class="text-3xl lg:text-4xl font-bold text-gray-900 mt-2 mb-4">Islamic Knowledge Programs</h2>
                <p class="text-gray-600">Comprehensive Arabic and Islamic studies designed to nurture righteous scholars.</p>
            </div>

            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                <!-- Program 1 -->
                <article class="card-hover bg-white rounded-2xl overflow-hidden shadow-lg group">
                    <div class="h-44 bg-gradient-to-br from-rose-400 to-pink-500 flex items-center justify-center relative overflow-hidden">
                        <i class="fas fa-book-open text-white text-5xl relative z-10 group-hover:scale-110 transition-transform"></i>
                        <div class="absolute inset-0 bg-black/10 group-hover:bg-black/0 transition-colors"></div>
                    </div>
                    <div class="p-6">
                        <span class="inline-block text-xs font-semibold text-rose-600 bg-rose-50 px-3 py-1 rounded-full mb-3">Beginners</span>
                        <h3 class="text-xl font-bold text-gray-900 mb-2">Quranic Foundation</h3>
                        <p class="text-gray-600 text-sm mb-4">Introduction to Quran reading, Arabic alphabet, and basic Islamic manners.</p>
                        <ul class="space-y-2 text-sm text-gray-600 mb-5">
                            <li class="flex items-center gap-2"><i class="fas fa-check text-green-500"></i>Nooraniyyah / Qa'idah</li>
                            <li class="flex items-center gap-2"><i class="fas fa-check text-green-500"></i>Arabic Alphabet</li>
                            <li class="flex items-center gap-2"><i class="fas fa-check text-green-500"></i>Basic Duas & Adab</li>
                        </ul>
                        <a href="{{ route('enrollment.token') }}" class="block text-center py-3 bg-gray-100 text-gray-700 font-semibold rounded-xl hover:bg-brand-500 hover:text-white transition-colors">Enroll Now</a>
                    </div>
                </article>

                <!-- Program 2 -->
                <article class="card-hover bg-white rounded-2xl overflow-hidden shadow-lg group">
                    <div class="h-44 bg-gradient-to-br from-blue-400 to-indigo-500 flex items-center justify-center relative overflow-hidden">
                        <i class="fas fa-quran text-white text-5xl relative z-10 group-hover:scale-110 transition-transform"></i>
                        <div class="absolute inset-0 bg-black/10 group-hover:bg-black/0 transition-colors"></div>
                    </div>
                    <div class="p-6">
                        <span class="inline-block text-xs font-semibold text-blue-600 bg-blue-50 px-3 py-1 rounded-full mb-3">Intermediate</span>
                        <h3 class="text-xl font-bold text-gray-900 mb-2">Hifz & Arabic Studies</h3>
                        <p class="text-gray-600 text-sm mb-4">Intensive Quran memorization with Arabic grammar and Islamic sciences.</p>
                        <ul class="space-y-2 text-sm text-gray-600 mb-5">
                            <li class="flex items-center gap-2"><i class="fas fa-check text-green-500"></i>Quran Memorization</li>
                            <li class="flex items-center gap-2"><i class="fas fa-check text-green-500"></i>Nahw & Sarf (Grammar)</li>
                            <li class="flex items-center gap-2"><i class="fas fa-check text-green-500"></i>Fiqh & Aqeedah</li>
                        </ul>
                        <a href="{{ route('enrollment.token') }}" class="block text-center py-3 bg-gray-100 text-gray-700 font-semibold rounded-xl hover:bg-brand-500 hover:text-white transition-colors">Enroll Now</a>
                    </div>
                </article>

                <!-- Program 3 -->
                <article class="card-hover bg-white rounded-2xl overflow-hidden shadow-lg group">
                    <div class="h-44 bg-gradient-to-br from-violet-400 to-purple-500 flex items-center justify-center relative overflow-hidden">
                        <i class="fas fa-user-graduate text-white text-5xl relative z-10 group-hover:scale-110 transition-transform"></i>
                        <div class="absolute inset-0 bg-black/10 group-hover:bg-black/0 transition-colors"></div>
                    </div>
                    <div class="p-6">
                        <span class="inline-block text-xs font-semibold text-violet-600 bg-violet-50 px-3 py-1 rounded-full mb-3">Advanced</span>
                        <h3 class="text-xl font-bold text-gray-900 mb-2">Advanced Islamic Studies</h3>
                        <p class="text-gray-600 text-sm mb-4">In-depth study of Islamic sciences, Hadith, Tafseer, and classical texts.</p>
                        <ul class="space-y-2 text-sm text-gray-600 mb-5">
                            <li class="flex items-center gap-2"><i class="fas fa-check text-green-500"></i>Hadith Sciences</li>
                            <li class="flex items-center gap-2"><i class="fas fa-check text-green-500"></i>Tafseer & Usul</li>
                            <li class="flex items-center gap-2"><i class="fas fa-check text-green-500"></i>Seerah & History</li>
                        </ul>
                        <a href="{{ route('enrollment.token') }}" class="block text-center py-3 bg-gray-100 text-gray-700 font-semibold rounded-xl hover:bg-brand-500 hover:text-white transition-colors">Enroll Now</a>
                    </div>
                </article>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="py-20 lg:py-28 bg-white reveal">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-2xl mx-auto mb-16">
                <span class="text-brand-500 font-semibold text-sm uppercase tracking-wide">Why Choose Us</span>
                <h2 class="text-3xl lg:text-4xl font-bold text-gray-900 mt-2 mb-4">What Makes Us Different</h2>
                <p class="text-gray-600">Authentic traditional Islamic education with qualified scholars and a nurturing environment.</p>
            </div>

            <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <div class="card-hover p-6 rounded-2xl bg-gradient-to-br from-brand-50 to-brand-100 border border-brand-200">
                    <div class="w-14 h-14 bg-brand-500 rounded-xl flex items-center justify-center mb-4 shadow-lg">
                        <i class="fas fa-quran text-white text-2xl"></i>
                    </div>
                    <h3 class="font-bold text-gray-900 mb-2">Quranic Excellence</h3>
                    <p class="text-gray-600 text-sm">Complete Hifz program with certified instructors and proper tajweed.</p>
                </div>

                <div class="card-hover p-6 rounded-2xl bg-gradient-to-br from-green-50 to-green-100 border border-green-200">
                    <div class="w-14 h-14 bg-green-600 rounded-xl flex items-center justify-center mb-4 shadow-lg">
                        <i class="fas fa-language text-white text-2xl"></i>
                    </div>
                    <h3 class="font-bold text-gray-900 mb-2">Arabic Language</h3>
                    <p class="text-gray-600 text-sm">Classical Arabic (Nahw, Sarf, Balagha) to understand sacred texts.</p>
                </div>

                <div class="card-hover p-6 rounded-2xl bg-gradient-to-br from-amber-50 to-amber-100 border border-amber-200">
                    <div class="w-14 h-14 bg-amber-600 rounded-xl flex items-center justify-center mb-4 shadow-lg">
                        <i class="fas fa-heart text-white text-2xl"></i>
                    </div>
                    <h3 class="font-bold text-gray-900 mb-2">Character Building</h3>
                    <p class="text-gray-600 text-sm">Emphasis on adab, discipline, and Islamic manners.</p>
                </div>

                <div class="card-hover p-6 rounded-2xl bg-gradient-to-br from-rose-50 to-rose-100 border border-rose-200">
                    <div class="w-14 h-14 bg-rose-600 rounded-xl flex items-center justify-center mb-4 shadow-lg">
                        <i class="fas fa-shield-alt text-white text-2xl"></i>
                    </div>
                    <h3 class="font-bold text-gray-900 mb-2">Safe Environment</h3>
                    <p class="text-gray-600 text-sm">Secure campus with caring staff and nurturing atmosphere.</p>
                </div>

                <div class="card-hover p-6 rounded-2xl bg-gradient-to-br from-indigo-50 to-indigo-100 border border-indigo-200">
                    <div class="w-14 h-14 bg-indigo-600 rounded-xl flex items-center justify-center mb-4 shadow-lg">
                        <i class="fas fa-book text-white text-2xl"></i>
                    </div>
                    <h3 class="font-bold text-gray-900 mb-2">Hadith & Fiqh</h3>
                    <p class="text-gray-600 text-sm">Study of prophetic traditions and Islamic jurisprudence.</p>
                </div>

                <div class="card-hover p-6 rounded-2xl bg-gradient-to-br from-teal-50 to-teal-100 border border-teal-200">
                    <div class="w-14 h-14 bg-teal-600 rounded-xl flex items-center justify-center mb-4 shadow-lg">
                        <i class="fas fa-user-tie text-white text-2xl"></i>
                    </div>
                    <h3 class="font-bold text-gray-900 mb-2">Qualified Scholars</h3>
                    <p class="text-gray-600 text-sm">Learned teachers with ijazah and traditional Islamic training.</p>
                </div>

                <div class="card-hover p-6 rounded-2xl bg-gradient-to-br from-cyan-50 to-cyan-100 border border-cyan-200">
                    <div class="w-14 h-14 bg-cyan-600 rounded-xl flex items-center justify-center mb-4 shadow-lg">
                        <i class="fas fa-mosque text-white text-2xl"></i>
                    </div>
                    <h3 class="font-bold text-gray-900 mb-2">Prayer Facilities</h3>
                    <p class="text-gray-600 text-sm">Dedicated prayer spaces for daily salah and spiritual growth.</p>
                </div>

                <div class="card-hover p-6 rounded-2xl bg-gradient-to-br from-orange-50 to-orange-100 border border-orange-200">
                    <div class="w-14 h-14 bg-orange-600 rounded-xl flex items-center justify-center mb-4 shadow-lg">
                        <i class="fas fa-scroll text-white text-2xl"></i>
                    </div>
                    <h3 class="font-bold text-gray-900 mb-2">Seerah & History</h3>
                    <p class="text-gray-600 text-sm">Prophetic biography and Islamic civilization history.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-20 lg:py-24 bg-brand-500 relative overflow-hidden reveal">
        <div class="absolute inset-0 islamic-pattern"></div>
        <div class="absolute top-0 right-0 w-96 h-96 bg-white/5 rounded-full -translate-y-1/2 translate-x-1/2"></div>
        <div class="absolute bottom-0 left-0 w-64 h-64 bg-white/5 rounded-full translate-y-1/2 -translate-x-1/2"></div>
        
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center relative z-10">
            <h2 class="text-3xl lg:text-5xl font-bold text-white mb-6">Raise Your Child Upon Knowledge</h2>
            <p class="text-xl text-white/85 mb-10 max-w-2xl mx-auto">Enroll your child in authentic Islamic education and plant the seeds of knowledge that will benefit them in this life and the hereafter.</p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="{{ route('enrollment.token') }}" class="group inline-flex items-center justify-center gap-2 bg-white text-brand-500 font-bold px-10 py-4 rounded-xl hover:shadow-2xl transition-all">
                    <i class="fas fa-user-plus group-hover:scale-110 transition-transform"></i>
                    <span>Enroll Your Child Now</span>
                </a>
                <a href="#contact" class="inline-flex items-center justify-center gap-2 border-2 border-white text-white font-bold px-10 py-4 rounded-xl hover:bg-white/10 transition-all">
                    <i class="fas fa-phone"></i>
                    <span>Contact Us</span>
                </a>
            </div>
        </div>
    </section>

    <!-- Contact Section -->
    <section id="contact" class="py-20 lg:py-28 bg-gray-50 reveal">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-2xl mx-auto mb-16">
                <span class="text-brand-500 font-semibold text-sm uppercase tracking-wide">Contact Us</span>
                <h2 class="text-3xl lg:text-4xl font-bold text-gray-900 mt-2 mb-4">Get In Touch</h2>
                <p class="text-gray-600">Have questions? We're here to help you make the right decision for your child.</p>
            </div>

            <div class="grid md:grid-cols-3 gap-8 max-w-4xl mx-auto">
                <div class="card-hover bg-white p-8 rounded-2xl shadow-lg text-center">
                    <div class="w-16 h-16 bg-brand-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-map-marker-alt text-brand-500 text-2xl"></i>
                    </div>
                    <h3 class="font-bold text-gray-900 mb-2">Location</h3>
                    <p class="text-gray-600 text-sm">{{ $schoolSettings->school_address ?? 'Our School Address' }}</p>
                </div>

                <div class="card-hover bg-white p-8 rounded-2xl shadow-lg text-center">
                    <div class="w-16 h-16 bg-green-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-phone text-green-600 text-2xl"></i>
                    </div>
                    <h3 class="font-bold text-gray-900 mb-2">Phone</h3>
                    <p class="text-gray-600 text-sm">{{ $schoolSettings->school_phone ?? '+234 XXX XXX XXXX' }}</p>
                </div>

                <div class="card-hover bg-white p-8 rounded-2xl shadow-lg text-center">
                    <div class="w-16 h-16 bg-purple-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-envelope text-purple-600 text-2xl"></i>
                    </div>
                    <h3 class="font-bold text-gray-900 mb-2">Email</h3>
                    <p class="text-gray-600 text-sm">{{ $schoolSettings->school_email ?? 'info@madrasah.edu' }}</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-12 mb-12">
                <div>
                    <div class="flex items-center gap-3 mb-6">
                        @if($schoolSettings->school_logo)
                            <img src="{{ asset('storage/' . $schoolSettings->school_logo) }}" alt="" class="h-12 w-12 rounded-xl object-cover">
                        @else
                            <div class="h-12 w-12 bg-brand-500 rounded-xl flex items-center justify-center">
                                <i class="fas fa-mosque text-white text-xl"></i>
                            </div>
                        @endif
                        <span class="font-bold text-xl">{{ $schoolSettings->school_name ?? 'Madrasah' }}</span>
                    </div>
                    <p class="text-gray-400 text-sm leading-relaxed mb-4">{{ $schoolSettings->school_motto ?? 'Excellence in Islamic Education' }}</p>
                    <div class="flex gap-3">
                        <a href="#" class="w-10 h-10 bg-gray-800 rounded-lg flex items-center justify-center hover:bg-brand-500 transition-colors"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="w-10 h-10 bg-gray-800 rounded-lg flex items-center justify-center hover:bg-brand-500 transition-colors"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="w-10 h-10 bg-gray-800 rounded-lg flex items-center justify-center hover:bg-brand-500 transition-colors"><i class="fab fa-instagram"></i></a>
                    </div>
                </div>

                <div>
                    <h4 class="font-bold mb-6">Quick Links</h4>
                    <ul class="space-y-3 text-gray-400 text-sm">
                        <li><a href="#home" class="hover:text-white transition-colors">Home</a></li>
                        <li><a href="{{ route('about') }}" class="hover:text-white transition-colors">About Us</a></li>
                        <li><a href="#programs" class="hover:text-white transition-colors">Programs</a></li>
                        <li><a href="{{ route('gallery') }}" class="hover:text-white transition-colors">Gallery</a></li>
                        <li><a href="{{ route('blog.index') }}" class="hover:text-white transition-colors">Blog</a></li>
                    </ul>
                </div>

                <div>
                    <h4 class="font-bold mb-6">Admissions</h4>
                    <ul class="space-y-3 text-gray-400 text-sm">
                        <li><a href="{{ route('enrollment.token') }}" class="hover:text-white transition-colors">Start Enrollment</a></li>
                        <li><a href="#contact" class="hover:text-white transition-colors">Contact Us</a></li>
                    </ul>
                </div>

                <div>
                    <h4 class="font-bold mb-6">Contact Info</h4>
                    <ul class="space-y-4 text-gray-400 text-sm">
                        <li class="flex items-start gap-3"><i class="fas fa-map-marker-alt mt-1 text-brand-400"></i><span>{{ $schoolSettings->school_address ?? 'Address' }}</span></li>
                        <li class="flex items-center gap-3"><i class="fas fa-phone text-brand-400"></i><span>{{ $schoolSettings->school_phone ?? 'Phone' }}</span></li>
                        <li class="flex items-center gap-3"><i class="fas fa-envelope text-brand-400"></i><span>{{ $schoolSettings->school_email ?? 'Email' }}</span></li>
                    </ul>
                </div>
            </div>

            <div class="border-t border-gray-800 pt-8 text-center text-gray-500 text-sm">
                <p>&copy; {{ date('Y') }} {{ $schoolSettings->school_name ?? 'Madrasah' }}. {{ $schoolSettings->footer_text ?? 'All rights reserved.' }}</p>
            </div>
        </div>
    </footer>

    <!-- WhatsApp Button -->
    @php $whatsappNumber = preg_replace('/\D/', '', $schoolSettings->school_phone ?? ''); @endphp
    @if($whatsappNumber)
    <a href="https://wa.me/{{ $whatsappNumber }}" target="_blank" class="fixed bottom-6 right-6 z-50 w-14 h-14 bg-green-500 rounded-full flex items-center justify-center shadow-xl hover:scale-110 transition-transform" aria-label="WhatsApp">
        <i class="fab fa-whatsapp text-white text-2xl"></i>
    </a>
    @endif

    <script>
        // Mobile Menu
        document.getElementById('mobileMenuBtn').addEventListener('click', function() {
            var menu = document.getElementById('mobileMenu');
            var icon = this.querySelector('i');
            menu.classList.toggle('hidden');
            icon.classList.toggle('fa-bars');
            icon.classList.toggle('fa-times');
        });

        // Scroll Reveal
        function reveal() {
            var reveals = document.querySelectorAll('.reveal');
            reveals.forEach(function(el) {
                var windowHeight = window.innerHeight;
                var elementTop = el.getBoundingClientRect().top;
                var elementVisible = 150;
                if (elementTop < windowHeight - elementVisible) {
                    el.classList.add('active');
                }
            });
        }
        window.addEventListener('scroll', reveal);
        reveal();

        // Smooth scroll
        document.querySelectorAll('a[href^="#"]').forEach(function(anchor) {
            anchor.addEventListener('click', function(e) {
                e.preventDefault();
                var target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    window.scrollTo({ top: target.offsetTop - 80, behavior: 'smooth' });
                }
            });
        });
    </script>
</body>
</html>