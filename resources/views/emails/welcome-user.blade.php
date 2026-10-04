<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to {{ $siteName }}</title>
</head>
<body style="margin:0; padding:0; background-color:#f8fafc; font-family:Arial, Helvetica, sans-serif; color:#1f2937;">
    <div style="max-width:680px; margin:0 auto; padding:32px 20px;">
        <div style="background:#ffffff; border:1px solid #e5e7eb; border-radius:18px; overflow:hidden;">
            <div style="background:linear-gradient(135deg, #dc2626, #f97316); padding:32px 24px; color:#ffffff;">
                <p style="margin:0 0 8px; font-size:12px; letter-spacing:0.18em; text-transform:uppercase;">Welcome</p>
                <h1 style="margin:0; font-size:28px; line-height:1.2;">Hello {{ $user->name }}, your account is ready.</h1>
            </div>

            <div style="padding:28px 24px;">
                <p style="margin:0 0 16px; font-size:16px; line-height:1.7;">
                    Thank you for joining <strong>{{ $siteName }}</strong>. We are excited to have you here.
                </p>


                    <p style="margin:0 0 16px; font-size:16px; line-height:1.7;">
                        Your customer account has been created successfully. You can now browse products, place orders, track purchases, and manage your account anytime.
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
