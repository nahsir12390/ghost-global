<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class VendorVerificationReceivedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public User $vendor)
    {
    }

    public function envelope(): Envelope
    {
        $siteName = \App\Helpers\SettingsHelper::get('site_name', config('app.name', 'Our Store'));

        return new Envelope(
            subject: 'We received your vendor request - ' . $siteName,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.vendor-verification-received',
            with: [
                'vendor' => $this->vendor,
                'siteName' => \App\Helpers\SettingsHelper::get('site_name', config('app.name', 'Our Store')),
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
