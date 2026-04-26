<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class StudentEnrollmentNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $parent;
    public $student;
    public $admissionNumber;
    public $schoolName;

    /**
     * Create a new message instance.
     */
    public function __construct($parent, $student, $admissionNumber)
    {
        $this->parent = $parent;
        $this->student = $student;
        $this->admissionNumber = $admissionNumber;
        $this->schoolName = config('app.name', 'Darul Arqam School');
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Student Enrollment Confirmation - ' . $this->schoolName,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.student-enrollment',
            with: [
                'parent' => $this->parent,
                'student' => $this->student,
                'admissionNumber' => $this->admissionNumber,
            ],
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
