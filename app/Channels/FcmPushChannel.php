<?php

namespace App\Channels;

use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * FcmPushChannel
 *
 * Sends push notifications via Firebase Cloud Messaging (FCM) HTTP v1 API.
 * Uses the server key from config('services.fcm.server_key').
 *
 * Usage: return ['database', 'fcm_push'] in the Notification's via() method.
 */
class FcmPushChannel
{
    /**
     * Send the given notification.
     */
    public function send(object $notifiable, Notification $notification): void
    {
        if (!method_exists($notification, 'toFcmPush')) {
            return;
        }

        $payload = $notification->toFcmPush($notifiable);

        $fcmToken  = $payload['fcmToken'] ?? null;
        $serverKey = config('services.fcm.server_key');

        if (!$fcmToken || !$serverKey) {
            Log::warning('FcmPushChannel: missing token or server key', [
                'user_id'    => $notifiable->id ?? null,
                'has_token'  => !empty($fcmToken),
                'has_key'    => !empty($serverKey),
            ]);
            return;
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => 'key=' . $serverKey,
                'Content-Type'  => 'application/json',
            ])->post('https://fcm.googleapis.com/fcm/send', [
                'to'           => $fcmToken,
                'notification' => [
                    'title' => $payload['title'] ?? 'Notification',
                    'body'  => $payload['body']  ?? '',
                    'sound' => 'default',
                    'badge' => 1,
                ],
                'data'     => $payload['data'] ?? [],
                'priority' => 'high',
            ]);

            if (!$response->successful()) {
                Log::error('FcmPushChannel: FCM request failed', [
                    'user_id' => $notifiable->id ?? null,
                    'status'  => $response->status(),
                    'body'    => $response->body(),
                ]);
            }
        } catch (\Exception $e) {
            Log::error('FcmPushChannel: exception sending push notification', [
                'user_id' => $notifiable->id ?? null,
                'error'   => $e->getMessage(),
            ]);
        }
    }
}
