<?php

namespace App\Mail;

use App\Models\NewsletterSubscriber;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class NewsletterSubscriptionConfirmation extends Mailable
{
    /**
     * Create a new message instance.
     */
    public function __construct(
        public NewsletterSubscriber $subscriber,
        public bool $isReactivation = false
    ) {}

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $siteName = \App\Helpers\SettingsHelper::get('site_name', config('app.name', 'Our Store'));
        
        return new Envelope(
            to: $this->subscriber->email,
            subject: $this->isReactivation 
                ? "Welcome Back to {$siteName} Newsletter!" 
                : "Subscription Confirmed - {$siteName} Newsletter",
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.newsletter-subscription-confirmation',
            with: [
                'subscriber' => $this->subscriber,
                'isReactivation' => $this->isReactivation,
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
