<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vendor request received</title>
</head>
<body style="margin:0; padding:0; background-color:#f8fafc; font-family:Arial, Helvetica, sans-serif; color:#1f2937;">
    <div style="max-width:680px; margin:0 auto; padding:32px 20px;">
        <div style="background:#ffffff; border:1px solid #e5e7eb; border-radius:18px; overflow:hidden;">
            <div style="background:linear-gradient(135deg, #1d4ed8, #2563eb); padding:32px 24px; color:#ffffff;">
                <p style="margin:0 0 8px; font-size:12px; letter-spacing:0.18em; text-transform:uppercase;">Vendor Request</p>
                <h1 style="margin:0; font-size:28px; line-height:1.2;">We received your vendor application.</h1>
            </div>

            <div style="padding:28px 24px;">
                <p style="margin:0 0 16px; font-size:16px; line-height:1.7;">
                    Hello {{ $vendor->name }}, your request to become a vendor on <strong>{{ $siteName }}</strong> has been submitted successfully.
                </p>

                <div style="margin:24px 0; padding:18px; background:#eff6ff; border:1px solid #bfdbfe; border-radius:14px;">
                    <p style="margin:0 0 10px; font-size:15px; font-weight:700;">Submitted details</p>
                    <p style="margin:0; font-size:15px; line-height:1.8;">
                        <strong>Store Name:</strong> {{ $vendor->store_name ?: 'Not provided' }}<br>
                        <strong>Verification Email:</strong> {{ $vendor->verification_email ?: $vendor->email }}<br>
                        <strong>Verification Phone:</strong> {{ $vendor->verification_phone ?: ($vendor->phone ?: 'Not provided') }}<br>
                        <strong>Status:</strong> Pending review
                    </p>
                </div>

                <p style="margin:0 0 16px; font-size:16px; line-height:1.7;">
                    Our team will review your information and send you another email as soon as your request is approved or declined.
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
