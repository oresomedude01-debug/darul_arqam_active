<?php

namespace App\Services;

use App\Models\Blog;
use Illuminate\Support\Facades\Cache;

class BlogCacheService
{
    private const CACHE_PREFIX    = 'blog_';
    private const CACHE_DURATION  = 3600; // 1 hour
    private const LIST_CACHE_KEY  = 'blog_list_all';
    private const CATEGORIES_KEY  = 'blog_categories';

    /**
     * Key used to track all cache keys belonging to the blog group.
     * This lets us flush all blog caches without tagging support.
     */
    private const REGISTRY_KEY = 'blog_cache_registry';

    // ─── Public API ──────────────────────────────────────────────────────────

    /**
     * Get all published blog posts with caching.
     */
    public function getPublishedPosts($category = 'all')
    {
        $cacheKey = $category === 'all'
            ? self::LIST_CACHE_KEY
            : self::CACHE_PREFIX . 'category_' . $category;

        return $this->remember($cacheKey, function () use ($category) {
            $query = Blog::published();
            if ($category !== 'all') {
                $query->where('category', $category);
            }
            return $query->get();
        });
    }

    /**
     * Get a single blog post by slug with caching.
     */
    public function getPostBySlug($slug)
    {
        $cacheKey = self::CACHE_PREFIX . 'slug_' . $slug;

        return $this->remember($cacheKey, function () use ($slug) {
            return Blog::published()
                ->where('slug', $slug)
                ->with('author')
                ->firstOrFail();
        });
    }

    /**
     * Get related posts for a given post.
     */
    public function getRelatedPosts(Blog $post, $limit = 3)
    {
        $cacheKey = self::CACHE_PREFIX . 'related_' . $post->id;

        return $this->remember($cacheKey, function () use ($post, $limit) {
            return Blog::published()
                ->where('category', $post->category)
                ->where('id', '!=', $post->id)
                ->take($limit)
                ->get();
        });
    }

    /**
     * Get blog categories with caching.
     */
    public function getCategories()
    {
        return $this->remember(self::CATEGORIES_KEY, function () {
            return Blog::published()
                ->distinct('category')
                ->pluck('category')
                ->toArray();
        });
    }

    /**
     * Get fresh posts without caching (for admin).
     */
    public function getFreshPosts($category = 'all')
    {
        $query = Blog::published();
        if ($category !== 'all') {
            $query->where('category', $category);
        }
        return $query->get();
    }

    // ─── Cache Invalidation ───────────────────────────────────────────────────

    /**
     * Invalidate cache for a specific blog post.
     */
    public function invalidatePost(Blog $post)
    {
        $this->forget(self::CACHE_PREFIX . 'slug_' . $post->slug);
        $this->forget(self::CACHE_PREFIX . 'related_' . $post->id);
    }

    /**
     * Invalidate all blog list / category caches.
     */
    public function invalidateLists()
    {
        $this->forget(self::LIST_CACHE_KEY);
        $this->forget(self::CATEGORIES_KEY);

        // Forget every category variant tracked in the registry
        $registry = Cache::get(self::REGISTRY_KEY, []);
        foreach ($registry as $key) {
            if (str_contains($key, 'category_')) {
                Cache::forget($key);
            }
        }
    }

    /**
     * Invalidate a single category cache.
     */
    public function invalidateCategory($category)
    {
        $this->forget(self::CACHE_PREFIX . 'category_' . $category);
    }

    /**
     * Invalidate ALL blog-related caches (uses registry, not Cache::flush()).
     * Cache::flush() would wipe session/other data on shared stores.
     */
    public function invalidateAll()
    {
        $registry = Cache::get(self::REGISTRY_KEY, []);

        foreach ($registry as $key) {
            Cache::forget($key);
        }

        // Clear the registry itself
        Cache::forget(self::REGISTRY_KEY);
    }

    // ─── Internals ────────────────────────────────────────────────────────────

    /**
     * Remember a value and track its key in the registry.
     */
    private function remember(string $key, callable $callback)
    {
        $this->registerKey($key);
        return Cache::remember($key, self::CACHE_DURATION, $callback);
    }

    /**
     * Forget a key and remove it from the registry.
     */
    private function forget(string $key): void
    {
        Cache::forget($key);
        $this->deregisterKey($key);
    }

    /**
     * Add a key to the blog cache registry.
     */
    private function registerKey(string $key): void
    {
        $registry = Cache::get(self::REGISTRY_KEY, []);
        if (!in_array($key, $registry, true)) {
            $registry[] = $key;
            Cache::put(self::REGISTRY_KEY, $registry, self::CACHE_DURATION * 24);
        }
    }

    /**
     * Remove a key from the blog cache registry.
     */
    private function deregisterKey(string $key): void
    {
        $registry = Cache::get(self::REGISTRY_KEY, []);
        $registry = array_values(array_filter($registry, fn($k) => $k !== $key));
        Cache::put(self::REGISTRY_KEY, $registry, self::CACHE_DURATION * 24);
    }
}
