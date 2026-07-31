@extends('layouts.admin')

@section('title', 'Order Invoice PDF - ' . $order->order_number)

@section('content')
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice #{{ $order->order_number }}</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; padding: 20px; }
        .header { display: flex; justify-content: space-between; margin-bottom: 30px; }
        .company-info, .customer-info { margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f5f5f5; }
        .total { text-align: right; margin-top: 20px; }
        .summary { float: right; width: 250px; }
        .summary div { display: flex; justify-content: space-between; margin-bottom: 5px; }
        .summary .total-row { border-top: 1px solid #000; padding-top: 5px; font-weight: bold; }
    </style>
</head>
<body>
    <div class="header">
        <div>
            <h1>INVOICE</h1>
            <p>Order #{{ $order->order_number }}</p>
            <p>Date: {{ $order->created_at->format('M d, Y') }}</p>
        </div>
        <div>
            <p>Status: {{ ucfirst($order->status) }}</p>
        </div>
    </div>

    <div style="display: flex; justify-content: space-between; margin-bottom: 30px;">
        <div class="company-info">
            <h3>From:</h3>
            <p><strong>{{ config('app.name') }}</strong></p>
            <p>{{ \App\Helpers\SettingsHelper::get('site_address', '123 Business St, City, Country') }}</p>
            <p>{{ \App\Helpers\SettingsHelper::get('site_email', config('mail.from.address')) }}</p>
            <p>{{ \App\Helpers\SettingsHelper::get('site_phone', '+1 234 567 8900') }}</p>
        </div>

        <div class="customer-info">
            <h3>To:</h3>
            <p><strong>{{ $order->shipping_full_name }}</strong></p>
            <p>{{ $order->shipping_address }}</p>
            <p>{{ $order->shipping_city }}, {{ $order->shipping_state }} {{ $order->shipping_postal_code }}</p>
            <p>{{ $order->shipping_country }}</p>
            <p>{{ $order->shipping_email }}</p>
            <p>{{ $order->shipping_phone }}</p>
        </div>
    </div>

    <h3>Order Items</h3>
    <table>
        <thead>
            <tr>
                <th>Product</th>
                <th>SKU</th>
                <th>Quantity</th>
                <th>Price</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($order->items as $item)
            <tr>
                <td>{{ $item->product_name }}</td>
                <td>{{ $item->product ? $item->product->sku : 'N/A' }}</td>
                <td>{{ $item->quantity }}</td>
                <td>{{ \App\Helpers\SettingsHelper::currency($item->price) }}</td>
                <td>{{ \App\Helpers\SettingsHelper::currency($item->total) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="summary">
        <div>
            <span>Subtotal:</span>
            <span>{{ \App\Helpers\SettingsHelper::currency($order->subtotal) }}</span>
        </div>
        <div>
            <span>Platform Service Fee:</span>
            <span>{{ \App\Helpers\SettingsHelper::currency($order->tax) }}</span>
        </div>
        <div>
            <span>Delivery Fee:</span>
            <span>{{ \App\Helpers\SettingsHelper::currency($order->shipping) }}</span>
        </div>
        <div class="total-row">
            <span>Total:</span>
            <span>{{ \App\Helpers\SettingsHelper::currency($order->total) }}</span>
        </div>
    </div>

    <div style="clear: both; margin-top: 40px;">
        <h3>Payment Information</h3>
        <p>Method: {{ ucfirst(str_replace('_', ' ', $order->payment_method)) }}</p>
        <p>Status: {{ ucfirst($order->payment_status) }}</p>
        @if($order->payment_reference)
            <p>Reference: {{ $order->payment_reference }}</p>
        @endif

        <h3>Shipping Information</h3>
        <p>Carrier: {{ $order->shipping_carrier ?: 'Standard Shipping' }}</p>
        <p>Tracking: {{ $order->tracking_number ?: 'Not available' }}</p>
    </div>
</body>
</html>
@endsection
