<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Status Update</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #1f2937;
            background-color: #f3f4f6;
            margin: 0;
            padding: 24px 0;
        }
        .wrapper {
            max-width: 620px;
            margin: 0 auto;
            background: #ffffff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.08);
        }
        .header {
            background: linear-gradient(135deg, #dc2626, #b91c1c);
            color: #ffffff;
            padding: 32px 24px;
            text-align: center;
        }
        .content {
            padding: 32px 24px;
        }
        .status {
            display: inline-block;
            margin: 12px 0 20px;
            padding: 10px 18px;
            border-radius: 999px;
            background: #fee2e2;
            color: #991b1b;
            font-weight: bold;
        }
        .details {
            background: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 10px;
            padding: 18px;
            margin: 24px 0;
        }
        .row {
            margin-bottom: 10px;
        }
        .label {
            color: #6b7280;
            font-weight: bold;
        }
        .button {
            display: inline-block;
            background: #dc2626;
            color: #ffffff !important;
            text-decoration: none;
            padding: 12px 22px;
            border-radius: 8px;
            font-weight: bold;
            margin-top: 18px;
        }
        .footer {
            padding: 24px;
            text-align: center;
            font-size: 13px;
            color: #6b7280;
            background: #f9fafb;
            border-top: 1px solid #e5e7eb;
        }
        .footer a {
            color: #dc2626;
            text-decoration: none;
            margin: 0 8px;
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="header">
            <h1>Order Status Updated</h1>
            <p>Order #{{ $order->order_number }}</p>
        </div>

        <div class="content">
            <p>Hello {{ $order->user->name ?? $order->shipping_first_name ?? 'Customer' }},</p>
            <p>Your order status has changed.</p>

            <div class="status">{{ ucfirst(str_replace('_', ' ', $newStatus)) }}</div>

            <p><strong>{{ $statusMessage }}</strong></p>

            <div class="details">
                <div class="row"><span class="label">Previous status:</span> {{ ucfirst(str_replace('_', ' ', $previousStatus)) }}</div>
                <div class="row"><span class="label">Current status:</span> {{ ucfirst(str_replace('_', ' ', $newStatus)) }}</div>
                <div class="row"><span class="label">Order number:</span> {{ $order->order_number }}</div>
                <div class="row"><span class="label">Order total:</span> NGN{{ number_format($order->total, 2) }}</div>
                @if($order->tracking_number)
                    <div class="row"><span class="label">Tracking number:</span> {{ $order->tracking_number }}</div>
                @endif
            </div>

            <p>You can open your order page any time to see the latest details.</p>

            <a href="{{ route('my.orders.show', $order) }}" class="button">View Order Details</a>
        </div>

        <div class="footer">
            <p>{{ $siteName }} order notification</p>
            <p>
                <a href="{{ route('home') }}">Store</a>
                <a href="{{ route('contact') }}">Contact</a>
                <a href="{{ route('privacy') }}">Privacy</a>
            </p>
        </div>
    </div>
</body>
</html>
