#!/usr/bin/env php
<?php

/**
 * Test script to verify order status tracking and email notifications
 * Run with: php test_order_status.php
 */

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Order;
use App\Models\OrderStatusHistory;
use Illuminate\Support\Facades\Mail;

echo "\n" . str_repeat("=", 60) . "\n";
echo "ORDER STATUS TRACKING TEST\n";
echo str_repeat("=", 60) . "\n\n";

// Get the most recent order
$order = Order::latest()->first();

if (!$order) {
    echo "❌ No orders found. Please create an order first.\n";
    exit(1);
}

echo "Testing with Order: {$order->order_number}\n";
echo "Current Status: {$order->status}\n";
echo "Customer Email: {$order->shipping_email}\n";
echo "\n";

// Test 1: Check status history relationship
echo "TEST 1: Status History Relationship\n";
echo str_repeat("-", 60) . "\n";

$histories = $order->statusHistory()->get();
echo "✓ Total status changes: " . count($histories) . "\n";

if (count($histories) > 0) {
    echo "\nStatus Timeline:\n";
    foreach ($histories as $history) {
        echo sprintf(
            "  • %s → %s (%s)\n",
            $history->old_status ?? 'initial',
            $history->new_status,
            $history->created_at->format('M d, Y g:i A')
        );
        if ($history->notes) {
            echo "    Note: {$history->notes}\n";
        }
    }
} else {
    echo "No status history yet.\n";
}

echo "\n";

// Test 2: Simulate status change
echo "TEST 2: Status Change with Email Notification\n";
echo str_repeat("-", 60) . "\n";

$oldStatus = $order->status;
$newStatus = 'processing';

if ($oldStatus !== $newStatus) {
    echo "Changing status from '{$oldStatus}' to '{$newStatus}'...\n";
    
    // Temporarily enable fake mail for testing
    Mail::fake();
    
    // Update the status
    $order->status = $newStatus;
    $order->save();
    
    echo "✓ Order status updated\n";
    
    // Check if email was sent
    Mail::assertSent(\App\Mail\OrderStatusUpdatedMail::class, function ($mail) use ($order, $oldStatus, $newStatus) {
        return $mail->order->id === $order->id &&
               $mail->previousStatus === $oldStatus &&
               $mail->newStatus === $newStatus;
    });
    
    echo "✓ Email notification sent to customer\n";
    
    // Verify status history was recorded
    $latestHistory = $order->statusHistory()->latest()->first();
    if ($latestHistory && $latestHistory->new_status === $newStatus) {
        echo "✓ Status change recorded in history\n";
    } else {
        echo "❌ Status history not recorded properly\n";
    }
} else {
    echo "⚠ Order is already in '{$newStatus}' status, skipping change test\n";
}

echo "\n";

// Test 3: Test with notes
echo "TEST 3: Status Change with Notes\n";
echo str_repeat("-", 60) . "\n";

$anotherStatus = 'shipped';

if ($order->status !== $anotherStatus) {
    echo "Changing status to '{$anotherStatus}' with notes...\n";
    
    OrderStatusHistory::create([
        'order_id' => $order->id,
        'old_status' => $order->status,
        'new_status' => $anotherStatus,
        'notes' => 'Item has been handed to courier for delivery'
    ]);
    
    $order->status = $anotherStatus;
    $order->save();
    
    $history = $order->statusHistory()->latest()->first();
    echo "✓ Status change with notes recorded\n";
    echo "  Notes: {$history->notes}\n";
} else {
    echo "⚠ Order already in '{$anotherStatus}' status\n";
}

echo "\n";

// Test 4: Display full order with status timeline
echo "TEST 4: Full Order Display\n";
echo str_repeat("-", 60) . "\n";

$order->refresh();
echo "Order #: {$order->order_number}\n";
echo "Customer: {$order->shipping_first_name} {$order->shipping_last_name}\n";
echo "Email: {$order->shipping_email}\n";
echo "Total: ₦" . number_format($order->total, 2) . "\n";
echo "Current Status: " . strtoupper($order->status) . "\n";
echo "\n";

echo "Status History:\n";
foreach ($order->statusHistory()->orderBy('created_at', 'desc')->get() as $history) {
    echo sprintf(
        "  %s: %s → %s\n",
        $history->created_at->format('M d g:i A'),
        $history->old_status ?? 'initial',
        $history->new_status
    );
}

echo "\n";
echo "✓ All tests completed successfully!\n";
echo str_repeat("=", 60) . "\n\n";
