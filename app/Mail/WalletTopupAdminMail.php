<?php

namespace App\Mail;

use App\Models\User;
use App\Models\WalletTransaction;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class WalletTopupAdminMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public User $user,
        public WalletTransaction $transaction
    ) {
    }

    public function envelope(): Envelope
    {
        $siteName = \App\Helpers\SettingsHelper::get('site_name', config('app.name', 'Our Store'));

        return new Envelope(
            subject: 'Customer Wallet Top-up Received - ' . $siteName,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.wallet-topup-admin',
            with: [
                'user' => $this->user,
                'transaction' => $this->transaction,
                'siteName' => \App\Helpers\SettingsHelper::get('site_name', config('app.name', 'Our Store')),
            ]
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
