<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Order Received</title>
</head>
<body style="margin:0; padding:0; background-color:#f8fafc; font-family:Arial, Helvetica, sans-serif; color:#1f2937;">
    <div style="max-width:680px; margin:0 auto; padding:32px 20px;">
        <div style="background:#ffffff; border:1px solid #e5e7eb; border-radius:18px; overflow:hidden;">
            <div style="background:linear-gradient(135deg, #111827, #334155); padding:32px 24px; color:#ffffff;">
                <p style="margin:0 0 8px; font-size:12px; letter-spacing:0.18em; text-transform:uppercase;">Admin Alert</p>
                <h1 style="margin:0; font-size:28px; line-height:1.2;">A new order has been placed.</h1>
            </div>

            <div style="padding:28px 24px;">
                <p style="margin:0 0 16px; font-size:16px; line-height:1.7;">
                    A new order requires review in the admin dashboard.
                </p>

                <div style="margin:24px 0; padding:18px; background:#f8fafc; border:1px solid #e5e7eb; border-radius:14px;">
                    <p style="margin:0 0 10px; font-size:15px; font-weight:700;">Order summary</p>
                    <p style="margin:0; font-size:15px; line-height:1.8;">
                        <strong>Order Number:</strong> {{ $order->order_number }}<br>
                        <strong>Tracking Code:</strong> {{ $order->tracking_number ?: $order->order_number }}<br>
                        <strong>Order Date:</strong> {{ $order->created_at->format('F d, Y H:i A') }}<br>
                        <strong>Payment Method:</strong> {{ ucfirst(str_replace('_', ' ', $order->payment_method)) }}<br>
                        <strong>Payment Status:</strong> {{ ucfirst($order->payment_status) }}<br>
                        <strong>Order Status:</strong> {{ ucfirst($order->status) }}<br>
                        <strong>Total:</strong> {{ \App\Helpers\SettingsHelper::currency($order->total) }}
                    </p>
                </div>

                <div style="margin:24px 0;">
                    <p style="margin:0 0 10px; font-size:15px; font-weight:700;">Customer details</p>
                    <p style="margin:0; font-size:15px; line-height:1.8;">
                        <strong>Name:</strong> {{ $order->shipping_first_name }} {{ $order->shipping_last_name }}<br>
                        <strong>Email:</strong> {{ $order->shipping_email }}<br>
                        <strong>Phone:</strong> {{ $order->shipping_phone }}
                    </p>
                </div>

                @if($order->shipping_address)
                    <div style="margin:24px 0;">
                        <p style="margin:0 0 10px; font-size:15px; font-weight:700;">Shipping address</p>
                        <p style="margin:0; font-size:15px; line-height:1.8;">
                            {{ $order->shipping_first_name }} {{ $order->shipping_last_name }}<br>
                            {{ $order->shipping_address }}<br>
                            {{ $order->shipping_city }}, {{ $order->shipping_state }} {{ $order->shipping_postal_code }}<br>
                            {{ $order->shipping_country }}
                        </p>
                    </div>
                @endif

                @if($order->items && count($order->items) > 0)
                    <div style="margin:24px 0;">
                        <p style="margin:0 0 12px; font-size:15px; font-weight:700;">Order items</p>
                        <table style="width:100%; border-collapse:collapse; font-size:14px;">
                            <tr>
                                <th style="text-align:left; padding:10px 8px; background:#f3f4f6; border-bottom:2px solid #e5e7eb;">Product</th>
                                <th style="text-align:center; padding:10px 8px; background:#f3f4f6; border-bottom:2px solid #e5e7eb;">Qty</th>
                                <th style="text-align:right; padding:10px 8px; background:#f3f4f6; border-bottom:2px solid #e5e7eb;">Total</th>
                            </tr>
                            @foreach($order->items as $item)
                                <tr>
                                    <td style="padding:10px 8px; border-bottom:1px solid #e5e7eb;">{{ $item->product_name }}</td>
                                    <td style="padding:10px 8px; border-bottom:1px solid #e5e7eb; text-align:center;">{{ $item->quantity }}</td>
                                    <td style="padding:10px 8px; border-bottom:1px solid #e5e7eb; text-align:right;">{{ \App\Helpers\SettingsHelper::currency($item->total) }}</td>
                                </tr>
                            @endforeach
                        </table>
                    </div>
                @endif

                <p style="margin:24px 0 0; font-size:15px; line-height:1.8;">
                    Next steps:
                    Review the order, prepare shipment, and update status from the admin dashboard.
                </p>
            </div>

            <div style="background:#f3f4f6; padding:16px 24px; text-align:center; font-size:12px; color:#6b7280; border-top:1px solid #e5e7eb;">
                <p style="margin:0;">© {{ date('Y') }} {{ $siteName }}. All rights reserved.</p>
            </div>
        </div>
    </div>
</body>
</html>
