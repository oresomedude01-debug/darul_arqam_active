<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @php $schoolSettings = \App\Models\SchoolSetting::getInstance(); @endphp
    <title>{{ $post->title }} - {{ $schoolSettings->school_name ?? 'Madrasah' }}</title>
    <meta name="description" content="{{ $post->excerpt }}">
    {{-- Open Graph (for social sharing) --}}
    <meta property="og:title"       content="{{ $post->title }}">
    <meta property="og:description" content="{{ $post->excerpt }}">
    @if($post->featured_image)
        <meta property="og:image" content="{{ asset('storage/'.$post->featured_image) }}">
    @endif
    <meta property="og:type" content="article">
    <meta property="og:url"  content="{{ url()->current() }}">

    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Lora:ital,wght@0,400;0,600;1,400&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: { 50:'#e6f0f5', 100:'#cce1eb', 500:'#0B4D73', 600:'#093e5c' },
                        warm:  { 50:'#fdfbf7' }
                    }
                }
            }
        }
    </script>
    <style>
        * { font-family: 'Inter', system-ui, sans-serif; }
        html { scroll-behavior: smooth; }

        /* ── Prose body ── */
        .prose-body { font-family:'Lora',serif; font-size:1.125rem; line-height:1.9; color:#374151; }
        .prose-body p  { margin-bottom: 1.6rem; }
        .prose-body h2 { font-family:'Inter',sans-serif; font-size:1.5rem; font-weight:700; margin:2.5rem 0 1rem; color:#111827; }
        .prose-body h3 { font-family:'Inter',sans-serif; font-size:1.2rem; font-weight:600; margin:2rem 0 .75rem; color:#111827; }
        .prose-body ul, .prose-body ol { margin:1.5rem 0; padding-left:1.75rem; }
        .prose-body li { margin-bottom:.5rem; }
        .prose-body blockquote { border-left:4px solid #0B4D73; padding-left:1.25rem; margin:2rem 0; color:#6b7280; font-style:italic; }

        /* ── Image hero ── */
        .hero-img-wrap { position:relative; width:100%; height:480px; overflow:hidden; }
        @media (max-width:768px) { .hero-img-wrap { height:280px; } }
        .hero-img-wrap img { width:100%; height:100%; object-fit:cover; }
        .hero-img-overlay { position:absolute; inset:0; background:linear-gradient(to bottom, rgba(0,0,0,.15) 0%, rgba(0,0,0,.55) 100%); }

        /* ── Pattern ── */
        .islamic-pattern {
            background-image: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M30 0L60 30L30 60L0 30z' fill='none' stroke='%23ffffff' stroke-width='0.5' opacity='0.1'/%3E%3C/svg%3E");
        }

        /* ── Sidebar related cards ── */
        .related-card { transition: all .3s ease; }
        .related-card:hover { transform: translateX(4px); }
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

{{-- ── HERO (featured image / video / gradient) ── --}}
@if($post->type === 'video' && $post->youtube_embed_url)
    {{-- Video hero --}}
    <div class="pt-16 bg-black">
        <div class="max-w-5xl mx-auto">
            <div class="relative aspect-video bg-black">
                <iframe class="absolute inset-0 w-full h-full"
                        src="{{ $post->youtube_embed_url }}?rel=0&autoplay=0"
                        title="{{ $post->title }}" frameborder="0"
                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                        allowfullscreen></iframe>
            </div>
        </div>
    </div>
    {{-- Post header below video --}}
    <div class="bg-brand-500 py-10 relative overflow-hidden">
        <div class="absolute inset-0 islamic-pattern"></div>
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            @include('blog._post_header')
        </div>
    </div>

@elseif($post->featured_image)
    {{-- Featured image hero (full-bleed with overlay) --}}
    <div class="hero-img-wrap mt-16">
        <img src="{{ asset('storage/'.$post->featured_image) }}" alt="{{ $post->title }}">
        <div class="hero-img-overlay"></div>
        {{-- Header text overlaid on the image --}}
        <div class="absolute inset-0 flex flex-col justify-end pb-10">
            <div class="max-w-4xl mx-auto w-full px-4 sm:px-6 lg:px-8">
                <nav class="text-sm mb-4 flex items-center gap-2">
                    <a href="{{ url('/') }}" class="text-white/70 hover:text-white">Home</a>
                    <span class="text-white/40">/</span>
                    <a href="{{ route('blog.index') }}" class="text-white/70 hover:text-white">Blog</a>
                    <span class="text-white/40">/</span>
                    <span class="text-white truncate max-w-xs">{{ $post->title }}</span>
                </nav>
                <span class="inline-block px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider bg-white/20 text-white mb-4 backdrop-blur-sm">
                    {{ $post->category_label }}
                </span>
                <h1 class="text-3xl lg:text-5xl font-extrabold text-white leading-tight mb-5 drop-shadow-md">
                    {{ $post->title }}
                </h1>
                <div class="flex flex-wrap items-center gap-5 text-white/80 text-sm">
                    <span class="flex items-center gap-2">
                        <div class="w-8 h-8 bg-white/20 rounded-full flex items-center justify-center font-bold text-white text-xs backdrop-blur-sm">
                            {{ substr($post->author->name ?? 'A', 0, 2) }}
                        </div>
                        {{ $post->author->name ?? 'Admin' }}
                    </span>
                    <span class="flex items-center gap-1.5"><i class="fas fa-calendar-alt"></i> {{ $post->published_at->format('d F Y') }}</span>
                    <span class="flex items-center gap-1.5"><i class="fas fa-clock"></i> {{ $post->reading_time }} min read</span>
                </div>
            </div>
        </div>
    </div>

@else
    {{-- Gradient fallback hero --}}
    <section class="bg-brand-500 pt-24 pb-12 lg:pt-32 lg:pb-16 relative overflow-hidden">
        <div class="absolute inset-0 islamic-pattern"></div>
        <div class="absolute top-10 right-10 w-48 h-48 bg-white/5 rounded-full blur-3xl"></div>
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <nav class="text-sm mb-6 flex items-center gap-2">
                <a href="{{ url('/') }}" class="text-white/70 hover:text-white">Home</a>
                <span class="text-white/40">/</span>
                <a href="{{ route('blog.index') }}" class="text-white/70 hover:text-white">Blog</a>
                <span class="text-white/40">/</span>
                <span class="text-white truncate max-w-xs">{{ $post->title }}</span>
            </nav>
            <div class="flex items-center gap-3 mb-4">
                <span class="inline-block px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider bg-white/20 text-white">
                    {{ $post->category_label }}
                </span>
            </div>
            <h1 class="text-3xl lg:text-5xl font-extrabold text-white leading-tight mb-6">{{ $post->title }}</h1>
            <div class="flex flex-wrap items-center gap-6 text-white/75 text-sm">
                <span class="flex items-center gap-2">
                    <div class="w-8 h-8 bg-white/20 rounded-full flex items-center justify-center font-bold text-white text-xs">
                        {{ substr($post->author->name ?? 'A', 0, 2) }}
                    </div>
                    {{ $post->author->name ?? 'Admin' }}
                </span>
                <span class="flex items-center gap-1.5"><i class="fas fa-calendar-alt"></i> {{ $post->published_at->format('d F Y') }}</span>
                <span class="flex items-center gap-1.5"><i class="fas fa-clock"></i> {{ $post->reading_time }} min read</span>
            </div>
        </div>
    </section>
@endif

{{-- ── Article Content ── --}}
<section class="py-12 lg:py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col lg:flex-row gap-12">

            {{-- ── Main Article ── --}}
            <article class="flex-1 min-w-0">

                {{-- Breadcrumb (only shown when no featured image — otherwise it's in the hero) --}}
                @unless($post->featured_image || ($post->type === 'video' && $post->youtube_embed_url))
                    {{-- Already shown in hero --}}
                @endunless

                {{-- Excerpt highlight --}}
                <div class="bg-brand-50 border-l-4 border-brand-500 rounded-r-2xl p-6 mb-8">
                    <p class="text-brand-600 font-medium text-lg leading-relaxed italic">{{ $post->excerpt }}</p>
                </div>

                {{-- Body content --}}
                <div class="prose-body">
                    {!! nl2br(e($post->body)) !!}
                </div>

                {{-- Tags / Share --}}
                <div class="mt-14 pt-8 border-t border-gray-200">
                    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                        <span class="inline-flex items-center gap-2 px-4 py-2 rounded-full text-sm font-semibold bg-brand-50 text-brand-500 border border-brand-100">
                            <i class="fas fa-tag"></i> {{ $post->category_label }}
                        </span>
                        <div class="flex items-center gap-3">
                            <span class="text-gray-500 text-sm font-medium">Share:</span>
                            <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(url()->current()) }}" target="_blank"
                               class="w-9 h-9 bg-blue-600 text-white rounded-lg flex items-center justify-center hover:bg-blue-700 hover:scale-110 transition-all">
                                <i class="fab fa-facebook-f text-sm"></i>
                            </a>
                            <a href="https://wa.me/?text={{ urlencode($post->title . ' ' . url()->current()) }}" target="_blank"
                               class="w-9 h-9 bg-green-500 text-white rounded-lg flex items-center justify-center hover:bg-green-600 hover:scale-110 transition-all">
                                <i class="fab fa-whatsapp text-sm"></i>
                            </a>
                            <a href="https://twitter.com/intent/tweet?url={{ urlencode(url()->current()) }}&text={{ urlencode($post->title) }}" target="_blank"
                               class="w-9 h-9 bg-sky-500 text-white rounded-lg flex items-center justify-center hover:bg-sky-600 hover:scale-110 transition-all">
                                <i class="fab fa-twitter text-sm"></i>
                            </a>
                        </div>
                    </div>
                </div>

                {{-- Back --}}
                <div class="mt-8">
                    <a href="{{ route('blog.index') }}" class="inline-flex items-center gap-2 text-brand-500 font-semibold hover:gap-3 transition-all">
                        <i class="fas fa-arrow-left text-sm"></i> Back to Blog
                    </a>
                </div>
            </article>

            {{-- ── Sidebar ── --}}
            <aside class="w-full lg:w-80 flex-shrink-0 space-y-6">

                {{-- Author card --}}
                <div class="bg-white rounded-2xl shadow-md border border-gray-100 p-6">
                    <h3 class="font-bold text-gray-700 mb-4 text-xs uppercase tracking-widest">About the Author</h3>
                    <div class="flex items-center gap-4">
                        <div class="w-14 h-14 bg-gradient-to-br from-brand-500 to-brand-600 rounded-full flex items-center justify-center text-white font-bold text-lg flex-shrink-0">
                            {{ substr($post->author->name ?? 'A', 0, 2) }}
                        </div>
                        <div>
                            <p class="font-bold text-gray-900">{{ $post->author->name ?? 'Admin' }}</p>
                            <p class="text-sm text-gray-500">Staff — {{ $schoolSettings->school_name ?? 'Madrasah' }}</p>
                        </div>
                    </div>
                </div>

                {{-- Related Posts --}}
                @if($related->isNotEmpty())
                <div class="bg-white rounded-2xl shadow-md border border-gray-100 p-6">
                    <h3 class="font-bold text-gray-700 mb-5 text-xs uppercase tracking-widest">Related Articles</h3>
                    <div class="space-y-5">
                        @foreach($related as $rel)
                        <a href="{{ route('blog.show', $rel->slug) }}" class="related-card flex gap-3 group">
                            {{-- Thumbnail --}}
                            <div class="w-16 h-16 flex-shrink-0 rounded-xl overflow-hidden relative">
                                @if($rel->featured_image)
                                    <img src="{{ asset('storage/'.$rel->featured_image) }}" alt="{{ $rel->title }}" class="w-full h-full object-cover">
                                @elseif($rel->type === 'video' && $rel->youtube_video_id)
                                    <img src="https://img.youtube.com/vi/{{ $rel->youtube_video_id }}/mqdefault.jpg" alt="{{ $rel->title }}" class="w-full h-full object-cover">
                                @else
                                    <div class="w-full h-full bg-gradient-to-br {{ $rel->cover_color }} flex items-center justify-center">
                                        <i class="{{ $rel->cover_icon }} text-white/50 text-2xl"></i>
                                    </div>
                                @endif
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-semibold text-gray-800 group-hover:text-brand-500 transition-colors line-clamp-2 leading-snug">
                                    {{ $rel->title }}
                                </p>
                                <p class="text-xs text-gray-400 mt-1.5 flex items-center gap-1">
                                    <i class="fas fa-calendar-alt"></i> {{ $rel->published_at->format('d M Y') }}
                                </p>
                            </div>
                        </a>
                        @endforeach
                    </div>
                </div>
                @endif

                {{-- Categories --}}
                <div class="bg-white rounded-2xl shadow-md border border-gray-100 p-6">
                    <h3 class="font-bold text-gray-700 mb-4 text-xs uppercase tracking-widest">Browse Categories</h3>
                    <div class="space-y-1.5">
                        @foreach(['all'=>'All Posts','news'=>'News','islamic'=>'Islamic Studies','events'=>'Events','tips'=>'Study Tips'] as $key=>$label)
                        <a href="{{ route('blog.index', $key!=='all' ? ['category'=>$key] : []) }}"
                           class="flex items-center justify-between px-3 py-2.5 rounded-xl text-sm transition-all
                                  {{ $post->category===$key ? 'bg-brand-500 text-white font-semibold' : 'text-gray-600 hover:bg-brand-50 hover:text-brand-500' }}">
                            <span>{{ $label }}</span>
                            <i class="fas fa-chevron-right text-xs opacity-50"></i>
                        </a>
                        @endforeach
                    </div>
                </div>

            </aside>
        </div>
    </div>
</section>

{{-- ── Footer ── --}}
<footer class="bg-gray-900 text-white py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="border-t border-gray-800 pt-8 text-center text-gray-500 text-sm">
            <p>&copy; {{ date('Y') }} {{ $schoolSettings->school_name ?? 'Madrasah' }}. All rights reserved.
                &mdash; <a href="{{ route('blog.index') }}" class="hover:text-white transition">Blog</a>
            </p>
        </div>
    </div>
</footer>

</body>
</html>
