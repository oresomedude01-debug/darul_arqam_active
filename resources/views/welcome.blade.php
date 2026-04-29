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
    
    <!-- PWA Manifest -->
    <link rel="manifest" href="{{ asset('manifest.json') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/icon-192x192.png') }}">
    
    <!-- PWA Manager Script (Load FIRST) -->
    <script src="{{ asset('js/unified-pwa-manager.js') }}"></script>
    
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

        /* ── Keyframes ── */
        @keyframes fadeInUp   { from { opacity:0; transform:translateY(32px);  } to { opacity:1; transform:translateY(0); } }
        @keyframes fadeInLeft { from { opacity:0; transform:translateX(-32px); } to { opacity:1; transform:translateX(0); } }
        @keyframes float      { 0%,100%{ transform:translateY(0);    } 50%{ transform:translateY(-18px); } }
        @keyframes pulse-soft { 0%,100%{ opacity:.3; } 50%{ opacity:.7; } }
        @keyframes fadeOut    { to { opacity:0; transform:translateY(-20px); } }
        @keyframes orbit      { from{ transform:rotate(0deg) translateX(120px) rotate(0deg); } to{ transform:rotate(360deg) translateX(120px) rotate(-360deg); } }
        @keyframes gradMove   { 0%,100%{ background-position:0% 50%; } 50%{ background-position:100% 50%; } }
        @keyframes scanline   { from{ transform:translateY(-100%); } to{ transform:translateY(100vh); } }
        @keyframes ripple     { 0%{ transform:scale(.8); opacity:1; } 100%{ transform:scale(2.4); opacity:0; } }
        @keyframes sparkle    { 0%,100%{ transform:scale(0) rotate(0deg); opacity:0; } 50%{ transform:scale(1) rotate(180deg); opacity:1; } }

        .animate-fade-in-up   { animation: fadeInUp   .8s ease-out forwards; }
        .animate-fade-in-left { animation: fadeInLeft .8s ease-out forwards; }
        .animate-float        { animation: float 6s ease-in-out infinite; }
        .animate-pulse-soft   { animation: pulse-soft 3s ease-in-out infinite; }
        .fade-out             { animation: fadeOut .5s ease-out forwards; }

        .delay-100 { animation-delay:.1s; opacity:0; }
        .delay-200 { animation-delay:.2s; opacity:0; }
        .delay-300 { animation-delay:.3s; opacity:0; }
        .delay-400 { animation-delay:.4s; opacity:0; }
        .delay-500 { animation-delay:.5s; opacity:0; }

        /* Islamic geometric pattern */
        .islamic-pattern {
            background-image: url("data:image/svg+xml,%3Csvg width='80' height='80' viewBox='0 0 80 80' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M40 0L80 40L40 80L0 40z' fill='none' stroke='%23ffffff' stroke-width='.5' opacity='.1'/%3E%3Ccircle cx='40' cy='40' r='15' fill='none' stroke='%23ffffff' stroke-width='.5' opacity='.08'/%3E%3C/svg%3E");
        }

        /* Cards */
        .card-hover { transition: all .4s cubic-bezier(.4,0,.2,1); }
        .card-hover:hover { transform:translateY(-10px); box-shadow:0 32px 64px -12px rgba(11,77,115,.25); }

        /* Hero */
        .hero-overlay { background: linear-gradient(135deg, rgba(4,22,40,.96) 0%, rgba(11,77,115,.88) 55%, rgba(11,77,115,.70) 100%); }
        .shape-blob   { border-radius: 60% 40% 30% 70% / 60% 30% 70% 40%; }

        /* Scroll reveal */
        .reveal { opacity:0; transform:translateY(30px); transition:all .8s ease; }
        .reveal.active { opacity:1; transform:translateY(0); }

        /* ── PWA Download Section ── */
        .pwa-section {
            background: linear-gradient(135deg, #020c18 0%, #041f2e 40%, #0B4D73 100%);
            background-size: 200% 200%;
            animation: gradMove 8s ease infinite;
            position: relative;
            overflow: hidden;
        }
        .pwa-section::before {
            content:'';
            position:absolute; inset:0;
            background-image: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' stroke='%23ffffff' stroke-width='.3' opacity='.06'%3E%3Cpolygon points='30,2 58,16 58,44 30,58 2,44 2,16'/%3E%3Ccircle cx='30' cy='30' r='10'/%3E%3C/g%3E%3C/svg%3E");
            pointer-events:none;
        }
        .pwa-glow { 
            position:absolute; border-radius:50%; filter:blur(80px); pointer-events:none;
        }
        /* Phone mockup */
        .phone-frame {
            width:200px; height:360px;
            background: linear-gradient(160deg, #1a2940, #0d1f33);
            border-radius: 32px;
            border: 2px solid rgba(255,255,255,.15);
            box-shadow: 0 0 0 1px rgba(255,255,255,.05), 0 40px 80px rgba(0,0,0,.5), inset 0 1px 0 rgba(255,255,255,.1);
            position: relative;
            display: flex; flex-direction: column; align-items: center;
            padding: 14px 10px 10px;
            flex-shrink: 0;
        }
        .phone-notch {
            width: 70px; height: 18px;
            background: #0a0f1a;
            border-radius: 0 0 12px 12px;
            margin-bottom: 8px;
        }
        .phone-screen {
            flex: 1; width: 100%;
            background: linear-gradient(160deg, #0d2137, #0B4D73);
            border-radius: 16px;
            overflow: hidden;
            display: flex; flex-direction: column;
            padding: 10px 8px;
            gap: 6px;
        }
        .phone-bar {
            height: 7px; border-radius: 4px;
            background: rgba(255,255,255,.15);
        }
        .phone-bar.accent { background: linear-gradient(90deg,#3387af,#66a5c3); width:60%; }
        .phone-tile {
            height: 32px; border-radius: 8px;
            background: rgba(255,255,255,.08);
            display: flex; align-items: center; padding: 0 8px; gap: 6px;
        }
        .phone-dot { width:8px; height:8px; border-radius:50%; }

        /* Store badge buttons */
        .store-btn {
            display: inline-flex; align-items: center; gap: 12px;
            padding: 12px 22px; border-radius: 14px;
            transition: all .3s cubic-bezier(.4,0,.2,1);
            cursor: pointer; border: none; text-decoration: none;
            min-width: 190px;
        }
        .store-btn:hover { transform: translateY(-3px); box-shadow: 0 20px 40px rgba(0,0,0,.4); }
        .store-btn:active { transform: translateY(-1px); }
        .store-btn-android {
            background: linear-gradient(135deg, #1a7c4e, #22a06b);
            border: 1px solid rgba(255,255,255,.15);
        }
        .store-btn-ios {
            background: linear-gradient(135deg, #2563eb, #1d4ed8);
            border: 1px solid rgba(255,255,255,.15);
        }
        .store-btn-desktop {
            background: linear-gradient(135deg, #6d28d9, #7c3aed);
            border: 1px solid rgba(255,255,255,.15);
        }
        .store-btn-play {
            background: linear-gradient(135deg, #374151, #111827);
            border: 1px solid rgba(255,255,255,.12);
        }
        .store-btn-icon { font-size: 26px; color: #fff; flex-shrink: 0; }
        .store-btn-text { display: flex; flex-direction: column; text-align: left; }
        .store-btn-label { font-size: 10px; color: rgba(255,255,255,.7); letter-spacing:.04em; text-transform:uppercase; }
        .store-btn-name  { font-size: 15px; font-weight: 700; color: #fff; line-height: 1.2; }

        /* Ripple ring */
        .ripple-ring {
            position: absolute; border-radius: 50%;
            border: 1.5px solid rgba(51,135,175,.4);
            animation: ripple 3s ease-out infinite;
        }

        /* Stat chip */
        .stat-chip {
            background: rgba(255,255,255,.07);
            border: 1px solid rgba(255,255,255,.12);
            backdrop-filter: blur(10px);
            border-radius: 12px;
            padding: 12px 18px;
        }

        /* PWA Install Buttons - Hidden by default */
        #pwa-hero-download-btn,
        #pwa-install-btn-header {
            display: none !important;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        #pwa-hero-download-btn[style*="display: inline"],
        #pwa-install-btn-header[style*="display: inline"] {
            opacity: 1 !important;
        }
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
                    <button id="pwa-install-btn-header" onclick="window.appDownloadManager && window.appDownloadManager.handleInstallClick()" class="hidden sm:inline-flex items-center gap-2 bg-gradient-to-r from-emerald-500 to-emerald-600 text-white rounded-lg hover:shadow-lg transition-all px-4 py-2 text-sm font-semibold opacity-0 transition-opacity duration-300" style="display: none;">
                        <i class="fas fa-download"></i>
                        <span>Install App</span>
                    </button>
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
                <div class="animate-fade-in-up inline-flex items-center gap-2.5 bg-white/10 backdrop-blur-md border border-white/20 px-5 py-2.5 rounded-full mb-7 shadow-lg">
                    <span class="relative flex h-2.5 w-2.5">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-emerald-400"></span>
                    </span>
                    <span class="text-white/95 text-sm font-semibold tracking-wide">Admissions Now Open</span>
                </div>

                <!-- Headline -->
                <h1 class="animate-fade-in-up delay-100 text-4xl sm:text-5xl lg:text-6xl font-extrabold text-white leading-tight mb-4">
                    {{ $schoolSettings->school_name ?? 'Excellence in' }}
                    <span class="block" style="background:linear-gradient(90deg,#ffffff,#99c3d7,#66a5c3);-webkit-background-clip:text;background-clip:text;color:transparent;">
                        Islamic Education
                    </span>
                </h1>

                <!-- Subtext -->
                <p class="animate-fade-in-up delay-200 text-lg lg:text-xl text-white/75 leading-relaxed mb-8 max-w-2xl">
                    {{ $schoolSettings->school_mission ?? 'Nurturing hearts and minds through authentic Quranic and Arabic education in the tradition of the righteous scholars.' }}
                </p>

                <!-- CTA Buttons -->
                <div class="animate-fade-in-up delay-300 flex flex-col sm:flex-row gap-4">
                    <a href="{{ route('enrollment.token') }}" class="group inline-flex items-center justify-center gap-2.5 bg-white text-brand-600 font-bold px-8 py-4 rounded-2xl hover:shadow-2xl transition-all duration-300 hover:-translate-y-1 text-center" style="box-shadow:0 8px 32px rgba(255,255,255,.25);">
                        <i class="fas fa-user-plus group-hover:scale-110 transition-transform"></i>
                        <span>Start Enrollment</span>
                        <i class="fas fa-arrow-right text-xs opacity-60 group-hover:translate-x-1 transition-transform"></i>
                    </a>
                    <button id="pwa-hero-download-btn" onclick="window.appDownloadManager && window.appDownloadManager.handleInstallClick()" class="group inline-flex items-center justify-center gap-2.5 bg-gradient-to-r from-emerald-500 to-emerald-600 text-white font-bold px-8 py-4 rounded-2xl hover:shadow-2xl transition-all duration-300 hover:-translate-y-1 text-center opacity-0 transition-opacity" style="box-shadow:0 8px 32px rgba(52,211,153,.25); display: none;">
                        <i class="fas fa-download group-hover:scale-110 transition-transform"></i>
                        <span>Install App</span>
                    </button>
                    <a href="#programs" class="inline-flex items-center justify-center gap-2.5 bg-white/10 backdrop-blur-md border border-white/25 text-white font-semibold px-8 py-4 rounded-2xl hover:bg-white/20 transition-all duration-300 text-center">
                        <i class="fas fa-book-quran"></i>
                        <span>View Programs</span>
                    </a>
                </div>

                <!-- Trust Stats -->
                <div class="animate-fade-in-up delay-400 grid grid-cols-3 gap-4 mt-14 pt-8 border-t border-white/15">
                    <div class="text-center sm:text-left">
                        <div class="text-3xl lg:text-4xl font-extrabold text-white">20+</div>
                        <div class="text-xs text-white/55 mt-1 uppercase tracking-wider font-semibold">Years Teaching</div>
                    </div>
                    <div class="text-center sm:text-left">
                        <div class="text-3xl lg:text-4xl font-extrabold text-white">100+</div>
                        <div class="text-xs text-white/55 mt-1 uppercase tracking-wider font-semibold">Huffaz Produced</div>
                    </div>
                    <div class="text-center sm:text-left">
                        <div class="text-3xl lg:text-4xl font-extrabold text-white">30+</div>
                        <div class="text-xs text-white/55 mt-1 uppercase tracking-wider font-semibold">Qualified Scholars</div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Scroll Indicator -->
        <div class="absolute bottom-8 left-1/2 -translate-x-1/2 text-white/60 animate-bounce">
            <i class="fas fa-chevron-down text-2xl"></i>
        </div>
    </section>

    <!-- ═══ PWA App Download Section ═══ -->
    <section id="app-download-section" class="pwa-section py-20 lg:py-28">

        <!-- Glow orbs -->
        <div class="pwa-glow" style="width:500px;height:500px;background:rgba(51,135,175,.18);top:-120px;right:-80px;"></div>
        <div class="pwa-glow" style="width:400px;height:400px;background:rgba(11,77,115,.25);bottom:-100px;left:-60px;"></div>

        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">

            <!-- Header -->
            <div class="text-center mb-16">
                <div class="inline-flex items-center gap-2 bg-white/10 backdrop-blur border border-white/15 text-white/80 text-xs font-semibold uppercase tracking-widest px-4 py-2 rounded-full mb-5">
                    <span class="w-1.5 h-1.5 bg-emerald-400 rounded-full animate-pulse"></span>
                    Available Now
                </div>
                <h2 class="text-4xl lg:text-5xl font-extrabold text-white mb-4 leading-tight">
                    Take the School Portal
                    <span class="block text-transparent" style="background:linear-gradient(90deg,#66a5c3,#3387af,#99c3d7);-webkit-background-clip:text;background-clip:text;">Everywhere You Go</span>
                </h2>
                <p class="text-white/65 text-lg max-w-xl mx-auto">
                    Install our Progressive Web App on any device — works offline, loads instantly, and feels native.
                </p>
            </div>

            <!-- Main Content: Phone + Buttons -->
            <div class="flex flex-col lg:flex-row items-center gap-12 lg:gap-20">

                <!-- Phone Mockup -->
                <div class="flex-shrink-0 relative flex items-center justify-center">
                    <!-- Ripple rings -->
                    <div class="ripple-ring" style="width:260px;height:260px;top:50%;left:50%;margin:-130px 0 0 -130px;"></div>
                    <div class="ripple-ring" style="width:320px;height:320px;top:50%;left:50%;margin:-160px 0 0 -160px;animation-delay:1s;"></div>
                    <div class="ripple-ring" style="width:380px;height:380px;top:50%;left:50%;margin:-190px 0 0 -190px;animation-delay:2s;"></div>

                    <div class="phone-frame animate-float">
                        <div class="phone-notch"></div>
                        <div class="phone-screen">
                            <!-- fake app UI -->
                            <div style="display:flex;align-items:center;gap:6px;margin-bottom:4px;">
                                <div class="phone-dot" style="background:#3387af;"></div>
                                <div class="phone-bar accent" style="height:6px;flex:1;"></div>
                            </div>
                            <div class="phone-tile"><div class="phone-dot" style="background:#66a5c3;"></div><div class="phone-bar" style="width:70%;height:5px;"></div></div>
                            <div class="phone-tile"><div class="phone-dot" style="background:#22a06b;"></div><div class="phone-bar" style="width:55%;height:5px;"></div></div>
                            <div class="phone-tile"><div class="phone-dot" style="background:#f59e0b;"></div><div class="phone-bar" style="width:80%;height:5px;"></div></div>
                            <div style="height:8px;"></div>
                            <div style="background:rgba(51,135,175,.2);border-radius:10px;height:60px;display:flex;align-items:center;justify-content:center;">
                                <i class="fas fa-mosque" style="color:rgba(102,165,195,.7);font-size:22px;"></i>
                            </div>
                            <div class="phone-bar" style="width:90%;height:5px;margin-top:4px;"></div>
                            <div class="phone-bar" style="width:75%;height:5px;"></div>
                            <div class="phone-bar accent" style="height:5px;width:50%;"></div>
                        </div>
                    </div>

                    <!-- Floating badge -->
                    <div style="position:absolute;top:10px;right:-20px;background:linear-gradient(135deg,#22a06b,#16a34a);border-radius:50px;padding:7px 14px;display:flex;align-items:center;gap:6px;box-shadow:0 8px 24px rgba(34,160,107,.4);">
                        <i class="fas fa-check-circle" style="color:#fff;font-size:12px;"></i>
                        <span style="color:#fff;font-size:11px;font-weight:700;">Offline Ready</span>
                    </div>
                    <div style="position:absolute;bottom:30px;left:-28px;background:rgba(255,255,255,.1);backdrop-filter:blur(10px);border:1px solid rgba(255,255,255,.15);border-radius:50px;padding:7px 14px;display:flex;align-items:center;gap:6px;">
                        <i class="fas fa-bolt" style="color:#f59e0b;font-size:12px;"></i>
                        <span style="color:#fff;font-size:11px;font-weight:600;">Lightning Fast</span>
                    </div>
                </div>

                <!-- Right: Buttons + Features -->
                <div class="flex-1 w-full">

                    <!-- Platform buttons -->
                    <div id="platform-specific-content" class="mb-8">
                        <!-- Loaded by JS; default shown below as fallback -->
                        <div id="pwa-default-btns">
                            <p class="text-white/50 text-sm mb-5 font-medium uppercase tracking-widest">Install on your device</p>
                            <div class="flex flex-col sm:flex-row flex-wrap gap-4">
                                <button id="pwa-install-btn" onclick="appDownloadManager && appDownloadManager.installApp()" class="store-btn store-btn-android">
                                    <i class="fab fa-android store-btn-icon"></i>
                                    <span class="store-btn-text">
                                        <span class="store-btn-label">Install for</span>
                                        <span class="store-btn-name">Android</span>
                                    </span>
                                </button>
                                <button id="pwa-ios-btn" class="store-btn store-btn-ios">
                                    <i class="fab fa-apple store-btn-icon"></i>
                                    <span class="store-btn-text">
                                        <span class="store-btn-label">Add to</span>
                                        <span class="store-btn-name">iPhone / iPad</span>
                                    </span>
                                </button>
                                <button id="pwa-desktop-btn" onclick="appDownloadManager && appDownloadManager.installApp()" class="store-btn store-btn-desktop">
                                    <i class="fas fa-desktop store-btn-icon"></i>
                                    <span class="store-btn-text">
                                        <span class="store-btn-label">Install on</span>
                                        <span class="store-btn-name">Desktop / PC</span>
                                    </span>
                                </button>
                            </div>
                            <p class="text-white/35 text-xs mt-4"><i class="fas fa-info-circle mr-1"></i>iOS: tap <strong class="text-white/50">Share</strong> → <strong class="text-white/50">Add to Home Screen</strong></p>
                        </div>
                    </div>

                    <!-- Stats chips -->
                    <div class="grid grid-cols-3 gap-3 mb-8">
                        <div class="stat-chip text-center">
                            <div class="text-2xl font-bold text-white">0 MB</div>
                            <div class="text-white/50 text-xs mt-0.5">To Download</div>
                        </div>
                        <div class="stat-chip text-center">
                            <div class="text-2xl font-bold text-white">100%</div>
                            <div class="text-white/50 text-xs mt-0.5">Offline</div>
                        </div>
                        <div class="stat-chip text-center">
                            <div class="text-2xl font-bold text-white">Free</div>
                            <div class="text-white/50 text-xs mt-0.5">Always</div>
                        </div>
                    </div>

                    <!-- Feature pills -->
                    <div class="flex flex-wrap gap-2">
                        <span class="inline-flex items-center gap-1.5 bg-white/10 border border-white/10 text-white/70 text-xs font-medium px-3 py-1.5 rounded-full">
                            <i class="fas fa-wifi text-emerald-400"></i>Works Offline
                        </span>
                        <span class="inline-flex items-center gap-1.5 bg-white/10 border border-white/10 text-white/70 text-xs font-medium px-3 py-1.5 rounded-full">
                            <i class="fas fa-bell text-sky-400"></i>Push Notifications
                        </span>
                        <span class="inline-flex items-center gap-1.5 bg-white/10 border border-white/10 text-white/70 text-xs font-medium px-3 py-1.5 rounded-full">
                            <i class="fas fa-sync-alt text-violet-400"></i>Auto Sync
                        </span>
                        <span class="inline-flex items-center gap-1.5 bg-white/10 border border-white/10 text-white/70 text-xs font-medium px-3 py-1.5 rounded-full">
                            <i class="fas fa-lock text-amber-400"></i>Secure
                        </span>
                        <span class="inline-flex items-center gap-1.5 bg-white/10 border border-white/10 text-white/70 text-xs font-medium px-3 py-1.5 rounded-full">
                            <i class="fas fa-bolt text-rose-400"></i>Lightning Fast
                        </span>
                    </div>
                </div>
            </div>

            <!-- Dismiss -->
            <div class="text-center mt-12">
                <button onclick="document.getElementById('app-download-section').style.display='none'" class="text-white/25 hover:text-white/50 text-sm transition-colors">
                    <i class="fas fa-times mr-1"></i>Dismiss
                </button>
            </div>
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

    <!-- PWA Welcome Modal -->
    <style>
        #pwa-welcome-modal {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.5);
            z-index: 999;
            align-items: center;
            justify-content: center;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        #pwa-welcome-modal.show {
            opacity: 1;
        }

        #pwa-welcome-modal-content {
            background: white;
            border-radius: 20px;
            padding: 32px;
            max-width: 500px;
            width: 90%;
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.15);
            animation: slideUp 0.3s ease;
        }

        @keyframes slideUp {
            from {
                transform: translateY(40px);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }
    </style>

    <div id="pwa-welcome-modal">
        <div id="pwa-welcome-modal-content">
            <!-- Close button -->
            <button onclick="window.appDownloadManager && window.appDownloadManager.closeWelcomeModal()" class="float-right text-gray-400 hover:text-gray-600 text-2xl leading-none">
                <i class="fas fa-times"></i>
            </button>

            <!-- Icon -->
            <div class="text-center mb-6 mt-4">
                <div class="w-16 h-16 bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-2xl flex items-center justify-center mx-auto shadow-lg mb-4">
                    <i class="fas fa-mobile-alt text-white text-2xl"></i>
                </div>
            </div>

            <!-- Title -->
            <h3 class="text-2xl font-bold text-gray-900 text-center mb-2">
                Take {{ $schoolSettings->school_name ?? 'School' }} Anywhere
            </h3>

            <!-- Description -->
            <p class="text-gray-600 text-center mb-6">
                Install our app on your device for instant access, offline support, and a native app experience.
            </p>

            <!-- Features -->
            <div class="space-y-3 mb-8">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 bg-emerald-100 rounded-full flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-wifi text-emerald-600 text-sm"></i>
                    </div>
                    <span class="text-sm text-gray-700">Works offline</span>
                </div>
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-bolt text-blue-600 text-sm"></i>
                    </div>
                    <span class="text-sm text-gray-700">Lightning fast</span>
                </div>
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 bg-purple-100 rounded-full flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-bell text-purple-600 text-sm"></i>
                    </div>
                    <span class="text-sm text-gray-700">Push notifications</span>
                </div>
            </div>

            <!-- Buttons -->
            <div class="flex flex-col gap-3">
                <button onclick="window.appDownloadManager && window.appDownloadManager.installApp()" class="w-full bg-gradient-to-r from-emerald-500 to-emerald-600 text-white font-semibold py-3 px-4 rounded-xl hover:shadow-lg transition-all">
                    <i class="fas fa-download mr-2"></i>Install App
                </button>
                <button onclick="window.appDownloadManager && window.appDownloadManager.closeWelcomeModal()" class="w-full bg-gray-100 text-gray-700 font-semibold py-3 px-4 rounded-xl hover:bg-gray-200 transition-all">
                    Later
                </button>
            </div>

            <!-- Footer text -->
            <p class="text-xs text-gray-500 text-center mt-6">
                <i class="fas fa-info-circle mr-1"></i>
                You can always install the app later from the download section
            </p>
        </div>
    </div>

    <!-- App Download Manager -->
    <script src="{{ asset('js/app-download-manager.js') }}"></script>
</body>
</html>