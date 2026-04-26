@extends('layouts.landing')

@section('title', 'Darul Arqam School - Excellence in Islamic Education')

@section('content')

<!-- Hero Section -->
<section class="relative hero-pattern min-h-screen flex items-center overflow-hidden">
    <!-- Animated Background Elements -->
    <div class="absolute inset-0 overflow-hidden">
        <div class="absolute top-20 left-10 w-72 h-72 bg-white/10 rounded-full blur-3xl animate-float"></div>
        <div class="absolute bottom-20 right-10 w-96 h-96 bg-white/10 rounded-full blur-3xl animate-float" style="animation-delay: 1s"></div>
    </div>

    <div class="container mx-auto px-4 relative z-10">
        <div class="grid md:grid-cols-2 gap-12 items-center">
            <!-- Text Content -->
            <div class="text-white space-y-6 animate-fade-in-up">
                <div class="inline-block">
                    <span class="bg-white/20 backdrop-blur-sm px-4 py-2 rounded-full text-sm font-semibold">
                        <i class="fas fa-star text-yellow-300 mr-2"></i>Rated #1 Islamic School
                    </span>
                </div>
                <h1 class="text-5xl md:text-6xl lg:text-7xl font-bold leading-tight">
                    Nurturing Young
                    <span class="block text-yellow-300">Minds & Hearts</span>
                </h1>
                <p class="text-xl md:text-2xl text-white/90 leading-relaxed">
                    Excellence in Islamic education combined with modern teaching methods for a brighter future.
                </p>
                <div class="flex flex-wrap gap-4 pt-4">
                    <a href="{{ route('enrollment.token') }}" class="group bg-white text-primary-700 px-8 py-4 rounded-full font-bold text-lg hover:shadow-2xl transition-all duration-300 hover:scale-105 flex items-center">
                        <span>Enroll Your Child</span>
                        <i class="fas fa-arrow-right ml-2 group-hover:translate-x-2 transition-transform"></i>
                    </a>
                    <a href="#about" class="glass text-white px-8 py-4 rounded-full font-bold text-lg hover:bg-white/20 transition-all duration-300 flex items-center">
                        <i class="fas fa-play-circle mr-2"></i>
                        <span>Learn More</span>
                    </a>
                </div>

                <!-- Stats -->
                <div class="grid grid-cols-3 gap-6 pt-8">
                    <div class="text-center">
                        <div class="text-4xl font-bold text-yellow-300">20+</div>
                        <div class="text-sm text-white/80 mt-1">Years Experience</div>
                    </div>
                    <div class="text-center">
                        <div class="text-4xl font-bold text-yellow-300">2K+</div>
                        <div class="text-sm text-white/80 mt-1">Students</div>
                    </div>
                    <div class="text-center">
                        <div class="text-4xl font-bold text-yellow-300">98%</div>
                        <div class="text-sm text-white/80 mt-1">Success Rate</div>
                    </div>
                </div>
            </div>

            <!-- Image/Illustration -->
            <div class="relative animate-slide-in-right hidden md:block">
                <div class="relative z-10">
                    <!-- SVG Illustration -->
                    <svg viewBox="0 0 500 500" class="w-full animate-float">
                        <!-- Students reading -->
                        <circle cx="250" cy="250" r="200" fill="#ffffff" opacity="0.1"/>
                        <circle cx="250" cy="250" r="150" fill="#ffffff" opacity="0.1"/>

                        <!-- Book -->
                        <path d="M200 200 L300 200 L300 320 L200 320 Z" fill="#fbbf24" opacity="0.9"/>
                        <path d="M250 200 L250 320" stroke="#ffffff" stroke-width="2"/>
                        <circle cx="230" cy="240" r="3" fill="#ffffff"/>
                        <circle cx="270" cy="240" r="3" fill="#ffffff"/>
                        <path d="M220 260 Q250 275 280 260" stroke="#ffffff" stroke-width="2" fill="none"/>

                        <!-- Graduation cap -->
                        <path d="M250 150 L200 170 L250 190 L300 170 Z" fill="#0ea5e9"/>
                        <rect x="245" y="150" width="10" height="60" fill="#0ea5e9"/>
                        <circle cx="250" cy="145" r="8" fill="#fbbf24"/>
                    </svg>
                </div>

                <!-- Floating Elements -->
                <div class="absolute top-10 -right-10 w-20 h-20 bg-yellow-300 rounded-full flex items-center justify-center animate-bounce-slow">
                    <i class="fas fa-star text-white text-2xl"></i>
                </div>
                <div class="absolute bottom-10 -left-10 w-16 h-16 bg-white rounded-full flex items-center justify-center animate-pulse-slow">
                    <i class="fas fa-book text-primary-600 text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Scroll Indicator -->
    <div class="absolute bottom-10 left-1/2 transform -translate-x-1/2 text-white text-center animate-bounce">
        <div class="text-sm mb-2">Scroll Down</div>
        <i class="fas fa-chevron-down text-2xl"></i>
    </div>
</section>

<!-- About Section -->
<section id="about" class="py-20 bg-gray-50">
    <div class="container mx-auto px-4">
        <div class="text-center mb-16 section-enter">
            <span class="text-primary-600 font-semibold text-lg">Who We Are</span>
            <h2 class="text-4xl md:text-5xl font-bold text-gray-900 mt-2 mb-4">About Darul Arqam School</h2>
            <p class="text-xl text-gray-600 max-w-3xl mx-auto">Building a generation of knowledgeable, confident, and compassionate leaders</p>
        </div>

        <div class="grid md:grid-cols-2 gap-12 items-center">
            <div class="space-y-6 section-enter">
                <div class="prose prose-lg">
                    <p class="text-gray-700 leading-relaxed">
                        For over <strong>20 years</strong>, Darul Arqam School has been at the forefront of Islamic education,
                        combining traditional values with modern teaching methodologies to create an unparalleled learning experience.
                    </p>
                    <p class="text-gray-700 leading-relaxed">
                        Our holistic approach focuses on developing well-rounded students who excel academically, spiritually,
                        and socially, preparing them to be future leaders in their communities.
                    </p>
                </div>

                <div class="grid grid-cols-2 gap-6 pt-4">
                    <div class="bg-white p-6 rounded-xl shadow-lg hover-lift">
                        <div class="w-12 h-12 bg-primary-100 rounded-lg flex items-center justify-center mb-4">
                            <i class="fas fa-quran text-primary-600 text-2xl"></i>
                        </div>
                        <h3 class="font-bold text-gray-900 mb-2">Islamic Values</h3>
                        <p class="text-gray-600 text-sm">Rooted in authentic Islamic teachings</p>
                    </div>
                    <div class="bg-white p-6 rounded-xl shadow-lg hover-lift">
                        <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center mb-4">
                            <i class="fas fa-graduation-cap text-green-600 text-2xl"></i>
                        </div>
                        <h3 class="font-bold text-gray-900 mb-2">Modern Education</h3>
                        <p class="text-gray-600 text-sm">Contemporary teaching methods</p>
                    </div>
                    <div class="bg-white p-6 rounded-xl shadow-lg hover-lift">
                        <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center mb-4">
                            <i class="fas fa-users text-yellow-600 text-2xl"></i>
                        </div>
                        <h3 class="font-bold text-gray-900 mb-2">Expert Teachers</h3>
                        <p class="text-gray-600 text-sm">Qualified and dedicated educators</p>
                    </div>
                    <div class="bg-white p-6 rounded-xl shadow-lg hover-lift">
                        <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center mb-4">
                            <i class="fas fa-building text-purple-600 text-2xl"></i>
                        </div>
                        <h3 class="font-bold text-gray-900 mb-2">Modern Facilities</h3>
                        <p class="text-gray-600 text-sm">State-of-the-art infrastructure</p>
                    </div>
                </div>
            </div>

            <div class="relative section-enter">
                <!-- Mission & Vision Cards -->
                <div class="space-y-6">
                    <div class="bg-gradient-primary text-white p-8 rounded-2xl shadow-xl">
                        <div class="flex items-center mb-4">
                            <div class="w-12 h-12 bg-white/20 rounded-lg flex items-center justify-center mr-4">
                                <i class="fas fa-bullseye text-2xl"></i>
                            </div>
                            <h3 class="text-2xl font-bold">Our Mission</h3>
                        </div>
                        <p class="leading-relaxed">
                            To provide excellence in Islamic education while nurturing intellectual curiosity,
                            moral character, and spiritual growth in every student.
                        </p>
                    </div>

                    <div class="bg-white p-8 rounded-2xl shadow-xl border-2 border-primary-100">
                        <div class="flex items-center mb-4">
                            <div class="w-12 h-12 bg-primary-100 rounded-lg flex items-center justify-center mr-4">
                                <i class="fas fa-eye text-primary-600 text-2xl"></i>
                            </div>
                            <h3 class="text-2xl font-bold text-gray-900">Our Vision</h3>
                        </div>
                        <p class="text-gray-700 leading-relaxed">
                            To be the leading institution in Islamic education, producing graduates who are
                            confident, knowledgeable, and positively contribute to society.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Features Section -->
<section class="py-20 bg-white">
    <div class="container mx-auto px-4">
        <div class="text-center mb-16 section-enter">
            <span class="text-primary-600 font-semibold text-lg">Why Choose Us</span>
            <h2 class="text-4xl md:text-5xl font-bold text-gray-900 mt-2 mb-4">Outstanding Features</h2>
            <p class="text-xl text-gray-600 max-w-3xl mx-auto">Discover what makes Darul Arqam School the best choice for your child's education</p>
        </div>

        <div class="grid md:grid-cols-3 gap-8">
            <div class="bg-gradient-to-br from-blue-50 to-blue-100 p-8 rounded-2xl hover-lift section-enter">
                <div class="w-16 h-16 bg-gradient-primary rounded-xl flex items-center justify-center mb-6 rotate-12 hover:rotate-0 transition-transform duration-300">
                    <i class="fas fa-book-open text-white text-3xl"></i>
                </div>
                <h3 class="text-2xl font-bold text-gray-900 mb-4">Comprehensive Curriculum</h3>
                <p class="text-gray-700 leading-relaxed">
                    Balanced integration of Islamic studies, Arabic, and modern academic subjects aligned with national standards.
                </p>
            </div>

            <div class="bg-gradient-to-br from-green-50 to-green-100 p-8 rounded-2xl hover-lift section-enter" style="animation-delay: 0.1s">
                <div class="w-16 h-16 bg-green-600 rounded-xl flex items-center justify-center mb-6 rotate-12 hover:rotate-0 transition-transform duration-300">
                    <i class="fas fa-user-graduate text-white text-3xl"></i>
                </div>
                <h3 class="text-2xl font-bold text-gray-900 mb-4">Qualified Teachers</h3>
                <p class="text-gray-700 leading-relaxed">
                    Our educators are highly qualified, certified, and passionate about nurturing young minds.
                </p>
            </div>

            <div class="bg-gradient-to-br from-purple-50 to-purple-100 p-8 rounded-2xl hover-lift section-enter" style="animation-delay: 0.2s">
                <div class="w-16 h-16 bg-purple-600 rounded-xl flex items-center justify-center mb-6 rotate-12 hover:rotate-0 transition-transform duration-300">
                    <i class="fas fa-laptop-code text-white text-3xl"></i>
                </div>
                <h3 class="text-2xl font-bold text-gray-900 mb-4">Modern Technology</h3>
                <p class="text-gray-700 leading-relaxed">
                    Smart classrooms, computer labs, and digital learning tools for enhanced education experience.
                </p>
            </div>

            <div class="bg-gradient-to-br from-yellow-50 to-yellow-100 p-8 rounded-2xl hover-lift section-enter" style="animation-delay: 0.3s">
                <div class="w-16 h-16 bg-yellow-600 rounded-xl flex items-center justify-center mb-6 rotate-12 hover:rotate-0 transition-transform duration-300">
                    <i class="fas fa-mosque text-white text-3xl"></i>
                </div>
                <h3 class="text-2xl font-bold text-gray-900 mb-4">Islamic Environment</h3>
                <p class="text-gray-700 leading-relaxed">
                    Prayer facilities, Quranic memorization programs, and an atmosphere of Islamic brotherhood.
                </p>
            </div>

            <div class="bg-gradient-to-br from-red-50 to-red-100 p-8 rounded-2xl hover-lift section-enter" style="animation-delay: 0.4s">
                <div class="w-16 h-16 bg-red-600 rounded-xl flex items-center justify-center mb-6 rotate-12 hover:rotate-0 transition-transform duration-300">
                    <i class="fas fa-shield-alt text-white text-3xl"></i>
                </div>
                <h3 class="text-2xl font-bold text-gray-900 mb-4">Safe & Secure</h3>
                <p class="text-gray-700 leading-relaxed">
                    24/7 security, CCTV monitoring, and a safe environment for your child's well-being.
                </p>
            </div>

            <div class="bg-gradient-to-br from-indigo-50 to-indigo-100 p-8 rounded-2xl hover-lift section-enter" style="animation-delay: 0.5s">
                <div class="w-16 h-16 bg-indigo-600 rounded-xl flex items-center justify-center mb-6 rotate-12 hover:rotate-0 transition-transform duration-300">
                    <i class="fas fa-futbol text-white text-3xl"></i>
                </div>
                <h3 class="text-2xl font-bold text-gray-900 mb-4">Sports & Activities</h3>
                <p class="text-gray-700 leading-relaxed">
                    Diverse extracurricular activities including sports, arts, and clubs for holistic development.
                </p>
            </div>
        </div>
    </div>
</section>

<!-- Programs Section -->
<section id="programs" class="py-20 bg-gray-50">
    <div class="container mx-auto px-4">
        <div class="text-center mb-16 section-enter">
            <span class="text-primary-600 font-semibold text-lg">Our Programs</span>
            <h2 class="text-4xl md:text-5xl font-bold text-gray-900 mt-2 mb-4">Academic Programs</h2>
            <p class="text-xl text-gray-600 max-w-3xl mx-auto">From nursery to secondary school, we offer comprehensive programs for every stage</p>
        </div>

        <div class="grid md:grid-cols-3 gap-8">
            <!-- Nursery -->
            <div class="bg-white rounded-2xl overflow-hidden shadow-lg hover-lift section-enter">
                <div class="h-48 bg-gradient-to-br from-pink-400 to-pink-600 flex items-center justify-center">
                    <i class="fas fa-baby text-white text-6xl"></i>
                </div>
                <div class="p-8">
                    <h3 class="text-2xl font-bold text-gray-900 mb-3">Nursery (Age 3-5)</h3>
                    <p class="text-gray-600 mb-4">Foundation years with play-based learning, early Quranic education, and social development.</p>
                    <ul class="space-y-2 text-gray-700 mb-6">
                        <li><i class="fas fa-check text-green-500 mr-2"></i>Play-based Learning</li>
                        <li><i class="fas fa-check text-green-500 mr-2"></i>Early Quran Introduction</li>
                        <li><i class="fas fa-check text-green-500 mr-2"></i>Social Skills Development</li>
                    </ul>
                    <a href="{{ route('enrollment.token') }}" class="block text-center bg-gradient-primary text-white py-3 rounded-lg font-semibold hover:shadow-lg transition-all">
                        Learn More
                    </a>
                </div>
            </div>

            <!-- Primary -->
            <div class="bg-white rounded-2xl overflow-hidden shadow-lg hover-lift section-enter" style="animation-delay: 0.1s">
                <div class="h-48 bg-gradient-to-br from-blue-400 to-blue-600 flex items-center justify-center">
                    <i class="fas fa-child text-white text-6xl"></i>
                </div>
                <div class="p-8">
                    <h3 class="text-2xl font-bold text-gray-900 mb-3">Primary (Grade 1-6)</h3>
                    <p class="text-gray-600 mb-4">Core academic subjects with Islamic studies, Arabic, and character building programs.</p>
                    <ul class="space-y-2 text-gray-700 mb-6">
                        <li><i class="fas fa-check text-green-500 mr-2"></i>National Curriculum</li>
                        <li><i class="fas fa-check text-green-500 mr-2"></i>Quran Memorization</li>
                        <li><i class="fas fa-check text-green-500 mr-2"></i>Arabic Language</li>
                    </ul>
                    <a href="{{ route('enrollment.token') }}" class="block text-center bg-gradient-primary text-white py-3 rounded-lg font-semibold hover:shadow-lg transition-all">
                        Learn More
                    </a>
                </div>
            </div>

            <!-- Secondary -->
            <div class="bg-white rounded-2xl overflow-hidden shadow-lg hover-lift section-enter" style="animation-delay: 0.2s">
                <div class="h-48 bg-gradient-to-br from-purple-400 to-purple-600 flex items-center justify-center">
                    <i class="fas fa-user-graduate text-white text-6xl"></i>
                </div>
                <div class="p-8">
                    <h3 class="text-2xl font-bold text-gray-900 mb-3">Secondary (JSS & SSS)</h3>
                    <p class="text-gray-600 mb-4">Advanced academics with exam preparation, leadership training, and career guidance.</p>
                    <ul class="space-y-2 text-gray-700 mb-6">
                        <li><i class="fas fa-check text-green-500 mr-2"></i>WAEC/JAMB Prep</li>
                        <li><i class="fas fa-check text-green-500 mr-2"></i>Leadership Programs</li>
                        <li><i class="fas fa-check text-green-500 mr-2"></i>Career Counseling</li>
                    </ul>
                    <a href="{{ route('enrollment.token') }}" class="block text-center bg-gradient-primary text-white py-3 rounded-lg font-semibold hover:shadow-lg transition-all">
                        Learn More
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Testimonials Section -->
<section class="py-20 bg-white">
    <div class="container mx-auto px-4">
        <div class="text-center mb-16 section-enter">
            <span class="text-primary-600 font-semibold text-lg">Testimonials</span>
            <h2 class="text-4xl md:text-5xl font-bold text-gray-900 mt-2 mb-4">What Parents Say</h2>
            <p class="text-xl text-gray-600 max-w-3xl mx-auto">Hear from our satisfied parents and students</p>
        </div>

        <div class="grid md:grid-cols-3 gap-8">
            <div class="bg-gray-50 p-8 rounded-2xl section-enter">
                <div class="flex items-center mb-4">
                    <div class="w-16 h-16 bg-gradient-primary rounded-full mr-4"></div>
                    <div>
                        <h4 class="font-bold text-gray-900">Amina Abdullah</h4>
                        <p class="text-sm text-gray-600">Parent</p>
                    </div>
                </div>
                <div class="text-yellow-400 mb-3">
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                </div>
                <p class="text-gray-700 italic leading-relaxed">
                    "The best decision we made was enrolling our children here. The combination of Islamic values and modern education is exactly what we were looking for."
                </p>
            </div>

            <div class="bg-gray-50 p-8 rounded-2xl section-enter" style="animation-delay: 0.1s">
                <div class="flex items-center mb-4">
                    <div class="w-16 h-16 bg-gradient-primary rounded-full mr-4"></div>
                    <div>
                        <h4 class="font-bold text-gray-900">Ibrahim Musa</h4>
                        <p class="text-sm text-gray-600">Parent</p>
                    </div>
                </div>
                <div class="text-yellow-400 mb-3">
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                </div>
                <p class="text-gray-700 italic leading-relaxed">
                    "Outstanding teachers who genuinely care about each student's success. My son's academic and spiritual growth has been remarkable."
                </p>
            </div>

            <div class="bg-gray-50 p-8 rounded-2xl section-enter" style="animation-delay: 0.2s">
                <div class="flex items-center mb-4">
                    <div class="w-16 h-16 bg-gradient-primary rounded-full mr-4"></div>
                    <div>
                        <h4 class="font-bold text-gray-900">Fatima Hassan</h4>
                        <p class="text-sm text-gray-600">Parent</p>
                    </div>
                </div>
                <div class="text-yellow-400 mb-3">
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                </div>
                <p class="text-gray-700 italic leading-relaxed">
                    "Safe environment, excellent facilities, and a curriculum that prepares students for both this life and the hereafter. Highly recommended!"
                </p>
            </div>
        </div>
    </div>
</section>

<!-- Gallery Section -->
<section id="gallery" class="py-20 bg-gray-50">
    <div class="container mx-auto px-4">
        <div class="text-center mb-16 section-enter">
            <span class="text-primary-600 font-semibold text-lg">Gallery</span>
            <h2 class="text-4xl md:text-5xl font-bold text-gray-900 mt-2 mb-4">Campus Life</h2>
            <p class="text-xl text-gray-600 max-w-3xl mx-auto">A glimpse into our vibrant school community</p>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="aspect-square bg-gradient-to-br from-blue-200 to-blue-400 rounded-xl hover-lift section-enter flex items-center justify-center">
                <i class="fas fa-image text-white text-4xl"></i>
            </div>
            <div class="aspect-square bg-gradient-to-br from-green-200 to-green-400 rounded-xl hover-lift section-enter flex items-center justify-center">
                <i class="fas fa-image text-white text-4xl"></i>
            </div>
            <div class="aspect-square bg-gradient-to-br from-purple-200 to-purple-400 rounded-xl hover-lift section-enter flex items-center justify-center">
                <i class="fas fa-image text-white text-4xl"></i>
            </div>
            <div class="aspect-square bg-gradient-to-br from-yellow-200 to-yellow-400 rounded-xl hover-lift section-enter flex items-center justify-center">
                <i class="fas fa-image text-white text-4xl"></i>
            </div>
            <div class="aspect-square bg-gradient-to-br from-red-200 to-red-400 rounded-xl hover-lift section-enter flex items-center justify-center">
                <i class="fas fa-image text-white text-4xl"></i>
            </div>
            <div class="aspect-square bg-gradient-to-br from-indigo-200 to-indigo-400 rounded-xl hover-lift section-enter flex items-center justify-center">
                <i class="fas fa-image text-white text-4xl"></i>
            </div>
            <div class="aspect-square bg-gradient-to-br from-pink-200 to-pink-400 rounded-xl hover-lift section-enter flex items-center justify-center">
                <i class="fas fa-image text-white text-4xl"></i>
            </div>
            <div class="aspect-square bg-gradient-to-br from-teal-200 to-teal-400 rounded-xl hover-lift section-enter flex items-center justify-center">
                <i class="fas fa-image text-white text-4xl"></i>
            </div>
        </div>
    </div>
</section>

<!-- Contact Section -->
<section id="contact" class="py-20 bg-white">
    <div class="container mx-auto px-4">
        <div class="text-center mb-16 section-enter">
            <span class="text-primary-600 font-semibold text-lg">Get In Touch</span>
            <h2 class="text-4xl md:text-5xl font-bold text-gray-900 mt-2 mb-4">Contact Us</h2>
            <p class="text-xl text-gray-600 max-w-3xl mx-auto">Have questions? We'd love to hear from you</p>
        </div>

        <div class="grid md:grid-cols-2 gap-12 max-w-6xl mx-auto">
            <!-- Contact Form -->
            <div class="section-enter">
                <form action="#" method="POST" class="space-y-6">
                    @csrf
                    <div>
                        <label class="block text-gray-700 font-semibold mb-2">Full Name</label>
                        <input type="text" name="name" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent" required>
                    </div>
                    <div class="grid md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-gray-700 font-semibold mb-2">Email</label>
                            <input type="email" name="email" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent" required>
                        </div>
                        <div>
                            <label class="block text-gray-700 font-semibold mb-2">Phone</label>
                            <input type="tel" name="phone" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                        </div>
                    </div>
                    <div>
                        <label class="block text-gray-700 font-semibold mb-2">Subject</label>
                        <input type="text" name="subject" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent" required>
                    </div>
                    <div>
                        <label class="block text-gray-700 font-semibold mb-2">Message</label>
                        <textarea name="message" rows="5" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent" required></textarea>
                    </div>
                    <button type="submit" class="w-full bg-gradient-primary text-white py-4 rounded-lg font-bold text-lg hover:shadow-xl transition-all duration-300 hover:scale-105">
                        <i class="fas fa-paper-plane mr-2"></i>Send Message
                    </button>
                </form>
            </div>

            <!-- Contact Info -->
            <div class="space-y-8 section-enter" style="animation-delay: 0.2s">
                <div class="bg-gradient-primary text-white p-8 rounded-2xl">
                    <h3 class="text-2xl font-bold mb-6">Visit Our Campus</h3>
                    <div class="space-y-4">
                        <div class="flex items-start">
                            <i class="fas fa-map-marker-alt text-2xl mr-4 mt-1"></i>
                            <div>
                                <h4 class="font-semibold mb-1">Address</h4>
                                <p>123 Education Street<br>Lagos, Nigeria</p>
                            </div>
                        </div>
                        <div class="flex items-start">
                            <i class="fas fa-phone text-2xl mr-4 mt-1"></i>
                            <div>
                                <h4 class="font-semibold mb-1">Phone</h4>
                                <p>+234 XXX XXX XXXX</p>
                            </div>
                        </div>
                        <div class="flex items-start">
                            <i class="fas fa-envelope text-2xl mr-4 mt-1"></i>
                            <div>
                                <h4 class="font-semibold mb-1">Email</h4>
                                <p>info@darularqam.edu</p>
                            </div>
                        </div>
                        <div class="flex items-start">
                            <i class="fas fa-clock text-2xl mr-4 mt-1"></i>
                            <div>
                                <h4 class="font-semibold mb-1">Office Hours</h4>
                                <p>Mon - Fri: 8:00 AM - 4:00 PM<br>Sat: 9:00 AM - 2:00 PM</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-gray-50 p-8 rounded-2xl">
                    <h3 class="text-2xl font-bold text-gray-900 mb-4">Schedule a Visit</h3>
                    <p class="text-gray-700 mb-4">We'd love to show you around our campus. Schedule a visit to see our facilities and meet our staff.</p>
                    <a href="{{ route('enrollment.token') }}" class="inline-block bg-gradient-primary text-white px-6 py-3 rounded-lg font-semibold hover:shadow-lg transition-all">
                        Book a Tour
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="py-20 gradient-bg text-white">
    <div class="container mx-auto px-4 text-center">
        <div class="max-w-3xl mx-auto section-enter">
            <h2 class="text-4xl md:text-5xl font-bold mb-6">Ready to Join Our School Family?</h2>
            <p class="text-xl mb-8 text-white/90">Take the first step towards your child's bright future. Enroll today!</p>
            <div class="flex flex-wrap gap-4 justify-center">
                <a href="{{ route('enrollment.token') }}" class="bg-white text-primary-700 px-8 py-4 rounded-full font-bold text-lg hover:shadow-2xl transition-all duration-300 hover:scale-105">
                    <i class="fas fa-edit mr-2"></i>Start Enrollment
                </a>
                <a href="#contact" class="glass text-white px-8 py-4 rounded-full font-bold text-lg hover:bg-white/20 transition-all duration-300">
                    <i class="fas fa-phone mr-2"></i>Contact Us
                </a>
            </div>
        </div>
    </div>
</section>

@endsection
