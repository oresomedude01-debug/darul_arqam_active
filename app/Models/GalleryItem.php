<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GalleryItem extends Model
{
    protected $fillable = [
        'gallery_id', 'title', 'image_path', 'description',
        'sort_order', 'is_visible', 'uploaded_at',
    ];

    protected $casts = [
        'is_visible' => 'boolean',
        'uploaded_at' => 'datetime',
    ];

    // ─── Relationships ───────────────────────────────────────────────────────────

    public function gallery(): BelongsTo
    {
        return $this->belongsTo(Gallery::class);
    }

    // ─── Scopes ──────────────────────────────────────────────────────────────────

    public function scopeVisible($query)
    {
        return $query->where('is_visible', true)->orderBy('sort_order');
    }

    // ─── Helpers ─────────────────────────────────────────────────────────────────

    public function getImageUrl(): string
    {
        return asset('storage/' . $this->image_path);
    }

    public function getDisplayTitle(): string
    {
        return $this->title ?? 'Gallery Image';
    }
}
