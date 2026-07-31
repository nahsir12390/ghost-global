<?php

namespace App\Observers;

use App\Helpers\SettingsHelper;
use App\Mail\AdminOrderCancelledMail;
use App\Mail\OrderStatusUpdated;
use App\Models\Order;
use App\Models\OrderStatusHistory;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class OrderObserver
{
    /**
     * Cache for original status values to avoid database column issues
     */
    private static $originalStatuses = [];

    /**
     * Handle the Order "updating" event.
     */
    public function updating(Order $order): void
    {
        self::$originalStatuses[$order->id] = $order->getOriginal('status');
    }

    /**
     * Handle the Order "updated" event.
     */
    public function updated(Order $order): void
    {
        $previousStatus = self::$originalStatuses[$order->id] ?? $order->getOriginal('status');
        $statusChanged = $previousStatus !== $order->status;

        unset(self::$originalStatuses[$order->id]);

        if (!$statusChanged) {
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

        $recipientEmail = $order->shipping_email ?: $order->user?->email;

        if ($recipientEmail) {
            try {
                Log::info('Sending order status update email for order: ' . $order->order_number, [
                    'recipient' => $recipientEmail,
                ]);

                Mail::to($recipientEmail)->send(new OrderStatusUpdated(
                    $order->fresh(['user', 'items.product', 'items.vendor']),
                    $previousStatus,
                    $order->status
                ));

                Log::info('Order status update email sent successfully for order: ' . $order->order_number);
            } catch (\Exception $e) {
                Log::error('Failed to send order status update email: ' . $e->getMessage(), [
                    'order_id' => $order->id,
                    'recipient' => $recipientEmail,
                ]);
            }
        } else {
            Log::warning('Skipped order status email because no recipient email was found.', [
                'order_id' => $order->id,
            ]);
        }

        if ($order->status === 'cancelled') {
            $adminEmail = SettingsHelper::get('site_email', config('mail.from.address'));

            if ($adminEmail) {
                try {
                    Log::info('Sending admin cancellation notification for order: ' . $order->order_number, [
                        'recipient' => $adminEmail,
                    ]);

                    Mail::to($adminEmail)->send(new AdminOrderCancelledMail(
                        $order->fresh(['user', 'items.product', 'items.vendor']),
                        $previousStatus
                    ));

                    Log::info('Admin cancellation notification sent successfully for order: ' . $order->order_number);
                } catch (\Exception $e) {
                    Log::error('Failed to send admin cancellation notification: ' . $e->getMessage(), [
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
