<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Cancelled</title>
</head>
<body style="margin:0;padding:0;background-color:#f8fafc;font-family:Arial,sans-serif;color:#1f2937;">
    <div style="max-width:640px;margin:0 auto;padding:32px 16px;">
        <div style="background:#ffffff;border:1px solid #e5e7eb;border-radius:16px;overflow:hidden;">
            <div style="background:linear-gradient(135deg,#dc2626,#7f1d1d);padding:24px 28px;color:#ffffff;">
                <p style="margin:0 0 8px;font-size:12px;letter-spacing:.14em;text-transform:uppercase;opacity:.85;">Admin Alert</p>
                <h1 style="margin:0;font-size:28px;line-height:1.2;">Order Cancelled</h1>
                <p style="margin:12px 0 0;font-size:14px;line-height:1.6;opacity:.92;">
                    A customer order was cancelled and may require review or refund follow-up.
                </p>
            </div>

            <div style="padding:28px;">
                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="border-collapse:collapse;">
                    <tr>
                        <td style="padding:0 0 14px;font-size:14px;color:#6b7280;">Order Number</td>
                        <td style="padding:0 0 14px;font-size:14px;font-weight:700;color:#111827;text-align:right;">{{ $order->order_number }}</td>
                    </tr>
                    <tr>
                        <td style="padding:0 0 14px;font-size:14px;color:#6b7280;">Customer</td>
                        <td style="padding:0 0 14px;font-size:14px;font-weight:600;color:#111827;text-align:right;">{{ $order->user?->name ?? $order->shipping_full_name }}</td>
                    </tr>
                    <tr>
                        <td style="padding:0 0 14px;font-size:14px;color:#6b7280;">Email</td>
                        <td style="padding:0 0 14px;font-size:14px;color:#111827;text-align:right;">{{ $order->shipping_email ?: ($order->user?->email ?? 'N/A') }}</td>
                    </tr>
                    <tr>
                        <td style="padding:0 0 14px;font-size:14px;color:#6b7280;">Previous Status</td>
                        <td style="padding:0 0 14px;font-size:14px;color:#111827;text-align:right;">{{ $previousStatus ? ucfirst(str_replace('_', ' ', $previousStatus)) : 'Unknown' }}</td>
                    </tr>
                    <tr>
                        <td style="padding:0 0 14px;font-size:14px;color:#6b7280;">New Status</td>
                        <td style="padding:0 0 14px;font-size:14px;font-weight:700;color:#b91c1c;text-align:right;">Cancelled</td>
                    </tr>
                    <tr>
                        <td style="padding:0 0 14px;font-size:14px;color:#6b7280;">Payment Status</td>
                        <td style="padding:0 0 14px;font-size:14px;color:#111827;text-align:right;">{{ ucfirst($order->payment_status) }}</td>
                    </tr>
                    <tr>
                        <td style="padding:0 0 14px;font-size:14px;color:#6b7280;">Order Total</td>
                        <td style="padding:0 0 14px;font-size:16px;font-weight:700;color:#111827;text-align:right;">N{{ number_format((float) $order->total, 2) }}</td>
                    </tr>
                    <tr>
                        <td style="padding:0;font-size:14px;color:#6b7280;">Cancelled At</td>
                        <td style="padding:0;font-size:14px;color:#111827;text-align:right;">{{ now()->format('M d, Y g:i A') }}</td>
                    </tr>
                </table>

                @if($order->payment_status === 'paid')
                    <div style="margin-top:24px;padding:16px 18px;border-radius:12px;background:#fef2f2;border:1px solid #fecaca;color:#991b1b;">
                        <strong style="display:block;margin-bottom:6px;">Refund attention</strong>
                        <span style="font-size:14px;line-height:1.6;">This order was marked as paid. Please review whether a refund or manual follow-up is needed.</span>
                    </div>
                @endif
            </div>
        </div>
    </div>
</body>
</html>
