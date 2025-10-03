<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Invitation</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Arial, sans-serif;
            line-height: 1.6;
            color: #000;
            background-color: #fff;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 640px;
            margin: 0 auto;
            padding: 32px 10px;
        }

        .card {
            border: 1px solid #e5e5e5;
            border-radius: 12px;
            padding: 16px;
            background: #fff;
        }

        .header {
            text-align: center;
            border-bottom: 1px solid #e5e5e5;
            padding-bottom: 16px;
            margin-bottom: 24px;
        }

        .header h1 {
            font-size: 24px;
            font-weight: 600;
            margin: 0;
            color: #000;
        }

        .content h2 {
            font-size: 20px;
            font-weight: 600;
            margin: 0 0 16px;
            color: #000;
        }

        .content p {
            margin: 12px 0;
            color: #111;
            font-size: 15px;
        }

        .button {
            display: inline-block;
            background-color: #000;
            color: #fff !important;
            padding: 12px 24px;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 500;
            font-size: 15px;
            margin: 24px 0;
        }

        .button:hover {
            background-color: #333;
        }

        ul {
            padding-left: 20px;
            margin: 12px 0;
        }

        ul li {
            margin-bottom: 8px;
            color: #111;
            font-size: 14px;
        }

        .footer {
            border-top: 1px solid #e5e5e5;
            padding-top: 20px;
            margin-top: 32px;
            font-size: 13px;
            color: #666;
            text-align: center;
        }

        strong {
            color: #000;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="card">
            <!-- Header -->
            <div class="header">
                <h1>Welcome to {{ config('app.name') }}</h1>
            </div>

            <!-- Content -->
            <div class="content">
                <h2>You're Invited</h2>
                <p>Hello,</p>

                <p>You have been invited to join <strong>{{ config('app.name') }}</strong> as a
                    <strong>{{ $invitation->role->label }}</strong>.
                </p>

                <p>To accept this invitation and set up your account, please click below:</p>

                <div style="text-align: center;">
                    <a href="{{ $invitationLink }}" class="button">Accept Invitation</a>
                </div>

                <p><strong>Important:</strong></p>
                <ul>
                    <li>This invitation expires on {{ $invitation->expires_at->format('F j, Y \a\t g:i A') }}.</li>
                    <li>If you prefer not to accept, you can ignore this email.</li>
                </ul>

                <p>If you have questions, please reach out to your administrator.</p>

                <p>Best regards,<br>
                    The {{ config('app.name') }} Team</p>
            </div>

            <!-- Footer -->
            <div class="footer">
                <p>This invitation was sent by {{ $invitation->inviter->name ?? 'an administrator' }}.</p>
                <p>If you weren’t expecting this, kindly disregard this email.</p>
            </div>
        </div>
    </div>
</body>

</html>
