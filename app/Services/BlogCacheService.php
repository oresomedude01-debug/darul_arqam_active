<?php

namespace App\Services;

use App\Models\Blog;
use Illuminate\Support\Facades\Cache;

class BlogCacheService
{
    private const CACHE_PREFIX = 'blog_';
    private const CACHE_DURATION = 3600; // 1 hour
    private const LIST_CACHE_KEY = 'blog_list_all';
    private const CATEGORIES_CACHE_KEY = 'blog_categories';

    /**
     * Get all published blog posts with caching
     */
    public function getPublishedPosts($category = 'all')
    {
        $cacheKey = $category === 'all' 
            ? self::LIST_CACHE_KEY 
            : self::CACHE_PREFIX . 'category_' . $category;

        return Cache::remember($cacheKey, self::CACHE_DURATION, function () use ($category) {
            $query = Blog::published();
            
            if ($category !== 'all') {
                $query->where('category', $category);
            }
            
            return $query->get();
        });
    }

    /**
     * Get a single blog post by slug with caching
     */
    public function getPostBySlug($slug)
    {
        $cacheKey = self::CACHE_PREFIX . 'slug_' . $slug;

        return Cache::remember($cacheKey, self::CACHE_DURATION, function () use ($slug) {
            return Blog::published()
                ->where('slug', $slug)
                ->with('author')
                ->firstOrFail();
        });
    }

    /**
     * Get related posts for a given post
     */
    public function getRelatedPosts(Blog $post, $limit = 3)
    {
        $cacheKey = self::CACHE_PREFIX . 'related_' . $post->id;

        return Cache::remember($cacheKey, self::CACHE_DURATION, function () use ($post, $limit) {
            return Blog::published()
                ->where('category', $post->category)
                ->where('id', '!=', $post->id)
                ->take($limit)
                ->get();
        });
    }

    /**
     * Get blog categories with caching
     */
    public function getCategories()
    {
        return Cache::remember(self::CATEGORIES_CACHE_KEY, self::CACHE_DURATION, function () {
            return Blog::published()
                ->distinct('category')
                ->pluck('category')
                ->toArray();
        });
    }

    /**
     * Invalidate specific blog post cache
     */
    public function invalidatePost(Blog $post)
    {
        Cache::forget(self::CACHE_PREFIX . 'slug_' . $post->slug);
        Cache::forget(self::CACHE_PREFIX . 'related_' . $post->id);
    }

    /**
     * Invalidate all blog list caches
     */
    public function invalidateLists()
    {
        Cache::forget(self::LIST_CACHE_KEY);
        Cache::tags(['blog'])->flush();
    }

    /**
     * Invalidate category cache
     */
    public function invalidateCategory($category)
    {
        Cache::forget(self::CACHE_PREFIX . 'category_' . $category);
    }

    /**
     * Invalidate all blog caches (nuclear option)
     */
    public function invalidateAll()
    {
        $this->invalidateLists();
        Cache::forget(self::CATEGORIES_CACHE_KEY);
        // Invalidate all blog-related keys
        Cache::flush();
    }

    /**
     * Get fresh posts without caching (for admin)
     */
    public function getFreshPosts($category = 'all')
    {
        $query = Blog::published();
        
        if ($category !== 'all') {
            $query->where('category', $category);
        }
        
        return $query->get();
    }
}
