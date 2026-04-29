<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class BlogController extends Controller
{
    public function index(Request $request)
    {
        $status   = $request->get('status', 'all');
        $category = $request->get('category', 'all');

        $query = Blog::with('author')->latest();

        if ($status !== 'all') {
            $query->where('status', $status);
        }
        if ($category !== 'all') {
            $query->where('category', $category);
        }

        $posts = $query->paginate(15);

        $stats = [
            'total'     => Blog::count(),
            'published' => Blog::where('status', 'published')->count(),
            'draft'     => Blog::where('status', 'draft')->count(),
        ];

        return view('admin.blog.index', compact('posts', 'stats', 'status', 'category'));
    }

    public function create()
    {
        return view('admin.blog.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'       => 'required|string|max:255',
            'category'    => 'required|in:news,islamic,events,tips',
            'type'        => 'required|in:article,video',
            'youtube_video_id' => 'nullable|string|max:50',
            'cover_color' => 'required|string',
            'cover_icon'  => 'required|string',
            'excerpt'     => 'required|string|max:500',
            'body'        => 'required|string',
            'status'      => 'required|in:draft,published',
        ]);

        $validated['slug']      = Blog::generateSlug($validated['title']);
        $validated['author_id'] = auth()->id();

        if ($validated['status'] === 'published') {
            $validated['published_at'] = now();
        }

        Blog::create($validated);

        return redirect()->route('admin.blog.index')
            ->with('success', 'Post created successfully!');
    }

    public function edit(Blog $blog)
    {
        return view('admin.blog.edit', compact('blog'));
    }

    public function update(Request $request, Blog $blog)
    {
        $validated = $request->validate([
            'title'       => 'required|string|max:255',
            'category'    => 'required|in:news,islamic,events,tips',
            'type'        => 'required|in:article,video',
            'youtube_video_id' => 'nullable|string|max:50',
            'cover_color' => 'required|string',
            'cover_icon'  => 'required|string',
            'excerpt'     => 'required|string|max:500',
            'body'        => 'required|string',
            'status'      => 'required|in:draft,published',
        ]);

        // Set published_at only when first publishing
        if ($validated['status'] === 'published' && $blog->status === 'draft') {
            $validated['published_at'] = now();
        }

        // Regenerate slug if title changed
        if ($validated['title'] !== $blog->title) {
            $validated['slug'] = Blog::generateSlug($validated['title']);
        }

        $blog->update($validated);

        return redirect()->route('admin.blog.index')
            ->with('success', 'Post updated successfully!');
    }

    public function destroy(Blog $blog)
    {
        $blog->delete();
        return redirect()->route('admin.blog.index')
            ->with('success', 'Post deleted.');
    }

    public function toggleStatus(Blog $blog)
    {
        if ($blog->status === 'draft') {
            $blog->update(['status' => 'published', 'published_at' => now()]);
            $message = 'Post published!';
        } else {
            $blog->update(['status' => 'draft']);
            $message = 'Post moved to draft.';
        }

        return back()->with('success', $message);
    }
}
