<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class ParentBillNotification extends Mailable
{

    public $parent;
    public $bills; // Collection of bills

    /**
     * Create a new message instance.
     */
    public function __construct($parent, $bills)
    {
        $this->parent = $parent;
        $this->bills = $bills;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            to: [$this->parent->email],
            subject: 'New Bill(s) Created - ' . config('app.name'),
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.parent-bill-notification',
            with: [
                'parent' => $this->parent,
                'bills' => $this->bills,
                'childrenCount' => $this->bills->groupBy('student_id')->count(),
                'totalAmount' => $this->bills->sum('total_amount'),
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
