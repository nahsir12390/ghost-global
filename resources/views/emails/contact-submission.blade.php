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
        .section h2 {
            color: #dc2626;
            font-size: 18px;
            margin: 0 0 10px 0;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .info-box {
            background-color: #f9fafb;
            border-left: 4px solid #dc2626;
            padding: 15px;
            margin: 15px 0;
            border-radius: 4px;
        }
        .info-label {
            font-weight: 600;
            color: #374151;
            margin-bottom: 5px;
        }
        .info-value {
            color: #555;
            word-break: break-all;
        }
        .message-box {
            background-color: #f3f4f6;
            padding: 20px;
            border-radius: 6px;
            border: 1px solid #e5e7eb;
            margin: 15px 0;
            white-space: pre-wrap;
            word-wrap: break-word;
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
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1>📬 New Contact Form Submission</h1>
        </div>

        <!-- Content -->
        <div class="content">
            <p style="margin: 0 0 20px 0;">You have received a new contact form submission from your website.</p>

            <!-- Sender Info -->
            <div class="section">
                <h2>Sender Information</h2>
                
                <div class="info-box">
                    <div class="info-label">From:</div>
                    <div class="info-value">{{ $senderName }}</div>
                </div>

                <div class="info-box">
                    <div class="info-label">Email:</div>
                    <div class="info-value">
                        <a href="mailto:{{ $senderEmail }}">{{ $senderEmail }}</a>
                    </div>
                </div>
            </div>

            <!-- Subject -->
            <div class="section">
                <h2>Subject</h2>
                <div class="info-box">
                    <div class="info-value" style="font-size: 16px; font-weight: 500;">{{ $contactSubject }}</div>
                </div>
            </div>

            <!-- Message -->
            <div class="section">
                <h2>Message</h2>
                <div class="message-box">{{ $contactMessage }}</div>
            </div>

            <!-- Quick Actions -->
            <div class="section" style="text-align: center; margin-top: 30px;">
                <a href="mailto:{{ $senderEmail }}?subject=Re: {{ $contactSubject }}" class="cta-button">Reply via Email</a>
            </div>

            <hr style="border: none; border-top: 1px solid #e5e7eb; margin: 25px 0;">
            <p style="color: #6b7280; font-size: 14px; margin: 10px 0;">
                💡 <strong>Tip:</strong> Make sure to respond to this message to keep your customer engaged and satisfied with your service.
            </p>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p style="margin: 0; padding: 0;">
                This email was sent from your {{ $siteTitle }} contact form. Please do not reply to this email address.
            </p>
            <p style="margin: 10px 0 0 0; padding: 0; color: #9ca3af; font-size: 11px;">
                © {{ date('Y') }} {{ $siteTitle }}. All rights reserved.
            </p>
        </div>
    </div>
</body>
</html>
