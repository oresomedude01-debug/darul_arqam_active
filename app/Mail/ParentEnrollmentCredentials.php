<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\User;
use App\Models\SchoolSetting;

class ParentEnrollmentCredentials extends Mailable
{
    use Queueable, SerializesModels;

    public $parentUser;
    public $studentName;
    public $defaultPassword = 'password123';

    /**
     * Create a new message instance.
     */
    public function __construct(User $parentUser, $studentName)
    {
        $this->parentUser = $parentUser;
        $this->studentName = $studentName;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your Parent Portal Account Credentials - ' . config('app.name'),
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        $schoolSetting = SchoolSetting::first();
        
        return new Content(
            view: 'emails.parent-enrollment-credentials',
            with: [
                'parentName' => $this->parentUser->name,
                'parentEmail' => $this->parentUser->email,
                'studentName' => $this->studentName,
                'defaultPassword' => $this->defaultPassword,
                'appName' => config('app.name'),
                'loginUrl' => route('login'),
                'schoolEmail' => $schoolSetting?->school_email ?? config('app.school_email', 'info@school.com'),
                'schoolPhone' => $schoolSetting?->school_phone ?? config('app.school_phone', '+1-000-000-0000'),
                'schoolName' => $schoolSetting?->school_name ?? config('app.name'),
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
