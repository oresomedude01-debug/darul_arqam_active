<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use App\Services\BlogCacheService;
use Illuminate\Http\Request;

class BlogController extends Controller
{
    private BlogCacheService $cacheService;

    public function __construct(BlogCacheService $cacheService)
    {
        $this->cacheService = $cacheService;
    }

    public function index(Request $request)
    {
        $category = $request->get('category', 'all');
        $posts = $this->cacheService->getPublishedPosts($category);

        return view('blog.index', compact('posts', 'category'));
    }

    public function show(string $slug)
    {
        $post = $this->cacheService->getPostBySlug($slug);
        $related = $this->cacheService->getRelatedPosts($post, 3);

        // Increment view count
        if ($post) {
            $post->incrementViewCount();
        }

        return view('blog.show', compact('post', 'related'));
    }
}
