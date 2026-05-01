<?php

namespace App\Notifications;

use App\Models\Blog;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\DatabaseMessage;

class NewBlogNotification extends Notification
{
    use Queueable;

    public function __construct(
        public readonly Blog $blog
    ) {}

    /**
     * Channels: database (in-app) + optionally fcm (push)
     */
    public function via(object $notifiable): array
    {
        $channels = ['database'];

        // Add FCM push channel if the user has a device token stored
        if (!empty($notifiable->fcm_token)) {
            $channels[] = 'fcm_push';
        }

        return $channels;
    }

    /**
     * In-app (database) notification payload
     */
    public function toDatabase(object $notifiable): array
    {
        return [
            'type'    => 'new_blog',
            'title'   => 'New Blog Post',
            'message' => "New blog post: {$this->blog->title}",
            'blog_id' => $this->blog->id,
            'category' => $this->blog->category,
            'excerpt' => $this->blog->excerpt,
            'author_name' => $this->blog->author->name ?? 'Admin',
        ];
    }

    /**
     * FCM push notification payload
     */
    public function toFcmPush(object $notifiable): array
    {
        return [
            'fcmToken' => $notifiable->fcm_token,
            'title'    => 'New Blog Post',
            'body'     => $this->blog->title,
            'data'     => [
                'type'    => 'new_blog',
                'blog_id' => $this->blog->id,
                'category' => $this->blog->category,
            ],
        ];
    }
}
