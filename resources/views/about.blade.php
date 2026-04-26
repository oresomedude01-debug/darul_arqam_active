<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @php
        $schoolSettings = \App\Models\SchoolSetting::getInstance();
    @endphp
    <title>About Us - {{ $schoolSettings->school_name ?? 'Madrasah' }}</title>
    <meta name="description"
        content="Learn about {{ $schoolSettings->school_name ?? 'our Madrasah' }} - our mission, vision, and commitment to Islamic education.">

    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: { 50: '#e6f0f5', 100: '#cce1eb', 500: '#0B4D73', 600: '#093e5c', 700: '#072e45' },
                        warm: { 50: '#fdfbf7', 100: '#f9f5eb', 200: '#f0e6d3' }
                    }
                }
            }
        }
    </script>

    <style>
        * {
            font-family: 'Inter', system-ui, sans-serif;
        }

        html {
            scroll-behavior: smooth;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-fade-in-up {
            animation: fadeInUp 0.8s ease-out forwards;
        }

        .delay-100 {
            animation-delay: 0.1s;
            opacity: 0;
        }

        .delay-200 {
            animation-delay: 0.2s;
            opacity: 0;
        }

        .card-hover {
            transition: all 0.3s ease;
        }

        .card-hover:hover {
            transform: translateY(-6px);
            box-shadow: 0 20px 40px -15px rgba(11, 77, 115, 0.2);
        }

        .islamic-pattern {
            background-image: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M30 0L60 30L30 60L0 30z' fill='none' stroke='%23ffffff' stroke-width='0.5' opacity='0.1'/%3E%3C/svg%3E");
        }
    </style>
</head>

<body class="antialiased bg-warm-50 text-gray-800">

    <!-- Navigation -->
    <header class="fixed top-0 left-0 right-0 z-50 bg-white/95 backdrop-blur-md shadow-sm">
        <nav class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <a href="{{ url('/') }}" class="flex items-center gap-3">
                    @if($schoolSettings->school_logo)
                        <img src="{{ asset('storage/' . $schoolSettings->school_logo) }}"
                            alt="{{ $schoolSettings->school_name }}" class="h-10 w-10 rounded-lg object-cover">
                    @else
                        <div class="h-10 w-10 bg-brand-500 rounded-lg flex items-center justify-center">
                            <i class="fas fa-mosque text-white"></i>
                        </div>
                    @endif
                    <span
                        class="font-bold text-brand-500 text-lg hidden sm:block">{{ $schoolSettings->school_name ?? 'Madrasah' }}</span>
                </a>

                <div class="hidden md:flex items-center gap-8">
                    <a href="{{ url('/') }}" class="text-gray-600 hover:text-brand-500 text-sm font-medium">Home</a>
                    <a href="{{ route('about') }}" class="text-brand-500 text-sm font-medium">About</a>
                    <a href="{{ url('/#programs') }}"
                        class="text-gray-600 hover:text-brand-500 text-sm font-medium">Programs</a>
                    <a href="{{ route('gallery') }}"
                        class="text-gray-600 hover:text-brand-500 text-sm font-medium">Gallery</a>
                    <a href="{{ route('blog.index') }}"
                        class="text-gray-600 hover:text-brand-500 text-sm font-medium">Blog</a>
                    <a href="{{ url('/#contact') }}"
                        class="text-gray-600 hover:text-brand-500 text-sm font-medium">Contact</a>
                </div>

                <div class="flex items-center gap-3">
                    @guest
                        <a href="{{ route('enrollment.token') }}"
                            class="bg-brand-500 hover:bg-brand-600 text-white text-sm font-semibold px-5 py-2.5 rounded-lg transition-all">Enroll</a>
                    @else
                        <a href="{{ url('/dashboard') }}"
                            class="bg-brand-500 hover:bg-brand-600 text-white text-sm font-semibold px-5 py-2.5 rounded-lg transition-all">Dashboard</a>
                    @endguest
                </div>
            </div>
        </nav>
    </header>

    <!-- Hero Section -->
    <section class="bg-brand-500 pt-24 pb-16 lg:pt-32 lg:pb-24 relative overflow-hidden">
        <div class="absolute inset-0 islamic-pattern"></div>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="max-w-3xl">
                <nav class="text-sm mb-6">
                    <a href="{{ url('/') }}" class="text-white/70 hover:text-white">Home</a>
                    <span class="text-white/50 mx-2">/</span>
                    <span class="text-white">About Us</span>
                </nav>
                <h1 class="animate-fade-in-up text-4xl lg:text-5xl font-extrabold text-white mb-6">About Our Madrasah
                </h1>
                <p class="animate-fade-in-up delay-100 text-lg text-white/85">
                    {{ $schoolSettings->school_motto ?? 'Dedicated to nurturing scholars of the Quran and Sunnah' }}
                </p>
            </div>
        </div>
    </section>

    <!-- Introduction -->
    <section class="py-16 lg:py-24 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid lg:grid-cols-2 gap-16 items-center">
                <div>
                    <span class="text-brand-500 font-semibold text-sm uppercase tracking-wide">Who We Are</span>
                    <h2 class="text-3xl lg:text-4xl font-bold text-gray-900 mt-2 mb-6">
                        {{ $schoolSettings->school_name ?? 'Our Madrasah' }}</h2>
                    <div class="prose prose-lg text-gray-600">
                        <p class="mb-4">
                            {{ $schoolSettings->school_vision ?? 'We are a dedicated Islamic educational institution committed to nurturing the next generation of Muslim scholars and righteous individuals.' }}
                        </p>
                        <p class="mb-4">Our curriculum is rooted in the authentic teachings of the Quran and Sunnah,
                            following the understanding of the righteous predecessors (Salaf). We focus on developing
                            strong foundations in Quranic memorization (Hifz), Arabic language, and Islamic sciences.
                        </p>
                        <p>Every child who enters our doors is given the opportunity to develop their relationship with
                            Allah through knowledge, practice, and excellent character.</p>
                    </div>
                </div>
                <div class="relative">
                    <div class="bg-brand-50 rounded-3xl p-8 relative">
                        <img src="{{ asset('images/hero-students.png') }}" alt="Students Learning"
                            class="rounded-2xl w-full shadow-xl">
                    </div>
                    <div
                        class="absolute -bottom-6 -left-6 w-24 h-24 bg-brand-500 rounded-2xl flex items-center justify-center shadow-xl">
                        <i class="fas fa-quran text-white text-3xl"></i>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Mission & Vision -->
    <section class="py-16 lg:py-24 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-2xl mx-auto mb-16">
                <span class="text-brand-500 font-semibold text-sm uppercase tracking-wide">Our Purpose</span>
                <h2 class="text-3xl lg:text-4xl font-bold text-gray-900 mt-2">Mission & Vision</h2>
            </div>

            <div class="grid md:grid-cols-2 gap-8">
                <div class="card-hover bg-brand-500 text-white p-10 rounded-3xl">
                    <div class="w-16 h-16 bg-white/20 rounded-2xl flex items-center justify-center mb-6">
                        <i class="fas fa-bullseye text-3xl"></i>
                    </div>
                    <h3 class="text-2xl font-bold mb-4">Our Mission</h3>
                    <p class="text-white/90 leading-relaxed text-lg">
                        {{ $schoolSettings->school_mission ?? 'To provide authentic Islamic education rooted in the Quran and Sunnah, nurturing scholars who will benefit the Ummah and call to the path of Allah with wisdom.' }}
                    </p>
                </div>

                <div class="card-hover bg-white p-10 rounded-3xl shadow-lg border border-gray-100">
                    <div class="w-16 h-16 bg-brand-50 rounded-2xl flex items-center justify-center mb-6">
                        <i class="fas fa-eye text-brand-500 text-3xl"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-4">Our Vision</h3>
                    <p class="text-gray-600 leading-relaxed text-lg">
                        {{ $schoolSettings->school_vision ?? 'To be a leading institution in Islamic education, producing graduates who are Huffaz of the Quran, fluent in Arabic, and knowledgeable in Islamic sciences.' }}
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Our Values -->
    <section class="py-16 lg:py-24 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-2xl mx-auto mb-16">
                <span class="text-brand-500 font-semibold text-sm uppercase tracking-wide">What Guides Us</span>
                <h2 class="text-3xl lg:text-4xl font-bold text-gray-900 mt-2">Our Core Values</h2>
            </div>

            <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <div
                    class="card-hover text-center p-8 rounded-2xl bg-gradient-to-br from-brand-50 to-brand-100 border border-brand-200">
                    <div
                        class="w-16 h-16 bg-brand-500 rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-lg">
                        <i class="fas fa-quran text-white text-2xl"></i>
                    </div>
                    <h3 class="font-bold text-gray-900 mb-2">Quran-Centered</h3>
                    <p class="text-gray-600 text-sm">The Quran is at the heart of everything we do</p>
                </div>

                <div
                    class="card-hover text-center p-8 rounded-2xl bg-gradient-to-br from-green-50 to-green-100 border border-green-200">
                    <div
                        class="w-16 h-16 bg-green-600 rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-lg">
                        <i class="fas fa-hands-praying text-white text-2xl"></i>
                    </div>
                    <h3 class="font-bold text-gray-900 mb-2">Taqwa</h3>
                    <p class="text-gray-600 text-sm">Cultivating consciousness and fear of Allah</p>
                </div>

                <div
                    class="card-hover text-center p-8 rounded-2xl bg-gradient-to-br from-amber-50 to-amber-100 border border-amber-200">
                    <div
                        class="w-16 h-16 bg-amber-600 rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-lg">
                        <i class="fas fa-heart text-white text-2xl"></i>
                    </div>
                    <h3 class="font-bold text-gray-900 mb-2">Ihsan</h3>
                    <p class="text-gray-600 text-sm">Excellence in worship and character</p>
                </div>

                <div
                    class="card-hover text-center p-8 rounded-2xl bg-gradient-to-br from-purple-50 to-purple-100 border border-purple-200">
                    <div
                        class="w-16 h-16 bg-purple-600 rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-lg">
                        <i class="fas fa-graduation-cap text-white text-2xl"></i>
                    </div>
                    <h3 class="font-bold text-gray-900 mb-2">Ilm (Knowledge)</h3>
                    <p class="text-gray-600 text-sm">Seeking beneficial knowledge continuously</p>
                </div>
            </div>
        </div>
    </section>

    <!-- What We Teach -->
    <section class="py-16 lg:py-24 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-2xl mx-auto mb-16">
                <span class="text-brand-500 font-semibold text-sm uppercase tracking-wide">Our Curriculum</span>
                <h2 class="text-3xl lg:text-4xl font-bold text-gray-900 mt-2">What We Teach</h2>
                <p class="text-gray-600 mt-4">A comprehensive Islamic education covering all essential sciences</p>
            </div>

            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
                <div class="card-hover bg-white p-6 rounded-xl shadow-lg border border-gray-100">
                    <div class="flex items-center gap-4 mb-4">
                        <div class="w-12 h-12 bg-brand-500 rounded-xl flex items-center justify-center">
                            <i class="fas fa-quran text-white text-xl"></i>
                        </div>
                        <h3 class="font-bold text-gray-900">Quran & Tajweed</h3>
                    </div>
                    <p class="text-gray-600 text-sm">Complete Hifz program with proper tajweed and understanding of the
                        Noble Quran.</p>
                </div>

                <div class="card-hover bg-white p-6 rounded-xl shadow-lg border border-gray-100">
                    <div class="flex items-center gap-4 mb-4">
                        <div class="w-12 h-12 bg-green-600 rounded-xl flex items-center justify-center">
                            <i class="fas fa-language text-white text-xl"></i>
                        </div>
                        <h3 class="font-bold text-gray-900">Arabic Language</h3>
                    </div>
                    <p class="text-gray-600 text-sm">Classical Arabic including Nahw (grammar), Sarf (morphology), and
                        Balagha (rhetoric).</p>
                </div>

                <div class="card-hover bg-white p-6 rounded-xl shadow-lg border border-gray-100">
                    <div class="flex items-center gap-4 mb-4">
                        <div class="w-12 h-12 bg-purple-600 rounded-xl flex items-center justify-center">
                            <i class="fas fa-book text-white text-xl"></i>
                        </div>
                        <h3 class="font-bold text-gray-900">Hadith Sciences</h3>
                    </div>
                    <p class="text-gray-600 text-sm">Study of prophetic traditions and their chain of narration (Isnad).
                    </p>
                </div>

                <div class="card-hover bg-white p-6 rounded-xl shadow-lg border border-gray-100">
                    <div class="flex items-center gap-4 mb-4">
                        <div class="w-12 h-12 bg-amber-600 rounded-xl flex items-center justify-center">
                            <i class="fas fa-balance-scale text-white text-xl"></i>
                        </div>
                        <h3 class="font-bold text-gray-900">Fiqh (Jurisprudence)</h3>
                    </div>
                    <p class="text-gray-600 text-sm">Islamic law covering worship, transactions, and daily life
                        according to the Sunnah.</p>
                </div>

                <div class="card-hover bg-white p-6 rounded-xl shadow-lg border border-gray-100">
                    <div class="flex items-center gap-4 mb-4">
                        <div class="w-12 h-12 bg-rose-600 rounded-xl flex items-center justify-center">
                            <i class="fas fa-star-and-crescent text-white text-xl"></i>
                        </div>
                        <h3 class="font-bold text-gray-900">Aqeedah (Creed)</h3>
                    </div>
                    <p class="text-gray-600 text-sm">Correct Islamic belief based on the Quran and Sunnah.</p>
                </div>

                <div class="card-hover bg-white p-6 rounded-xl shadow-lg border border-gray-100">
                    <div class="flex items-center gap-4 mb-4">
                        <div class="w-12 h-12 bg-teal-600 rounded-xl flex items-center justify-center">
                            <i class="fas fa-scroll text-white text-xl"></i>
                        </div>
                        <h3 class="font-bold text-gray-900">Seerah & History</h3>
                    </div>
                    <p class="text-gray-600 text-sm">Life of Prophet Muhammad ﷺ and Islamic history.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA -->
    <section class="py-16 lg:py-20 bg-brand-500 relative overflow-hidden">
        <div class="absolute inset-0 islamic-pattern"></div>
        <div class="max-w-4xl mx-auto px-4 text-center relative z-10">
            <h2 class="text-3xl lg:text-4xl font-bold text-white mb-6">Ready to Begin the Journey?</h2>
            <p class="text-xl text-white/85 mb-8">Give your child the gift of Islamic knowledge that will benefit them
                in this world and the hereafter.</p>
            <a href="{{ route('enrollment.token') }}"
                class="inline-flex items-center gap-2 bg-white text-brand-500 font-bold px-8 py-4 rounded-xl hover:shadow-xl transition-all">
                <i class="fas fa-user-plus"></i>
                <span>Enroll Your Child</span>
            </a>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid md:grid-cols-4 gap-8 mb-8">
                <div>
                    <div class="flex items-center gap-3 mb-4">
                        @if($schoolSettings->school_logo)
                            <img src="{{ asset('storage/' . $schoolSettings->school_logo) }}" alt=""
                                class="h-10 w-10 rounded-lg object-cover">
                        @else
                            <div class="h-10 w-10 bg-brand-500 rounded-lg flex items-center justify-center">
                                <i class="fas fa-mosque text-white"></i>
                            </div>
                        @endif
                        <span class="font-bold">{{ $schoolSettings->school_name ?? 'Madrasah' }}</span>
                    </div>
                    <p class="text-gray-400 text-sm">
                        {{ $schoolSettings->school_motto ?? 'Excellence in Islamic Education' }}</p>
                </div>
                <div>
                    <h4 class="font-bold mb-4">Quick Links</h4>
                    <ul class="space-y-2 text-gray-400 text-sm">
                        <li><a href="{{ url('/') }}" class="hover:text-white">Home</a></li>
                        <li><a href="{{ route('about') }}" class="hover:text-white">About</a></li>
                        <li><a href="{{ route('gallery') }}" class="hover:text-white">Gallery</a></li>
                        <li><a href="{{ route('blog.index') }}" class="hover:text-white">Blog</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-bold mb-4">Contact</h4>
                    <ul class="space-y-2 text-gray-400 text-sm">
                        <li>{{ $schoolSettings->school_phone ?? 'Phone' }}</li>
                        <li>{{ $schoolSettings->school_email ?? 'Email' }}</li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-bold mb-4">Follow Us</h4>
                    <div class="flex gap-3">
                        <a href="#"
                            class="w-10 h-10 bg-gray-800 rounded-lg flex items-center justify-center hover:bg-brand-500"><i
                                class="fab fa-facebook-f"></i></a>
                        <a href="#"
                            class="w-10 h-10 bg-gray-800 rounded-lg flex items-center justify-center hover:bg-brand-500"><i
                                class="fab fa-instagram"></i></a>
                    </div>
                </div>
            </div>
            <div class="border-t border-gray-800 pt-8 text-center text-gray-500 text-sm">
                <p>&copy; {{ date('Y') }} {{ $schoolSettings->school_name ?? 'Madrasah' }}. All rights reserved.</p>
            </div>
        </div>
    </footer>
</body>

</html>
