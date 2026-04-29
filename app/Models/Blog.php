<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Blog extends Model
{
    protected $fillable = [
        'author_id', 'title', 'slug', 'category', 'type',
        'cover_color', 'cover_icon', 'excerpt', 'body',
        'featured_image', 'youtube_video_id', 'status', 'published_at',
    ];

    protected $casts = [
        'published_at' => 'datetime',
    ];

    // ─── Relationships ───────────────────────────────────────────────────────────

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    // ─── Scopes ──────────────────────────────────────────────────────────────────

    public function scopePublished($query)
    {
        return $query->where('status', 'published')
                     ->whereNotNull('published_at')
                     ->orderByDesc('published_at');
    }

    // ─── Helpers ─────────────────────────────────────────────────────────────────

    public static function generateSlug(string $title): string
    {
        $slug = Str::slug($title);
        $count = static::where('slug', 'like', "{$slug}%")->count();
        return $count ? "{$slug}-{$count}" : $slug;
    }

    public function getReadingTimeAttribute(): int
    {
        $words = str_word_count(strip_tags($this->body));
        return max(1, (int) ceil($words / 200));
    }

    public function getCategoryLabelAttribute(): string
    {
        return match($this->category) {
            'news'    => 'News',
            'islamic' => 'Islamic Studies',
            'events'  => 'Events',
            'tips'    => 'Study Tips',
            default   => ucfirst($this->category),
        };
    }

    public function getCategoryColorAttribute(): string
    {
        return match($this->category) {
            'news'    => 'bg-brand-50 text-brand-500',
            'islamic' => 'bg-green-50 text-green-700',
            'events'  => 'bg-purple-50 text-purple-700',
            'tips'    => 'bg-amber-50 text-amber-700',
            default   => 'bg-gray-100 text-gray-600',
        };
    }

    public function getYoutubeEmbedUrlAttribute(): ?string
    {
        if (!$this->youtube_video_id) {
            return null;
        }
        return "https://www.youtube.com/embed/{$this->youtube_video_id}";
    }
}
