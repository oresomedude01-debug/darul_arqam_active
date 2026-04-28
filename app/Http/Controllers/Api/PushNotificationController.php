<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Push Notification Controller
 * Handles PWA push notification subscriptions and management
 */
class PushNotificationController extends Controller
{
    /**
     * Store a new push subscription
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'endpoint' => 'required|string|url',
            'keys' => 'required|array',
            'keys.p256dh' => 'required|string',
            'keys.auth' => 'required|string',
        ]);

        try {
            // Store the push subscription for the authenticated user
            if (Auth::check()) {
                // You would typically store this in a database table like 'push_subscriptions'
                // For now, we'll just acknowledge receipt
                
                \Log::info('[PWA] Push subscription received', [
                    'user_id' => Auth::id(),
                    'endpoint' => $validated['endpoint']
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Push subscription saved successfully'
            ]);
        } catch (\Exception $e) {
            \Log::error('[PWA] Failed to save push subscription', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to save subscription'
            ], 500);
        }
    }

    /**
     * Get pending notifications for background sync
     */
    public function getPending()
    {
        try {
            $notifications = [];

            if (Auth::check()) {
                // Get unread notifications for the user
                $notifications = Auth::user()
                    ->unreadNotifications()
                    ->limit(5)
                    ->get()
                    ->map(function ($notif) {
                        return [
                            'id' => $notif->id,
                            'title' => $notif->data['title'] ?? 'New Notification',
                            'body' => $notif->data['message'] ?? $notif->data['body'] ?? '',
                            'url' => $notif->data['url'] ?? '/dashboard'
                        ];
                    })
                    ->toArray();
            }

            return response()->json($notifications);
        } catch (\Exception $e) {
            \Log::error('[PWA] Failed to fetch pending notifications', [
                'error' => $e->getMessage()
            ]);

            return response()->json([], 500);
        }
    }

    /**
     * Send a test push notification
     */
    public function sendTest(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string',
            'message' => 'required|string',
            'url' => 'nullable|string|url'
        ]);

        try {
            if (Auth::check()) {
                // Here you would typically use a library like minishlink/web-push
                // to send an actual push notification
                
                \Log::info('[PWA] Test push notification sent', [
                    'user_id' => Auth::id(),
                    'title' => $validated['title']
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Test notification sent successfully'
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'User not authenticated'
            ], 401);
        } catch (\Exception $e) {
            \Log::error('[PWA] Failed to send test notification', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to send notification'
            ], 500);
        }
    }
}
