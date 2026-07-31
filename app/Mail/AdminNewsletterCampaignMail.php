<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class AdminNewsletterCampaignMail extends Mailable
{
    /**
     * Create a new message instance.
     */
    public function __construct(
        public string $emailSubject,
        public string $messageBody
    ) {}

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->emailSubject,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.admin-newsletter-campaign',
            with: [
                'messageBody' => $this->messageBody,
                'siteName' => \App\Helpers\SettingsHelper::get('site_name', config('app.name', 'Our Store')),
            ]
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
