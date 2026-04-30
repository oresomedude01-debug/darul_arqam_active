<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use App\Services\BlogCacheService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class BlogController extends Controller
{
    private BlogCacheService $cacheService;

    public function __construct(BlogCacheService $cacheService)
    {
        $this->cacheService = $cacheService;
    }

    public function index(Request $request)
    {
        $status   = $request->get('status', 'all');
        $category = $request->get('category', 'all');
        $search   = $request->get('search', '');

        $query = Blog::with('author')->latest();

        if ($status !== 'all') {
            $query->where('status', $status);
        }
        if ($category !== 'all') {
            $query->where('category', $category);
        }
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', '%' . $search . '%')
                  ->orWhere('excerpt', 'like', '%' . $search . '%');
            });
        }

        $posts = $query->paginate(15)->withQueryString();

        $stats = [
            'total'     => Blog::count(),
            'published' => Blog::where('status', 'published')->count(),
            'draft'     => Blog::where('status', 'draft')->count(),
        ];

        return view('admin.blog.index', compact('posts', 'stats', 'status', 'category', 'search'));
    }

    public function create()
    {
        return view('admin.blog.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'            => 'required|string|max:255',
            'category'         => 'required|in:news,islamic,events,tips',
            'type'             => 'required|in:article,video',
            'youtube_video_id' => 'nullable|string|max:50',
            'cover_color'      => 'required|string',
            'cover_icon'       => 'required|string',
            'excerpt'          => 'required|string|max:500',
            'body'             => 'required|string',
            'status'           => 'required|in:draft,published',
            'featured_image'   => 'nullable|image|mimes:jpeg,png,jpg,webp|max:3072',
        ]);

        $validated['slug']      = Blog::generateSlug($validated['title']);
        $validated['author_id'] = auth()->id();

        if ($validated['status'] === 'published') {
            $validated['published_at'] = now();
        }

        // Handle featured image upload
        if ($request->hasFile('featured_image')) {
            $ext  = $request->file('featured_image')->getClientOriginalExtension();
            $name = 'blog-images/img_' . time() . '_' . Str::random(6) . '.' . $ext;
            $request->file('featured_image')->storeAs('', $name, 'public');
            $validated['featured_image'] = $name;
        }

        $blog = Blog::create($validated);

        // Invalidate cache after creating published post
        if ($blog->status === 'published') {
            $this->cacheService->invalidateLists();
            $this->cacheService->invalidateCategory($blog->category);
        }

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
            'title'            => 'required|string|max:255',
            'category'         => 'required|in:news,islamic,events,tips',
            'type'             => 'required|in:article,video',
            'youtube_video_id' => 'nullable|string|max:50',
            'cover_color'      => 'required|string',
            'cover_icon'       => 'required|string',
            'excerpt'          => 'required|string|max:500',
            'body'             => 'required|string',
            'status'           => 'required|in:draft,published',
            'featured_image'   => 'nullable|image|mimes:jpeg,png,jpg,webp|max:3072',
            'remove_image'     => 'nullable|boolean',
        ]);

        // Set published_at only when first publishing
        if ($validated['status'] === 'published' && $blog->status === 'draft') {
            $validated['published_at'] = now();
        }

        // Regenerate slug if title changed
        if ($validated['title'] !== $blog->title) {
            $validated['slug'] = Blog::generateSlug($validated['title']);
        }

        // Handle featured image removal
        if ($request->boolean('remove_image') && $blog->featured_image) {
            Storage::disk('public')->delete($blog->featured_image);
            $validated['featured_image'] = null;
        }

        // Handle new featured image upload
        if ($request->hasFile('featured_image')) {
            // Delete old image
            if ($blog->featured_image) {
                Storage::disk('public')->delete($blog->featured_image);
            }
            $ext  = $request->file('featured_image')->getClientOriginalExtension();
            $name = 'blog-images/img_' . time() . '_' . Str::random(6) . '.' . $ext;
            $request->file('featured_image')->storeAs('', $name, 'public');
            $validated['featured_image'] = $name;
        }

        $oldCategory = $blog->category;
        $blog->update($validated);

        // Invalidate cache after update
        $this->cacheService->invalidatePost($blog);
        $this->cacheService->invalidateLists();

        // If category changed, invalidate both old and new category caches
        if ($oldCategory !== $blog->category) {
            $this->cacheService->invalidateCategory($oldCategory);
            $this->cacheService->invalidateCategory($blog->category);
        } else {
            $this->cacheService->invalidateCategory($blog->category);
        }

        return redirect()->route('admin.blog.index')
            ->with('success', 'Post updated successfully!');
    }

    public function destroy(Blog $blog)
    {
        $category = $blog->category;

        // Delete featured image from storage
        if ($blog->featured_image) {
            Storage::disk('public')->delete($blog->featured_image);
        }

        $blog->delete();

        // Invalidate cache after deletion
        $this->cacheService->invalidatePost($blog);
        $this->cacheService->invalidateLists();
        $this->cacheService->invalidateCategory($category);

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

        // Invalidate cache
        $this->cacheService->invalidatePost($blog);
        $this->cacheService->invalidateLists();
        $this->cacheService->invalidateCategory($blog->category);

        return back()->with('success', $message);
    }
}
