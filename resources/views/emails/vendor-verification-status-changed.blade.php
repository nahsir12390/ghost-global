<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vendor Verification Status</title>
</head>
<body style="font-family: Arial, sans-serif; background: #f8fafc; color: #111827; margin: 0; padding: 24px;">
    <div style="max-width: 640px; margin: 0 auto; background: #ffffff; border: 1px solid #e5e7eb; border-radius: 16px; overflow: hidden;">
        @if($status === 'approved')
            <div style="background: linear-gradient(135deg, #059669, #10b981); color: #ffffff; padding: 24px;">
                <h1 style="margin: 0; font-size: 22px;">Vendor Verification Approved</h1>
                <p style="margin: 8px 0 0; opacity: 0.9;">Your vendor account is now verified on {{ $siteName }}.</p>
            </div>
        @elseif($status === 'rejected')
            <div style="background: linear-gradient(135deg, #991b1b, #dc2626); color: #ffffff; padding: 24px;">
                <h1 style="margin: 0; font-size: 22px;">Vendor Verification Declined</h1>
                <p style="margin: 8px 0 0; opacity: 0.9;">Your verification request was not approved.</p>
            </div>
        @else
            <div style="background: linear-gradient(135deg, #7c3aed, #a855f7); color: #ffffff; padding: 24px;">
                <h1 style="margin: 0; font-size: 22px;">Vendor Verification Updated</h1>
                <p style="margin: 8px 0 0; opacity: 0.9;">Your verification status has changed.</p>
            </div>
        @endif

        <div style="padding: 24px;">
            <p style="margin-top: 0; font-size: 16px;">Hello {{ $vendor->name }},</p>
            <p>Your vendor verification status on {{ $siteName }} is now <strong>{{ ucfirst($status) }}</strong>.</p>

            @if($notes)
                <div style="background: #f8fafc; border-left: 4px solid #3b82f6; padding: 12px; margin: 16px 0; border-radius: 4px;">
                    <p style="margin: 0; font-weight: 600; color: #1e40af;">Admin Notes:</p>
                    <p style="margin: 8px 0 0; color: #1e3a8a;">{{ $notes }}</p>
                </div>
            @endif

            <table style="width: 100%; border-collapse: collapse; margin: 24px 0;">
                <tr><td style="padding: 10px 0; color: #6b7280; width: 180px;">Store Name</td><td style="padding: 10px 0; font-weight: 600;">{{ $vendor->store_name ?: 'N/A' }}</td></tr>
                <tr><td style="padding: 10px 0; color: #6b7280;">Status</td><td style="padding: 10px 0; font-weight: 600;">{{ ucfirst($status) }}</td></tr>
                <tr><td style="padding: 10px 0; color: #6b7280;">Updated At</td><td style="padding: 10px 0; font-weight: 600;">{{ now()->format('M d, Y H:i') }}</td></tr>
            </table>
        </div>

        <div style="background: #f3f4f6; padding: 16px; text-align: center; font-size: 12px; color: #6b7280; border-top: 1px solid #e5e7eb;">
            <p style="margin: 0;">© {{ now()->year }} {{ $siteName }}. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
