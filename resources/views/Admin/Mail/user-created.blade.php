<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Account Created</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Arial, sans-serif;
            line-height: 1.6;
            background: #fff;
            color: #000;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 640px;
            margin: 0 auto;
            padding: 32px 20px;
        }

        .card {
            border: 1px solid #e5e5e5;
            border-radius: 12px;
            padding: 32px;
            background: #fff;
        }

        .header {
            text-align: center;
            border-bottom: 1px solid #e5e5e5;
            padding-bottom: 16px;
            margin-bottom: 24px;
        }

        .header h1 {
            font-size: 22px;
            font-weight: 600;
            margin: 0;
        }

        .content p {
            margin: 12px 0;
            font-size: 15px;
            color: #111;
        }

        .button {
            display: inline-block;
            margin: 24px 0;
            padding: 12px 24px;
            background: #000;
            color: #fff !important;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 500;
            font-size: 15px;
        }

        .footer {
            margin-top: 24px;
            padding-top: 16px;
            border-top: 1px solid #e5e5e5;
            font-size: 13px;
            color: #666;
            text-align: center;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="card">
            <div class="header">
                <h1>Account Created Successfully</h1>
            </div>

            <div class="content">
                <p>Hello {{ $user->name }},</p>
                <p>Your account has been created successfully under <strong>{{ config('app.name') }}</strong>.</p>
                <p>You can now log in using your email: <strong>{{ $user->email }}</strong></p>

                <div style="text-align: center;">
                    <a href="{{ env('FRONTEND_URL') }}/login" class="button">Login to Dashboard</a>
                </div>

                <p>We’re excited to have you onboard.</p>
                <p>Regards,<br>The {{ config('app.name') }} Team</p>
            </div>

            <div class="footer">
                <p>If you didn’t request this account, please contact our support team immediately.</p>
            </div>
        </div>
    </div>
</body>

</html>
