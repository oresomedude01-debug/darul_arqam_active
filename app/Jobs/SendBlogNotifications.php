<?php

namespace App\Jobs;

use App\Mail\NewBlogMail;
use App\Models\Blog;
use App\Models\User;
use App\Notifications\NewBlogNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendBlogNotifications implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 300;
    public $tries = 3;

    public function __construct(
        public readonly Blog $blog
    ) {}

    public function handle(): void
    {
        // Get all users
        $users = User::all();

        foreach ($users as $user) {
            // Send in-app and push notifications to all users
            $user->notify(new NewBlogNotification($this->blog));

            // Send email notification to all users EXCEPT students
            if (!empty($user->email) && !$user->hasRole('student')) {
                try {
                    Mail::to($user->email)->send(new NewBlogMail($this->blog));
                } catch (\Exception $e) {
                    // Log error but continue with other users
                    \Illuminate\Support\Facades\Log::error('Failed to send blog email to ' . $user->email, [
                        'error' => $e->getMessage(),
                    ]);
                }
            }
        }
    }
}
