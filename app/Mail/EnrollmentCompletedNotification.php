<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\User;
use App\Models\UserProfile;

class EnrollmentCompletedNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $studentUser;
    public $parentUser;
    public $enrollmentData;

    /**
     * Create a new message instance.
     */
    public function __construct(User $studentUser, User $parentUser, array $enrollmentData)
    {
        $this->studentUser = $studentUser;
        $this->parentUser = $parentUser;
        $this->enrollmentData = $enrollmentData;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $studentName = trim(
            ($this->enrollmentData['first_name'] ?? '') . ' ' .
            ($this->enrollmentData['middle_name'] ?? '') . ' ' .
            ($this->enrollmentData['last_name'] ?? '')
        );

        return new Envelope(
            subject: "New Student Enrollment Completed - {$studentName}",
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        $studentProfile = $this->studentUser->profile;
        $parentProfile = $this->parentUser->profile;
        
        $studentName = trim(
            ($this->enrollmentData['first_name'] ?? '') . ' ' .
            ($this->enrollmentData['middle_name'] ?? '') . ' ' .
            ($this->enrollmentData['last_name'] ?? '')
        );

        return new Content(
            view: 'emails.enrollment-completed',
            with: [
                'studentName' => $studentName,
                'studentEmail' => $this->studentUser->email,
                'admissionNumber' => $studentProfile->admission_number ?? 'N/A',
                'parentName' => $parentProfile->first_name ?? $this->parentUser->name,
                'parentEmail' => $this->parentUser->email,
                'enrollmentData' => $this->enrollmentData,
                'dateOfBirth' => $this->enrollmentData['date_of_birth'] ?? null,
                'gender' => $this->enrollmentData['gender'] ?? null,
                'address' => $this->enrollmentData['address'] ?? null,
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
