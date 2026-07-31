<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f5f5f5;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        .header {
            background: linear-gradient(135deg, #dc2626 0%, #991b1b 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 28px;
            font-weight: 600;
        }
        .content {
            padding: 30px;
        }
        .section {
            margin-bottom: 25px;
        }
        .success-icon {
            width: 60px;
            height: 60px;
            background-color: #d1fae5;
            border-radius: 50%;
            margin: 0 auto 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 30px;
        }
        .footer {
            background-color: #f9fafb;
            padding: 20px;
            text-align: center;
            border-top: 1px solid #e5e7eb;
            font-size: 12px;
            color: #6b7280;
        }
        .cta-button {
            display: inline-block;
            background-color: #dc2626;
            color: white;
            padding: 12px 30px;
            border-radius: 6px;
            text-decoration: none;
            font-weight: 600;
            margin: 15px 0;
        }
        .cta-button:hover {
            background-color: #991b1b;
        }
        a {
            color: #dc2626;
            text-decoration: none;
        }
        a:hover {
            text-decoration: underline;
        }
        .info-box {
            background-color: #f0fdf4;
            border-left: 4px solid #16a34a;
            padding: 15px;
            margin: 15px 0;
            border-radius: 4px;
            color: #166534;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1>✓ Message Received</h1>
        </div>

        <!-- Content -->
        <div class="content">
            <div style="text-align: center;">
                <div class="success-icon">✓</div>
                <h2 style="color: #374151; margin-top: 0;">Thank You, {{ ucfirst($userName) }}!</h2>
            </div>

            <p style="text-align: center; color: #6b7280; font-size: 16px;">
                We've received your message and appreciate you reaching out to us. Our team will review your inquiry and get back to you shortly.
            </p>

            <!-- What to expect -->
            <div class="info-box">
                <strong>What Happens Next?</strong>
                <ul style="margin: 10px 0; padding-left: 20px;">
                    <li>Our support team will review your message</li>
                    <li>We'll respond within 24 business hours</li>
                    <li>You'll receive a reply at this email address</li>
                </ul>
            </div>

            <!-- Contact info -->
            <div class="section">
                <h3 style="color: #374151; margin-bottom: 10px;">Urgent Response Needed?</h3>
                <p style="margin: 0; color: #6b7280;">
                    For immediate assistance, please contact us directly via WhatsApp or phone. Check our contact page for support options.
                </p>
            </div>

            <hr style="border: none; border-top: 1px solid #e5e7eb; margin: 25px 0;">

            <!-- Reference -->
            <div style="background-color: #f3f4f6; padding: 15px; border-radius: 6px; text-align: center;">
                <p style="margin: 0 0 5px 0; font-size: 12px; color: #6b7280;">Message received on</p>
                <p style="margin: 0; font-weight: 600; color: #374151;">{{ now()->format('M d, Y \a\t g:i A') }}</p>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p style="margin: 0; padding: 0;">
                Questions? Visit our <a href="{{ config('app.url') }}/contact" style="color: #dc2626;">contact page</a> for more information.
            </p>
            <p style="margin: 10px 0 0 0; padding: 0; color: #9ca3af; font-size: 11px;">
                © {{ date('Y') }} {{ $siteTitle }}. All rights reserved.
            </p>
        </div>
    </div>
</body>
</html>
