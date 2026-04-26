<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TeacherWelcomeMail extends Mailable
{
    use Queueable, SerializesModels;

    public $teacher;
    public $email;
    public $password;
    public $schoolName;

    /**
     * Create a new message instance.
     */
    public function __construct($teacher, $email, $password)
    {
        $this->teacher = $teacher;
        $this->email = $email;
        $this->password = $password;
        $this->schoolName = config('app.name', 'Darul Arqam School');
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Welcome to ' . $this->schoolName . ' - Teacher Account Created',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.teacher-welcome',
            with: [
                'teacher' => $this->teacher,
                'teacherEmail' => $this->email,
                'temporaryPassword' => $this->password,
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
