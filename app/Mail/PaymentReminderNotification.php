<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class PaymentReminderNotification extends Mailable
{

    public $parent;
    public $outstandingBills; // Collection of outstanding bills

    /**
     * Create a new message instance.
     */
    public function __construct($parent, $outstandingBills)
    {
        $this->parent = $parent;
        $this->outstandingBills = $outstandingBills;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            to: [$this->parent->email],
            subject: 'Payment Reminder - Outstanding Bills - ' . config('app.name'),
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.payment-reminder',
            with: [
                'parent' => $this->parent,
                'bills' => $this->outstandingBills,
                'totalDue' => $this->outstandingBills->sum('balance_due'),
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
