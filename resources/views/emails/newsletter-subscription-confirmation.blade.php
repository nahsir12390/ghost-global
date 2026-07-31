<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $isReactivation ? 'Welcome Back!' : 'Subscription Confirmed' }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            background: #f9fafb;
        }
        
        .email-container {
            max-width: 600px;
            margin: 0 auto;
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        
        .header {
            background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%);
            padding: 40px 20px;
            text-align: center;
            color: white;
        }
        
        .header h1 {
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 10px;
        }
        
        .header p {
            font-size: 16px;
            opacity: 0.95;
        }
        
        .content {
            padding: 40px 30px;
        }
        
        .content h2 {
            font-size: 22px;
            color: #1f2937;
            margin-bottom: 20px;
        }
        
        .content p {
            font-size: 15px;
            color: #4b5563;
            margin-bottom: 18px;
            line-height: 1.8;
        }
        
        .highlight {
            background: #fef3c7;
            border-left: 4px solid #dc2626;
            padding: 15px 20px;
            margin: 25px 0;
            border-radius: 6px;
        }
        
        .highlight h3 {
            color: #92400e;
            font-size: 16px;
            margin-bottom: 8px;
        }
        
        .highlight ul {
            margin-left: 20px;
            color: #78350f;
        }
        
        .highlight li {
            margin-bottom: 8px;
            font-size: 14px;
        }
        
        .cta-button {
            display: inline-block;
            background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%);
            color: white;
            padding: 14px 40px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            font-size: 16px;
            margin-top: 25px;
            transition: transform 0.2s;
        }
        
        .cta-button:hover {
            transform: translateY(-2px);
        }
        
        .divider {
            height: 1px;
            background: #e5e7eb;
            margin: 30px 0;
        }
        
        .footer {
            background: #f3f4f6;
            padding: 30px;
            text-align: center;
            color: #6b7280;
            font-size: 13px;
            border-top: 1px solid #e5e7eb;
        }
        
        .footer-links {
            margin-top: 15px;
        }
        
        .footer-links a {
            color: #dc2626;
            text-decoration: none;
            margin: 0 15px;
            font-weight: 500;
        }
        
        .footer-links a:hover {
            text-decoration: underline;
        }
        
        .email-address {
            background: #f9fafb;
            padding: 12px;
            border-radius: 6px;
            font-weight: 600;
            color: #dc2626;
            display: inline-block;
            margin-top: 10px;
        }
        
        .icon {
            display: inline-block;
            width: 20px;
            height: 20px;
            margin-right: 8px;
            vertical-align: middle;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <!-- Header -->
        <div class="header">
            <h1>{{ $isReactivation ? '👋 Welcome Back!' : '✅ Subscription Confirmed' }}</h1>
            <p>{{ $isReactivation ? 'We look forward to staying in touch!' : 'Thank you for subscribing to our newsletter!' }}</p>
        </div>
        
        <!-- Content -->
        <div class="content">
            <h2>{{ $isReactivation ? 'Your subscription is active again!' : 'You\'re all set!' }}</h2>
            
            <p>
                Hi there! 👋
            </p>
            
            <p>
                {{ $isReactivation 
                    ? 'We\'ve successfully reactivated your subscription. We\'re thrilled to have you back in our newsletter family!'
                    : 'We\'re excited to have you join our newsletter community. Your email address has been successfully added to our mailing list.'
                }}
            </p>
            
            <p>
                Your subscription email: 
                <span class="email-address">{{ $subscriber->email }}</span>
            </p>
            
            <!-- What to Expect -->
            <div class="highlight">
                <h3>📬 What You'll Get</h3>
                <ul>
                    <li><strong>Exclusive Offers:</strong> Early access to special deals and promotions</li>
                    <li><strong>New Arrivals:</strong> First to know about our latest products and collections</li>
                    <li><strong>Style Tips:</strong> Fashion advice and product recommendations</li>
                    <li><strong>Updates:</strong> Latest news about our brand and services</li>
                </ul>
            </div>
            
            <p>
                We respect your inbox and will never spam you. You can manage your subscription preferences or unsubscribe at any time directly from our emails.
            </p>
            
            <div class="divider"></div>
            
            <p>
                If you no longer wish to receive emails from us, you can easily unsubscribe from any newsletter email, or visit your account settings.
            </p>
            
            <p>
                Thank you for supporting {{ $siteName }}! We look forward to keeping you updated on all the exciting things happening with us.
            </p>
            
            <p style="margin-top: 30px;">
                <strong>Happy shopping!</strong><br>
                The {{ $siteName }} Team 🎉
            </p>
        </div>
        
        <!-- Footer -->
        <div class="footer">
            <p>{{ $siteName }} Newsletter</p>
            <p style="margin-top: 12px; color: #9ca3af;">
                © {{ date('Y') }} {{ $siteName }}. All rights reserved.
            </p>
            <div class="footer-links">
                <a href="{{ route('home') }}">Visit Our Store</a>
                <a href="{{ route('about') }}">About Us</a>
                <a href="{{ route('contact') }}">Contact</a>
                <a href="{{ route('privacy') }}">Privacy</a>
            </div>
            <p style="margin-top: 15px; font-size: 12px; color: #d1d5db;">
                If clicking the links above doesn't work, copy and paste this URL into your browser: {{ route('home') }}
            </p>
        </div>
    </div>
</body>
</html>
