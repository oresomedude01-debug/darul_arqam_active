<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @php $schoolSettings = \App\Models\SchoolSetting::getInstance(); @endphp
    <title>{{ $post->title }} - {{ $schoolSettings->school_name ?? 'Madrasah' }}</title>
    <meta name="description" content="{{ $post->excerpt }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Lora:ital,wght@0,400;0,600;1,400&display=swap" rel="stylesheet">
    <script>tailwind.config={theme:{extend:{colors:{brand:{50:'#e6f0f5',100:'#cce1eb',500:'#0B4D73',600:'#093e5c'},warm:{50:'#fdfbf7'}}}}}</script>
    <style>
        *{font-family:'Inter',system-ui,sans-serif}html{scroll-behavior:smooth}
        .prose-body{font-family:'Lora',serif;font-size:1.125rem;line-height:1.85;color:#374151}
        .prose-body p{margin-bottom:1.5rem}
        .prose-body h2{font-family:'Inter',sans-serif;font-size:1.5rem;font-weight:700;margin:2.5rem 0 1rem;color:#111827}
        .prose-body h3{font-family:'Inter',sans-serif;font-size:1.25rem;font-weight:600;margin:2rem 0 0.75rem;color:#111827}
        .prose-body ul,.prose-body ol{margin:1.5rem 0;padding-left:1.75rem}
        .prose-body li{margin-bottom:.5rem}
        .prose-body blockquote{border-left:4px solid #0B4D73;padding-left:1.25rem;margin:2rem 0;color:#6b7280;font-style:italic}
        .islamic-pattern{background-image:url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M30 0L60 30L30 60L0 30z' fill='none' stroke='%23ffffff' stroke-width='0.5' opacity='0.1'/%3E%3C/svg%3E")}
        .related-card{transition:all .3s ease}.related-card:hover{transform:translateY(-3px);box-shadow:0 12px 30px rgba(11,77,115,.1)}
    </style>
</head>
<body class="antialiased bg-warm-50 text-gray-800">

<!-- Nav -->
<header class="fixed top-0 left-0 right-0 z-50 bg-white/95 backdrop-blur-md shadow-sm">
    <nav class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">
            <a href="{{ url('/') }}" class="flex items-center gap-3">
                @if($schoolSettings->school_logo)
                    <img src="{{ asset('storage/'.$schoolSettings->school_logo) }}" class="h-10 w-10 rounded-lg object-cover">
                @else
                    <div class="h-10 w-10 bg-brand-500 rounded-lg flex items-center justify-center"><i class="fas fa-mosque text-white"></i></div>
                @endif
                <span class="font-bold text-brand-500 text-lg hidden sm:block">{{ $schoolSettings->school_name ?? 'Madrasah' }}</span>
            </a>
            <div class="hidden md:flex items-center gap-7">
                <a href="{{ url('/') }}" class="text-gray-600 hover:text-brand-500 text-sm font-medium">Home</a>
                <a href="{{ route('about') }}" class="text-gray-600 hover:text-brand-500 text-sm font-medium">About</a>
                <a href="{{ route('gallery') }}" class="text-gray-600 hover:text-brand-500 text-sm font-medium">Gallery</a>
                <a href="{{ route('blog.index') }}" class="text-brand-500 text-sm font-semibold border-b-2 border-brand-500">Blog</a>
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

<!-- Hero -->
<section class="bg-brand-500 pt-24 pb-12 lg:pt-32 lg:pb-16 relative overflow-hidden">
    <div class="absolute inset-0 islamic-pattern"></div>
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        <nav class="text-sm mb-6 flex items-center gap-2">
            <a href="{{ url('/') }}" class="text-white/70 hover:text-white">Home</a>
            <span class="text-white/40">/</span>
            <a href="{{ route('blog.index') }}" class="text-white/70 hover:text-white">Blog</a>
            <span class="text-white/40">/</span>
            <span class="text-white truncate max-w-xs">{{ $post->title }}</span>
        </nav>
        <span class="inline-block px-3 py-1 rounded-full text-xs font-semibold uppercase tracking-wide bg-white/20 text-white mb-4">{{ $post->category_label }}</span>
        <h1 class="text-3xl lg:text-5xl font-extrabold text-white leading-tight mb-6">{{ $post->title }}</h1>
        <div class="flex flex-wrap items-center gap-6 text-white/75 text-sm">
            <span class="flex items-center gap-2">
                <div class="w-8 h-8 bg-white/20 rounded-full flex items-center justify-center font-bold text-white text-xs">
                    {{ substr($post->author->name ?? 'A', 0, 2) }}
                </div>
                {{ $post->author->name ?? 'Admin' }}
            </span>
            <span><i class="fas fa-calendar-alt mr-1.5"></i> {{ $post->published_at->format('d F Y') }}</span>
            <span><i class="fas fa-clock mr-1.5"></i> {{ $post->reading_time }} min read</span>
        </div>
    </div>
</section>

<!-- Post Content -->
<section class="py-12 lg:py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col lg:flex-row gap-12">

            <!-- Article -->
            <article class="flex-1 min-w-0">
                <!-- Visual Header -->
                @if($post->type === 'video' && $post->youtube_embed_url)
                    <div class="rounded-2xl overflow-hidden aspect-video bg-black mb-10 shadow-xl relative">
                        <iframe class="absolute inset-0 w-full h-full" 
                                src="{{ $post->youtube_embed_url }}?rel=0" 
                                title="YouTube video player" frameborder="0" 
                                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" 
                                allowfullscreen></iframe>
                    </div>
                @else
                    <div class="rounded-2xl overflow-hidden h-64 bg-gradient-to-br {{ $post->cover_color }} flex items-center justify-center mb-10 shadow-xl">
                        <i class="{{ $post->cover_icon }} text-white/25 text-9xl"></i>
                    </div>
                @endif

                <!-- Excerpt highlight -->
                <div class="bg-brand-50 border-l-4 border-brand-500 rounded-r-xl p-6 mb-8">
                    <p class="text-brand-600 font-medium text-lg leading-relaxed italic">{{ $post->excerpt }}</p>
                </div>

                <!-- Body -->
                <div class="prose-body">
                    {!! nl2br(e($post->body)) !!}
                </div>

                <!-- Tags / Share -->
                <div class="mt-12 pt-8 border-t border-gray-200 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                    <span class="inline-block px-4 py-1.5 rounded-full text-sm font-semibold {{ $post->category_color }}">
                        <i class="fas fa-tag mr-1"></i> {{ $post->category_label }}
                    </span>
                    <div class="flex items-center gap-3">
                        <span class="text-gray-500 text-sm font-medium">Share:</span>
                        <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(url()->current()) }}" target="_blank"
                           class="w-9 h-9 bg-blue-600 text-white rounded-lg flex items-center justify-center hover:bg-blue-700 transition">
                            <i class="fab fa-facebook-f text-sm"></i>
                        </a>
                        <a href="https://wa.me/?text={{ urlencode($post->title . ' ' . url()->current()) }}" target="_blank"
                           class="w-9 h-9 bg-green-500 text-white rounded-lg flex items-center justify-center hover:bg-green-600 transition">
                            <i class="fab fa-whatsapp text-sm"></i>
                        </a>
                        <a href="https://twitter.com/intent/tweet?url={{ urlencode(url()->current()) }}&text={{ urlencode($post->title) }}" target="_blank"
                           class="w-9 h-9 bg-sky-500 text-white rounded-lg flex items-center justify-center hover:bg-sky-600 transition">
                            <i class="fab fa-twitter text-sm"></i>
                        </a>
                    </div>
                </div>

                <!-- Back -->
                <div class="mt-8">
                    <a href="{{ route('blog.index') }}" class="inline-flex items-center gap-2 text-brand-500 font-semibold hover:gap-3 transition-all">
                        <i class="fas fa-arrow-left text-sm"></i> Back to Blog
                    </a>
                </div>
            </article>

            <!-- Sidebar -->
            <aside class="w-full lg:w-80 flex-shrink-0 space-y-8">

                <!-- About the Author -->
                <div class="bg-white rounded-2xl shadow-md border border-gray-100 p-6">
                    <h3 class="font-bold text-gray-800 mb-4 text-sm uppercase tracking-wider">About the Author</h3>
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 bg-brand-500 rounded-full flex items-center justify-center text-white font-bold">
                            {{ substr($post->author->name ?? 'A', 0, 2) }}
                        </div>
                        <div>
                            <p class="font-semibold text-gray-800">{{ $post->author->name ?? 'Admin' }}</p>
                            <p class="text-sm text-gray-500">Staff, {{ $schoolSettings->school_name ?? 'Madrasah' }}</p>
                        </div>
                    </div>
                </div>

                <!-- Related Posts -->
                @if($related->isNotEmpty())
                <div class="bg-white rounded-2xl shadow-md border border-gray-100 p-6">
                    <h3 class="font-bold text-gray-800 mb-4 text-sm uppercase tracking-wider">Related Articles</h3>
                    <div class="space-y-4">
                        @foreach($related as $rel)
                        <a href="{{ route('blog.show', $rel->slug) }}" class="related-card flex gap-3 group">
                            <div class="w-14 h-14 flex-shrink-0 rounded-xl bg-gradient-to-br {{ $rel->cover_color }} flex items-center justify-center">
                                <i class="{{ $rel->cover_icon }} text-white/50 text-xl"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-semibold text-gray-800 group-hover:text-brand-500 transition-colors line-clamp-2 leading-snug">{{ $rel->title }}</p>
                                <p class="text-xs text-gray-400 mt-1">{{ $rel->published_at->format('d M Y') }}</p>
                            </div>
                        </a>
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- Categories -->
                <div class="bg-white rounded-2xl shadow-md border border-gray-100 p-6">
                    <h3 class="font-bold text-gray-800 mb-4 text-sm uppercase tracking-wider">Categories</h3>
                    <div class="space-y-2">
                        @foreach(['all'=>'All Posts','news'=>'News','islamic'=>'Islamic Studies','events'=>'Events','tips'=>'Study Tips'] as $key=>$label)
                        <a href="{{ route('blog.index', $key!=='all' ? ['category'=>$key] : []) }}"
                           class="flex items-center justify-between px-3 py-2 rounded-lg hover:bg-brand-50 hover:text-brand-500 text-sm text-gray-600 transition-colors {{ $post->category===$key ? 'bg-brand-50 text-brand-500 font-semibold' : '' }}">
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

<!-- Footer -->
<footer class="bg-gray-900 text-white py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="border-t border-gray-800 pt-8 text-center text-gray-500 text-sm">
            <p>&copy; {{ date('Y') }} {{ $schoolSettings->school_name ?? 'Madrasah' }}. All rights reserved.
                &mdash; <a href="{{ route('blog.index') }}" class="hover:text-white">Blog</a>
            </p>
        </div>
    </div>
</footer>
</body>
</html>
