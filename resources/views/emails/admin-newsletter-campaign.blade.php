<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $siteName }}</title>
</head>
<body style="margin:0; padding:0; background-color:#f3f4f6; font-family:Arial, Helvetica, sans-serif; color:#111827;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background-color:#f3f4f6; padding:24px 12px;">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="max-width:640px; background-color:#ffffff; border-radius:12px; overflow:hidden;">
                    <tr>
                        <td style="background:linear-gradient(135deg, #2563eb, #1d4ed8); padding:32px 24px; text-align:center;">
                            <h1 style="margin:0; color:#ffffff; font-size:28px; font-weight:700;">{{ $siteName }}</h1>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:32px 24px;">
                            <div style="font-size:16px; line-height:1.7; color:#374151;">
                                {!! nl2br(e($messageBody)) !!}
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:0 24px 32px; font-size:13px; line-height:1.6; color:#6b7280;">
                            You are receiving this email because you subscribed to updates from {{ $siteName }}.
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
