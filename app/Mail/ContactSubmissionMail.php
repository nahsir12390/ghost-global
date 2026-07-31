<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ContactSubmissionMail extends Mailable
{
    use Queueable, SerializesModels;

    public $name;
    public $email;
    public $subject;
    public $message;
    public $siteName;

    /**
     * Create a new message instance.
     */
    public function __construct(
        string $name,
        string $email,
        string $subject,
        string $message,
        string $siteName
    ) {
        $this->name = $name;
        $this->email = $email;
        $this->subject = $subject;
        $this->message = $message;
        $this->siteName = $siteName;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'New Contact Form Submission: ' . $this->subject,
            from: 'noreply@' . parse_url(config('app.url'), PHP_URL_HOST),
            replyTo: $this->email,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.contact-submission',
            with: [
                'senderName' => $this->name,
                'senderEmail' => $this->email,
                'contactSubject' => $this->subject,
                'contactMessage' => $this->message,
                'siteTitle' => $this->siteName,
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
