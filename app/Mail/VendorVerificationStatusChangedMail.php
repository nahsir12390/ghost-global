<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class VendorVerificationStatusChangedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public User $vendor,
        public string $status,
        public ?string $notes = null
    ) {
    }

    public function envelope(): Envelope
    {
        $statusText = match($this->status) {
            'approved' => 'Approved',
            'rejected' => 'Declined',
            default => 'Updated'
        };

        return new Envelope(
            subject: 'Vendor Verification Status: ' . $statusText . ' - ' . \App\Helpers\SettingsHelper::get('site_name', config('app.name', 'Our Store'))
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.vendor-verification-status-changed',
            with: [
                'vendor' => $this->vendor,
                'status' => $this->status,
                'notes' => $this->notes,
                'siteName' => \App\Helpers\SettingsHelper::get('site_name', config('app.name', 'Our Store')),
            ]
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
