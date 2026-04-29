<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use Illuminate\Http\Request;

class BlogController extends Controller
{
    public function index(Request $request)
    {
        $category = $request->get('category', 'all');

        $query = Blog::published();
        if ($category && $category !== 'all') {
            $query->where('category', $category);
        }

        $posts = $query->get();

        return view('blog.index', compact('posts', 'category'));
    }

    public function show(string $slug)
    {
        $post = Blog::published()->where('slug', $slug)->firstOrFail();

        $related = Blog::published()
            ->where('category', $post->category)
            ->where('id', '!=', $post->id)
            ->take(3)
            ->get();

        return view('blog.show', compact('post', 'related'));
    }
}
