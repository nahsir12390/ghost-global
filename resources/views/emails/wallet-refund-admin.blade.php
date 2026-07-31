<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Wallet refund alert</title>
</head>
<body style="margin:0; padding:0; background-color:#f8fafc; font-family:Arial, Helvetica, sans-serif; color:#1f2937;">
    <div style="max-width:680px; margin:0 auto; padding:32px 20px;">
        <div style="background:#ffffff; border:1px solid #e5e7eb; border-radius:18px; overflow:hidden;">
            <div style="background:linear-gradient(135deg, #b91c1c, #ef4444); padding:32px 24px; color:#ffffff;">
                <p style="margin:0 0 8px; font-size:12px; letter-spacing:0.18em; text-transform:uppercase;">Refund Notice</p>
                <h1 style="margin:0; font-size:28px; line-height:1.2;">A wallet refund was issued for a cancelled order.</h1>
            </div>

            <div style="padding:28px 24px;">
                <p style="margin:0 0 16px; font-size:16px; line-height:1.7;">
                    A customer cancellation on <strong>{{ $siteName }}</strong> triggered an automatic wallet refund.
                </p>

                <div style="margin:24px 0; padding:18px; background:#fef2f2; border:1px solid #fecaca; border-radius:14px;">
                    <p style="margin:0 0 10px; font-size:15px; font-weight:700;">Refund details</p>
                    <p style="margin:0; font-size:15px; line-height:1.8;">
                        <strong>Customer:</strong> {{ $user->name }} ({{ $user->email }})<br>
                        <strong>Order Number:</strong> {{ $order->order_number }}<br>
                        <strong>Refund Amount:</strong> {{ \App\Helpers\SettingsHelper::currency($transaction->amount) }}<br>
                        <strong>Refund Reference:</strong> {{ $transaction->reference }}<br>
                        <strong>Payment Method:</strong> {{ ucfirst(str_replace('_', ' ', (string) $order->payment_method)) }}<br>
                        <strong>Payment Status:</strong> {{ ucfirst($order->payment_status) }}
                    </p>
                </div>

                <p style="margin:0; font-size:16px; line-height:1.7;">
                    Please review the order in the admin dashboard if any manual follow-up is needed.
                </p>

                <p style="margin:24px 0 0; font-size:16px; line-height:1.7;">
                    Regards,<br>
                    <strong>{{ $siteName }}</strong>
                </p>
            </div>
        </div>
    </div>
</body>
</html>
