<?php

namespace App\Mail;

use App\Models\Order;
use App\Models\User;
use App\Models\WalletTransaction;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class WalletRefundAdminMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public User $user,
        public Order $order,
        public WalletTransaction $transaction
    ) {
    }

    public function envelope(): Envelope
    {
        $siteName = \App\Helpers\SettingsHelper::get('site_name', config('app.name', 'Our Store'));

        return new Envelope(
            subject: 'Wallet refund issued for cancelled order ' . $this->order->order_number . ' - ' . $siteName,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.wallet-refund-admin',
            with: [
                'user' => $this->user,
                'order' => $this->order,
                'transaction' => $this->transaction,
                'siteName' => \App\Helpers\SettingsHelper::get('site_name', config('app.name', 'Our Store')),
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
