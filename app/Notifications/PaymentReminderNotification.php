<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\DatabaseMessage;

class PaymentReminderNotification extends Notification
{
    use Queueable;

    /**
     * @param \Illuminate\Support\Collection $bills  Outstanding bills for this parent
     * @param float   $totalOwed  Total amount owed across all bills
     */
    public function __construct(
        public readonly \Illuminate\Support\Collection $bills,
        public readonly float $totalOwed
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
        $billCount  = $this->bills->count();
        $currency   = config('app.currency', '₦');
        $amount     = number_format($this->totalOwed, 2);

        return [
            'type'    => 'payment_reminder',
            'title'   => 'Payment Reminder',
            'message' => "You have {$billCount} outstanding " . str('bill')->plural($billCount) .
                         " totalling {$currency}{$amount}. Please make payment at your earliest convenience.",
            'amount'  => $this->totalOwed,
            'bills'   => $this->bills->map(fn($b) => [
                'id'          => $b->id,
                'description' => $b->description ?? ('Bill #' . $b->id),
                'balance_due' => $b->balance_due,
                'due_date'    => $b->due_date?->toDateString(),
            ])->toArray(),
            'action_url'  => '/parent-portal/bills',
            'action_text' => 'View Bills',
            'icon'        => 'fas fa-file-invoice-dollar',
            'color'       => 'orange',
        ];
    }

    /**
     * Push notification payload – dispatched to the fcm_push channel driver.
     * The driver reads `fcmToken`, `title`, `body`, and `data` from this array.
     */
    public function toFcmPush(object $notifiable): array
    {
        $currency = config('app.currency', '₦');
        $amount   = number_format($this->totalOwed, 2);

        return [
            'fcmToken' => $notifiable->fcm_token,
            'title'    => '💳 Payment Reminder',
            'body'     => "You have outstanding fees of {$currency}{$amount}. Tap to view your bills.",
            'data'     => [
                'type'       => 'payment_reminder',
                'action_url' => '/parent-portal/bills',
            ],
        ];
    }
}
