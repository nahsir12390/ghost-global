<?php

namespace App\Observers;

use App\Helpers\SettingsHelper;
use App\Mail\AdminOrderCancelledMail;
use App\Mail\OrderStatusUpdated;
use App\Models\Order;
use App\Models\OrderStatusHistory;
use App\Services\WebPushService;
use Illuminate\Contracts\Events\ShouldHandleEventsAfterCommit;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class OrderObserver implements ShouldHandleEventsAfterCommit
{
    /**
     * Cache for original status values to avoid database column issues
     */
    private static $originalStatuses = [];

    private static $originalPaymentStatuses = [];

    /**
     * Handle the Order "updating" event.
     */
    public function updating(Order $order): void
    {
        self::$originalStatuses[$order->id] = $order->getOriginal('status');
        self::$originalPaymentStatuses[$order->id] = $order->getOriginal('payment_status');
    }

    /**
     * Handle the Order "updated" event.
     */
    public function updated(Order $order): void
    {
        $previousStatus = self::$originalStatuses[$order->id] ?? $order->getOriginal('status');
        $previousPaymentStatus = self::$originalPaymentStatuses[$order->id] ?? $order->getOriginal('payment_status');
        $statusChanged = $previousStatus !== $order->status;
        $paymentCompleted = $previousPaymentStatus !== 'paid' && $order->payment_status === 'paid';

        unset(self::$originalStatuses[$order->id]);
        unset(self::$originalPaymentStatuses[$order->id]);

        if ($paymentCompleted) {
            app(WebPushService::class)->sendPaidOrderAlerts($order);
        }

        if (! $statusChanged) {
            return;
        }

        OrderStatusHistory::create([
            'order_id' => $order->id,
            'old_status' => $previousStatus,
            'new_status' => $order->status,
            'notes' => null,
        ]);

        Log::info('Order status changed', [
            'order_id' => $order->id,
            'order_number' => $order->order_number,
            'from' => $previousStatus,
            'to' => $order->status,
        ]);

        $recipientEmail = $order->contact_email;

        if ($recipientEmail) {
            try {
                Log::info('Sending order status update email for order: '.$order->order_number, [
                    'recipient' => $recipientEmail,
                ]);

                Mail::to($recipientEmail)->send(new OrderStatusUpdated(
                    $order->fresh(['user', 'items.product']),
                    $previousStatus,
                    $order->status
                ));

                Log::info('Order status update email sent successfully for order: '.$order->order_number);
            } catch (\Exception $e) {
                Log::error('Failed to send order status update email: '.$e->getMessage(), [
                    'order_id' => $order->id,
                    'recipient' => $recipientEmail,
                ]);
            }
        } else {
            Log::warning('Skipped order status email because no recipient email was found.', [
                'order_id' => $order->id,
            ]);
        }

        $orderUser = $order->user;

        if ($orderUser) {
            app(WebPushService::class)->sendToUser(
                $orderUser,
                'Order update',
                "Your order {$order->order_number} is now {$order->status}.",
                route('my.orders.show', $order)
            );
        }

        if ($order->status === 'cancelled') {
            $adminEmail = SettingsHelper::get('site_email', config('mail.from.address'));

            if ($adminEmail) {
                try {
                    Log::info('Sending admin cancellation notification for order: '.$order->order_number, [
                        'recipient' => $adminEmail,
                    ]);

                    Mail::to($adminEmail)->send(new AdminOrderCancelledMail(
                        $order->fresh(['user', 'items.product']),
                        $previousStatus
                    ));

                    Log::info('Admin cancellation notification sent successfully for order: '.$order->order_number);
                } catch (\Exception $e) {
                    Log::error('Failed to send admin cancellation notification: '.$e->getMessage(), [
                        'order_id' => $order->id,
                        'recipient' => $adminEmail,
                    ]);
                }
            }
        }
    }

    public function created(Order $order): void
    {
        //
    }

    public function deleted(Order $order): void
    {
        //
    }

    public function restored(Order $order): void
    {
        //
    }

    public function forceDeleted(Order $order): void
    {
        //
    }
}
