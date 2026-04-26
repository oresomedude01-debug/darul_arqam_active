<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\User;

class StudentEnrolledNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $parentUser;
    public $studentName;
    public $admissionNumber;

    /**
     * Create a new message instance.
     */
    public function __construct(User $parentUser, $studentName, $admissionNumber)
    {
        $this->parentUser = $parentUser;
        $this->studentName = $studentName;
        $this->admissionNumber = $admissionNumber;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'New Student Enrollment - ' . $this->studentName . ' - ' . config('app.name'),
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.student-enrolled-notification',
            with: [
                'parentName' => $this->parentUser->name,
                'studentName' => $this->studentName,
                'admissionNumber' => $this->admissionNumber,
                'appName' => config('app.name'),
                'portalUrl' => route('parent-portal.index'),
            ]
        );
    }

    /**
     * Get the attachments for the message.
     */
    public function attachments(): array
    {
        return [];
    }
}
