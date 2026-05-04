<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Gallery extends Model
{
    protected $fillable = [
        'title', 'description', 'cover_color', 'cover_icon',
        'status', 'uploaded_at', 'view_count',
    ];

    protected $casts = [
        'uploaded_at' => 'datetime',
    ];

    // ─── Relationships ───────────────────────────────────────────────────────────

    public function items()
    {
        return $this->hasMany(GalleryItem::class);
    }

    // ─── Scopes ──────────────────────────────────────────────────────────────────

    public function scopePublished($query)
    {
        return $query->where('status', 'published')
                     ->whereNotNull('uploaded_at')
                     ->orderByDesc('uploaded_at');
    }

    // ─── Helpers ─────────────────────────────────────────────────────────────────

    public static function generateTitle(string $title): string
    {
        return ucfirst(trim($title));
    }

    public function getStatusBadgeClass(): string
    {
        return match ($this->status) {
            'published' => 'bg-green-50 text-green-700',
            'draft' => 'bg-amber-50 text-amber-700',
            default => 'bg-gray-50 text-gray-700',
        };
    }

    public function getStatusBadgeIcon(): string
    {
        return match ($this->status) {
            'published' => '✅',
            'draft' => '📝',
            default => '📸',
        };
    }

    public function getItemsCount(): int
    {
        return $this->items()->count();
    }

    public function getPublishedItemsCount(): int
    {
        return $this->items()->where('is_visible', true)->count();
    }
}
