<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Gallery;
use App\Models\GalleryItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class GalleryController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->get('status', 'all');
        $search = $request->get('search', '');

        $query = Gallery::withCount('items')->latest();

        if ($status !== 'all') {
            $query->where('status', $status);
        }
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', '%' . $search . '%')
                  ->orWhere('description', 'like', '%' . $search . '%');
            });
        }

        $galleries = $query->paginate(15)->withQueryString();

        $stats = [
            'total'     => Gallery::count(),
            'published' => Gallery::where('status', 'published')->count(),
            'draft'     => Gallery::where('status', 'draft')->count(),
        ];

        return view('admin.gallery.index', compact('galleries', 'stats', 'status', 'search'));
    }

    public function create()
    {
        return view('admin.gallery.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'cover_color' => 'required|string|regex:/^#[0-9a-f]{6}$/i',
            'cover_icon'  => 'required|string',
            'status'      => 'required|in:draft,published',
        ]);

        $validated['title'] = Gallery::generateTitle($validated['title']);

        if ($validated['status'] === 'published') {
            $validated['uploaded_at'] = now();
        }

        $gallery = Gallery::create($validated);

        return redirect()->route('admin.gallery.edit', $gallery)
            ->with('success', 'Gallery created successfully!');
    }

    public function edit(Gallery $gallery)
    {
        $gallery->load('items');
        return view('admin.gallery.edit', compact('gallery'));
    }

    public function update(Request $request, Gallery $gallery)
    {
        $validated = $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'cover_color' => 'required|string|regex:/^#[0-9a-f]{6}$/i',
            'cover_icon'  => 'required|string',
            'status'      => 'required|in:draft,published',
        ]);

        $validated['title'] = Gallery::generateTitle($validated['title']);

        if ($validated['status'] === 'published' && $gallery->status === 'draft') {
            $validated['uploaded_at'] = now();
        }

        $gallery->update($validated);

        return redirect()->route('admin.gallery.index')
            ->with('success', 'Gallery updated successfully!');
    }

    public function destroy(Gallery $gallery)
    {
        // Delete all images
        foreach ($gallery->items as $item) {
            if ($item->image_path && Storage::disk('public')->exists($item->image_path)) {
                Storage::disk('public')->delete($item->image_path);
            }
            $item->delete();
        }

        $gallery->delete();

        return redirect()->route('admin.gallery.index')
            ->with('success', 'Gallery deleted successfully.');
    }

    public function toggleStatus(Gallery $gallery)
    {
        if ($gallery->status === 'draft') {
            $gallery->update(['status' => 'published', 'uploaded_at' => now()]);
            $message = 'Gallery published!';
        } else {
            $gallery->update(['status' => 'draft']);
            $message = 'Gallery moved to draft.';
        }

        return redirect()->route('admin.gallery.index')
            ->with('success', $message);
    }

    public function uploadImage(Request $request, Gallery $gallery)
    {
        $validated = $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,webp,gif|max:5120',
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:500',
        ]);

        $ext = $request->file('image')->getClientOriginalExtension();
        $name = 'gallery-images/img_' . time() . '_' . Str::random(6) . '.' . $ext;
        $request->file('image')->storeAs('', $name, 'public');

        $maxOrder = $gallery->items()->max('sort_order') ?? 0;

        GalleryItem::create([
            'gallery_id' => $gallery->id,
            'image_path' => $name,
            'title' => $validated['title'] ?? null,
            'description' => $validated['description'] ?? null,
            'sort_order' => $maxOrder + 1,
            'is_visible' => true,
            'uploaded_at' => now(),
        ]);

        return back()->with('success', 'Image uploaded successfully!');
    }

    public function deleteImage(GalleryItem $item)
    {
        $gallery = $item->gallery;

        if ($item->image_path && Storage::disk('public')->exists($item->image_path)) {
            Storage::disk('public')->delete($item->image_path);
        }

        $item->delete();

        return back()->with('success', 'Image deleted successfully!');
    }

    public function reorderImages(Request $request, Gallery $gallery)
    {
        $validated = $request->validate([
            'items' => 'required|array',
            'items.*.id' => 'required|exists:gallery_items,id',
            'items.*.sort_order' => 'required|integer|min:0',
        ]);

        foreach ($validated['items'] as $itemData) {
            GalleryItem::find($itemData['id'])->update(['sort_order' => $itemData['sort_order']]);
        }

        return response()->json(['success' => true, 'message' => 'Images reordered successfully!']);
    }

    public function toggleImageVisibility(GalleryItem $item)
    {
        $item->update(['is_visible' => !$item->is_visible]);

        $message = $item->is_visible ? 'Image shown!' : 'Image hidden!';

        return back()->with('success', $message);
    }
}
