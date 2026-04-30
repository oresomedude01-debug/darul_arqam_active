<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @php $schoolSettings = \App\Models\SchoolSetting::getInstance(); @endphp
    <title>Blog - {{ $schoolSettings->school_name ?? 'Madrasah' }}</title>
    <meta name="description" content="News, reflections and updates from {{ $schoolSettings->school_name ?? 'our Madrasah' }}.">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Lora:wght@400;600&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: { 50: '#e6f0f5', 100: '#cce1eb', 500: '#0B4D73', 600: '#093e5c' },
                        warm:  { 50: '#fdfbf7' }
                    },
                    fontFamily: { sans: ['Inter', 'system-ui', 'sans-serif'] }
                }
            }
        }
    </script>
    <style>
        * { font-family: 'Inter', system-ui, sans-serif; }
        html { scroll-behavior: smooth; }

        /* ── Card hover effects ── */
        .blog-card { transition: all .35s cubic-bezier(.4,0,.2,1); }
        .blog-card:hover { transform: translateY(-6px); box-shadow: 0 24px 48px rgba(11,77,115,.14); }
        .blog-card:hover .card-img { transform: scale(1.07); }
        .card-img { transition: transform .6s cubic-bezier(.4,0,.2,1); }

        /* ── Featured (hero) card ── */
        .hero-card:hover { transform: translateY(-4px); box-shadow: 0 28px 56px rgba(11,77,115,.16); }
        .hero-card { transition: all .35s cubic-bezier(.4,0,.2,1); }
        .hero-card:hover .card-img { transform: scale(1.04); }

        /* ── Pattern ── */
        .islamic-pattern {
            background-image: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M30 0L60 30L30 60L0 30z' fill='none' stroke='%23ffffff' stroke-width='0.5' opacity='0.1'/%3E%3C/svg%3E");
        }

        /* ── Animations ── */
        @keyframes fadeInUp { from { opacity:0; transform:translateY(24px); } to { opacity:1; transform:translateY(0); } }
        .animate-fade-in-up { animation: fadeInUp .65s ease-out forwards; }
        .delay-100 { animation-delay:.1s; opacity:0; }
        .delay-200 { animation-delay:.2s; opacity:0; }

        /* ── Category pill ── */
        .pill-news    { background:#e6f0f5; color:#0B4D73; }
        .pill-islamic { background:#dcfce7; color:#15803d; }
        .pill-events  { background:#f3e8ff; color:#7e22ce; }
        .pill-tips    { background:#fef9c3; color:#854d0e; }
        .pill-default { background:#f1f5f9; color:#475569; }
    </style>
</head>
<body class="antialiased bg-warm-50 text-gray-800">

{{-- ── Navbar ── --}}
<header class="fixed top-0 left-0 right-0 z-50 bg-white/95 backdrop-blur-md shadow-sm">
    <nav class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">
            <a href="{{ url('/') }}" class="flex items-center gap-3">
                @if($schoolSettings->school_logo)
                    <img src="{{ asset('storage/'.$schoolSettings->school_logo) }}" class="h-10 w-10 rounded-lg object-cover" alt="Logo">
                @else
                    <div class="h-10 w-10 bg-brand-500 rounded-lg flex items-center justify-center"><i class="fas fa-mosque text-white"></i></div>
                @endif
                <span class="font-bold text-brand-500 text-lg hidden sm:block">{{ $schoolSettings->school_name ?? 'Madrasah' }}</span>
            </a>
            <div class="hidden md:flex items-center gap-7">
                <a href="{{ url('/') }}" class="text-gray-600 hover:text-brand-500 text-sm font-medium">Home</a>
                <a href="{{ route('about') }}" class="text-gray-600 hover:text-brand-500 text-sm font-medium">About</a>
                <a href="{{ route('gallery') }}" class="text-gray-600 hover:text-brand-500 text-sm font-medium">Gallery</a>
                <a href="{{ route('blog.index') }}" class="text-brand-500 text-sm font-semibold border-b-2 border-brand-500 pb-0.5">Blog</a>
                <a href="{{ url('/#contact') }}" class="text-gray-600 hover:text-brand-500 text-sm font-medium">Contact</a>
            </div>
            <div>
                @guest
                    <a href="{{ route('enrollment.token') }}" class="bg-brand-500 hover:bg-brand-600 text-white text-sm font-semibold px-5 py-2.5 rounded-lg transition-all">Enroll</a>
                @else
                    <a href="{{ url('/dashboard') }}" class="bg-brand-500 hover:bg-brand-600 text-white text-sm font-semibold px-5 py-2.5 rounded-lg transition-all">Dashboard</a>
                @endguest
            </div>
        </div>
    </nav>
</header>

{{-- ── Hero Banner ── --}}
<section class="bg-brand-500 pt-24 pb-16 lg:pt-32 lg:pb-20 relative overflow-hidden">
    <div class="absolute inset-0 islamic-pattern"></div>
    {{-- Decorative blobs --}}
    <div class="absolute top-10 right-10 w-64 h-64 bg-white/5 rounded-full blur-3xl"></div>
    <div class="absolute bottom-0 left-0 w-48 h-48 bg-white/5 rounded-full blur-3xl"></div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        <nav class="text-sm mb-6">
            <a href="{{ url('/') }}" class="text-white/70 hover:text-white">Home</a>
            <span class="text-white/50 mx-2">/</span>
            <span class="text-white">Blog</span>
        </nav>
        <h1 class="animate-fade-in-up text-4xl lg:text-5xl font-extrabold text-white mb-4 tracking-tight">Our Blog</h1>
        <p class="animate-fade-in-up delay-100 text-lg text-white/85 max-w-xl">
            News, reflections and updates from {{ $schoolSettings->school_name ?? 'our Madrasah' }}.
        </p>
        {{-- Stats --}}
        <div class="animate-fade-in-up delay-200 flex items-center gap-6 mt-8">
            <div class="flex items-center gap-2 text-white/75 text-sm">
                <i class="fas fa-newspaper"></i>
                <span>{{ $posts->count() }} {{ Str::plural('article', $posts->count()) }}</span>
            </div>
            <div class="w-px h-4 bg-white/30"></div>
            <div class="flex items-center gap-2 text-white/75 text-sm">
                <i class="fas fa-tags"></i>
                <span>{{ count(array_filter(['news','islamic','events','tips'], fn($c) => $posts->contains('category', $c))) }} categories</span>
            </div>
        </div>
    </div>
</section>

{{-- ── Category Filter ── --}}
<section class="py-5 bg-white border-b sticky top-16 z-40 shadow-sm">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-wrap gap-2 justify-center">
        @foreach(['all'=>'All Posts','news'=>'📰 News','islamic'=>'📖 Islamic Studies','events'=>'🎉 Events','tips'=>'💡 Study Tips'] as $key=>$label)
            <a href="{{ route('blog.index', $key!=='all' ? ['category'=>$key] : []) }}"
               class="px-5 py-2 rounded-full text-sm font-semibold transition-all
                      {{ $category===$key
                         ? 'bg-brand-500 text-white shadow-md shadow-brand-500/30'
                         : 'bg-gray-100 text-gray-700 hover:bg-brand-500 hover:text-white' }}">
                {{ $label }}
            </a>
        @endforeach
    </div>
</section>

{{-- ── Posts Grid ── --}}
<section class="py-14 lg:py-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        @if($posts->isEmpty())
            <div class="text-center py-28">
                <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-6">
                    <i class="fas fa-newspaper text-gray-300 text-4xl"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-500">No posts yet</h3>
                <p class="text-gray-400 mt-2">Check back soon for new articles!</p>
            </div>

        @else
            @php
                $featured = $posts->first();
                $rest     = $posts->skip(1);
            @endphp

            {{-- ── Featured / Hero Card ── --}}
            <a href="{{ route('blog.show', $featured->slug) }}"
               class="hero-card group block bg-white rounded-3xl overflow-hidden shadow-lg border border-gray-100 mb-12">
                <div class="lg:flex">
                    {{-- Image --}}
                    <div class="lg:w-3/5 overflow-hidden relative" style="min-height:340px;">
                        @if($featured->featured_image)
                            <img src="{{ asset('storage/'.$featured->featured_image) }}"
                                 alt="{{ $featured->title }}"
                                 class="card-img w-full h-full object-cover absolute inset-0">
                        @elseif($featured->type === 'video' && $featured->youtube_video_id)
                            <img src="https://img.youtube.com/vi/{{ $featured->youtube_video_id }}/maxresdefault.jpg"
                                 alt="{{ $featured->title }}"
                                 class="card-img w-full h-full object-cover absolute inset-0">
                            <div class="absolute inset-0 flex items-center justify-center bg-black/20">
                                <div class="w-16 h-16 bg-white/90 backdrop-blur-sm rounded-full flex items-center justify-center shadow-xl">
                                    <i class="fas fa-play text-brand-500 text-xl ml-1"></i>
                                </div>
                            </div>
                        @else
                            <div class="absolute inset-0 bg-gradient-to-br {{ $featured->cover_color }} flex items-center justify-center">
                                <i class="{{ $featured->cover_icon }} text-white/20 text-9xl"></i>
                            </div>
                        @endif
                        {{-- Overlay gradient for text readability on mobile --}}
                        <div class="absolute inset-0 bg-gradient-to-r from-black/10 to-transparent lg:hidden"></div>
                    </div>

                    {{-- Content --}}
                    <div class="lg:w-2/5 p-8 lg:p-10 flex flex-col justify-center">
                        <div class="flex items-center gap-3 mb-4">
                            <span class="inline-block px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider pill-{{ $featured->category }}">
                                {{ $featured->category_label }}
                            </span>
                            @if($featured->type === 'video')
                                <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-semibold bg-red-50 text-red-600">
                                    <i class="fas fa-play-circle"></i> Video
                                </span>
                            @endif
                        </div>

                        <h2 class="text-2xl lg:text-3xl font-extrabold text-gray-900 leading-tight mb-4 group-hover:text-brand-500 transition-colors">
                            {{ $featured->title }}
                        </h2>
                        <p class="text-gray-500 leading-relaxed mb-6 line-clamp-3">{{ $featured->excerpt }}</p>

                        <div class="flex items-center gap-4 text-sm text-gray-400 mb-6">
                            <span class="flex items-center gap-1.5">
                                <div class="w-7 h-7 bg-brand-500 rounded-full flex items-center justify-center text-white text-xs font-bold">
                                    {{ substr($featured->author->name ?? 'A', 0, 2) }}
                                </div>
                                {{ $featured->author->name ?? 'Admin' }}
                            </span>
                            <span class="flex items-center gap-1"><i class="fas fa-calendar-alt"></i> {{ $featured->published_at->format('d M Y') }}</span>
                            <span class="flex items-center gap-1"><i class="fas fa-clock"></i> {{ $featured->reading_time }} min</span>
                        </div>

                        <span class="inline-flex items-center gap-2 text-brand-500 font-bold text-sm group-hover:gap-3 transition-all">
                            Read Full Article <i class="fas fa-arrow-right"></i>
                        </span>
                    </div>
                </div>
            </a>

            {{-- ── Regular Grid ── --}}
            @if($rest->isNotEmpty())
            <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-8">
                @foreach($rest as $post)
                    <article class="blog-card bg-white rounded-2xl overflow-hidden shadow-md border border-gray-100 flex flex-col">

                        {{-- Thumbnail --}}
                        <a href="{{ route('blog.show', $post->slug) }}"
                           class="block overflow-hidden relative" style="height:220px;">
                            @if($post->featured_image)
                                <img src="{{ asset('storage/'.$post->featured_image) }}"
                                     alt="{{ $post->title }}"
                                     class="card-img w-full h-full object-cover">
                            @elseif($post->type === 'video' && $post->youtube_video_id)
                                <img src="https://img.youtube.com/vi/{{ $post->youtube_video_id }}/mqdefault.jpg"
                                     alt="{{ $post->title }}"
                                     class="card-img w-full h-full object-cover">
                                <div class="absolute inset-0 flex items-center justify-center bg-black/25">
                                    <div class="w-12 h-12 bg-white/90 rounded-full flex items-center justify-center">
                                        <i class="fas fa-play text-brand-500 text-sm ml-0.5"></i>
                                    </div>
                                </div>
                            @else
                                <div class="w-full h-full bg-gradient-to-br {{ $post->cover_color }} flex items-center justify-center">
                                    <i class="{{ $post->cover_icon }} card-img text-white/25 text-7xl"></i>
                                </div>
                            @endif

                            {{-- Category badge overlay --}}
                            <div class="absolute top-3 left-3">
                                <span class="px-2.5 py-1 rounded-full text-xs font-bold uppercase tracking-wider pill-{{ $post->category }} shadow-sm">
                                    {{ $post->category_label }}
                                </span>
                            </div>
                        </a>

                        {{-- Body --}}
                        <div class="p-6 flex flex-col flex-1">
                            <h2 class="text-lg font-bold text-gray-900 leading-snug line-clamp-2 mb-2">
                                <a href="{{ route('blog.show', $post->slug) }}"
                                   class="hover:text-brand-500 transition-colors">{{ $post->title }}</a>
                            </h2>
                            <p class="text-gray-500 text-sm line-clamp-3 mb-4 leading-relaxed flex-1">{{ $post->excerpt }}</p>

                            <div class="flex items-center justify-between text-xs text-gray-400 pt-4 border-t border-gray-100">
                                <div class="flex items-center gap-2">
                                    <div class="w-6 h-6 bg-brand-500 rounded-full flex items-center justify-center text-white text-xs font-bold">
                                        {{ substr($post->author->name ?? 'A', 0, 1) }}
                                    </div>
                                    <span>{{ $post->published_at->format('d M Y') }}</span>
                                </div>
                                <div class="flex items-center gap-3">
                                    <span class="flex items-center gap-1"><i class="fas fa-clock"></i> {{ $post->reading_time }} min</span>
                                    <a href="{{ route('blog.show', $post->slug) }}"
                                       class="flex items-center gap-1 text-brand-500 font-semibold hover:gap-2 transition-all">
                                        Read <i class="fas fa-arrow-right text-xs"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>
            @endif
        @endif
    </div>
</section>

{{-- ── Stay Updated ── --}}
<section class="py-16 bg-brand-500 relative overflow-hidden">
    <div class="absolute inset-0 islamic-pattern"></div>
    <div class="absolute top-0 right-0 w-72 h-72 bg-white/5 rounded-full blur-3xl"></div>
    <div class="max-w-4xl mx-auto px-4 text-center relative z-10">
        <div class="w-16 h-16 bg-white/10 rounded-2xl flex items-center justify-center mx-auto mb-6">
            <i class="fas fa-bell text-white text-2xl"></i>
        </div>
        <h2 class="text-3xl font-bold text-white mb-4">Stay Updated</h2>
        <p class="text-white/85 mb-8 text-lg">Follow us on social media for the latest news and updates.</p>
        <div class="flex justify-center gap-4 flex-wrap">
            <a href="#" class="inline-flex items-center gap-2 bg-white text-brand-500 font-bold px-6 py-3 rounded-xl hover:shadow-2xl hover:-translate-y-1 transition-all">
                <i class="fab fa-facebook-f"></i> Facebook
            </a>
            <a href="#" class="inline-flex items-center gap-2 bg-white text-pink-600 font-bold px-6 py-3 rounded-xl hover:shadow-2xl hover:-translate-y-1 transition-all">
                <i class="fab fa-instagram"></i> Instagram
            </a>
            <a href="#" class="inline-flex items-center gap-2 bg-white text-green-600 font-bold px-6 py-3 rounded-xl hover:shadow-2xl hover:-translate-y-1 transition-all">
                <i class="fab fa-whatsapp"></i> WhatsApp
            </a>
        </div>
    </div>
</section>

{{-- ── Footer ── --}}
<footer class="bg-gray-900 text-white py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid md:grid-cols-4 gap-8 mb-8">
            <div>
                <div class="flex items-center gap-3 mb-4">
                    @if($schoolSettings->school_logo)
                        <img src="{{ asset('storage/'.$schoolSettings->school_logo) }}" class="h-10 w-10 rounded-lg object-cover" alt="Logo">
                    @else
                        <div class="h-10 w-10 bg-brand-500 rounded-lg flex items-center justify-center"><i class="fas fa-mosque text-white"></i></div>
                    @endif
                    <span class="font-bold">{{ $schoolSettings->school_name ?? 'Madrasah' }}</span>
                </div>
                <p class="text-gray-400 text-sm">{{ $schoolSettings->school_motto ?? 'Excellence in Islamic Education' }}</p>
            </div>
            <div>
                <h4 class="font-bold mb-4 text-sm uppercase tracking-wider">Quick Links</h4>
                <ul class="space-y-2 text-gray-400 text-sm">
                    <li><a href="{{ url('/') }}" class="hover:text-white transition">Home</a></li>
                    <li><a href="{{ route('about') }}" class="hover:text-white transition">About</a></li>
                    <li><a href="{{ route('gallery') }}" class="hover:text-white transition">Gallery</a></li>
                    <li><a href="{{ route('blog.index') }}" class="hover:text-white transition">Blog</a></li>
                </ul>
            </div>
            <div>
                <h4 class="font-bold mb-4 text-sm uppercase tracking-wider">Contact</h4>
                <ul class="space-y-2 text-gray-400 text-sm">
                    <li class="flex items-center gap-2"><i class="fas fa-phone w-4"></i>{{ $schoolSettings->school_phone ?? '—' }}</li>
                    <li class="flex items-center gap-2"><i class="fas fa-envelope w-4"></i>{{ $schoolSettings->school_email ?? '—' }}</li>
                </ul>
            </div>
            <div>
                <h4 class="font-bold mb-4 text-sm uppercase tracking-wider">Follow Us</h4>
                <div class="flex gap-3">
                    <a href="#" class="w-10 h-10 bg-gray-800 rounded-lg flex items-center justify-center hover:bg-blue-600 transition"><i class="fab fa-facebook-f"></i></a>
                    <a href="#" class="w-10 h-10 bg-gray-800 rounded-lg flex items-center justify-center hover:bg-pink-600 transition"><i class="fab fa-instagram"></i></a>
                    <a href="#" class="w-10 h-10 bg-gray-800 rounded-lg flex items-center justify-center hover:bg-green-600 transition"><i class="fab fa-whatsapp"></i></a>
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
