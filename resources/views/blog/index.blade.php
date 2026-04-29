<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @php $schoolSettings = \App\Models\SchoolSetting::getInstance(); @endphp
    <title>Blog - {{ $schoolSettings->school_name ?? 'Madrasah' }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script>tailwind.config={theme:{extend:{colors:{brand:{50:'#e6f0f5',500:'#0B4D73',600:'#093e5c'},warm:{50:'#fdfbf7'}}}}}</script>
    <style>
        *{font-family:'Inter',system-ui,sans-serif}html{scroll-behavior:smooth}
        @keyframes fadeInUp{from{opacity:0;transform:translateY(20px)}to{opacity:1;transform:translateY(0)}}
        .animate-fade-in-up{animation:fadeInUp .6s ease-out forwards}
        .blog-card{transition:all .35s ease}.blog-card:hover{transform:translateY(-4px);box-shadow:0 20px 40px rgba(11,77,115,.12)}
        .blog-card:hover .card-img{transform:scale(1.05)}.card-img{transition:transform .5s ease}
        .islamic-pattern{background-image:url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M30 0L60 30L30 60L0 30z' fill='none' stroke='%23ffffff' stroke-width='0.5' opacity='0.1'/%3E%3C/svg%3E")}
    </style>
</head>
<body class="antialiased bg-warm-50 text-gray-800">

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

<section class="bg-brand-500 pt-24 pb-16 lg:pt-32 lg:pb-20 relative overflow-hidden">
    <div class="absolute inset-0 islamic-pattern"></div>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 max-w-3xl">
        <nav class="text-sm mb-6">
            <a href="{{ url('/') }}" class="text-white/70 hover:text-white">Home</a>
            <span class="text-white/50 mx-2">/</span>
            <span class="text-white">Blog</span>
        </nav>
        <h1 class="animate-fade-in-up text-4xl lg:text-5xl font-extrabold text-white mb-4">Our Blog</h1>
        <p class="animate-fade-in-up text-lg text-white/85">News, reflections and updates from {{ $schoolSettings->school_name ?? 'our Madrasah' }}.</p>
    </div>
</section>

<section class="py-6 bg-white border-b sticky top-16 z-40">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-wrap gap-3 justify-center">
        @foreach(['all'=>'All','news'=>'News','islamic'=>'Islamic Studies','events'=>'Events','tips'=>'Study Tips'] as $key=>$label)
            <a href="{{ route('blog.index', $key!=='all' ? ['category'=>$key] : []) }}"
               class="px-5 py-2 rounded-full text-sm font-medium transition-all {{ $category===$key ? 'bg-brand-500 text-white' : 'bg-gray-100 text-gray-700 hover:bg-brand-500 hover:text-white' }}">
                {{ $label }}
            </a>
        @endforeach
    </div>
</section>

<section class="py-12 lg:py-20 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        @if($posts->isEmpty())
            <div class="text-center py-24">
                <i class="fas fa-newspaper text-gray-300 text-6xl mb-4"></i>
                <h3 class="text-xl font-bold text-gray-500">No posts yet</h3>
                <p class="text-gray-400 mt-2">Check back soon!</p>
            </div>
        @else
            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                @foreach($posts as $post)
                    <article class="blog-card bg-white rounded-2xl overflow-hidden shadow-md relative">
                        <a href="{{ route('blog.show', $post->slug) }}" class="block overflow-hidden h-48 bg-gradient-to-br {{ $post->cover_color }} flex items-center justify-center relative">
                            <i class="{{ $post->cover_icon }} card-img text-white/30 text-7xl"></i>
                            @if($post->type === 'video')
                                <div class="absolute inset-0 flex items-center justify-center">
                                    <div class="w-14 h-14 bg-white/20 backdrop-blur-sm rounded-full flex items-center justify-center">
                                        <i class="fas fa-play text-white text-xl ml-1"></i>
                                    </div>
                                </div>
                            @endif
                        </a>
                        <div class="p-6">
                        <span class="inline-block px-3 py-1 rounded-full text-xs font-semibold uppercase tracking-wide {{ $post->category_color }}">{{ $post->category_label }}</span>
                        <h2 class="mt-3 text-lg font-bold text-gray-800 leading-snug line-clamp-2">
                            <a href="{{ route('blog.show', $post->slug) }}" class="hover:text-brand-500 transition-colors">{{ $post->title }}</a>
                        </h2>
                        <p class="mt-2 text-gray-500 text-sm line-clamp-3">{{ $post->excerpt }}</p>
                        <div class="mt-4 flex items-center justify-between text-xs text-gray-400">
                            <span><i class="fas fa-calendar-alt mr-1"></i> {{ $post->published_at->format('M Y') }}</span>
                            <span><i class="fas fa-clock mr-1"></i> {{ $post->reading_time }} min read</span>
                        </div>
                        <a href="{{ route('blog.show', $post->slug) }}" class="mt-4 inline-flex items-center gap-1 text-brand-500 font-semibold text-sm hover:gap-2 transition-all">
                            Read more <i class="fas fa-arrow-right text-xs"></i>
                        </a>
                    </div>
                </article>
                @endforeach
            </div>
        @endif
    </div>
</section>

<section class="py-16 bg-brand-500 relative overflow-hidden">
    <div class="absolute inset-0 islamic-pattern"></div>
    <div class="max-w-4xl mx-auto px-4 text-center relative z-10">
        <h2 class="text-3xl font-bold text-white mb-4">Stay Updated</h2>
        <p class="text-white/85 mb-8">Follow us on social media for the latest news and updates.</p>
        <div class="flex justify-center gap-4">
            <a href="#" class="inline-flex items-center gap-2 bg-white text-brand-500 font-bold px-6 py-3 rounded-xl hover:shadow-xl transition-all"><i class="fab fa-facebook-f"></i> Facebook</a>
            <a href="#" class="inline-flex items-center gap-2 bg-white text-brand-500 font-bold px-6 py-3 rounded-xl hover:shadow-xl transition-all"><i class="fab fa-instagram"></i> Instagram</a>
        </div>
    </div>
</section>

<footer class="bg-gray-900 text-white py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid md:grid-cols-4 gap-8 mb-8">
            <div>
                <div class="flex items-center gap-3 mb-4">
                    @if($schoolSettings->school_logo)
                        <img src="{{ asset('storage/'.$schoolSettings->school_logo) }}" class="h-10 w-10 rounded-lg object-cover">
                    @else
                        <div class="h-10 w-10 bg-brand-500 rounded-lg flex items-center justify-center"><i class="fas fa-mosque text-white"></i></div>
                    @endif
                    <span class="font-bold">{{ $schoolSettings->school_name ?? 'Madrasah' }}</span>
                </div>
                <p class="text-gray-400 text-sm">{{ $schoolSettings->school_motto ?? 'Excellence in Islamic Education' }}</p>
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
                    <a href="#" class="w-10 h-10 bg-gray-800 rounded-lg flex items-center justify-center hover:bg-brand-500"><i class="fab fa-facebook-f"></i></a>
                    <a href="#" class="w-10 h-10 bg-gray-800 rounded-lg flex items-center justify-center hover:bg-brand-500"><i class="fab fa-instagram"></i></a>
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
