# Quick Reference - Professional Improvements

## 🚀 Quick Start

### Initialize Everything
```bash
# Clear all caches
php artisan optimize:clear

# Verify migrations (creates sessions table if needed)
php artisan migrate

# Test it's working
php artisan tinker
>>> auth('web')->check()
```

---

## 🎯 Quick API Reference

### PWA Authentication API

#### Login
```bash
curl -X POST http://localhost:8000/api/pwa/auth/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "user@example.com",
    "password": "password"
  }'

# Response
{
  "success": true,
  "message": "Login successful",
  "user": {
    "id": 1,
    "name": "John Doe",
    "email": "user@example.com"
  },
  "expiresIn": 2592000
}
```

#### Get Current User
```bash
curl http://localhost:8000/api/pwa/auth/me \
  -H "Cookie: pwa_auth_token=..." \
  --cookie-jar - \
  --cookie-variable cookies
```

#### Refresh Token
```bash
curl -X POST http://localhost:8000/api/pwa/auth/refresh \
  -H "Cookie: pwa_refresh_token=..."
```

#### Logout
```bash
curl -X POST http://localhost:8000/api/pwa/auth/logout
```

#### Invalidate Cache (Admin Only)
```bash
curl -X POST http://localhost:8000/api/pwa/cache/invalidate \
  -H "Content-Type: application/json" \
  -d '{"type": "blog"}' \
  -H "Cookie: pwa_auth_token=..."
```

---

## 📝 Blog Cache Service Usage

### In Controllers
```php
use App\Services\BlogCacheService;

class MyController extends Controller
{
    public function index(BlogCacheService $cacheService)
    {
        // Get cached posts
        $posts = $cacheService->getPublishedPosts('news');
        
        // Get single post
        $post = $cacheService->getPostBySlug('my-post');
        
        // Get related posts
        $related = $cacheService->getRelatedPosts($post);
    }
}
```

### Manual Cache Invalidation
```php
// Invalidate specific post
$cacheService->invalidatePost($blog);

// Invalidate category
$cacheService->invalidateCategory('news');

// Invalidate all blog lists
$cacheService->invalidateLists();

// Nuclear option - clear everything
$cacheService->invalidateAll();
```

### In Tinker
```bash
php artisan tinker

# Get all published posts
>>> \App\Models\Blog::published()->get()

# Clear specific blog cache
>>> app(\App\Services\BlogCacheService::class)->invalidateCategory('news')

# Get cache stats
>>> cache()->get('blog_list_all')  // null if not cached
```

---

## ⏱️ Session Management

### Check Session Configuration
```bash
php artisan tinker
>>> config('session.lifetime')
120  # Minutes

>>> config('session.driver')
'database'
```

### Manually Invalidate User Session
```bash
php artisan tinker
>>> auth()->logout()

# Or for specific user
>>> \App\Models\User::find(1)->logout()
```

### Clear All Sessions
```bash
php artisan session:flush
```

---

## 🔐 PWA Token Management

### View Token Details
```php
// In controller or tinker
$token = request()->cookie(\App\Services\PWAAuthService::getTokenCookieName());

try {
    $decoded = \Illuminate\Support\Facades\Crypt::decryptString($token);
    dd(json_decode($decoded, true));
} catch (\Exception $e) {
    echo "Invalid token: " . $e->getMessage();
}
```

### Create Test Token
```bash
php artisan tinker

>>> $user = \App\Models\User::find(1)
>>> app(\App\Services\PWAAuthService::class)->generatePersistentToken($user)
```

---

## 🌐 Service Worker Commands

### Clear Service Worker Cache
```javascript
// In browser console
caches.keys().then(names => {
  names.forEach(name => caches.delete(name));
})

// Then reload page
location.reload();
```

### Check Cached URLs
```javascript
caches.keys().then(cacheName => {
  caches.open(cacheName).then(cache => {
    cache.keys().then(requests => {
      requests.forEach(req => console.log(req.url));
    });
  });
});
```

### Disable Service Worker (DevTools)
1. Open DevTools → Application
2. Select Service Workers tab
3. Click "Unregister"
4. Clear storage: Clear site data

---

## 🔍 Debugging

### Check Blog Cache in Database
```bash
php artisan tinker

# Using Redis cache driver
>>> cache()->get('blog_list_all')

# Using database driver
>>> \DB::table('cache')->where('key', 'like', 'blog%')->get()
```

### Monitor Session Activity
```bash
php artisan tinker

# Last activity
>>> \DB::table('sessions')->latest('last_activity')->first()

# Active sessions
>>> \DB::table('sessions')->count()

# Expired sessions (older than 120 mins)
>>> \DB::table('sessions')
    ->where('last_activity', '<', now()->subMinutes(120)->timestamp)
    ->count()
```

### Test Session Expiry Middleware
```php
// In route (for testing only)
Route::get('/test-session-expiry', function () {
    session(['last_activity' => time() - 8000]); // Set to 130 minutes ago
    return 'Session timestamp set. Next request should expire.';
});
```

### Monitor Cache Hits in Browser
```javascript
// Add to service worker for logging
self.addEventListener('fetch', (event) => {
    caches.match(event.request).then(cached => {
        if (cached) {
            console.log(`📦 Cache hit: ${event.request.url}`);
        } else {
            console.log(`🌐 Network: ${event.request.url}`);
        }
    });
});
```

---

## 📊 Performance Testing

### Blog Performance
```javascript
// In browser console on /blog
performance.mark('blog-start');
// ... wait for page load
performance.mark('blog-end');
performance.measure('blog', 'blog-start', 'blog-end');
performance.getEntriesByName('blog')[0];
```

### API Response Time
```bash
curl -w "@curl-format.txt" -o /dev/null -s http://localhost:8000/api/blogs
```

### Service Worker Performance
```javascript
// DevTools → Network
// Filter by: All, Cached, Fetch
// Check Response time column
// Cached should show 0-10ms
```

---

## 🐛 Common Issues & Fixes

### Issue: Blog posts not updating after edit
```bash
# Solution: Clear cache
php artisan cache:clear
php artisan route:clear

# Or via API
POST /api/pwa/cache/invalidate
```

### Issue: Session not expiring
```bash
# Check middleware is registered
grep -n "HandleSessionExpiry" bootstrap/app.php

# Check session driver
php artisan tinker
>>> config('session.driver')  // Should be 'database'

# Verify sessions table
php artisan migrate
```

### Issue: PWA not persisting login
```bash
# Check cookies in DevTools
# Application → Cookies → http://localhost:8000

# Verify HttpOnly flag
# Network tab → Response Headers → Set-Cookie

# Check if using HTTPS in production
# Cookies require secure flag over HTTPS
```

### Issue: Service worker not caching blog
```bash
# Check service worker status
DevTools → Application → Service Workers

# Check cache storage
DevTools → Application → Cache Storage

# Verify routes in service worker
grep -n "BLOG_CACHE_CONFIG" public/service-worker.js
```

---

## 🔧 Maintenance Commands

### Daily
```bash
# Check logs for errors
tail -f storage/logs/laravel.log

# Monitor cache performance
php artisan cache:monitor  # If monitoring enabled
```

### Weekly
```bash
# Clear old sessions
php artisan session:flush

# Prune old cache entries
php artisan cache:prune
```

### Monthly
```bash
# Full optimization
php artisan optimize

# Clear everything
php artisan optimize:clear
php artisan migrate:refresh --seed  # Only in dev!
```

---

## 📚 File Locations

| Purpose | File |
|---------|------|
| Blog Cache Logic | `app/Services/BlogCacheService.php` |
| PWA Authentication | `app/Services/PWAAuthService.php` |
| Session Expiry | `app/Http/Middleware/HandleSessionExpiry.php` |
| PWA API Endpoints | `app/Http/Controllers/Api/PWAAuthController.php` |
| Blog Public Controller | `app/Http/Controllers/BlogController.php` |
| Blog Admin Controller | `app/Http/Controllers/Admin/BlogController.php` |
| Service Worker | `public/service-worker.js` |
| Blog View | `resources/views/blog/index.blade.php` |
| Public Layout | `resources/views/layouts/public.blade.php` |

---

## 📖 Full Documentation

For complete details, see:
- `PROFESSIONAL_IMPROVEMENTS_GUIDE.md` - Comprehensive guide
- `IMPLEMENTATION_SUMMARY.md` - Summary of all changes

---

**Last Updated**: April 30, 2026
**Quick Version**: 1.0
