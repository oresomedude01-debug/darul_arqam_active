<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @php
        $schoolSettings = \App\Models\SchoolSetting::getInstance();
    @endphp
    <title>Gallery - {{ $schoolSettings->school_name ?? 'Madrasah' }}</title>
    <meta name="description"
        content="View photos and memories from {{ $schoolSettings->school_name ?? 'our Madrasah' }}">

    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: { 50: '#e6f0f5', 100: '#cce1eb', 500: '#0B4D73', 600: '#093e5c' },
                        warm: { 50: '#fdfbf7', 100: '#f9f5eb' }
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
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-fade-in-up {
            animation: fadeInUp 0.6s ease-out forwards;
        }

        .gallery-item {
            transition: all 0.4s ease;
        }

        .gallery-item:hover {
            transform: scale(1.02);
        }

        .gallery-item:hover img {
            transform: scale(1.1);
        }

        .lightbox {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.95);
            z-index: 100;
        }

        .lightbox.active {
            display: flex;
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
                    <a href="{{ route('about') }}"
                        class="text-gray-600 hover:text-brand-500 text-sm font-medium">About</a>
                    <a href="{{ url('/#programs') }}"
                        class="text-gray-600 hover:text-brand-500 text-sm font-medium">Programs</a>
                    <a href="{{ route('gallery') }}" class="text-brand-500 text-sm font-medium">Gallery</a>
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
    <section class="bg-brand-500 pt-24 pb-16 lg:pt-32 lg:pb-20 relative overflow-hidden">
        <div class="absolute inset-0 islamic-pattern"></div>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="max-w-3xl">
                <nav class="text-sm mb-6">
                    <a href="{{ url('/') }}" class="text-white/70 hover:text-white">Home</a>
                    <span class="text-white/50 mx-2">/</span>
                    <span class="text-white">Gallery</span>
                </nav>
                <h1 class="animate-fade-in-up text-4xl lg:text-5xl font-extrabold text-white mb-6">Our Gallery</h1>
                <p class="animate-fade-in-up text-lg text-white/85">Capturing moments of learning, growth, and Islamic
                    education at our Madrasah.</p>
            </div>
        </div>
    </section>

    <!-- Gallery Filters -->
    <section class="py-8 bg-white border-b sticky top-16 z-40">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-wrap gap-3 justify-center">
                <button
                    class="filter-btn active px-5 py-2 rounded-full bg-brand-500 text-white text-sm font-medium transition-all"
                    data-filter="all">All</button>
                <button
                    class="filter-btn px-5 py-2 rounded-full bg-gray-100 text-gray-700 text-sm font-medium hover:bg-brand-500 hover:text-white transition-all"
                    data-filter="quran">Quran Classes</button>
                <button
                    class="filter-btn px-5 py-2 rounded-full bg-gray-100 text-gray-700 text-sm font-medium hover:bg-brand-500 hover:text-white transition-all"
                    data-filter="arabic">Arabic Studies</button>
                <button
                    class="filter-btn px-5 py-2 rounded-full bg-gray-100 text-gray-700 text-sm font-medium hover:bg-brand-500 hover:text-white transition-all"
                    data-filter="events">Events</button>
                <button
                    class="filter-btn px-5 py-2 rounded-full bg-gray-100 text-gray-700 text-sm font-medium hover:bg-brand-500 hover:text-white transition-all"
                    data-filter="campus">Campus</button>
            </div>
        </div>
    </section>

    <!-- Gallery Grid -->
    <section class="py-12 lg:py-20 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div id="galleryGrid" class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">

                <!-- Gallery Items - Using placeholder colors since no actual images exist -->
                <div class="gallery-item group relative rounded-2xl overflow-hidden shadow-lg cursor-pointer"
                    data-category="quran" onclick="openLightbox(this)">
                    <div
                        class="aspect-square bg-gradient-to-br from-brand-400 to-brand-600 flex items-center justify-center">
                        <i class="fas fa-quran text-white/30 text-6xl group-hover:scale-110 transition-transform"></i>
                    </div>
                    <div
                        class="absolute inset-0 bg-gradient-to-t from-black/70 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity flex items-end p-4">
                        <div>
                            <h3 class="text-white font-semibold">Quran Circle</h3>
                            <p class="text-white/70 text-sm">Students reciting Quran</p>
                        </div>
                    </div>
                </div>

                <div class="gallery-item group relative rounded-2xl overflow-hidden shadow-lg cursor-pointer"
                    data-category="arabic" onclick="openLightbox(this)">
                    <div
                        class="aspect-square bg-gradient-to-br from-green-400 to-green-600 flex items-center justify-center">
                        <i
                            class="fas fa-language text-white/30 text-6xl group-hover:scale-110 transition-transform"></i>
                    </div>
                    <div
                        class="absolute inset-0 bg-gradient-to-t from-black/70 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity flex items-end p-4">
                        <div>
                            <h3 class="text-white font-semibold">Arabic Class</h3>
                            <p class="text-white/70 text-sm">Learning classical Arabic</p>
                        </div>
                    </div>
                </div>

                <div class="gallery-item group relative rounded-2xl overflow-hidden shadow-lg cursor-pointer"
                    data-category="events" onclick="openLightbox(this)">
                    <div
                        class="aspect-square bg-gradient-to-br from-purple-400 to-purple-600 flex items-center justify-center">
                        <i
                            class="fas fa-graduation-cap text-white/30 text-6xl group-hover:scale-110 transition-transform"></i>
                    </div>
                    <div
                        class="absolute inset-0 bg-gradient-to-t from-black/70 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity flex items-end p-4">
                        <div>
                            <h3 class="text-white font-semibold">Graduation Ceremony</h3>
                            <p class="text-white/70 text-sm">Huffaz graduation day</p>
                        </div>
                    </div>
                </div>

                <div class="gallery-item group relative rounded-2xl overflow-hidden shadow-lg cursor-pointer"
                    data-category="campus" onclick="openLightbox(this)">
                    <div
                        class="aspect-square bg-gradient-to-br from-amber-400 to-amber-600 flex items-center justify-center">
                        <i class="fas fa-mosque text-white/30 text-6xl group-hover:scale-110 transition-transform"></i>
                    </div>
                    <div
                        class="absolute inset-0 bg-gradient-to-t from-black/70 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity flex items-end p-4">
                        <div>
                            <h3 class="text-white font-semibold">Prayer Hall</h3>
                            <p class="text-white/70 text-sm">Our beautiful masjid</p>
                        </div>
                    </div>
                </div>

                <div class="gallery-item group relative rounded-2xl overflow-hidden shadow-lg cursor-pointer"
                    data-category="quran" onclick="openLightbox(this)">
                    <div
                        class="aspect-square bg-gradient-to-br from-teal-400 to-teal-600 flex items-center justify-center">
                        <i
                            class="fas fa-book-open text-white/30 text-6xl group-hover:scale-110 transition-transform"></i>
                    </div>
                    <div
                        class="absolute inset-0 bg-gradient-to-t from-black/70 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity flex items-end p-4">
                        <div>
                            <h3 class="text-white font-semibold">Hifz Program</h3>
                            <p class="text-white/70 text-sm">Memorization session</p>
                        </div>
                    </div>
                </div>

                <div class="gallery-item group relative rounded-2xl overflow-hidden shadow-lg cursor-pointer"
                    data-category="events" onclick="openLightbox(this)">
                    <div
                        class="aspect-square bg-gradient-to-br from-rose-400 to-rose-600 flex items-center justify-center">
                        <i class="fas fa-star text-white/30 text-6xl group-hover:scale-110 transition-transform"></i>
                    </div>
                    <div
                        class="absolute inset-0 bg-gradient-to-t from-black/70 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity flex items-end p-4">
                        <div>
                            <h3 class="text-white font-semibold">Quran Competition</h3>
                            <p class="text-white/70 text-sm">Annual recitation contest</p>
                        </div>
                    </div>
                </div>

                <div class="gallery-item group relative rounded-2xl overflow-hidden shadow-lg cursor-pointer"
                    data-category="arabic" onclick="openLightbox(this)">
                    <div
                        class="aspect-square bg-gradient-to-br from-indigo-400 to-indigo-600 flex items-center justify-center">
                        <i
                            class="fas fa-pen-fancy text-white/30 text-6xl group-hover:scale-110 transition-transform"></i>
                    </div>
                    <div
                        class="absolute inset-0 bg-gradient-to-t from-black/70 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity flex items-end p-4">
                        <div>
                            <h3 class="text-white font-semibold">Arabic Calligraphy</h3>
                            <p class="text-white/70 text-sm">Art of Islamic writing</p>
                        </div>
                    </div>
                </div>

                <div class="gallery-item group relative rounded-2xl overflow-hidden shadow-lg cursor-pointer"
                    data-category="campus" onclick="openLightbox(this)">
                    <div
                        class="aspect-square bg-gradient-to-br from-cyan-400 to-cyan-600 flex items-center justify-center">
                        <i class="fas fa-school text-white/30 text-6xl group-hover:scale-110 transition-transform"></i>
                    </div>
                    <div
                        class="absolute inset-0 bg-gradient-to-t from-black/70 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity flex items-end p-4">
                        <div>
                            <h3 class="text-white font-semibold">Campus View</h3>
                            <p class="text-white/70 text-sm">Our learning environment</p>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Empty State Message -->
            <div id="emptyState" class="hidden text-center py-20">
                <i class="fas fa-images text-gray-300 text-6xl mb-4"></i>
                <h3 class="text-xl font-bold text-gray-600">No images in this category</h3>
                <p class="text-gray-500">Check back soon for more photos!</p>
            </div>
        </div>
    </section>

    <!-- Lightbox -->
    <div id="lightbox" class="lightbox items-center justify-center" onclick="closeLightbox(event)">
        <button onclick="closeLightbox(event)"
            class="absolute top-6 right-6 text-white text-3xl hover:text-gray-300 z-10">
            <i class="fas fa-times"></i>
        </button>
        <div class="max-w-4xl max-h-[90vh] p-4">
            <div id="lightboxContent" class="bg-gray-800 rounded-2xl p-8 text-center">
                <i id="lightboxIcon" class="fas fa-quran text-white/50 text-9xl mb-6"></i>
                <h3 id="lightboxTitle" class="text-white text-2xl font-bold mb-2">Image Title</h3>
                <p id="lightboxDesc" class="text-gray-400">Image description</p>
            </div>
        </div>
    </div>

    <!-- CTA -->
    <section class="py-16 bg-brand-500 relative overflow-hidden">
        <div class="absolute inset-0 islamic-pattern"></div>
        <div class="max-w-4xl mx-auto px-4 text-center relative z-10">
            <h2 class="text-3xl font-bold text-white mb-4">Want Your Child in Our Community?</h2>
            <p class="text-white/85 mb-8">Join our family of learners and be part of these beautiful moments.</p>
            <a href="{{ route('enrollment.token') }}"
                class="inline-flex items-center gap-2 bg-white text-brand-500 font-bold px-8 py-4 rounded-xl hover:shadow-xl transition-all">
                <i class="fas fa-user-plus"></i>
                <span>Enroll Now</span>
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

    <script>
        // Filter functionality
        document.querySelectorAll('.filter-btn').forEach(function (btn) {
            btn.addEventListener('click', function () {
                // Update active state
                document.querySelectorAll('.filter-btn').forEach(function (b) {
                    b.classList.remove('active', 'bg-brand-500', 'text-white');
                    b.classList.add('bg-gray-100', 'text-gray-700');
                });
                this.classList.add('active', 'bg-brand-500', 'text-white');
                this.classList.remove('bg-gray-100', 'text-gray-700');

                var filter = this.dataset.filter;
                var items = document.querySelectorAll('.gallery-item');
                var visibleCount = 0;

                items.forEach(function (item) {
                    if (filter === 'all' || item.dataset.category === filter) {
                        item.style.display = 'block';
                        visibleCount++;
                    } else {
                        item.style.display = 'none';
                    }
                });

                document.getElementById('emptyState').style.display = visibleCount === 0 ? 'block' : 'none';
            });
        });

        // Lightbox
        function openLightbox(el) {
            var title = el.querySelector('h3').textContent;
            var desc = el.querySelector('p').textContent;
            var icon = el.querySelector('i').className;

            document.getElementById('lightboxTitle').textContent = title;
            document.getElementById('lightboxDesc').textContent = desc;
            document.getElementById('lightboxIcon').className = icon.replace('text-6xl', 'text-9xl');
            document.getElementById('lightbox').classList.add('active');
            document.body.style.overflow = 'hidden';
        }

        function closeLightbox(e) {
            if (e.target.id === 'lightbox' || e.target.closest('button')) {
                document.getElementById('lightbox').classList.remove('active');
                document.body.style.overflow = '';
            }
        }

        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') {
                document.getElementById('lightbox').classList.remove('active');
                document.body.style.overflow = '';
            }
        });
    </script>
</body>

</html>