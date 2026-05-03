<?php

namespace App\Models;

use App\Jobs\SendBlogNotifications;
use App\Mail\NewBlogMail;
use App\Notifications\NewBlogNotification;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class Blog extends Model
{
    protected $fillable = [
        'author_id', 'title', 'slug', 'category', 'type',
        'cover_color', 'cover_icon', 'excerpt', 'body',
        'featured_image', 'youtube_video_id', 'status', 'published_at', 'view_count',
    ];

    protected $casts = [
        'published_at' => 'datetime',
    ];

    // ─── Mutators ────────────────────────────────────────────────────────────────

    public function setBodyAttribute($value)
    {
        // Sanitize HTML to prevent XSS while preserving formatting
        $allowed_tags = '<p><br><strong><b><em><i><u><s><del><a><ul><ol><li><h1><h2><h3><h4><h5><h6><blockquote><code><pre><hr><img><table><thead><tbody><tr><td><th><span>';
        $this->attributes['body'] = strip_tags($value, $allowed_tags);
    }

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

    public function incrementViewCount(): void
    {
        $this->increment('view_count');
    }

    public function getYoutubeEmbedUrlAttribute(): ?string
    {
        if (!$this->youtube_video_id) {
            return null;
        }
        return "https://www.youtube.com/embed/{$this->youtube_video_id}";
    }

    // ─── Model Events ────────────────────────────────────────────────────────────

    protected static function booted(): void
    {
        static::saved(function (self $blog) {
            // Dispatch job to send notifications asynchronously when a blog is created and published
            if ($blog->wasRecentlyCreated && $blog->status === 'published') {
                SendBlogNotifications::dispatch($blog);
            }
        });
    }

    private static function notifyAllUsers(self $blog): void
    {
        // This method is now called via the SendBlogNotifications job
        // Get all users
        $users = User::all();

        foreach ($users as $user) {
            // Send in-app and push notifications to all users
            $user->notify(new NewBlogNotification($blog));

            // Send email notification to all users EXCEPT students
            if (!empty($user->email) && !$user->hasRole('student')) {
                Mail::to($user->email)->send(new NewBlogMail($blog));
            }
        }
    }
}
