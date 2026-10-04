<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class OrderStatusUpdated extends Mailable
{
    /**
     * Create a new message instance.
     */
    public function __construct(
        public Order $order,
        public string $previousStatus,
        public string $newStatus
    ) {}

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $siteName = \App\Helpers\SettingsHelper::get('site_name', config('app.name', 'Our Store'));

        return new Envelope(
            subject: "Order {$this->order->order_number} Status Updated - {$siteName}",
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        $statusMessages = [
            'ordered' => 'Your order has been received and is now in our system.',
            'confirmed' => 'Your order has been confirmed and is being prepared.',
            'picked_up' => 'Your order has been packed and is ready for shipment.',
            'on_the_way' => 'Your order is on the way to you!',
            'delivered' => 'Your order has been delivered. Thank you for your purchase!',
            'cancelled' => 'Your order has been cancelled.',
            'failed' => 'Unfortunately, your order could not be fulfilled.',
        ];

        return new Content(
            view: 'emails.order-status-updated',
            with: [
                'order' => $this->order,
                'previousStatus' => $this->previousStatus,
                'newStatus' => $this->newStatus,
                'statusMessage' => $statusMessages[$this->newStatus] ?? 'Your order status has been updated.',
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
