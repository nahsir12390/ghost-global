<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vendor Verification Submitted</title>
</head>
<body style="font-family: Arial, sans-serif; background: #f8fafc; color: #111827; margin: 0; padding: 24px;">
    <div style="max-width: 640px; margin: 0 auto; background: #ffffff; border: 1px solid #e5e7eb; border-radius: 16px; overflow: hidden;">
        <div style="background: linear-gradient(135deg, #991b1b, #dc2626); color: #ffffff; padding: 24px;">
            <h1 style="margin: 0; font-size: 22px;">New Vendor Verification Request</h1>
            <p style="margin: 8px 0 0; opacity: 0.9;">A vendor has submitted verification documents for admin review.</p>
        </div>

        <div style="padding: 24px;">
            <p style="margin-top: 0;">A new vendor verification request has been submitted on {{ $siteName }} with the following details:</p>

            <table style="width: 100%; border-collapse: collapse; margin: 16px 0;">
                <tr><td style="padding: 10px 0; color: #6b7280; width: 180px;">Vendor Name</td><td style="padding: 10px 0; font-weight: 600;">{{ $vendor->name }}</td></tr>
                <tr><td style="padding: 10px 0; color: #6b7280;">Store Name</td><td style="padding: 10px 0; font-weight: 600;">{{ $vendor->store_name ?: 'N/A' }}</td></tr>
                <tr><td style="padding: 10px 0; color: #6b7280;">Account Email</td><td style="padding: 10px 0; font-weight: 600;">{{ $vendor->email }}</td></tr>
                <tr><td style="padding: 10px 0; color: #6b7280;">Verification Email</td><td style="padding: 10px 0; font-weight: 600;">{{ $vendor->verification_email }}</td></tr>
                <tr><td style="padding: 10px 0; color: #6b7280;">Phone</td><td style="padding: 10px 0; font-weight: 600;">{{ $vendor->verification_phone }}</td></tr>
                <tr><td style="padding: 10px 0; color: #6b7280;">Submitted At</td><td style="padding: 10px 0; font-weight: 600;">{{ optional($vendor->verification_submitted_at)->format('M d, Y H:i') }}</td></tr>
            </table>

            <p style="margin-bottom: 0;">Open the admin dashboard to review the submitted documents and approve or decline the request.</p>
        </div>
    </div>
</body>
</html>
