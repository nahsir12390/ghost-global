<?php

namespace App\Services;

use App\Models\User;
use App\Models\Order;
use App\Models\WebPushSubscription;
use Illuminate\Support\Facades\Log;
use Minishlink\WebPush\Subscription;
use Minishlink\WebPush\WebPush;

class WebPushService
{
    public function configured(): bool
    {
        return filled(config('webpush.vapid.public_key'))
            && filled(config('webpush.vapid.private_key'))
            && filled(config('webpush.vapid.subject'));
    }

    public function publicKey(): ?string
    {
        return $this->configured() ? config('webpush.vapid.public_key') : null;
    }

    public function sendToUser(User $user, string $title, string $body, string $url = '/'): void
    {
        if (! $this->configured()) {
            return;
        }

        $subscriptions = WebPushSubscription::query()
            ->where('user_id', $user->id)
            ->latest('updated_at')
            ->limit(3)
            ->get();

        if ($subscriptions->isEmpty()) {
            return;
        }

        try {
            $webPush = new WebPush([
                'VAPID' => [
                    'subject' => config('webpush.vapid.subject'),
                    'publicKey' => config('webpush.vapid.public_key'),
                    'privateKey' => config('webpush.vapid.private_key'),
                ],
            ], [
                'TTL' => 60 * 60 * 12,
                'urgency' => 'normal',
                'batchSize' => 3,
            ]);

            $payload = json_encode([
                'title' => $title,
                'body' => $body,
                'icon' => route('pwa.icon', ['size' => 192]),
                'badge' => route('pwa.icon', ['size' => 192]),
                'tag' => 'store-order-update',
                'url' => url($url),
            ], JSON_THROW_ON_ERROR);

            foreach ($subscriptions as $subscription) {
                $webPush->queueNotification(Subscription::create([
                    'endpoint' => $subscription->endpoint,
                    'keys' => [
                        'p256dh' => $subscription->public_key,
                        'auth' => $subscription->auth_token,
                    ],
                    'contentEncoding' => $subscription->content_encoding,
                ]), $payload);
            }

            foreach ($webPush->flush() as $report) {
                if ($report->isSubscriptionExpired()) {
                    WebPushSubscription::query()
                        ->where('endpoint_hash', hash('sha256', $report->getEndpoint()))
                        ->delete();
                }

                if (! $report->isSuccess()) {
                    Log::warning('Web push delivery failed.', ['reason' => $report->getReason()]);
                }
            }
        } catch (\Throwable $exception) {
            // Push must never interrupt checkout or an order status update on shared hosting.
            Log::warning('Web push could not be sent.', ['message' => $exception->getMessage()]);
        }
    }

    public function sendPaidOrderAlerts(Order $order): void
    {
        $order->loadMissing('items.vendor');
        $orderUrl = route('admin.orders.show', $order);
        $total = number_format((float) $order->total, 2);

        User::query()
            ->where(fn ($query) => $query->where('is_admin', true)->orWhere('role', 'admin'))
            ->each(fn (User $admin) => $this->sendToUser(
                $admin,
                'New paid order',
                "{$order->order_number} has been paid. Total: N{$total}.",
                $orderUrl
            ));

        $order->items
            ->pluck('vendor')
            ->filter()
            ->unique('id')
            ->each(fn (User $vendor) => $this->sendToUser(
                $vendor,
                'New order for your store',
                "Order {$order->order_number} includes one or more of your products.",
                $orderUrl
            ));
    }
}
